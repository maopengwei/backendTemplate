<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 会员卡
 */
class Wallet extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	//详情
	public function detail($where, $field = "*") {
		return $this->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($uid, $jine, $type, $note = "") {
		$info = model('User')->get($uid);
		$type_text = array(
			1 => '对碰奖励',
			2 => '见点奖励',
			3 => '静态拓展奖励',
			4 => '管理奖励',
			5 => '精英奖励',
			6 => '报单中心奖励',
			7 => '直推奖励',
			// 7 => '二代佣金奖励',

			8 => '充值',
			9 => '开通vip',
			10 => '续费vip',
			11 => '开通vipplus',
			12 => '续费vipplus',
			13 => 'vip升vipplus',
			14 => '开通经销商',
			15 => '升级经销商',
		);
		$array = array(
			'us_id' => $uid,
			'wa_num' => $jine,
			'wa_type' => $type,
			'wa_note' => $type_text[$type],
			'wa_add_time' => date('Y-m-d H:i:s'),
		);
		// if($type == 3){
		// 	$array['wa_add_time'] = '2012-08-21 23:55:00';
		// }
		$rel = $this->insertGetId($array);
		if ($rel) {
			if (in_array($type, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13,14,15))) {
				model('User')->where('id', $uid)->setInc('us_wallet', $jine);
			} elseif (in_array($type, array(14))) {
				model('User')->where('id', $uid)->setDec('us_wallet', $jine);
			}
			switch ($type) {
			case $type == 9:
				$time = date("Y-m-d H:i:s", strtotime("+1years", strtotime($array['wa_add_time']))); //过期时间 明年现在
				$number = level_number();
				model("User")->update([
					'us_level_day' => $time,
					'us_level_number'=>$number,
					'us_level' => 1,
					'id' => $uid,
				]);
				break;
			case $type == 10:
				$time = date("Y-m-d H:i:s", strtotime("+1years", strtotime($info['us_level_day']))); //过期时间 之前过期时间加1年
				model("User")->update([
					'us_level_day' => $time,
					'id' => $uid,
				]);
				break;
			case $type == 11:
				$time = date("Y-m-d H:i:s", strtotime("+1years", strtotime($array['wa_add_time']))); //过期时间 明年现在
				$number = level_number();
				model("User")->update([
					'us_level_day' => $time,
					'us_level_number'=>$number,
					'us_level' => 2,
					'id' => $uid,
				]);
				break;
			case $type == 12:
				$time = date("Y-m-d H:i:s", strtotime("+1years", strtotime($info['us_level_day']))); //过期时间 之前过期时间加1年
				model("User")->update([
					'us_level_day' => $time,
					'id' => $uid,
				]);
				break;
			case $type == 13:
				$time = date("Y-m-d H:i:s", strtotime("+1years", strtotime($array['wa_add_time']))); //过期时间 明年现在
				model("User")->update([
					'us_level_day' => $time,
					'us_level' => 2,
					'id' => $uid,
				]);
				break;
			default:
				break;
			}
		}
		return $rel;
	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		return $this->save($data, $where);
	}

	//用户账号
	public function getUsTextAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_account');
		return $name;
	}
	//真实姓名
	public function getUsNameAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_real_name');
		return $name;
	}
}
