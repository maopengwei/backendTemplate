<?php
namespace app\admin\controller;

use Cache;

/**
 * 公共控制器
 */
class Every extends Common
{

    public function __construct()
    {
        parent::__construct();
    }
    //所有删除
    public function allDel()
    {
        if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        if (input('post.key')) {
            $key = input('post.key');
        } else {
            $this->error('数据表不存在');
        }
        $array = array(
            'Admin', 'Carouse', 'Center', 'Code', 'Message', 'Msc', 'Order', 'Product', 'Wallet', 'Purchase', 'Sell', 'Tixian', 'Transfer', 'Cate', 'User',
        );
        if (!in_array($key, $array)) {
            $this->error('非法操作');
        }
        $info = model($key)->get($id);
        if ($info) {
            $rel = model($key)->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
    }
    //清楚缓存
    public function clear()
    {
        Cache::clear();
    }
}
