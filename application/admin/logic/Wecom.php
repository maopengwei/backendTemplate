<?php
namespace app\admin\logic;

class Wecom
{

    public function index($message){
        #key=企业微信机器人中的值
        $key = '65de135c-fcee-4b8f-9ca7-d4dfecbef72a';
        $webhook = "https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=".$key;   #这里需要修改
        # curl初始化
        $curl = curl_init();
        #需要推送的url
        curl_setopt($curl, CURLOPT_URL, $webhook);
        curl_setopt($curl, CURLOPT_POST, 1);
        #content必须是utf8编码
        curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type: application/json;charset=utf-8'));
        #content 为需要推送的内容

        $post = [
            'msgtype' => 'text',
            'text' => array(
                'content' => $message
            )
        ];
        $post_data = json_encode($post);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($curl);
        curl_close($curl);
    }
}