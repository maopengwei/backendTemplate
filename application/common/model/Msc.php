<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class Msc extends Model {
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
		$type_text = array(
			1 => '对碰奖励',
			2 => '见点奖励',
			3 => '静态拓展奖励',
			4 => '管理奖励',
			5 => '精英奖励',
			6 => '报单中心奖励',
			7 => '客户提现',
			8 => '提现驳回',
			9 => '直推奖励',
		);
		if ($note == '') {
			$note = $type_text[$type];
		}
		$array = array(
			'us_id' => $uid,
			'msc_num' => $jine,
			'msc_type' => $type,
			'msc_note' => $note,
			'msc_add_time' => date('Y-m-d H:i:s'),
		);
		// if($type == 3){
		// 	$array['msc_add_time'] = '2012-08-21 23:55:00';
		// }
		$rel = $this->insertGetId($array);
		if ($rel) {
			if (in_array($type, array(1, 2, 3, 4, 5, 6, 8, 9))) {
				model('User')->where('id', $uid)->setInc('us_msc', $jine);
			} elseif (in_array($type, array(7))) {
				model('User')->where('id', $uid)->setDec('us_msc', $jine);
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
