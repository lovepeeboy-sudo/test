<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: 平安批量发贴 商业版1.5.5
 *      下载地址: https://addon.dismall.com/plugins/boan_batchpost.html
 *      应用开发者: 平安网络科技
 *      开发者QQ: 527340870
 *      更新日期: 202502051743
 *      授权域名: cajian.angellily.cn
 *      授权码: 2025010315Hd4d99drex
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
define('BOAN_BATCHPOST_NAME', 'plugin/boan_batchpost');
require_once   DISCUZ_ROOT.'./source/plugin/boan_batchpost/common.func.php';
global $_G;

$av = $_G['setting']['plugins']['available'];

!is_array($av) && $av = array();
in_array("boan_h5upload",$av) !==false &&
((file_exists(DISCUZ_ROOT.'./source/plugin/boan_h5upload/oss/loadoss.php') &&  require_once(DISCUZ_ROOT.'./source/plugin/boan_h5upload/oss/loadoss.php'))
|| (file_exists(DISCUZ_ROOT.'./source/plugin/boan_oss/loadoss.php') &&  require_once(DISCUZ_ROOT.'./source/plugin/boan_oss/loadoss.php')));

if(empty($_G['cache']['plugin'])){
    loadcache('plugin');
}
$vars = $_G['cache']['plugin']['boan_batchpost'];
loadcache('boan_batchpost_onkey',true);
$cache = $_G['cache']['boan_batchpost_onkey'];

if(!is_array($cache)){
    $cache = array();
}

$info = array(
    'code' => '201',
    'msg' => lang(BOAN_BATCHPOST_NAME, '201'),
);

