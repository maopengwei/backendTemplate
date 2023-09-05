<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *充值
 */
class OrderDetail extends Model {
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
		$data['or_de_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetId($data);
		return $rel;
	}
	public function getStTextAttr($value, $data) {
		if ($data['st_id'] == "") {
			return '';
		}
		$name = model('Store')->where('id', $data['st_id'])->value('st_name');
		return $name;
	}
	public function getCaTextAttr($value, $data) {
		if ($data['ca_id'] == "") {
			return '';
		}
		$name = model('Cate')->where('id', $data['ca_id'])->value('ca_name');
		return $name;
	}

	public function Order() {
		return $this->hasOne('Order', 'or_number', 'or_number');
	}

}
