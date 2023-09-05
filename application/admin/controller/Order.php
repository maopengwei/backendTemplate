<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @todo
 */
class Order extends Common {

	// ------------------------------------------------------------------------
	// 订单列表
	public function index() {
		if (is_post()) {

			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.or_status') != "") {
			$this->map[] = ['or_status', '=', input('get.or_status')];
		}
		if (input('get.st_serial_number') != "") {
			$st_id = model("Store")->where('st_serial_number|st_name', input('get.st_serial_number'))->value('id');
			if (!$st_id) {
				$st_id = 0;
			}
			$this->map[] = ['st_id', '=', $st_id];
		}
		if (input('get.or_number') != "") {
			$this->map[] = ['or_number', '=', input('get.or_number')];
		}
		if (input('get.start') && input('get.end') == "") {
			$this->map[] = ['or_add_time', '>=', input('get.start')];
		}
		if (input('get.start') == "" && input('get.end')) {
			$this->map[] = ['or_add_time', '=<', input('get.end')];
		}
		if (input('get.start') && input('get.end')) {
			$this->map[] = ['or_add_time', 'between', array(input('get.start'), input('get.end'))];
		}
		if (input('get.a') == 1) {
			$list = model("Order")->where($this->map)->select();
			// $url = action('Excel/order', ['list' => $list]);
			$bb = env('ROOT_PATH') . "public\order.xlsx";
			if (file_exists($bb)) {
				unlink($bb);
			}
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$sheet->setCellValue('A1', '订单编号')
				->setCellValue('B1', '店铺名称')
				->setCellValue('C1', '客户名称')
				->setCellValue('D1', '总价值')
				->setCellValue('E1', '购物币消耗')
				->setCellValue('F1', '购买方式')
				->setCellValue('G1', '订单状态')
				->setCellValue('H1', '添加时间')
				->setCellValue('I1', '预约时间');
			$i = 2;
			foreach ($list as $k => $v) {
				$sheet->setCellValue('A' . $i, $v['or_number'])
					->setCellValue('B' . $i, $v['st_text'])
					->setCellValue('C' . $i, $v['us_text'])
					->setCellValue('D' . $i, $v['or_total'])
					->setCellValue('E' . $i, $v['or_coin'])
					->setCellValue('F' . $i, $v['type_text'])
					->setCellValue('G' . $i, $v['status_text'])
					->setCellValue('H' . $i, $v['or_add_time'])
					->setCellValue('I' . $i, $v['or_opinion_time']);
				$i++;
			}

			$writer = new Xlsx($spreadsheet);
			$writer->save('order.xlsx');
			return "http://" . $_SERVER['HTTP_HOST'] . "/order.xlsx";
		}
		$list = model('Order')->chaxun($this->map, $this->order, 5);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}
	// 订单详细列表
	public function detail() {
		if (is_post()) {

			$rst = model('Order_detail')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.id')) {
			$this->map[] = ['or_id', '=', input('get.id')];
		}
		if (input('get.or_number')) {
			$this->map[] = ['or_number', '=', input('get.or_number')];
		}
		if (input('get.ca_name')) {
			$ca_id = model("Cate")->where('ca_name', input('get.ca_name'))->select();
			if (!$ca_id) {
				$this->map[] = ['ca_id', '=', 0];
			} else {
				$array = [];
				foreach ($ca_id as $k => $v) {
					array_push($array, $v['id']);
				}
				$this->map[] = ['ca_id', 'in', $array];
			}

		}
		if (input('get.pd_name')) {
			$this->map[] = ['or_de_name', 'like', "%" . input('get.pd_name') . "%"];
		}
		if (input('get.a') == 1) {
			$list = model("OrderDetail")->where($this->map)->select();
			// $url = action('Excel/order', ['list' => $list]);
			$bb = env('ROOT_PATH') . "public\detail.xlsx";
			if (file_exists($bb)) {
				unlink($bb);
			}
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$sheet->setCellValue('A1', '订单编号')
				->setCellValue('B1', '分类名称')
				->setCellValue('C1', '产品名称')
				->setCellValue('D1', '产品单价')
				->setCellValue('E1', '购物币需求')
				->setCellValue('F1', '产品数量')
				->setCellValue('G1', '订单状态')
				->setCellValue('H1', '添加时间');
			$i = 2;
			foreach ($list as $k => $v) {
				$order = model("Order")->where('or_number', $v['or_number'])->find();
				$sheet->setCellValue('A' . $i, $order['or_number'])
					->setCellValue('B' . $i, $v['ca_text'])
					->setCellValue('C' . $i, $v['or_de_name'])
					->setCellValue('D' . $i, $v['or_de_price'])
					->setCellValue('E' . $i, $v['or_de_coin'])
					->setCellValue('F' . $i, $v['or_de_num'])
					->setCellValue('G' . $i, $order['status_text'])
					->setCellValue('H' . $i, $order['or_add_time']);
				$i++;
			}

			$writer = new Xlsx($spreadsheet);
			$writer->save('detail.xlsx');
			return "http://" . $_SERVER['HTTP_HOST'] . "/detail.xlsx";
		}
		$list = model('OrderDetail')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}
}
