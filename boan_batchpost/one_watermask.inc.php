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

if($_GET['pmod'] == 'one_watermask'){
    if(!submitcheck('water_submit')){
        loadcache('boan_batchpost_onkey',true);
        $cache = $_G['cache']['boan_batchpost_onkey'];
        if(!$_G['cache']['boan_batchpost_onkey']){
            $cache = array(
                'waterW' => 0,
                'waterH' => 0,
                'waterA' => 0,
                'waterO' => 0,
                'watertextstyle' => 'sitename:{siteanme}{n}sitedomin:{sitedomin}',
                'watertextfile' => 0,
                'watertextsize' => '24',
                'watertextcolor' => '#FFFFFF',
                'watertextO' => 10,
                'watertextA' => 0,
                'watertextpos' => '0',
                'watertextHO' => 50,
                'watertextVO' => 50,
                'watertextHS' => 0,
                'watertextVS' => 0,
                'watertextSC' => '',
                'waterlogoallow' => 0,
                'waterlogoO' => 0,
                'waterlogopos' => 0,
                'waterlogoHO' => 0,
                'waterlogoVO' => 0,
              
            );
        }
        cpheader();
        showtips(lang(BOAN_BATCHPOST_NAME, 'onekey_watermask_explain'));
        showformheader('plugins&operation=config&do='.$pluginid.'&pmod=one_watermask','enctype');
        showtableheader('','','',9);
        showtagheader('tbody', '',true);
        
        if(!file_exists(DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/water.png')){
            $waterF_comment =lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterF_comment2');
        }else{
            $waterF_comment =lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterF_comment1');
        }
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterF'), 'waterF','','file',0,0,$waterF_comment);
        
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterW'), 'waterW',  $cache['waterW'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterW_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterH'), 'waterH',  $cache['waterH'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterH_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterA'), 'waterA',  $cache['waterA'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterA_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterO'), 'waterO',  $cache['waterO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterO_comment'));
        
        $checkwm['text'] = array('','','','','','','','','');
        $checkwm['text'][$cache['watertextpos']] = 'checked';
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextpos'), '', '', '<table style="margin-bottom: 3px; margin-top:3px;"><tr><td><input class="radio" type="radio" name="watertextpos" value="0" '.$checkwm['text'][0].'> #1</td><td><input class="radio" type="radio" name="watertextpos" value="1" '.$checkwm['text'][1].'> #2</td><td><input class="radio" type="radio" name="watertextpos" value="2" '.$checkwm['text'][2].'> #3</td></tr><tr><td><input class="radio" type="radio" name="watertextpos" value="3" '.$checkwm['text'][3].'> #4</td><td><input class="radio" type="radio" name="watertextpos" value="4" '.$checkwm['text'][4].'> #5</td><td><input class="radio" type="radio" name="watertextpos" value="5" '.$checkwm['text'][5].'> #6</td></tr><tr><td><input class="radio" type="radio" name="watertextpos" value="6" '.$checkwm['text'][6].'> #7</td><td><input class="radio" type="radio" name="watertextpos" value="7" '.$checkwm['text'][7].'> #8</td><td><input class="radio" type="radio" name="watertextpos" value="8" '.$checkwm['text'][8].'> #9</td></tr></table>');
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextstyle'), 'watertextstyle',  $cache['watertextstyle'],'textarea',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextstyle_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterFontFile'), 'waterFontFile','','file',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterFontFile_comment'));
        
        
        $arr = array();
        $dir = opendir(DISCUZ_ROOT.'./source/plugin/boan_batchpost/font');
        while(false !== ($file = readdir($dir))){
            if($file == '.' || $file == '..'){
                continue;
            }
            if(!is_dir($file)){
                $arr[] = array($file,$file);
            }
        }
        closedir($dir);
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextfile'),
            array('watertextfile',
                $arr),
            $cache['watertextfile'],'select',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextfile_comment')
            );
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextsize'), 'watertextsize',  $cache['watertextsize'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextsize_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextcolor'), 'watertextcolor',  $cache['watertextcolor'],'color',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextcolor_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextO'), 'watertextO',  $cache['watertextO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextO_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextA'), 'watertextA',  $cache['watertextA'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextA_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextHO'), 'watertextHO',  $cache['watertextHO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextHO_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextVO'), 'watertextVO',  $cache['watertextVO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextVO_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextHS'), 'watertextHS',  $cache['watertextHS'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextHS_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextVS'), 'watertextVS',  $cache['watertextVS'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextVS_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextSC_comment'), 'watertextSC',  $cache['watertextSC'],'color',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_watertextSC_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoallow'), 'waterlogoallow',$cache['waterlogoallow'],'radio',0,0,'');
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterLogoFile'), 'waterLogoFile','','file',0,0,'');
        
        $checkwm['logo'] = array('','','','','','','','','');
        $checkwm['logo'][$cache['waterlogopos']] = 'checked';
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogopos'), '', '', '<table style="margin-bottom: 3px; margin-top:3px;"><tr><td><input class="radio" type="radio" name="waterlogopos" value="0" '.$checkwm['logo'][0].'> #1</td><td><input class="radio" type="radio" name="waterlogopos" value="1" '.$checkwm['logo'][1].'> #2</td><td><input class="radio" type="radio" name="waterlogopos" value="2" '.$checkwm['logo'][2].'> #3</td></tr><tr><td><input class="radio" type="radio" name="waterlogopos" value="3" '.$checkwm['logo'][3].'> #4</td><td><input class="radio" type="radio" name="waterlogopos" value="4" '.$checkwm['logo'][4].'> #5</td><td><input class="radio" type="radio" name="waterlogopos" value="5" '.$checkwm['logo'][5].'> #6</td></tr><tr><td><input class="radio" type="radio" name="waterlogopos" value="6" '.$checkwm['logo'][6].'> #7</td><td><input class="radio" type="radio" name="waterlogopos" value="7" '.$checkwm['logo'][7].'> #8</td><td><input class="radio" type="radio" name="waterlogopos" value="8" '.$checkwm['logo'][8].'> #9</td></tr></table>');
        
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoHO'), 'waterlogoHO',  $cache['waterlogoHO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoHO_comment'));
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoVO'), 'waterlogoVO',  $cache['waterlogoHO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoVO_comment'));
        
        showsetting(lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoO'), 'waterlogoO',$cache['waterlogoO'],'number',0,0,lang(BOAN_BATCHPOST_NAME, 'onekey_one_waterlogoO_comment'));
        
        showtagfooter('tbody');
        showtablefooter();
        showsubmit('water_submit');
        showformfooter();
    }else{
        saveconfig();
        
        $backurl = 'action=plugins&operation=config&do='.$pluginid.'&pmod=one_watermask';
        $backurl = preg_match('/^https?:\/\//is', $backurl) ? $backurl : ADMINSCRIPT.'?'.$backurl;
        
        $_GET['watertextstyle'] = urlencode($_GET['watertextstyle']);
        $_GET['watertextfile'] = urlencode($_GET['watertextfile']);
        $_GET['watertextcolor'] = urlencode($_GET['watertextcolor']);
        $_GET['watertextSC'] = urlencode($_GET['watertextSC']);
        
        $url = "plugin.php?id=boan_batchpost:preview&width={$_GET['waterW']}&height={$_GET['waterH']}&angle={$_GET['waterA']}&opacity={$_GET['waterO']}&watertextstyle={$_GET['watertextstyle']}
        &watertextfile={$_GET['watertextfile']}&watertextcolor={$_GET['watertextcolor']}&watertextsize={$_GET['watertextsize']}&watertextO={$_GET['watertextO']}&watertextpos={$_GET['watertextpos']}
        &watertextHO={$_GET['watertextHO']}&watertextVO={$_GET['watertextVO']}&watertextA={$_GET['watertextA']}&watertextHS={$_GET['watertextHS']}&watertextVS={$_GET['watertextVS']}&watertextSC= {$_GET['watertextSC']}";
        
        
        if(intval($_GET['waterlogoallow'])){
            $url .= "&waterlogoallow={$_GET['waterlogoallow']}&waterlogoO={$_GET['waterlogoO']}&waterlogopos={$_GET['waterlogopos']}&waterlogoHO={$_GET['waterlogoHO']}&waterlogoVO={$_GET['waterlogoVO']}";
        }
        
        echo <<<EOF
            <style>
                .y {float: right;}
                .zoominner { padding: 5px 10px 10px; background:#FFF; text-align: left; }
		.zoominner p { padding: 8px 0; }
			.zoominner p a { float: left; margin-left: 10px; width: 17px; height: 17px; background: url(source/plugin/boan_batchpost/images/imgzoom_tb.gif) no-repeat 0 0; line-height: 100px; overflow: hidden; }
				.zoominner p a:hover { background-position: 0 -39px; }
			.zoominner p a.imgadjust { background-position: -40px 0; }
				.zoominner p a.imgadjust:hover { background-position: -40px -39px; }
			.zoominner p a.imgclose { background-position: -80px 0; }
				.zoominner p a.imgclose:hover { background-position: -80px -39px; }
            
.zimg_c { position: relative; }
.zimg_prev, .zimg_next { display: block; position: absolute; width: 80px; height: 100%; background: url({IMGDIR}/pic-prev.png) no-repeat 0 -100px; cursor: pointer; }
.zimg_next { right: 10px; background-image: url({IMGDIR}/pic-next.png); background-position: 100% -100px; }
.zimg_c img { margin: 0 auto; }
.zimg_p strong { display: none; }
            </style>
EOF;
        echo "<img id=\"imgtest\" src=\"$url\" zoomfile=\"$url\" file=\"$url\" class=\"zoom\" style=\"display:none;\"><script>zoom($('imgtest'),$('imgtest').src,0,0,0); setTimeout(function(e){
        _attachEvent(document.getElementsByClassName('imgclose')[0],'click',function(e){window.location.href='$backurl';});
    },200);</script>";
        
    }
}



