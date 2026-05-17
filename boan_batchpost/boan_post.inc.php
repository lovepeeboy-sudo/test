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
class boan_post{
    var $forum = null;
    var $param = [];
    var $tid;
    var $pid;
    var $vars;
    public function __construct($fid = null){
        global $_G;
        if(empty($_G['cache']['plugin'])){
            loadcache('plugin');
        }
        $this->vars = $_G['cache']['plugin']['boan_batchpost'];

        require_once libfile('class/credit');
        
        require_once libfile('function/post');
        
        include_once libfile('function/forum');
        
        if($fid) {
            loadforum($fid);
            $this->forum = C::app()->var['forum'];
        }
    }
    
    private function call_func($func){
        
        $class = $func['class'];
        $method = $func['method'];
        $param_arr = $func['para'];
        if(!empty($class) && !empty($method)){
            call_user_func_array(array($class,$method),$param_arr);
        }elseif(!empty($method)){
            call_user_func_array($method, $param_arr);
        }
    }
    
    
    public function setthreadcover($pid, $tid = 0, $aid = 0, $countimg = 0, $imgurl = '',$cuttype = 2) {
        global $_G;
        $cover = 0;
        if(empty($_G['uid']) || empty($_G['setting']['forumpicstyle']['thumbheight']) || empty($_G['setting']['forumpicstyle']['thumbwidth'])) {
            return false;
        }
        
        if(($pid || $aid) && empty($countimg)) {
            
            if(empty($imgurl)) {
                if($aid) {
                    $attachtable = 'aid:'.$aid;
                    $attach = C::t('forum_attachment_n')->fetch('aid:'.$aid, $aid, array(1, -1));
                } else {
                    $attachtable = 'pid:'.$pid;
                    $attach = C::t('forum_attachment_n')->fetch_max_image('pid:'.$pid, 'pid', $pid);
                }
                if(!$attach) {
                    return false;
                }
                if(empty($_G['forum']['ismoderator']) && $_G['uid'] != $attach['uid']) {
                    return false;
                }
                $pid = empty($pid) ? $attach['pid'] : $pid;
                $tid = empty($tid) ? $attach['tid'] : $tid;
                $picsource = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachdir']).'forum/'.$attach['attachment'];
            } else {
                $picsource = $imgurl;
            }
            
            $basedir = !$_G['setting']['attachdir'] ? (DISCUZ_ROOT.'./data/attachment/') : $_G['setting']['attachdir'];
            $coverdir = 'threadcover/'.substr(md5($tid), 0, 2).'/'.substr(md5($tid), 2, 2).'/';
            dmkdir($basedir.'./forum/'.$coverdir);
            
           require_once libfile('class/image');
            $image = new image();
            $tmpfilename = '';
            $parse = parse_url($picsource);
            if($parse['path'] && $_G['BOAN_OSS']){
                $parse['path'][0] == '/' && ($parse['path'] = substr($parse['path'], 1));
                if($_G['BOAN_OSS']->isObject($parse['path'])){
                    $tmpfilename =$_G['setting']['attachdir'].'temp/'.random(16).substr(strrchr($parse['path'], '.'), 0);
                    if($_G['BOAN_OSS']->downFile($tmpfilename, $parse['path'])){
                        $picsource = $tmpfilename;
                    }
                    
                }
            }
          
            if($image->Thumb($picsource, 'forum/'.$coverdir.$tid.'.jpg', $_G['setting']['forumpicstyle']['thumbwidth'], $_G['setting']['forumpicstyle']['thumbheight'], $cuttype)) {
              
                $remote = '';
                if(getglobal('setting/ftp/on')) {
                    if(ftpcmd('upload', 'forum/'.$coverdir.$tid.'.jpg')) {
                        $remote = '-';
                    }
                }
                $cover = C::t('forum_attachment_n')->count_image_by_id($attachtable, 'pid', $pid);
                if($imgurl && empty($cover)) {
                    $cover = 1;
                }
                $cover = $remote.$cover;
            } else {
                !empty($tmpfilename) &&  @unlink($tmpfilename);
                return false;
            }
        }
       !empty($tmpfilename) &&  @unlink($tmpfilename);
        if($countimg) {
            if(empty($cover)) {
                $thread = C::t('forum_thread')->fetch($tid);
                $oldcover = $thread['cover'];
                
                $cover = C::t('forum_attachment_n')->count_image_by_id('tid:'.$tid, 'pid', $pid);
                if($cover) {
                    $cover = $oldcover < 0 ? '-'.$cover : $cover;
                }
            }
        }
        if($cover) {
            C::t('forum_thread')->update($tid, array('cover' => $cover));
            if($attach){
                C::t('forum_threadimage')->delete_by_tid($tid);
                C::t('forum_threadimage')->insert(array(
                    'tid' => $tid,
                    'attachment' => $attach['attachment'],
                    'remote' => $attach['remote'],
                ));
            }
           
            return true;
        }
    }
    
