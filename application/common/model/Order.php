<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *充值
 */
class Order extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

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
		$rel = $this->insertGetId($data);
		return $rel;
	}

	public function getUsTextAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$status = model('User')->where('id', $data['us_id'])->value('us_account');
		return $status;
	}
	public function getStTextAttr($value, $data) {
		if ($data['st_id'] == "") {
			return '';
		}
		$status = model('Store')->where('id', $data['st_id'])->value('st_name');
		return $status;
	}
	public function getCoTextAttr($value, $data) {
		if ($data['co_id'] == "") {
			return '';
		}
		$name = model('Courier')->where('id', $data['co_id'])->value('co_name');
		return $name;
	}
	public function getTypeTextAttr($value, $data) {
		$array = [
			0 => '正常',
			1 => '预购',
		];
		return $array[$data['or_type']];
	}
	public function getStyleTextAttr($value, $data) {
		$array = [
			0 => '全额',
			1 => '购物币',
		];
		return $array[$data['or_style']];
	}
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '待付款',
			1 => '待配送',
			2 => '待收货',
			3 => '已完成',
		];
		return $array[$data['or_status']];
	}
	public function xiugai($array, $where) {
		$this->save($array, $where);
		return [
			'code' => '1',
			'msg' => '修改成功',
		];
	}

	public function zhifu($id) {
		$info = $this->get($id);
		$array = [
			'or_status' => 1,
			'or_pay_time' => date('Y-m-d H:i:s'),
		];
		$rel = $this->xiugai($array, ['id' => $id]);
	}
}
