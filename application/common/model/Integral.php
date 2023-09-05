<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class Integral extends Model {
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
	public function tianjia($uid, $jine, $type) {
		$type_text = array(
			1 => '购物奖励',
			2 => '购物消耗',
		);
		$array = array(
			'us_id' => $uid,
			'in_num' => $jine,
			'in_type' => $type,
			'in_note' => $type_text,
			'in_add_time' => date('Y-m-d H:i'),
		);
		$rel = $this->insertGetId($array);
		if ($rel) {
			if ($type == 1) {
				model('User')->where('id', $uid)->setInc('us_integral', $jine);
			} else {
				model('User')->where('id', $uid)->setDec('us_integral', $jine);
			}
			return [
				'code' => 1,
				'msg' => '添加成功',
			];
		} else {
			return [
				'code' => 0,
				'msg' => '添加失败',
			];
		}

	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		$rel = $this->save($data, $where);
		if ($rel) {
			return [
				'code' => 1,
				'msg' => '修改成功',
			];
		} else {
			return [
				'code' => 0,
				'msg' => '修改失败',
			];
		}
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
