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

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
define('BOAN_BATCHPOST_NAME', 'plugin/boan_batchpost');
global $_G;
if(empty($_G['cache']['plugin'])){
    loadcache('plugin');
}
$vars = $_G['cache']['plugin']['boan_batchpost'];
if($_GET['pmod'] == 'one_key'){
    if(!submitcheck('one_submit') &&  !$_GET['step']){
       
        if(submitcheck('test_submit')){
            
            saveconfig();
        }
        
        loadcache('boan_batchpost_onkey',true);
        $cache = $_G['cache']['boan_batchpost_onkey'];
        
        if(!$_G['cache']['boan_batchpost_onkey']){
            $cache = array(
                'limitW' => 2600,
                'userlist' => '',
                'begintime' => '',
                'posttime' => 0,
                'price' => '',
                'att_price' => '0',
                'tags' => '',
                'tagct' => 0,
                'cuttype' => 0,
                'att_readaccess' => '0',
                'readperm' => '0',
                'forum' => null,
                'typeid' => 0,
                'sortid' => 0,
                'typeoption' => array(),
                'message' => '',
                'showtxt' => 0,
                'txtcharset' => 'gbk',
                'watermethod' => 0,
                'waterW' => 0,
                'waterH' => 0,
                'waterA' => 0,
                'waterO' => 0,
                'waterMinW' => 0,
                'waterMinH' => 0,
                'watertextstyle' => 'sitename:{siteanme}{n}sitedomin:{sitedomin}',
                'watertextfile' => 0,
                'watertextsize' => '24',
                'watertextcolor' => '#FFFFFF',
                'watertextO' => 100,
                'att_repeat' => 0,
                'pmethod' => 1,
                'subject' => '',
                'separate' => '_',
                'process_count' => 3,
            );
        }
        
        cpheader();
        echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';
        showtips(lang(BOAN_BATCHPOST_NAME, 'onekey_one_explain'));
        showformheader('plugins&operation=config&do='.$pluginid.'&pmod=one_key');
        echo '<input type="hidden" name="reload" id="reload" value="" />';
        showtableheader('','','',9);
        showtagheader('tbody', '',true);
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_userlist'), 'userlist',  $cache['userlist'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_userlist_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_begintime'), 'begintime',$cache['begintime'],'calendar',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_begintime_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_posttime'), 'posttime',  $cache['posttime'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_posttime_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_price'), 'price',  $cache['price'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_price_comment'));
       
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_tags'), 'tags', $cache['tags'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_tags_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_tagct'), 'tagct', $cache['tagct'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_tagct_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_cuttype'), 'cuttype', $cache['cuttype'],'radio',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_cuttype_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_subject'), 'subject',  $cache['subject'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_subject_comment'));
        
   
        $var =  array('title'=>lang(BOAN_BATCHPOST_NAME,'onekey_one_forum'),
            'type' => 'forum',
            'description' => lang(BOAN_BATCHPOST_NAME,'onekey_one_forum_comment'),
            'variable' => 'forum',
            'value' => $cache['forum'],
            
        );
        
        show_forum($var);
        
        $fid = dunserialize($cache['forum']);
        $fid = $fid[0];
        loadforum($fid);
        
        echo <<<EOF
<script>
               $('forum').onchange=function(){
                   $('reload').value = 1;
                   $('submit_one_submit').click();
                }
                
          </script>
EOF;
        if(isset($_G['forum']['threadtypes']['types']) && count($_G['forum']['threadtypes']['types']) > 0){
           
            $arr = array(array('0',lang(BOAN_BATCHPOST_NAME, 'onekey_one_none')));
            foreach ($_G['forum']['threadtypes']['types'] as $key => $val){
                $arr[] = array($key,$val);
            }
            
            showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_typeid'),
                array('typeid',
                   $arr),
                $cache['typeid'],'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_typeid_comment'),'id=typeid'
                );
        }
        
        if(isset($_G['forum']['threadsorts']['types']) && count($_G['forum']['threadsorts']['types']) > 0){
            
            $arr = array(array('0',lang(BOAN_BATCHPOST_NAME, 'onekey_one_none')));
            foreach ($_G['forum']['threadsorts']['types'] as $key => $val){
                $arr[] = array($key,$val);
            }
            
            $sortid = array_key_exists($cache['sortid'],$_G['forum']['threadsorts']['types']) ? $cache['sortid'] : 0;
            showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_sortid'),
                array('sortid',
                    $arr),
                $sortid,'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_sortid_comment'),'id=sortid'
                );
            
            
            echo <<<EOF
