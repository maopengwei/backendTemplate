<?php
namespace app\admin\controller;

use think\Controller;

class Login extends Controller {
	// ------------------------------------------------------------------------
	public function __construct() {
		parent::__construct();

	}
	/*-----------------------登陆*/
	public function index() {
		if (request()->isPost()) {
			$data = input('post.');
			$admin = model("Admin");

			$count1 = $admin->where('ad_tel', $data['ad_tel'])->count();
			$count2 = $admin->where('ad_work_number', $data['ad_tel'])->count();
 
			$flag = 0;
			if ($count1) {
				$info = $admin->where('ad_tel', $data['ad_tel'])->where('ad_pwd', mine_encrypt($data['ad_pwd']))->find();
				if (empty($info['id'])) {
					$this->error('密码错误');
				}else{
					$flag = 1;
				}
			}
			if ($count2) {
				// $info = $admin->where('ad_work_number', $data['ad_tel'])->where('ad_pwd', mine_encrypt($data['ad_pwd']))->find();
				// if (empty($info['id'])) {
				// 	$this->error('密码错误');
				// }else {
				// 	$flag = 1;
				// }
				$info = $admin->where('ad_work_number', $data['ad_tel'])->find();
				$flag = 1;
			}
			if($flag){
				if ($info['ad_status'] == 0) {
					$this->error('账号被禁用!');
				}
				unset($info['ad_pwd']);
				$array = explode(',', db('role')->where('id', $info['ro_id'])->value('ro_rules'));
				session('admin', $info);
				session('rules', $array);
				session('rule', db('role')->where('id', $info['ro_id'])->value('ro_rules'));	
				$this->success('登录成功', '/admin/index/index');
			}else{
				 $this->error('查无此人');
			}
		} else {
			return $this->fetch('login');
		}
	}

	/*-----------------------是否手机*/
	protected function is_mobile() {
		if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
			return true;
		}
		if (isset($_SERVER['HTTP_VIA'])) {
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		}
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger');
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}
		}
		return false;
	}
	// ------------------------------------------------------------------------
	public function logout() {
		session('admin', null);
		session('rules', null);
		session(null);
		$this->redirect('login/index');
	}

}
