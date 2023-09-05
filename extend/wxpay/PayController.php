<?php
namespace app\mall\controller;

use think\Controller;
use app\common\model\Order;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\JsApiPay;
use wxpay\NativePay;
use wxpay\PayNotifyCallBack;
use think\Log;
use wxpay\WxPayApi;
use wxpay\WxPayConfig;

class Pay extends Controller
{
    
    /**
     * 微信支付使用 JSAPI 的样例
     * @return mixed
     */
    public function index()
    {
        // if (isset($id) && $id != 0) {
            $id = session('orderId');
            //获取用户openid
            $tools = new JsApiPay();
            if (!session('userUid')) {
                $openId = $tools->getOpenid();
            } else {
                $openId = session('userUid');
            }

            //统一下单
            $money = session('money');
            $input = new WxPayUnifiedOrder();
            $input->setBody(session('body'));
            // $input->setAttach("test");
            $input->setOutTradeNo(WxPayConfig::$MCHID . date("YmdHis"));
            $input->setTotalFee($money * 100);
            $input->setTimeStart(date("YmdHis"));
            $input->setTimeExpire(date("YmdHis", time() + 600));
            // $input->setGoodsTag("Reward");
            $input->setNotifyUrl("http://www.77tec.top/mall/pay/notify/id/" . $id);
            $input->setTradeType("JSAPI");
            $input->setOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);

            $jsApiParameters = $tools->getJsApiParameters($order);

            $this->assign('order', $order);
            $this->assign('jsApiParameters', $jsApiParameters);
            return $this->fetch();
        // }
    }

    /**
     * 异步接收订单返回信息，订单成功付款
     * @param int $id 订单编号
     */
    public function notify($id = 0)
    {
        $notify = new PayNotifyCallBack();
        $notify->handle(true);

        //找到匹配签名的订单
        $order = Order::get($id);
        if (!isset($order)) {
            Log::write('未找到订单，id= ' . $id);
        }
        $succeed = ($notify->getReturnCode() == 'SUCCESS') ? true : false;
        if ($succeed) {
            Log::write('订单' . $order->id . '支付成功');
            $order->save([
                'status' => '0',
                'payment' => 2,
                'is_pay' => 1
                ], ['id' => $order->id]);
            Log::write('订单' . $order->id . '状态更新成功');

            $memInfo = db('member')->where('uid',$order->uid)->find();

            $datas = [
                'uid' => $order->uid,
                'name' => $memInfo['name'],
                'tel' => $memInfo['tel'],
                'sum' => $order->order_money,
                'note' => '订单编号：'.$order->order_no,
                'create_time' => date('Y-m-d H:i:s')
            ];
            db('pay_log')->insert($datas);
        } else {
            // 返还金币
            model('Order')->updateMemAcc($order->uid, 0, $order->order_coin, '+', '支付未成功，订单退回');

            Log::write('订单' . $id . '支付失败');
        }
    }
}
