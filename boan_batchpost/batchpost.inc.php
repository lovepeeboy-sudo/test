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
if(empty($_G['cache']['plugin'])){
    loadcache('plugin');
}

define('BOAN_BATCHPOST_NAME', 'plugin/boan_batchpost');

global $_G;
$vars = $_G['cache']['plugin']['boan_batchpost'];
$allow_groups = dunserialize($vars['allow_groups']);
if(!in_array($_G['groupid'], $allow_groups)){
    showmessage(lang(BOAN_BATCHPOST_NAME, '302'));    
}


require_once libfile('function/forumlist');

if(empty($_GET['setting']) && empty($_GET['reload']) && !submitcheck('onekeysubmit')){
      $_GET['setting'] = getcookie('boan_batchpost_onkey');
 }

if(!empty($_GET['setting'])){
    $setting = base64_decode($_GET['setting']);
    $setting = json_decode($setting,true);
    unset($_GET['setting']);
    $_GET = array_merge($_GET,$setting);
}


$userlist = empty($_GET['userlist']) ? '' : dhtmlspecialchars($_GET['userlist']);
$begintime = empty($_GET['begintime']) ? '' : dhtmlspecialchars($_GET['begintime']);

$posttime = empty($_GET['posttime']) ? '' : intval($_GET['posttime']);



$price = empty($_GET['price']) ? 0 : intval($_GET['price']);

$tags = empty($_GET['tags']) ? '' : dhtmlspecialchars($_GET['tags']);
$tagct = empty($_GET['tagct']) ? 0 : intval($_GET['tagct']);



$subject = empty($_GET['subject']) ? '' : dhtmlspecialchars($_GET['subject']);

$forum_id = empty($_GET['forum_id']) ? 0 : intval($_GET['forum_id']);

$forumlist =  '<select id="forumlist" name="forum_id">'.forumselect(FALSE, 0, $forum_id, TRUE).'</select>';


$typeid = intval($_GET['typeid']);

$typelist = '';

$sorts = '';

$sortid = intval($_GET['sortid']);

$message = dhtmlspecialchars($_GET['message']);

empty($message) && $message = "{1}{2}{3}{4}{5} at {date}";

$attprice = intval($_GET['attprice']);

$readperm_id = intval($_GET['readperm']);

$readaccess_id = intval($_GET['att_readaccess']);

$watermethod_id = intval($_GET['watermethod']);

$waterminw = intval($_GET['waterminw']);
$waterminh = intval($_GET['waterminh']);

$pmethod_id = intval($_GET['pmethod']);


$limitW = isset($_GET['limitW']) ? intval($_GET['limitW']) : 2600;


$separate = dhtmlspecialchars($_GET['separate']);
empty(getcookie('boan_batchpost_onkey')) && empty($cache1['separate']) && $separate = '-';

$cache1 = array();
$cache1['userlist']  =  $userlist;
$cache1['begintime'] = $begintime;
$cache1['posttime'] = $posttime;
$cache1['price'] = $price;
$cache1['tags'] = $tags;
$cache1['tagct'] = $tagct;
$cache1['subject'] = $subject;
$cache1['forum_id'] = $forum_id;
$cache1['typeid'] = $typeid;
$cache1['sortid'] = $sortid;
$cache1['typeoption'] = $_GET['typeoption'];
$cache1['message'] = $message;
$cache1['att_price'] = $attprice;
$cache1['att_readaccess'] = $readaccess_id;
$cache1['readperm'] = $readperm_id;
$cache1['watermethod'] = $watermethod_id;
$cache1['waterMinW'] = $waterminw;
$cache1['waterMinH'] = $waterminh;
$cache1['pmethod'] = $pmethod_id;
$cache1['limitW'] = $limitW;
$cache1['separate']  = $separate;



loadcache('boan_batchpost_onkey',true);
$cache = $_G['cache']['boan_batchpost_onkey'];

if(!isset($cache) || !is_array($cache)){
    $cache = array();
}

$cache = array_merge($cache,$cache1);

