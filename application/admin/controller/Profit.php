<?php
namespace app\admin\controller;

/**
 * 利润表
 */
class Profit extends Common {

	public function __construct() {
		parent::__construct();
	}

	/*--------------------支付------------------------*/
	public function payRecord() {
		if (is_post()) {

			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.pay_type') != "") {
			$this->map[] = ['pay_type', '=', input('get.pay_type')];
		}
		if (input('get.pay_lei') != "") {
			$this->map[] = ['pay_lei', '=', input('get.pay_lei')];
		}
		$list = model('PayRecord')->chaxun($this->map, $this->order, $this->size);
		$num = model("PayRecord")->where($this->map)->sum('pay_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	/*--------------------提现-------------------------*/
	public function commission() {
		if (is_post()) {

			$rst = model('Tixian')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.tx_status') != "") {
			$this->map[] = ['tx_status', '=', input('get.tx_status')];
		}
		$list = model('Tixian')->chaxun($this->map, $this->order, $this->size);
		$num = model("Tixian")->where($this->map)->sum('tx_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}
	public function txCheck() {
		$id = input('post.id');
		$info = model('Tixian')->get($id);
		$rst = model('Tixian')->xiugai(['tx_status' => input('post.status')], ['id' => input('post.id')]);
		if ($rst) {
			if (input('post.status') == 2) {
				model("Wallet")->tianjia($info['us_id'], $info['tx_num'], 8);
				$this->success('已驳回');
			}else{
				$this->success('审核通过');
			}
		} else {
			$this->error('操作失败');
		}
	}

	/*---------------------会员卡----------------------*/
	public function wallet() {
		if (is_post()) {
			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.wa_type') != "") {
			$this->map[] = ['wa_type', '=', input('get.wa_type')];
		}
		$list = model('Wallet')->chaxun($this->map, $this->order, $this->size);
		$num = model("Wallet")->where($this->map)->sum('wa_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}


	/*---------------------奖金----------------------*/
	public function msc() {
		if (is_post()) {
			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.msc_type') != "") {
			$this->map[] = ['msc_type', '=', input('get.msc_type')];
		}
		$list = model('Msc')->chaxun($this->map, $this->order, $this->size);
		$num = model("Msc")->where($this->map)->sum('msc_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	/*--------------积分-----------------*/
	public function integral() {
		if (is_post()) {
			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.in_type') != "") {
			$this->map[] = ['in_type', '=', input('get.in_type')];
		}
		$list = model('Integral')->chaxun($this->map, $this->order, $this->size);
		$num = model("Integral")->where($this->map)->sum('in_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}
	
}
