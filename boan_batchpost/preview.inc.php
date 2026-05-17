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
require_once   DISCUZ_ROOT.'./source/plugin/boan_batchpost/common.func.php';

$options = array('width' => intval($_GET['width']),
                 'height' => intval($_GET['height']),
                 'angle' => intval($_GET['angle']),
                 'opacity' => intval($_GET['opacity']),
                 
);
$water = DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/t_water.png';
if(file_exists(DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/water.png')){
    $water = DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/water.png';
}

$textoptions = array('text'=>trim($_GET['watertextstyle']),
    'file'=>DISCUZ_ROOT.'./source/plugin/boan_batchpost/font/'.$_GET['watertextfile'],
    'color'=>trim($_GET['watertextcolor']),
    'size'=>$_GET['watertextsize'],
    'opacity'=>$_GET['watertextO'], 
    'pos' => $_GET['watertextpos'],
    'angle' => $_GET['watertextA'],
    'x_pos' => $_GET['watertextHO'],
    'y_pos' =>  $_GET['watertextVO'],
    'shadowx' => $_GET['watertextHS'],
    'shadowy' => $_GET['watertextVS'],
    'shadowcolor' => trim($_GET['watertextSC']),
    'para' => array('username'=>'admin',
                    'threadtitle' => 'subject',
                    'threadid' => '666666'),
);

$url .= "&waterlogoO={$_GET['waterlogoO']}&waterlogopos={$_GET['waterlogopos']}&waterlogoHO={$_GET['waterlogoHO']}&waterlogoVO={$_GET['waterlogoVO']}";
$logooptions = array('file' => DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/logo.png',
    'opacity' => intval($_GET['waterlogoO']),
    'pos' => intval($_GET['waterlogopos']),
    'x_pos' => intval($_GET['waterlogoHO']),
    'y_pos' => intval($_GET['waterlogoHO']),
);
if(!$_GET['waterlogoallow']){
    $logooptions = array();
}
full_screen($water,
            DISCUZ_ROOT.'./source/plugin/boan_batchpost/images/test.jpg','',
    $options,$textoptions,$logooptions,true);