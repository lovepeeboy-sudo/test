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

class plugin_boan_batchpost {
    var $vars;
    function __construct(){
        global $_G;
        if(empty($_G['cache']['plugin'])){
            loadcache('plugin');
        }
        $this->vars = $_G['cache']['plugin']['boan_batchpost'];
    }
    
}
class plugin_boan_batchpost_forum extends plugin_boan_batchpost{
    
    public  function forumdisplay_postbutton_top_output(){
        global $_G;
        $allow_groups = dunserialize($this->vars['allow_groups']);
        $str = '';
        if(in_array($_G['groupid'], $allow_groups)){
            $str = "<a href=\"javascript:;\" style=\"margin-left:10px;\"  onclick=\"location.href='plugin.php?id=boan_batchpost:batchpost&forum_id={$_G['fid']}'\"><img src=\"source/plugin/boan_batchpost/images/pn_post.jpg\"></a>";
        }
        
        return $str;
    }
}