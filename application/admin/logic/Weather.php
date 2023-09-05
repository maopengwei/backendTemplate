<?php
namespace app\admin\logic;

use think\facade\Log;

class Weather
{
    public function index(){

        $base_url = 'https://restapi.amap.com/v3/weather/weatherInfo';
        $key = '1d867cc016d463723e96874032525ad5c';
        $parameters = "?key=".$key."&city=410526&extensions=all&output=json";
        //410526 滑县
        $url = $base_url.$parameters;

        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        #content必须是utf8编码
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER,array('Content-Type: application/json;charset=utf-8'));

        $phoneList = curl_exec($cURLConnection);

        if(!$phoneList){
            Log::warning('测试天气服务器异常'); return;
        }

        curl_close($cURLConnection);
        dump($phoneList);
        $jsonArrayResponse = json_decode($phoneList,true);
        if($jsonArrayResponse['status'] == 1){
            $array =$jsonArrayResponse['forecasts'][0];
            $arr = $array['casts'][0];
            $message = substr($array['reporttime'],0,10).' '.$array['province'].$array['city']
            .", 今天白天天气".$arr['dayweather'].",".$arr['daytemp']."度," . $arr['daywind']."风".$arr['daypower']."级, 晚间天气"
            .$arr['nightweather'].",".$arr['nighttemp']."度," . $arr['nightwind']."风".$arr['nightpower']."级";

        }else{
            Log::warning('测试天气预报异常');
            $message = '获取天气异常，请联系管理员';
        }

        $wecom = new Wecom();
        $wecom->index($message);

    }
}