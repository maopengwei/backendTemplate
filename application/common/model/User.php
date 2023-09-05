<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class User extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	public function parent() {
		return $this->hasOne('User', 'id', 'us_pid');
	}
	// 状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '禁用',
			1 => '正常',
		];
		return $array[$data['status']];
	}
	//父账号
	public function getPtelAttr($value, $data) {
		if ($data['us_pid']) {
			return $this->where('id', $data['us_pid'])->value('us_account');
		} else {
			return '空';
		}
	}
	//节点人
	public function getAtelAttr($value, $data) {
		if ($data['us_aid']) {
			return $this->where('id', $data['us_aid'])->value('us_account');
		} else {
			return '空';
		}
	}
	/*----------------我的报单中心*/
	public function getCenterTextAttr($value, $data) {
		if ($data['us_center']) {
			return $this->where('id', $data['us_center'])->value('us_account');
		} else {
			return '';
		}
	}
	public function getAnameAttr($value, $data) {
		return model('Admin')->where('Id', $data['aid'])->value('real_name');
	}
	public function getUsStatusTextAttr($value, $data) {
		$array = [
			0 => '已禁用',
			1 => '正常',
		];
		return $array[$data['us_status']];
	}
	//详情
	public function detail($where, $field = "*") {
		return $this->with('parent')->where('id',$where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		return $this->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
	}
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {

		$count = $this->where('us_tel', $data['us_tel'])->count();
		if ($count) {
			return [
				'code' => 0,
				'msg' => '该手机号已存在',
			];
		}
		// $number = db('user')->order('id desc')->value('us_account');
		// if ($number) {
		// 	$bb = substr($number, -5);
		// 	$cc = substr($number, 0, 3);
		// 	$dd = $bb + 1;
		// 	$new_number = $cc . $dd;
		// } else {
		// 	$new_number = 'als10001';
		// }
		$new_number = 'als'.rand(111,999).date('is').rand(000,999);
		$data['us_account'] = $new_number;
		$data['us_add_time'] = date('Y-m-d H:i:s');
		$data['us_head_pic'] = '/static/mobile/img/tu9.jpg';
		$data['us_pwd'] = mine_encrypt($data['us_pwd']);
		if (key_exists('us_safe_pwd', $data)) {
			$data['us_safe_pwd'] = mine_encrypt($data['us_safe_pwd']);
		}
		$rel = $this->insertGetId($data);
		return [
			'code' => 1,
			'msg' => '注册成功',
			'id' => $rel,
		];
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
				'code' => '1',
				'msg' => '修改成功',
			];
		} else {
			return [
				'code' => 0,
				'msg' => '您没有做出修改',
			];
		}

	}

}
