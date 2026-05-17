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

if(!function_exists('boan_get_cache')){
    function boan_get_cache($plugin_name,$force = false){
        global $_G;
        loadcache($plugin_name,$force);
        return $_G['cache'][$plugin_name];
    }
    
}


if(!function_exists('boan_set_cache')){
    function boan_set_cache($plugin_name,$data){
        global $_G;
        savecache($plugin_name, $data);
    }
}


if(!function_exists('boan_load_script')){
    function boan_load_script($js_file,$plugin_name) {
        $js_pre='<script type="text/javascript" src="source/plugin/'.$plugin_name.'/js/';
        $js_code='';
    
        $var=getglobal('boan/js_file/'.$js_file);
        if(!isset($var)){
            $js_code=$js_pre.$js_file.'?'.VERHASH.'" charset="utf-8"></script>';
            setglobal('boan/js_file/'.$js_file, 1); //����ȫ�ֱ�������ֹ�ظ����ء�
        }
        return $js_code;
    }
}

if(!function_exists('full_screen')){
    /**
     * 
     * @param unknown $water
     * @param unknown $source
     * @param string $target
     * @param array $options (width,height,angle,opacity)
     * @param array textoptions(file,text,color,opacity,pos,x_pos,y_pos,angle,shadowx,shadowy,shadowcolor) 
     * @$logooptions(file,pos,x_pos,y_pos,opacity)
     * @param string $preview
     * @return number|number|boolean
     */
    function full_screen($water, $source, $target = '',$options = array(),$textoptions = array(),$logooptions = array(),$preview = false){
        
        $img = $source;
        $source = $water;
        
        
        $ww = $options['width'] ? $options['width'] : 0;  
        $hh = $options['height'] ? $options['height'] : 0;  
        
        $angle = $options['angle'] ? $options['angle'] : 0;
        
        $opacity =  $options['opacity'] ? $options['opacity'] : 100;
        
        if(!file_exists($source) || !file_exists($water)){
            return 0;
        }
        
        $imgInfo = getimagesize($img);
        $sourceInfo = getimagesize($source);
        
        if($imgInfo == FALSE || $sourceInfo == FALSE){
            return 0;
        }
        
        $fun   = 'imagecreatefrom' . image_type_to_extension($sourceInfo[2], false);
        $water = $fun($source);
        
        $water = imagerotate($water,$angle, imageColorAllocateAlpha($water, 0, 0, 0, 127));
        
        $sourceInfo[0] = imagesx($water);
        $sourceInfo[1] = imagesy($water);
        
        imagealphablending($water, true);
        
        $src = imagecreatetruecolor($sourceInfo[0], $sourceInfo[1]);
        
        $color = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $color);
        
        $fun   = 'imagecreatefrom' . image_type_to_extension($imgInfo[2], false);
        $thumb = $fun($img);
        
        $x_length = $imgInfo[0] - 10; 
        $y_length = $imgInfo[1] - 10; 

        for ($x = 0; $x < $x_length; $x) {
            for ($y = 0; $y < $y_length; $y) {
                imagecopy($src, $thumb, 0, 0, $x, $y, $sourceInfo[0], $sourceInfo[1]);
                imagecopy($src, $water, 0, 0, 0, 0, $sourceInfo[0], $sourceInfo[1]);
                imagecopymerge($thumb, $src, $x, $y, 0, 0, $sourceInfo[0], $sourceInfo[1], $opacity);
                $y += $sourceInfo[1] + $hh;
            }
            $x += $sourceInfo[0] + $ww;
        }
        
        if(file_exists($logooptions['file'])  && $imgInfo = getimagesize($logooptions['file'])){
            $logooptions['pos'] = $logooptions['pos'] ? $logooptions['pos'] : 0;
            $x_pos = $logooptions['x_pos'] ? $logooptions['x_pos'] : 0;
            $y_pos = $logooptions['y_pos'] ? $logooptions['y_pos'] : 0;
            $logooptions['opacity'] = $logooptions['opacity'] ? $logooptions['opacity'] : 100;
            $fun   = 'imagecreatefrom' . image_type_to_extension($imgInfo[2], false);
            $logo = $fun($logooptions['file']);
            imagedestroy($src);
            $src = imagecreatetruecolor($imgInfo[0], $imgInfo[1]);
            $color = imagecolorallocate($src, 255, 255, 255);
            imagefill($src, 0, 0, $color);
            
            $img_x = $img_y = 5;
            $sourceInfo[0] = imagesx($thumb);
            $sourceInfo[1] = imagesy($thumb);
            
            if($logooptions['pos'] == 0){
                $img_x += $x_pos;
                $img_y += $y_pos;
            }elseif($logooptions['pos'] == 1){
                $img_x =  ($sourceInfo[0] - $imgInfo[0])/2;
                $img_y += $y_pos;
            }elseif($logooptions['pos'] == 2){
                $img_x = $sourceInfo[0] - $imgInfo[0];
                
                $img_x -= $x_pos;
                $img_y += $y_pos;
            }elseif($logooptions['pos'] == 3){
                $img_y = ($sourceInfo[1] - $imgInfo[1])/2;
                
                $img_x += $x_pos;
            }elseif($logooptions['pos'] == 4){
                $img_x =  ($sourceInfo[0] - $imgInfo[0])/2;
                $img_y = ($sourceInfo[1] - $imgInfo[1])/2;
            }elseif($logooptions['pos'] == 5){
                $img_x =  ($sourceInfo[0] - $imgInfo[0]);
                $img_y = ($sourceInfo[1] - $imgInfo[1])/2;
                
                $img_x -= $x_pos;
            }elseif($logooptions['pos'] == 6){
                $img_y = ($sourceInfo[1] - $imgInfo[1]);
                
                $img_x += $x_pos;
                $img_y -= $y_pos;
            }elseif($logooptions['pos'] == 7){
                $img_x =  ($sourceInfo[0] - $imgInfo[0])/2;
                $img_y = $sourceInfo[1] - $imgInfo[1];
                
                $img_y -= $y_pos;
            }elseif($logooptions['pos'] == 8){
                $img_x =  $sourceInfo[0] - $imgInfo[0];
                $img_y = $sourceInfo[1] - $imgInfo[1];
                
                $img_x -= $x_pos;
                $img_y -= $y_pos;
            }
            imagecopy($src, $thumb,  0, 0,$img_x, $img_y, $imgInfo[0], $imgInfo[1]);
            imagecopy($src, $logo, 0,0, 0, 0, $imgInfo[0], $imgInfo[1]);
            imagecopymerge($thumb, $src, $img_x, $img_y, 0, 0, $imgInfo[0], $imgInfo[1], $logooptions['opacity']);
            
        }
        
        $font_file =  $textoptions['file']; 
        $fontO = !$textoptions['opacity'] ? 120 : $textoptions['opacity'];
        $text = $textoptions['text'];
        $font_size = !$textoptions['size'] ? 24 : $textoptions['size']; 
        $color = !$textoptions['color'] ? '#FFFFFF' : $textoptions['color'];
        $textpara = $textoptions['para'];
        
        $parsetext = function($text,$textpara){
            return preg_replace_callback('/\{([\S\s]+?)\}/is', function($m) use($textpara) {
                global $_G;
                if($m[1] == 'n'){
                    return PHP_EOL;
                }else if($m[1] == 'sitename'){
                    return $_G['setting']['sitename'];
                }else if($m[1] == 'sitedomin'){
                    return $_G['setting']['siteurl'];
                }else if(substr($m[1],0,8) == 'username'){
                    return $textpara['username'];
                }else if(substr($m[1],0,8) == 'threadid'){
                    return $textpara['threadid'];
                }else if(substr($m[1],0,11) == 'threadtitle'){
                    return $textpara['threadtitle'];
                }
                
                return $m[0];
            }, $text);
        };
        
        $hextorgb = function ($color) {
            $color = str_replace('#', '', $color);
            $color = str_replace(' ', '', $color);
            if (strlen($color) > 3) {
                $rgb = array(
                    'r' => hexdec(substr($color, 0, 2)),
                    'g' => hexdec(substr($color, 2, 2)),
                    'b' => hexdec(substr($color, 4, 2))
                );
            } else {
                $r = substr($color, 0, 1) . substr($color, 0, 1);
                $g = substr($color, 1, 1) . substr($color, 1, 1);
                $b = substr($color, 2, 1) . substr($color, 2, 1);
                $rgb = array(
                    'r' => hexdec($r),
                    'g' => hexdec($g),
                    'b' => hexdec($b)
                );
            }
            return $rgb;
        };
        
        if(file_exists($font_file) && !empty($text)){
            
            $text = $parsetext($text,$textpara);
            $text = diconv($text, CHARSET,'utf8');
            $color = $hextorgb($color);
            
            $sourceInfo[0] = imagesx($thumb);
            $sourceInfo[1] = imagesy($thumb);
            
            $angle = $textoptions['angle'];
            $shadowx = $textoptions['shadowx'];
            $shadowy = $textoptions['shadowy'];
            $shadowcolor = $textoptions['shadowcolor'];
        
            $box = imagettfbbox($font_size,$angle,$font_file,$text);
            $ax = min($box[0], $box[6]) * -1;
            $ay = min($box[5], $box[7]) * -1;
            $f_height = max($box[1], $box[3]) - min($box[5], $box[7]);
            $f_width  = max($box[2], $box[4]) - min($box[0], $box[6]);
   
            $f_x = $f_y = 5;
            $x_pos = $textoptions['x_pos']; 
            $y_pos = $textoptions['y_pos'];
            if($textoptions['pos'] == 0){
                $f_x += $x_pos;
                $f_y += $y_pos;
            }elseif($textoptions['pos'] == 1){
                $f_x =  ($sourceInfo[0] - $f_width)/2;
                $f_y += $y_pos;
            }elseif($textoptions['pos'] == 2){
                $f_x = $sourceInfo[0] - $f_width;
                
                $f_x -= $x_pos;
                $f_y += $y_pos;
            }elseif($textoptions['pos'] == 3){
                $f_y = ($sourceInfo[1] - $f_height)/2;
                
                $f_x += $x_pos;
            }elseif($textoptions['pos'] == 4){
                $f_x =  ($sourceInfo[0] - $f_width)/2;
                $f_y = ($sourceInfo[1] - $f_height)/2;
            }elseif($textoptions['pos'] == 5){
                $f_x =  ($sourceInfo[0] - $f_width);
                $f_y = ($sourceInfo[1] - $f_height)/2;
                
                $f_x -= $x_pos;
            }elseif($textoptions['pos'] == 6){
                $f_y = ($sourceInfo[1] - $f_height);

                $f_x += $x_pos;
                $f_y -= $y_pos;
            }elseif($textoptions['pos'] == 7){
                $f_x =  ($sourceInfo[0] - $f_width)/2;
                $f_y = $sourceInfo[1] - $f_height;
                
                $f_y -= $y_pos;
            }elseif($textoptions['pos'] == 8){
                $f_x =  $sourceInfo[0] - $f_width;
                $f_y = $sourceInfo[1] - $f_height;
                
                $f_x -= $x_pos;
                $f_y -= $y_pos;
            }
            if(($shadowx || $shadowy) && $shadowcolor) {
                $shadowcolorrgb = $hextorgb($shadowcolor);
                
                $shadowcolor = imagecolorallocatealpha($thumb, $shadowcolorrgb['r'], $shadowcolorrgb['g'], $shadowcolorrgb['b'],$fontO);
                imagettftext($thumb, $font_size, $angle, $f_x + $ax + $shadowx, $f_y + $ay + $shadowy, $shadowcolor, $font_file,$text);
            }
            
            $font_color = imagecolorallocatealpha($thumb, $color['r'], $color['g'], $color['b'], $fontO); 
          
            imagettftext($thumb, $font_size,$angle, $f_x + $ax,$f_y + $ay, $font_color ,$font_file,$text); 
        }
     
        if($preview){
           header("Content-type:image/jpeg");
           imagejpeg($thumb);
           exit();
        }else{
            imagejpeg($thumb,$target ? $target : $img);
        }
        
        
        imagedestroy($src);
        imagedestroy($water);
        
        return 1;
    }
}

