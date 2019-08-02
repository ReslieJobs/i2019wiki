<?php
header("Content-type: text/html; charset=utf-8");
function getIP()
{
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}
$rip=getIP();
function getCity($ip,$type)
{
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(file_get_contents($url));   
        if((string)$ip->code=='1'){
           return false;
        }
        // $data = (array)$ip->data;
        $country = (array)$ip->data->country;
        $region = (array)$ip->data->region;
        $city = (array)$ip->data->city;
        $citycode=(array)$ip->data->city_id;
        $mars='火星';
    if($country[0]==''||$country[0]=='XX'){
        return $mars;
    }else{
        if($type=='city'){
          return $country[0].$region[0].$city[0];
        }else if($type=='code'){
          return $city[0];
        }
    }
}
$defarea=getCity($rip,'city');
if(strstr($defarea, 'XX')){
    $lsarea = preg_replace('/XX/','',$defarea);
    $nowarea = $lsarea;
}else{
    $nowarea = $defarea;
}
$cityname=getCity($rip,'code');

//获取天气
function getWeather($ctna)
{
    $url="http://api.help.bj.cn/apis/weather2d?id=".$ctna;
        $wip=json_decode(file_get_contents($url));   
        if((string)$wip->status=='1'){
           return false;
        }
        $wdata=$wip;
        $wcity=$wdata->city;
        $marsw='不时会有沙尘暴，注意安全哦！';
        
        if($wip->city==''){
            return $marsw;
        }else{
            return $wdata;
        }
        // $data = (array)$ip->data;
        // $country = (array)$ip->data->country;
        // $region = (array)$ip->data->region;
        // $city = (array)$ip->data->city;
        // $citycode=(array)$ip->data->city_id;
    // return $data;
}
$weather=getWeather($cityname);
//获取系统
function getSys(){
    $agent=$_SERVER['HTTP_USER_AGENT'];
    if(strstr($agent, 'Android')){
        return 'Android';
    }else if(strstr($agent, 'iPhone')||strstr($agent, 'iPad')){
        return 'iOS';
    }else if(strstr($agent, 'Windows')){
        if(strstr($agent, 'Windows NT 10.0')){
            return 'Windows 10';
        }else if(strstr($agent, 'Windows NT 5.1')){
            return 'Windows XP';
        }else if(strstr($agent, 'Windows NT 6.0')){
            return 'Windows Vista';
        }else if(strstr($agent, 'Windows NT 6.1')){
            return 'Windows 7';
        }else if(strstr($agent, 'Windows NT 6.2')){
            return 'Windows 8';
        }else if(strstr($agent, 'Windows NT 6.3')){
            return 'Windows 8.1';
        }
    }else if(strstr($agent, 'Macintosh')){
        return 'MacOS';
    }
}
// print_r(getSys());
$nowagent=getSys();
//创建画布
$width=580;
$height=400;
$img=imagecreatetruecolor($width,$height);
$color=imagecolorallocate($img,200,200,200);
//设置透明颜色
$color_alpha=imagecolorallocatealpha($img,60,0,125,0);
$redcolor=imagecolorallocate($img,163,7,5);
//设置画布背景
//imagefilledrectangle ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
//x1y1左上角坐标，x2y2右下角坐标
imagefilledrectangle($img,0,0,$width,$height,$color);
// imagefilledrectangle($img,500,0,580,400,$color_alphab);
//设置水印文字
$font=25;
$str='Hello world!';
$fontwidth=imagefontwidth($font);
$fontheight=imagefontheight($font)+25;
$x=40;
$y=$fontheight+$font;
$sy=$fontheight+$font*2+20;
$ty=$fontheight+$font*3+40;
$fy=$fontheight+$font*4+60;
$siy=$fontheight+$font*5+80;
$sey=$fontheight+$font*6+100;
$ey=$fontheight+$font*7+120;
$ny=$fontheight+$font*8+140;
imagettftext($img,$font,0,$x,$y,$color_alpha,'./zkklt.ttf','欢迎您，来自'.$nowarea.'的网友');
if($weather=='不时会有沙尘暴，注意安全哦！'){
    if($nowarea==''){
        imagettftext($img,$font,0,$x,$sy,$color_alpha,'./zkklt.ttf','火星上'.$weather);
    }else{
        if(strstr($nowarea, '香港')){
            imagettftext($img,$font,0,$x,$sy,$color_alpha,'./zkklt.ttf','强烈谴责一切暴力行为！');
        }else{
            imagettftext($img,$font,0,$x,$sy,$color_alpha,'./zkklt.ttf','抱歉，获取不到天气哟');
        }  
    }
    imagettftext($img,$font,0,$x,$ty,$color_alpha,'./zkklt.ttf','您的ip为：'.$rip);
    imagettftext($img,$font,0,$x,$fy,$color_alpha,'./zkklt.ttf','您的系统为：'.$nowagent);
    imagettftext($img,$font,0,$x,$siy,$color_alpha,'./zkklt.ttf','图片生成于'.date("Y-m-d H:i:s"));
    if($nowarea==''){
        imagettftext($img,$font,0,$x,$sey,$redcolor,'./zkklt.ttf','玄隐铺路局祝您探火愉快！');
    }else{
        imagettftext($img,$font,0,$x,$sey,$redcolor,'./zkklt.ttf','玄隐铺路局祝您潜水愉快！');
    }
}else{
    imagettftext($img,$font,0,$x,$sy,$color_alpha,'./zkklt.ttf','今天'.$weather->weather.' '.$weather->temp);
    imagettftext($img,$font,0,$x,$ty,$color_alpha,'./zkklt.ttf','明天'.$weather->tomorrow->weather.' '.$weather->tomorrow->temp);
    imagettftext($img,$font,0,$x,$fy,$color_alpha,'./zkklt.ttf','您的ip为：'.$rip);
    imagettftext($img,$font,0,$x,$siy,$color_alpha,'./zkklt.ttf','您的系统为：'.$nowagent);
    imagettftext($img,$font,0,$x,$sey,$color_alpha,'./zkklt.ttf','图片生成于'.date("Y-m-d H:i:s"));
    imagettftext($img,$font,0,$x,$ey,$redcolor,'./zkklt.ttf','玄隐铺路局祝您潜水愉快！');
}

//输出画布图像
header("content-type:image/png");
imagepng($img);
imagedestroy($img);
?>