<script>
               $('sortid').onchange=function(){
                   $('reload').value = 1;
                   $('submit_one_submit').click();
                }
                
          </script>
EOF;
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
                
                include template('boan_batchpost:post_sortoption');
            }
            
        }
        showtagfooter('tbody');
        showtablefooter();
        
        
        showtableheader('','','',9);
        showtagheader('tbody', '',true);
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_showtxt'), 'showtxt', $cache['showtxt'],'radio',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_showtxt_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_charset'),
            array('txtcharset',
                array(
                    array('gbk','GBK'),
                    array('utf8','UTF-8'),
                )
            ),
            $cache['txtcharset'],'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_charset_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_message'), 'message',   $cache['message'],'textarea',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_message_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_attprice'), 'attprice',  $cache['att_price'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_attprice_comment'));
        
        show_forumreadperm($cache['readperm']);
        
        show_groupreadaccess($cache['att_readaccess']);
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watermethod'),
            array('watermethod',
                array(array('0',lang(BOAN_BATCHPOST_NAME,'onekey_one_watermethod_1')),
                    array('1',lang(BOAN_BATCHPOST_NAME,'onekey_one_watermethod_2')),
                        array('2',lang(BOAN_BATCHPOST_NAME,'onekey_one_watermethod_3'))
                )),
            $cache['watermethod'],'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watermethod_comment')
            );
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterMinWH'), array('waterminw', 'waterminh'), array(intval($cache['waterMinW']), intval($cache['waterMinH'])), 'multiply',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterMinWH_comment'));
       
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_pmethod'),
            array('pmethod',
                array(array('1',lang(BOAN_BATCHPOST_NAME,'onekey_one_pmethod_1')),
                    array('2',lang(BOAN_BATCHPOST_NAME, 'onekey_one_pmethod_2')),
                    array('3',lang(BOAN_BATCHPOST_NAME, 'onekey_one_pmethod_3'))
                )),
            $cache['pmethod'],'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_pmethod_comment'),''
            );
        
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_limitW'), 'limitW', $cache['limitW'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_limitW_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_separate'), 'separate',  $cache['separate'],'text',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_separate_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_repeat'), 'repeat', $cache['att_repeat'],'radio',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_repeat_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME,'onekey_one_process'), 'process_count', $cache['process_count'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_process_comment'));
        showtagfooter('tbody');
        showtablefooter();
        showsubmit('one_submit');
        showformfooter();
    }else if(!$_GET['step']){
        saveconfig();
        if($_GET['reload']){
            $url = 'action=plugins&operation=config&do='.$pluginid.'&pmod=one_key';
            $url = preg_match('/^https?:\/\//is', $url) ? $url : ADMINSCRIPT.'?'.$url;
            dheader('location:'.$url);
        }
      
        $url = 'action=plugins&operation=config&do='.$pluginid.'&pmod=one_key&step=2';
        $url = preg_match('/^https?:\/\//is', $url) ? $url : ADMINSCRIPT.'?'.$url;
        dheader('location:'.$url);
    }else{
        (file_exists(DISCUZ_ROOT.'./source/plugin/boan_h5upload/oss/loadoss.php') &&  require_once(DISCUZ_ROOT.'./source/plugin/boan_h5upload/oss/loadoss.php'))
        || (file_exists(DISCUZ_ROOT.'./source/plugin/boan_oss/loadoss.php') &&  require_once(DISCUZ_ROOT.'./source/plugin/boan_oss/loadoss.php'));
        
        loadcache('boan_batchpost_onkey',true);
        $cache = $_G['cache']['boan_batchpost_onkey'];
        cpheader();
        $url = 'action=plugins&operation=config&do='.$pluginid.'&pmod=one_key';
        $url = preg_match('/^https?:\/\//is', $url) ? $url : ADMINSCRIPT.'?'.$url;
        if(empty(trim($cache['message'])) && empty($cache['typeoption'])){
            cpmsg(lang(BOAN_BATCHPOST_NAME, 'onekey_one_error1'),'', 'error');  
        }
     
        if(!isset($_G['cache']['forums'])) {
            loadcache('forums');
        }
        $forumcache = &$_G['cache']['forums'];
        $forum = unserialize($cache['forum']);
        $forum = $forumcache[$forum[0]];
        
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
        $vars['Ignore_error'] = intval($vars['Ignore_error']);
        include template('boan_batchpost:onekey');
    }
}


