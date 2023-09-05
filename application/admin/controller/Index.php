<?php
namespace app\admin\controller;

/**
 * @todo 首页操作
 */
class Index extends Common {
	// ------------------------------------------------------------------------
	public function index() {
		return $this->fetch();
	}

	// ------------------------------------------------------------------------
	public function welcome() {
		// 获取平台账户详情
		$us_count = model("User")->count();
		$us_today = model("User")->whereTime('us_add_time', 'today')->count();
		$st_count = model('Store')->count();
		$st_today = model("Store")->whereTime('st_add_time', 'today')->count();
		$pd_count = model("Product")->count();
		$pd_today = model("Product")->whereTime('pd_add_time', 'today')->count();
		$yong = model("User")->sum('us_msc');
		$tixian = model("tixian")->whereTime('tx_add_time', 'today')->sum('tx_num');
		$coin = model("User")->sum('us_wallet');
		$order_today = model("Order")->whereTime('or_add_time', 'today')->count();
		$order_jine = model("Order")->whereTime('or_add_time', 'today')->sum('or_total');

		$order_count = model("Order")->count();
		$order_total = model("Order")->sum('or_total');

		$this->assign(array(
			'us_count' => $us_count,
			'us_today' => $us_today,
			'st_count' => $st_count,
			'st_today' => $st_today,
			'pd_count' => $pd_count,
			'pd_today' => $pd_today,
			'yong' => $yong,
			'tixian' => $tixian,
			'coin' => $coin,
			'order_today' => $order_today,
			'order_jine' => $order_jine,
			'order_count' => $order_count,
			'order_total' => $order_total,
		));
		return $this->fetch();
	}
	// ------------------------------------------------------------------------

}
