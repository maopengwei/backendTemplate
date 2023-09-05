<?php
namespace app\common\controller;

class Api extends Base {
	public function initialize() {
		parent::initialize();
		$this->size = 10;
		/*允许跨域*/
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";
		header('Access-Control-Allow-Origin:' . $origin);
		header('Access-Control-Allow-Credentials: true');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authToken");
		if (is_options()) {
			$this->result("1", 402, "option请求", "json");
		}
	}
	protected function s_msg($msg = "成功", $data = "") {
		if ($msg == null) {
			$msg = "成功";
		}
		$this->result($data, 1, $msg, 'json');
	}
	protected function e_msg($msg = "失败", $code = 0, $data = "") {
		if ($msg == null) {
			$msg = "失败";
		}
		$this->result($data, $code, $msg, 'json');
	}
	protected function msg($data) {
		$msg = '';
		$code = 1;
		$dada = $data;
		if (is_array($data)) {
			$msg = $data['msg'];
			$code = $data['code'];
			$dada = key_exists('data', $data) ?: "";
		}
		$this->result($dada, $code, $msg, 'json');
	}
}