    public function newthread($para,$aids = array(),$optiondata = array(),$before = array(),$after = array()){
        global $_G;
        $this->tid = $this->pid = 0;
        $this->_init_parameters($para);
        $member = getuserbyuid($this->param['uid']);
        $author = !$this->param['isanonymous'] ? $member['username'] : '';
     
        if(trim($this->param['subject']) == '') {
            
            return false;
        }
        
        if(!$this->param['sortid'] && !$this->param['special'] && trim($this->param['message']) == '') {
            return false;
        }
        
        
        if(!empty($this->forum) || $this->forum['fid'] != $this->param['fid']){
            loadforum($this->param['fid']);
            $this->forum = C::app()->var['forum'];
        }
       
        $this->call_func($before);

        if(!empty($this->vars['is_publish']) && $this->param['dateline'] > TIMESTAMP){
            $this->param['displayorder'] = -4;
            $this->param['invisible'] = -3;
        }

        $newthread = array(
            'fid' => $this->param['fid'],
            'posttableid' => 0,
            'readperm' => $this->param['readperm'],
            'price' => $this->param['price'],
            'typeid' => $this->param['typeid'],
            'sortid' => $this->param['sortid'],
            'author' => $author,
            'authorid' => $member['uid'],
            'subject' => $this->param['subject'],
            'dateline' => $this->param['dateline'],
            'lastpost' => $this->param['lastpost'],
            'lastposter' => $author,
            'displayorder' => $this->param['displayorder'],
            'digest' => $this->param['digest'],
            'special' => $this->param['special'],
            'attachment' => $this->param['attachment'],
            'moderated' => $this->param['moderated'],
            'status' => $this->param['status'],
            'isgroup' => $this->param['isgroup'],
            'replycredit' => $this->param['replycredit'],
            'closed' => $this->param['closed'] ? 1 : 0
        );

        
        $this->tid = C::t('forum_thread')->insert($newthread, true);
        
        if(!$this->tid){
            return false;
        }
        if($this->param['displayorder'] == -4){
            loadcache('cronpublish',true);
            $cron_publish_ids = getglobal('cache/cronpublish');
            if(empty($cron_publish_ids)){
                $cron_publish_ids = array();
            }else if(!is_array($cron_publish_ids)){
                $cron_publish_ids = dunserialize($cron_publish_ids);
            }

            $cron_publish_ids[$this->tid] = $this->tid;
            $cron_publish_ids = serialize($cron_publish_ids);
            savecache('cronpublish', $cron_publish_ids);
        }

        $dataChanged = false;

        useractionlog($member['uid'], 'tid');
        
        if((TIMESTAMP - $this->param['dateline']) <= 5*60){
            C::t('forum_newthread')->insert(array(
                'tid' => $this->tid,
                'fid' => $this->forum['fid'],
                'dateline' => $this->param['dateline'],
            ));
        }
        
        $class_tag = new tag();
        
        $this->param['tagstr'] = $class_tag->add_tag($this->param['tags'], $this->tid, 'tid');
        
        $this->pid = insertpost(array(
            'fid' => $this->param['fid'],
            'tid' => $this->tid,
            'first' => '1',
            'author' => $member['username'],
            'authorid' => $member['uid'],
            'subject' => $this->param['subject'],
            'dateline' => $this->param['dateline'],
            'message' => $this->param['message'],
            'useip' => $this->param['useip'] ? $this->param['useip'] : getglobal('clientip'),
            'port' => $this->param['port'] ? $this->param['port'] : getglobal('remoteport'),
            'invisible' => $this->param['invisible'],
            'anonymous' => $this->param['isanonymous'],
            'usesig' => $this->param['usesig'],
            'htmlon' => $this->param['htmlon'],
            'bbcodeoff' => $this->param['bbcodeoff'],
            'smileyoff' => $this->param['smileyoff'],
            'parseurloff' => $this->param['parseurloff'],
            'attachment' => '0',
            'tags' => $this->param['tagstr'],
            'replycredit' => 0,
            'status' => $this->param['pstatus']
        ));
        
        if(!$this->pid){
            return false;
        }
        
        updatepostcredits('+',  $member['uid'], 'post', $this->tid);
        
        $attachment = 0;
        $image_atts = array();
        foreach ($aids as $aid){
            $attach = C::t('forum_attachment_n')->fetch(127,$aid);
            if(count($attach) > 0 ){
                $attach['tid'] = $this->tid;
                $attach['pid'] = $this->pid;
                $tableid = substr($this->tid, -1,1);
                C::t('forum_attachment_n')->insert($tableid,$attach,false, true);
                C::t('forum_attachment_unused')->delete($aid);
                C::t('forum_attachment')->update($aid, array('tid' => $this->tid, 'pid' => $this->pid, 'tableid' =>$tableid));
                $attach['isimage'] && $attachment = 2;
                $attachment == 0 && $attachment = 1;
                if($attach['isimage'] == 1){
                    $image_atts[] = $attach;
                }
            }
        }
        if($attachment){
            C::t('forum_thread')->update($this->tid,array('attachment' => $attachment));
            C::t('forum_post')->update(0,$this->pid,array('attachment' => $attachment));
        }
        
        $types = is_array($_G['forum']['threadsorts']['types']) ? $_G['forum']['threadsorts']['types'] : array();
        $sortid = array_key_exists($this->param['sortid'],$types) ? $this->param['sortid'] : 0;
       
        if($sortid > 0 && count($optiondata)){
            require_once libfile('post/threadsorts', 'include');
            $tid = $this->tid;
            $fid = $this->param['fid'];
            $field = '';
            $value = '';
            foreach ($_G['forum_optionlist'] as $optionid => $option){
               
                if(isset($optiondata[$option['identifier']])){
                    $v = $optiondata[$option['identifier']];
                }elseif($option['type'] == 'image' && !empty($image_atts)){
                    $temp = array_shift($image_atts);
                    
                    $v = array('aid' => $temp['aid'],'url' => ($temp['remote'] == 0 ? $_G['setting']['attachurl'] : $_G['setting']['ftp']['attachurl']).'forum/'.$temp['attachment']);
                    $v =daddslashes(serialize($v));
                  
                }else{
                    $v = '';
                }
                $field .= empty($field) ? $option['identifier'] : ','.$option['identifier'];
                if(is_array($v)){
                    $v = implode("\t", $v); 
                }
                $v = DB::quote($v);
                $value .= empty($value) ? $v : ','.$v;
               
                DB::query("INSERT INTO %t (sortid,tid,fid,optionid,expiration,value)VALUES($sortid,$tid,$fid,$optionid,0,$v)",array('forum_typeoptionvar'));
            }
            if($field){
                DB::query("INSERT INTO %t{$sortid} ($field,tid,fid,dateline,expiration)VALUES($value,'$tid','$fid',0,0)",array('forum_optionvalue'));;
            }
        }
        
        
        $subject = str_replace("\t", ' ', $this->param['subject']);
        $lastpost = "$this->tid\t".$subject."\t".TIMESTAMP."\t$author";
        C::t('forum_forum')->update($this->forum['fid'], array('lastpost' => $lastpost));
        C::t('forum_forum')->update_forum_counter($this->forum['fid'], 1, 1, 1);
        if($this->forum['type'] == 'sub') {
            C::t('forum_forum')->update($this->forum['fup'], array('lastpost' => $lastpost));
        }
        
        
        $this->call_func($after);
        
        return true;
    }
    
    protected function _init_parameters($param){
        $varname = array(
            'fid' => 0,
            'uid' => 0,
            'posttableid' => 0,
            'readperm' => 0,
            'price' => 0,
            'typeid' => 0,
            'sortid' => 0,
            'subject' => '',
            'dateline' => time(),
            'lastpost' => time(),
            'lastposter' => '',
            'displayorder' => 0,
            'digest' => 0,
            'special' => 0,
            'attachment' => 0,
            'moderated' => 0,
            'status' => 32,
            'isgroup' =>0,
            'replycredit' => 0,
            'closed' => 0,
            //post
            'message' => '',
            'useip' => '',
            'port' => '',
            'invisible' => 0,
            'anonymous' => 0,
            'usesig' => 0,
            'htmlon' => 0,
            'bbcodeoff' => 0,
            'smileyoff' => -1,
            'parseurloff' => 0,
            'attachment' => '0',
            'tags' => '',
            'replycredit' => 0,
            'pstatus' => 0,
        );
        $this->param = $varname;
        
        foreach($varname as $key => $val) {
            if(isset($param[$key])){
                $this->param[$key] = $param[$key];
            }
        }
    }
}