if($_GET['op'] == 'onekey' && $_GET['hash'] == md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])){
    $uid = $_G['uid'];
    $uids = explode(',', $cache['userlist']);
    $aids = array();
    for($i = 0; $i<100; $i++){
        if(isset($_GET['aid'.$i])){
            $aids[] = intval($_GET['aid'.$i]);
        }else{
            break;
        }
    }
    if(!empty($_GET['setting'])){
        $setting = base64_decode($_GET['setting']);
        $setting = json_decode($setting,true);
        if(is_array($setting)){
            $cache = array_merge($cache,$setting);
        }
      
    }
    if(!empty($aids) && $uid){
        $member = getuserbyuid($uid);
        
        require_once(DISCUZ_ROOT.'./source/plugin/boan_batchpost/boan_post.inc.php');
        
        require_once libfile('function/post');
        require_once libfile('function/forum');
        
        if(!empty($cache['forum_id'])){
            $fid = $cache['forum_id'];
        }else{
            $fid = dunserialize($cache['forum']);
            $fid = $fid[0];
        }
        
        $info = array(
            'code' => '301',
            'msg' => lang(BOAN_BATCHPOST_NAME, '301'),
        );
       $allow_groups = dunserialize($vars['allow_groups']);
       if($member['adminid'] || in_array($member['groupid'], $allow_groups)){
            $info = array(
                'code' => '302',
                'msg' => lang(BOAN_BATCHPOST_NAME, '302'),
            );
            $_G['fid'] = $fid;
            $attachlist = array();
            $imagelist = array();
            foreach ($aids as $aid){
                
                $attachs = getattach(0,0,$aid);
                $images = $attachs['imgattachs']['unused'];
                !is_array($images) && $images = array();
                $attachs = $attachs['attachs']['unused'];
                !is_array($attachs) && $attachs = array();
                if(is_array($attachs[0]) && count($attachs[0]) > 0 ){
                    $attachlist[] = $attachs[0];
                }
                if(is_array($images[0]) && count($images[0]) > 0 ){
                    $imagelist[]  = $images[0];
                }
                
            }
            if( !empty($attachlist) || !empty($imagelist) ){
                $p = new boan_post();
                $htmlon = 0;
                $info = array(
                    'code' => '303',
                    'msg' => lang(BOAN_BATCHPOST_NAME, '303'),
                );
               
               $subject = trim($cache['subject']);
               if(empty($subject)){
                   $subject = dhtmlspecialchars($_GET['subject']);
                   $subject = diconv(urldecode($subject), 'UTF-8');
               }
               $message = $cache['message'];
               $message = preg_replace_callback('/\{(\d+)\}/i', function($m) use($imagelist) {
                   if(isset($m[1]) && $imagelist[$m[1]-1]){
                       return '[attach]'.$imagelist[$m[1]-1]['aid'].'[/attach]';
                   }
                   return '';
               }, $message);
               
             
           
               $message = preg_replace_callback('/\{a(\d+)\}/i', function($m) use($attachlist) {
                   if(isset($m[1]) && $attachlist[$m[1]-1]){
                       return '[attach]'.$attachlist[$m[1]-1]['aid'].'[/attach]';
                   }
                   return '';
               }, $message);
               
            
               
               if(!empty($cache['showtxt'])){
                   $basedir = !$_G['setting']['attachdir'] ? (DISCUZ_ROOT.'./data/attachment/') : $_G['setting']['attachdir'];
                   $showarr = array('txt','html','htm');
                   foreach ($attachlist as $k => $att){
                       
                       $tmpfilename =$_G['setting']['attachdir'].'temp/'.random(16).'.tmp';
                       if(!in_array(strtolower($att['ext']), $showarr)){
                           continue;
                       }
                       if(!$att['remote']){
                           $source = $basedir.'./forum/'.$att['attachment'];
                       }else{
                           $tmpfilename && @unlink($tmpfilename);
                           $object = OSS_BASEDIR.'forum/'.$att['attachment'];
                           if($_G['BOAN_OSS'] && $_G['BOAN_OSS']->isObject($object) && $_G['BOAN_OSS']->downFile($tmpfilename, $object)){
                               $source = $tmpfilename;
                           }else{
                               continue;
                           }
                       }
                       $temp_s = file_get_contents($source);
                      
                       empty($cache['txtcharset']) &&  $cache['txtcharset'] = 'gbk';
                       $cache['txtcharset'] == 'utf8' && $cache['txtcharset'] = 'utf-8';
                       $temp_s = diconv($temp_s, $cache['txtcharset'],CHARSET);
                       
                       if(strpos($message,'[attach]'.$att['aid'].'[/attach]') !== FALSE){
                 
                           $message = str_replace('[attach]'.$att['aid'].'[/attach]', $temp_s, $message);
                       }else{
                           $message .= $temp_s;
                       }
                      
                       $tmpfilename && @unlink($tmpfilename);
                       DB::delete('forum_attachment_unused', 'aid='.$att['aid']);
                       DB::delete('forum_attachment','aid='.$att['aid']);
                       dunlink($att);
                       unset($attachlist[$k]);
                       $att['ext'] != 'txt' && $htmlon = 1;
                   }
               }
               
               
               if($cache['att_repeat']){
                   $arr = array();
                   $arr =  DB::fetch_first('SELECT * FROM %t WHERE  subject=%s',array('forum_thread',$subject));
                   if(isset($arr['tid'])){
                        $temp = array_merge($imagelist,$attachlist);
                        foreach ($temp as $attach){
                            C::t('forum_attachment_unused')->delete($attach['aid']);
                            C::t('forum_attachment')->delete_by_id('aid',$attach['aid']);
                            dunlink($attach);
                        }
                        echo json_encode(array('code'=>304,'msg' => lang(BOAN_BATCHPOST_NAME, '304')));
                        dexit();
                    }
                }
                
               
                $tags = $cache['tags'];
                $typeid = $cache['typeid'];
                $sortid = $cache['sortid'];
                $optiondata = $cache['typeoption'];
              
                
                $attsize = 0;
                foreach ($attachlist as $att){
                    $attsize += $att['filesize'];
                }
              
                $message = parseVar($message, ['subject'=>$subject,'attsize'=>$attsize]);
                foreach ($optiondata as $k => $v){
                    if(!is_array($v)){
                        $optiondata[$k] = parseVar($v, ['subject'=>$subject,'attsize'=>$attsize]);
                    }
                   
                }
              
                $tags1 = explode(',', $tags);
                if($cache['tagct'] && count($tags1) > $cache['tagct']){
                    $tags_t = array_rand($tags1,$cache['tagct']);
                    $tags = [];
                    foreach ($tags_t as $k){
                        $tags[] = $tags1[$k];
                    }
                    $tags = implode(',', $tags);
                }
                
                
                    
                $uids = explode(',', $cache['userlist']);
                if(isset($uids[0]) && $uids[0]){
                    $t1 = rand(0,count($uids)-1);
                    $uid = $uids[$t1];
                }
                $member = getuserbyuid($uid);
                if(empty($cache['begintime'])){
                    $posttime = time() - rand(0,$cache['posttime'])*60;
                }else{
                    $posttime = strtotime($cache['begintime']) + rand(0,$cache['posttime'])*60;
                }
                
                $cache['readperm'] = intval($cache['readperm']);
                
                $params = array(
                    'fid' => $fid,
                    'uid' => $uid,
                    'price' => $cache['price'],
                    'subject' => $subject,
                    'message' => censor($message),
                    'readperm' => $cache['readperm'],
                    'dateline' => $posttime,
                    'tags' => $tags,
                    'typeid' => $typeid,
                    'sortid' => $sortid,
                    'htmlon' => $htmlon,
                );  
                
                
                if(isset($optiondata['Texture_size']) && !empty($_GET['hw'])){
                    $optiondata['Texture_size'] = dhtmlspecialchars($_GET['hw']);
                }
                    $p->newthread($params,$aids,$optiondata);
                    if($p->tid && $p->pid){
                        $attach['tid'] = $p->tid;
                        $attach['pid'] = $p->pid;
                        
                        loadcache('groupreadaccess');
                        $readperm = 0;
                        foreach ($_G['cache']['groupreadaccess'] as $v){
                            if($v['groupid'] == $cache['att_readaccess']){
                                $readperm = $v['readaccess'];
                                break;
                            }
                        }
                        
                        $price = $cache['att_price'];
                        foreach ($attachlist as $attach){
                            C::t('forum_attachment_n')->update('tid:'.$p->tid,$attach['aid'],array('price'=>$price,'readperm'=>$readperm));
                        }
                        $p->setthreadcover($p->pid,$p->tid,0,0,'',$cache['cuttype'] ? 1 : 2);
                        
                        $basedir = !$_G['setting']['attachdir'] ? (DISCUZ_ROOT.'./data/attachment/') : $_G['setting']['attachdir'];
                        if($cache['watermethod'] == 1 || ($cache['watermethod'] == 2 && file_exists(DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/water.png'))){
                            require_once libfile('class/image');
                            $image = new image();
                            $tmpfilename =$_G['setting']['attachdir'].'temp/'.random(16).'.tmp';
                            foreach ($imagelist as $attimage){
                                if(!$attimage['remote']){
                                    $source = $basedir.'./forum/'.$attimage['attachment'];
                                }else{
                                    $tmpfilename && @unlink($tmpfilename);
                                    $object = OSS_BASEDIR.'forum/'.$attimage['attachment'];
                                    if($_G['BOAN_OSS'] && $_G['BOAN_OSS']->isObject($object) && $_G['BOAN_OSS']->downFile($tmpfilename, $object)){
                                        $source = $tmpfilename;
                                    }else{
                                        continue;
                                    }
                                }
                                
                                if($cache['waterMinW'] || $cache['waterMinH']){
                                    $imginfo = @getimagesize($source);
                                    if(($imginfo === FALSE) || ($cache['waterMinW'] && $imginfo[0] < $cache['waterMinW']) || ($cache['waterMinH'] && $imginfo[1] < $cache['waterMinH'])){
                                        continue;
                                    }
                                }
                                
                                if($cache['watermethod'] == 1){
                                    $image->Watermark($source);
                                }else{
                                    $options = array('width' => intval($cache['waterW']),
                                        'height' => intval($cache['waterH']),
                                        'angle' => intval($cache['waterA']),
                                        'opacity' => intval($cache['waterO']),);
                                    $textoptions = array();
                                    
                                    if($cache['watertextstyle']){
                                        $textoptions = array('text'=>$cache['watertextstyle'],
                                            'file'=>DISCUZ_ROOT.'./source/plugin/boan_batchpost/font/'.$cache['watertextfile'],
                                            'color'=>$cache['watertextcolor'],
                                            'size'=>$cache['watertextsize'],
                                            'opacity'=>$cache['watertextO'],
                                            'angle' => $cache['watertextA'],
                                            'x_pos' => $cache['watertextHO'],
                                            'y_pos' =>  $cache['watertextVO'],
                                            'shadowx' => $cache['watertextHS'],
                                            'shadowy' => $cache['watertextVS'],
                                            'shadowcolor' => $cache['watertextSC'],
                                            'para' => array('username' => $member['username'],
                                                            'threadid' => $p->tid,
                                                            'threadtitle' => $subject,),
                                            'pos' => $cache['watertextpos'],
                                        );
                                    }
                                    
                                    $logooptions = array();
                                    if($cache['waterlogoallow']){
                                        $logooptions = array('file' => DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/logo.png',
                                            'opacity' => intval($cache['waterlogoO']),
                                            'pos' => intval($cache['waterlogopos']),
                                            'x_pos' => intval($cache['waterlogoHO']),
                                            'y_pos' => intval($cache['waterlogoHO']),
                                        );
                                    }
                                    
                                    clearstatcache();
                                    full_screen(DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/water.png',
                                        $source,'',
                                        $options,
                                        $textoptions,
                                        $logooptions);
                                }
                                
                                if($attimage['remote']){
                                    $_G['BOAN_OSS']->uploadFile($tmpfilename, $object,'public');
                                }
                               
                            }
                        }
                        $tmpfilename && @unlink($tmpfilename);
                        
                        if(getglobal('setting/ftp/on') && empty($_G['BOAN_OSS'])){
                            $temp = array_merge($imagelist,$attachlist);
                            foreach ($temp as $attach){
                                if(ftpcmd('upload','forum/'.$attach['attachment'])){
                                    @unlink($basedir.'./forum/'.$attach['attachment']);
                                    $thumb = $basedir.'./forum/'.$attach['attachment'].'.thumb.jpg';
                                    if(file_exists($thumb) && ftpcmd('upload','forum/'.$attach['attachment'].'.thumb.jpg')){
                                        @unlink($thumb);
                                    }
                                    $attach['remote'] = 1;
                                    $tableid = substr($p->tid, -1,1);
                                    C::t('forum_attachment_n')->update($tableid,$attach['aid'],array('remote'=>1));
                                }
                            }
                       }
                        $info = array(
                            'code' => '200',
                            'msg' => 'ok',
                        );
                    }
            }
        }
    }
}
$info['msg'] = diconv($info['msg'],CHARSET,'utf-8');
echo json_encode($info);

function parseVar($var,$params){
    global $_G;
    $str = $var;
    $str = preg_replace_callback('/\{(subject)\}/i', function($m) use($params) {
       if(isset($m[1])){
           return $params['subject'];
       }
       return $m[0];
    }, $str);
    
   $str = preg_replace_callback('/\{(sitename)\}/i', function($m) use($_G) {
        if(isset($m[1])){
            return $_G['setting'] ['sitename'];
        }
        return $m[0];
    }, $str);
   
   $str = preg_replace_callback('/\{(siteurl)\}/i', function($m) use($_G) {
       if(isset($m[1])){
           return $_G['siteurl'];
       }
       return $m[0];
   }, $str);
   
   $str = preg_replace_callback('/\{(username)\}/i', function($m) use($_G) {
       if(isset($m[1])){
           return $_G['username'];
       }
       return $m[0];
   }, $str);
   
   $str = preg_replace_callback('/\{(date)\}/i', function($m) use($_G) {
       if(isset($m[1])){
           
           return dgmdate(TIMESTAMP);
       }
       return $m[0];
   }, $str);
   
   $str = preg_replace_callback('/\{(size)\}/i', function($m) use($params) {
       if(isset($m[1])){
           
           return number_format($params['attsize']/(1024*1024),2);
       }
       return $m[0];
   }, $str);
           
    return $str;
}