function saveconfig(){
    global $_G;
  
    if(isset($_FILES['waterF']) && $_FILES['waterF']['error'] == UPLOAD_ERR_OK  && file_exists($_FILES['waterF']['tmp_name']) && $_FILES['waterF']['size'] >0 ){
        $imageinfo = @getimagesize($_FILES['waterF']['tmp_name']);
        if($imageinfo){
            @move_uploaded_file($_FILES['waterF']['tmp_name'], DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/water.png');
        }else{
            @unlink($_FILES['waterF']['tmp_name']);
        }
       
    }
    
    if(isset($_FILES['waterLogoFile']) && $_FILES['waterLogoFile']['error'] == UPLOAD_ERR_OK  && file_exists($_FILES['waterLogoFile']['tmp_name']) && $_FILES['waterLogoFile']['size'] >0 ){
        $imageinfo = @getimagesize($_FILES['waterLogoFile']['tmp_name']);
        if($imageinfo){
            @move_uploaded_file($_FILES['waterLogoFile']['tmp_name'], DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/logo.png');
        }else{
            @unlink($_FILES['waterLogoFile']['tmp_name']);
        }
        
    }
    
    if(isset($_FILES['waterFontFile']) && $_FILES['waterFontFile']['error'] == UPLOAD_ERR_OK  && file_exists($_FILES['waterFontFile']['tmp_name']) && $_FILES['waterFontFile']['size'] >0 ){
        @move_uploaded_file($_FILES['waterFontFile']['tmp_name'], DISCUZ_ROOT.'./source/plugin/boan_batchpost/font/'.$_FILES['waterFontFile']['name']);
        @unlink($_FILES['waterFontFile']['tmp_name']);
    }
    
    loadcache('boan_batchpost_onkey',true);
    $cache = $_G['cache']['boan_batchpost_onkey'];
    if(!isset($cache) || !is_array($cache)){
        $cache = array();
    }
    
    $cache1 = array(
        'waterW' => $_GET['waterW'],
        'waterH' => $_GET['waterH'],
        'waterA' => $_GET['waterA'],
        'waterO' => $_GET['waterO'],
        'watertextstyle' => $_GET['watertextstyle'],
        'watertextfile' => $_GET['watertextfile'],
        'watertextsize' => $_GET['watertextsize'],
        'watertextcolor' => $_GET['watertextcolor'],
        'watertextpos' => intval($_GET['watertextpos']),
        'watertextA' => intval($_GET['watertextA']),
        'watertextHS' => intval($_GET['watertextHS']),
        'watertextVS' => intval($_GET['watertextVS']),
        'watertextSC' => $_GET['watertextSC'],
        'watertextO' => intval($_GET['watertextO']),
        'watertextHO' => intval($_GET['watertextHO']),
        'watertextVO' => intval($_GET['watertextVO']),
        
        'waterlogoallow' => intval($_GET['waterlogoallow']),
        'waterlogoO' => intval($_GET['waterlogoO']),
        'waterlogopos' => intval($_GET['waterlogopos']),
        'waterlogoHO' => intval($_GET['waterlogoHO']),
        'waterlogoVO' => intval($_GET['waterlogoVO']),
        
    );
    $cache = array_merge($cache,$cache1);
    savecache('boan_batchpost_onkey',$cache);
}