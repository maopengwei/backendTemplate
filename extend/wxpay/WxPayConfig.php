<?php
namespace wxpay;

/**
 * 微信配置账号信息文件，在使用框架的时候可以使用其他方式来替代
 *
 * Class WxPayConfig
 * @package wxpay
 * @author goldeagle
 */
class WxPayConfig
{
    //=======【基本信息设置】=====================================
    //
    /**
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @var string
     */
    // public static $APPID = 'wxaeb8f0a1f273f400';
    // public static $MCHID = '1494724522';
    // public static $KEY = 'vLH0ZpuZoZnAsvKqz9BXA2qwkqSyS5JJ';
    // public static $APPSECRET = '4a2f363a52d3715517c2bc97c452fc8c';
    public static $APPID = 'wxbefe55cc82802a4c';
    public static $MCHID = '1508989651';
    public static $KEY = 'qwertyuiopLKJHGFDSA654321ZXCvbnm';
    public static $APPSECRET = 'e5cad3753be560f79027e2f0a4cafedf';
    // public static $NOTIFY_URL = '';

    //=======【证书路径设置】=====================================
    /**
     * 设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var string path
     */
    public static $SSLCERT_PATH = '../cert/apiclient_cert.pem';
    public static $SSLKEY_PATH = '../cert/apiclient_key.pem';

    //=======【curl代理设置】===================================
    /**
     * 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var string unknown_type
     */
    public static $CURL_PROXY_HOST = "0.0.0.0"; //"10.152.18.220";
    public static $CURL_PROXY_PORT = 0; //8080;

    //=======【上报信息配置】===================================
    /**
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    public static $REPORT_LEVENL = 1;

    private static $instance = null;

    /**
     * Cache constructor. 私有化构造器，避免被构造
     */
    private function __construct()
    {
    }

    /**
     * 克隆会被触发错误
     */
    public function __clone()
    {
        trigger_error('Singleton CANNOT NOT be cloned!', E_USER_ERROR);
    }

    /**
     * 创建并返回实例
     * @return null
     */
    public static function getInstance()
    {
        if (self::$instance === null || !(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
