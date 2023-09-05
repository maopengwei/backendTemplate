<?php
namespace app\common\model;

use think\Model;

/**
 * Ccart
 */
class Ccart extends Model {
	public function tianjia($number, $pd_id, $pd_num) {
		$data = array(
			'cc_number' => $number,
			'pd_id' => $pd_id,
			'pd_num' => $pd_num,
		);
		$rel = $this->insertGetId($data);
		return $rel;
	}

}