function saveconfig(){
    global $_G;
    loadcache('boan_batchpost_onkey',true);
    $cache = $_G['cache']['boan_batchpost_onkey'];
    
    if(!isset($cache) || !is_array($cache)){
        $cache = array();
    }
    
    $cache1 = array(
        'limitW' => intval($_GET['limitW']),
        'userlist' => dhtmlspecialchars($_GET['userlist']),
        'begintime' => $_GET['begintime'],
        'posttime' => intval($_GET['posttime']),
        'price' => intval($_GET['price']),
        'att_price' => intval($_GET['attprice']),
        'tags' => dhtmlspecialchars($_GET['tags']),
        'tagct' => intval($_GET['tagct']),
        'cuttype' => intval($_GET['cuttype']),
        'subject' => dhtmlspecialchars($_GET['subject']),
        'readperm' => intval($_GET['readperm']),
        'att_readaccess' => intval($_GET['attreadaccess']),
        'forum' => serialize($_GET['forum']),
        'typeid' => intval($_GET['typeid']),
        'sortid' => intval($_GET['sortid']),
        'message'    =>  $_GET['message'],
        'typeoption' => $_GET['typeoption'],
        'att_repeat' => intval($_GET['repeat']),
        'pmethod' => $_GET['pmethod'],
        'separate' => $_GET['separate'],
        'process_count' => $_GET['process_count'],
        'showtxt' => $_GET['showtxt'],
        'txtcharset' => $_GET['txtcharset'],
        'watermethod' => $_GET['watermethod'],
        'waterMinW' => intval($_GET['waterminw']),
        'waterMinH' => intval($_GET['waterminh']),
    );
    $cache = array_merge($cache,$cache1);
    savecache('boan_batchpost_onkey',$cache);
}

function show_forum($var){
   require_once libfile('function/forumlist');
   
    $var['description'] = ($var['description'] ? (isset($lang[$var['description']]) ? $lang[$var['description']] : $var['description'])."\n" : '').$lang['plugins_edit_vars_multiselect_comment']."\n".$var['comment'];
    $var['value'] = dunserialize($var['value']);
    
    $var['value'] = is_array($var['value']) ? $var['value'] : array();
    $var['type'] = '<select id="forum" name="'.$var['variable'].'[]"><option value="">'.cplang('plugins_empty').'</option>'.forumselect(FALSE, 0, 0, TRUE);
    
    
    foreach (DB::fetch_all('SELECT fid FROM %t WHERE status=%d',array('forum_forum',3)) as $row){
        $groupids[$row['fid']] = $row['fid'];
    }
    
    $resourceid = DB::query('SELECT fid,fup,name FROM %t WHERE type = %s AND fup IN (%n)',array('forum_forum','sub',$groupids));
    if(!empty(DB::num_rows($resourceid))){
        $var['type'] .= '<optgroup label="'.lang('admincp','nav_group').'">';
        while ($row = DB::fetch($resourceid)){
            $var['type'] .= '<option value="'.$row['fid'].'">'.$row['name'].'</option>';
        }
        $var['type'] .= '</optgroup>';
    }
    
    $var['type'] .= '</select>';
    
    foreach($var['value'] as $v) {
        $var['type'] = str_replace('<option value="'.$v.'">', '<option value="'.$v.'" selected>', $var['type']);
    }
    
    $var['variable'] = $var['value'] = '';
    showsetting(isset($lang[$var['title']]) ? $lang[$var['title']] : dhtmlspecialchars($var['title']), $var['variable'], $var['value'], $var['type'], '', 0, isset($lang[$var['description']]) ? $lang[$var['description']] : nl2br(dhtmlspecialchars($var['description'])), dhtmlspecialchars($var['extra']), '', true);
}


function show_forumreadperm($var){
    global $_G;
    loadcache('groupreadaccess');
    $arr = [];
    $arr[] = array('0',lang(BOAN_BATCHPOST_NAME, 'onekey_one_nolimit'));
    foreach ($_G['cache']['groupreadaccess'] as $v){
        array_push($arr, array($v['readaccess'],$v['grouptitle']));
    }
    $arr[] = array('255',lang(BOAN_BATCHPOST_NAME, 'onekey_one_highest'));
    
    showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_readperm'),
        array('readperm',$arr),
        $var,'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_readperm_comment')
        );
}


function show_groupreadaccess($var){
    global $_G;
    loadcache('groupreadaccess');
    $arr = [];
    $arr[] = array('0',lang(BOAN_BATCHPOST_NAME, 'onekey_one_nolimit'));
    foreach ($_G['cache']['groupreadaccess'] as $v){
        array_push($arr, array($v['groupid'],$v['grouptitle']));
    }
    showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_attreadaccess'),
        array('attreadaccess',$arr),
        $var,'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_attreadaccess_comment')
        );
}