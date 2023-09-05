<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *产品
 * status  1 下架 2 上架 0 仓库
 */
class Product extends Model {
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
	public function tianjia($data) {
		$data['pd_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetid($data);
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
		$rel = $this->save($data, $where);
		return [
			'code' => 1,
			'msg' => '修改成功',
			'data' => $rel,
		];
	}

}