if(submitcheck('onekeysubmit')){
    $av = $_G['setting']['plugins']['available'];
    
    !is_array($av) && $av = array();
    in_array("boan_h5upload",$av) !==false &&( (file_exists(DISCUZ_ROOT.'./source/plugin/boan_h5upload/oss/loadoss.php') &&  require_once(DISCUZ_ROOT.'./source/plugin/boan_h5upload/oss/loadoss.php'))
    || (file_exists(DISCUZ_ROOT.'./source/plugin/boan_oss/loadoss.php') &&  require_once(DISCUZ_ROOT.'./source/plugin/boan_oss/loadoss.php')));
   
    if(empty(trim($cache['message'])) && empty($cache['typeoption'])){
        showmessage(lang(BOAN_BATCHPOST_NAME, 'onekey_one_error1'),'', 'error');
    }
    if(!isset($_G['cache']['forums'])) {
        loadcache('forums');
    }
    $forumcache = &$_G['cache']['forums'];
    $forum = $forumcache[$cache['forum_id']];
    
    loadcache('groupreadaccess');
    $ratitle = lang(BOAN_BATCHPOST_NAME, 'onekey_one_nolimit');
    foreach ($_G['cache']['groupreadaccess'] as $v){
        if($cache['att_readaccess'] == $v['groupid']){
            $ratitle = $v['grouptitle'];
            break;
        }
    }
    
    $typeids = DB::fetch_all('SELECT typeid,fid,name FROM %t',array('forum_threadclass'));
    $typetitle = lang(BOAN_BATCHPOST_NAME, 'onekey_one_none');
    foreach ($typeids as $v){
        if($cache['typeid'] == $v['typeid']){
            $typetitle = $v['name'];
            break;
        }
    }
    
    $cache['process_count'] = intval($cache['process_count']);
    $process_count = $cache['process_count'] ? $cache['process_count'] : 3;
    
    $postmaxsize = return_bytes(ini_get('post_max_size'));
    $hash = md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid']);
    $ossserver = '';
    $post_params = "{uid:{$_G['uid']},hash:'$hash',type:'attach'}";
   
    if(empty($_G['BOAN_OSSCONFIG'])){
        
        $upload_url = 'misc.php?mod=swfupload&action=swfupload&operation=upload';
        $fileVal = 'Filedata';
        $postmaxsize = return_bytes(ini_get('post_max_size'));
    }else{
        $ossserver = $_G['BOAN_OSS']::$oss_server_name;
        $upload_url =  $_G['BOAN_OSSCONFIG']['oss_bucket_url'];
        $fileVal = 'file';
        $postmaxsize = 1000000000;
    }
    
    if(empty($cache['limitW'])){
        $compress = 'false';
    }else{
        $compress = '{';
        $compress .= "width:{$cache['limitW']},";
        $compress .= 'noCompressIfLarger:false,crop:false,}';
    }
    
    $setting = json_encode($cache1);
    $setting = base64_encode($setting);

    dsetcookie('boan_batchpost_onkey', $setting, 86400*7);

    $url = $_G['siteurl'].'plugin.php?id=boan_batchpost:batchpost&setting='.$setting;
    
    $vars['Ignore_error'] = intval($vars['Ignore_error']);
    include template('boan_batchpost:header_common');
    include template('boan_batchpost:header');
    include template('boan_batchpost:onekey');
    include template('boan_batchpost:footer');
    
    exit();
    
}else{
    if(!empty($forum_id)){
        require_once libfile('function/forum');
        loadforum($forum_id);
        if(isset($_G['forum']['threadtypes']['types']) && count($_G['forum']['threadtypes']['types']) > 0){
            
            $typelist = '<select id="typeid" name="typeid">';
            $typelist .= '<option value="0">'.lang(BOAN_BATCHPOST_NAME, 'onekey_one_none').'</option>';
            
            foreach ($_G['forum']['threadtypes']['types'] as $key => $val){
                $selected = $typeid == $key ? 'selected' : '';
                $typelist .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
            }
            $typelist .= '</select>';
            
        }
        
        if(isset($_G['forum']['threadsorts']['types']) && count($_G['forum']['threadsorts']['types']) > 0){
            
            $sorts = '<select id="sortid" name="sortid">';
            $sorts .= '<option value="0">'.lang(BOAN_BATCHPOST_NAME, 'onekey_one_none').'</option>';
            foreach ($_G['forum']['threadsorts']['types'] as $key => $val){
                $selected = $sortid == $key ? 'selected' : '';
                $sorts .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                
            }
            $sorts .= '</select>';
            
            if($sortid){
                require_once libfile('post/threadsorts', 'include');
                
                foreach ($_G['forum_optionlist'] as $optionid => $option){
                    $val = $cache['typeoption'][$option['identifier']];
                    if($option['type'] == 'image'){
                        unset($_G['forum_optionlist'][$optionid]);
                        continue;
                    }
                    if(!empty($val)){
                        if($option['type'] == 'radio') {
                            $_G['forum_optionlist'][$optionid]['value'] = array($val => 'checked="checked"');
                        }elseif($option['type'] == 'select') {
                            $_G['forum_optionlist'][$optionid]['value'] = array($val => 'selected="selected"');
                        }elseif($option['type'] == 'checkbox'){
                            $arr1 = array();
                            foreach ($val as $v1){
                                $arr1[$v1][$v1] = 'checked="checked"';
                            }
                            $_G['forum_optionlist'][$optionid]['value'] = $arr1;
                            
                        }else {
                            $_G['forum_optionlist'][$optionid]['value'] = $val;
                        }
                    }
                }
            }
        }
        
    }
    
    loadcache('groupreadaccess');
    $groupreadaccess = '<select name="att_readaccess"><option value="0">'.lang(BOAN_BATCHPOST_NAME, 'onekey_one_nolimit').'</option>';
    foreach ($_G['cache']['groupreadaccess'] as $v){
        $groupreadaccess .= '<option value="'.$v['groupid'].($v['groupid'] == $readaccess_id ? '" selected' : '"').'>'.$v['grouptitle'].'</option>';
    }
    $groupreadaccess .= '</select>';
    
    $watermethod = '<select name="watermethod"><option value="0"'.(0 == $watermethod_id ? '" selected' : '"').'>'.lang(BOAN_BATCHPOST_NAME,'onekey_one_watermethod_1').'</option>';
    $watermethod .= '<option value="1"'.(1 == $watermethod_id ? '" selected' : '"').'>'.lang(BOAN_BATCHPOST_NAME,'onekey_one_watermethod_2').'</option>';
    $watermethod .= '<option value="2"'.(2 == $watermethod_id ? '" selected' : '"').'>'.lang(BOAN_BATCHPOST_NAME,'onekey_one_watermethod_3').'</option>';
    $watermethod .= '</select>';
    $pmethod = '<select name="pmethod"><option value="1"'.(1 == $pmethod_id ? ' selected' : '').'>'.lang(BOAN_BATCHPOST_NAME,'onekey_one_pmethod_1').'</option>';
    $pmethod .= '<option value="2"'.(2 == $pmethod_id ? ' selected' : '').'>'.lang(BOAN_BATCHPOST_NAME,'onekey_one_pmethod_2').'</option>';
    $pmethod .= '<option value="3"'.(3 == $pmethod_id ? ' selected' : '').'>'.lang(BOAN_BATCHPOST_NAME,'onekey_one_pmethod_3').'</option>';
    $pmethod .= '</select>';
    
    
    $readperm = '<select name="readperm"><option value="0">'.lang(BOAN_BATCHPOST_NAME, 'onekey_one_nolimit').'</option>';
    foreach ($_G['cache']['groupreadaccess'] as $v){
        $readperm .= '<option value="'.$v['readaccess'].($v['readaccess'] == $readperm_id ? '" selected' : '"').'>'.$v['grouptitle'].'</option>';
    }
    $readperm .= '</select>';
    
    include template('boan_batchpost:postconfig');
}