<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class Store extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	// 性别
	public function getSexTextAttr($value, $data) {
		$array = [
			0 => '女',
			1 => '男',
		];
		return $array[$data['sex']];
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
		return $this->where('Id', $data['pid'])->value('mobile');
	}
	public function getAnameAttr($value, $data) {
		return model('Admin')->where('Id', $data['aid'])->value('real_name');
	}
	public function getLevelTextAttr($value, $data) {
		if ($data['level'] == 0) {
			return '普通会员';
		}
		return model('Compo')->where('level', $data['level'])->value('name');
	}
	//详情
	public function detail($where, $field = "*") {
		return $this->where($where)->field($field)->find();
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
		//手机号不能重复
		$tel = $this->where('st_tel', $data['st_tel'])->count();
		if ($tel) {
			return [
				'code' => 0,
				'msg' => '手机号已存在',
			];
		}
		//门店名称
		$name = $this->where('st_name', $data['st_name'])->count();
		if ($tel) {
			return [
				'code' => 0,
				'msg' => '门店名称已存在',
			];
		}
		// 编号
		$number = $this->order('id desc')->value('st_serial_number');
		if ($number) {
			$bb = substr($number, -5);
			$cc = substr($number, 0, 2);
			$dd = $bb + 1;
			$new_number = $cc . $dd;
		} else {
			$new_number = 'md10001';
		}
		$data['st_pic'] = implode(",", $data['st_pic']);
		$data['st_label'] = implode(",", $data['st_label']);
		$data['st_pwd'] = encrypt($data['st_pwd']);
		$data['st_safe_pwd'] = encrypt($data['st_safe_pwd']);
		$data['st_serial_number'] = $new_number;
		$data['st_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetId($data);
		return [
			'code' => 1,
			'msg' => '添加成功',
			'data' => $rel,
		];
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

}