if(!function_exists('boan_load_jq')){
    function boan_load_jq($js_file,$plugin_name){
        $js_code='
         <script type="text/javascript">
           if (typeof jQuery != \'undefined\'){
              var boan_old_jq = jQuery;
           } 
         </script>';
        $js_pre='<script type="text/javascript" src="source/plugin/'.$plugin_name.'/js/';
        $var=getglobal('boan/jq_redim_var');
        if(!isset($var)){
            $js_code.=$js_pre.$js_file.'?'.VERHASH.'" charset="utf-8"></script>';
          $js_code.= '
          <script type="text/javascript">
            try{
             var boan_jq=$.noConflict();
             if (typeof  boan_old_jq != \'undefined\'){
               jQuery = boan_old_jq;
              }
            }catch(e){} 
            
         </script>';
            setglobal('boan/jq_redim_var',1);
        }
        return $js_code;
    }
}



if(!function_exists('boan_set_jq')){
    function boan_set_jq(){
        $js_code = '';
        $js_code .= '
         <script type="text/javascript">
           if (typeof boan_jq != \'undefined\'){
             boan_old_jq = jQuery; 
             jQuery = boan_jq;
           } 
         </script>';
        return $js_code;
    }
   
}


if(!function_exists('boan_reset_jq')){
    function boan_reset_jq(){
        $js_code = '';
        $js_code .= '
         <script type="text/javascript">
           if (typeof  boan_old_jq != \'undefined\'){
               jQuery = boan_old_jq;
           }
         </script>';
        return $js_code;
    }
}