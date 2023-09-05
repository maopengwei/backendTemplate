<?php
namespace app\admin\controller;
use app\admin\logic\Excel;
use think\Db;

/**
 * @todo 会员管理 查看，状态变更，密码重置
 */
class User extends Common {

	// ------------------------------------------------------------------------
	//用户列表
	public function index() {

		if (is_post()) {
			$rst = model('User')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$this->map[] = ['us_tel|us_account|us_real_name', '=', input('get.keywords')];
		}
		if (is_numeric(input('get.us_status'))) {
			$this->map[] = ['us_status', '=', input('get.us_status')];
		}
		if (is_numeric(input('get.us_is_jing'))) {
			$this->map[] = ['us_is_jing', '=', input('get.us_is_jing')];
		}
		$get = $this->request->get();
		
		if(!empty($get['excel'])){
            $sql =  DB::table('new_user')
            ->alias('u')
            ->join('user pu','pu.id = u.us_pid')
            ->where($this->map)
            ->field('
				u.id,
                u.us_account,
                u.us_tel,
				u.us_real_name,
				u.us_status,
				pu.us_account as p_account,
                u.us_add_time
            ')
            ->order('u.id desc')
            ->fetchSql(true)
            ->select();
            Excel::sql('user',$sql);

        }

		$list = model('User')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'yuming' => $_SERVER['HTTP_HOST'],
			'list' => $list,
		));
		return $this->fetch();
	}
	//添加
	public function add() {
		if (is_post()) {
			$data = input('post.');
			$validate = validate('Verify');
			$res = $validate->scene('addUser')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$pinf = model("User")->where('us_tel', $data['ptel'])->find();
			if (count($pinf)) {
				if ($pinf['us_jibie'] == 0) {
					return [
						'code' => 0,
						'msg' => '该推荐人不是经销商，没有推荐资格',
					];
				}
				
				$data['us_pid'] = $pinf['id'];
				$data['us_path'] = $pinf['us_path'] . ',' . $pinf['id'];
				$data['us_path_long'] = $pinf['us_path_long'] + 1;
			} else {
				$data['us_pid'] = 0;
				$data['us_path'] = 0;
				$data['us_path_long'] = 0;
			}
			$rel = model('User')->tianjia($data);
			return $rel;
		}
		return $this->fetch();
	}
	//升级等级
	public function level(){
		$info = model('User')->get(input('id'));
		if(is_post()){
			$data = input('post.');

			if($info['us_level']==0){
				if($data['us_level']==1){
					$type = 1;
				}elseif($data['us_level']==2){
					$type = 3;
				}
			}else{
				$type = 15;
			}
			$money = buy_type($type);
			$note = buy_note($type);
				
			$rel = model('PayRecord')->tianjia(5,$info['id'],$money,$type,$note);
			if($rel){
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
			
		}
		$this->assign('info', $info);
		return $this->fetch();
	}
	//升级经销商
	public function up(){
		$info = model('User')->get(input('id'));
		if(is_post()){
			$data = input('post.');
			$aa = get_type($info['us_jibie'],$data['us_jibie']-1);
			if($aa['code']){
				$money = buy_type($aa['type']);
				$note = buy_note($aa['type']);
				$rel = model('PayRecord')->tianjia(5,$info['id'],$money,$aa['type'],$note,$data['type']);
				if($rel){
					$this->success('操作成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				return $aa;
			}
			
		}
		$this->assign('info', $info);
		return $this->fetch();
	}
	//修改
	public function edit() {
		$info = model('User')->get(input('id'));
		if (is_post()) {
			$data = input('post.');
			if ($data['us_pwd'] != "") {
				$data['us_pwd'] = mine_encrypt($data['us_pwd']);
			} else {
				unset($data['us_pwd']);
			}
			if ($data['us_safe_pwd'] != "") {
				$data['us_safe_pwd'] = mine_encrypt($data['us_safe_pwd']);
			} else {
				unset($data['us_safe_pwd']);
			}
			//
			if($info['center_text']!=$data['us_center']){
				$center = model('User')->where('us_account', $data['us_center'])->find();
				if(!$center){
					$this->error('该报单中心不存在');
				}
				if($center['us_is_center']!=1){
					$this->error('该用户不是报单中心');
				}
				$array = explode(',',$info['us_path']);
				if(!in_array($center['id'],$array)){
					$this->error('该报单中心不是我的上级中人');
				}
				$data['us_center'] = $center['id'];
			}else{
				unset($data['us_center']);
			}
			if(key_exists('ptel',$data)){
				if($data['ptel']<>""){
					$parent = model('User')->where('us_tel', $data['ptel'])->find();
					if (count($parent)) {
						if($parent['id'] == $info['id']){
							$this->error('推荐人不能是自己');
						}
						if ($parent['us_jibie'] == 0) {
							$this->error('该推荐人没有推荐资格');
						}
						$data['us_pid'] = $parent['id'];
						$data['us_path'] = $parent['us_path'] . "," . $parent['id'];
						$data['us_path_long'] = $parent['us_path_long'] + 1;
					} else {
						$this->error('推荐人不存在');
					}
				}
				unset($data['ptel']);
			}
			$rel = model('User')->update($data);
			if($rel){
				$this->success('修改成功');
			}else{
				$this->error('您没有修改任何东西');
			}
		}
		$this->assign('info', $info);
		return $this->fetch();
	}

	//团队
	public function team() {
		if (is_post()) {
			$info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->field('id,us_path,us_pid,us_account,us_tel')->find();
			if (!$info) {
				return [
					'code' => 0,
					'msg' => "查无此人",
				];
			}
			$base = array(
				'id' => $info['id'],
				'pId' => $info['us_pid'],
				'name' => $info['us_account'] . "," . $info['us_tel'],
			);
			$znote[] = $base;
			$where[] = array('us_path', 'like', $info['us_path'] . "," . $info['id'] . "%");
			$list = Model('User')->where($where)->field('id,us_pid,us_account,us_tel')->select();
			foreach ($list as $k => $v) {
				$base = array(
					'id' => $v['id'],
					'pId' => $v['us_pid'],
					'name' => $v['us_account'] . "," . $v['us_tel'],
				);
				$znote[] = $base;
			}
			return [
				'code' => 1,
				'data' => $znote,
			];
		}
		if(input('get.id')){
			$this->assign('us_account',input('get.id'));
		}
		return $this->fetch();
	}

	//配送员列表
	public function courier() {
		if (is_post()) {
			$rst = model('Courier')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error('修改失败');
			}
			return $rst;
		}
		if (input('get.keywords')) {
			$this->map[] = ['co_number|co_name|co_tel', '=', trim(input('get.keywords'))];
		}
		if (is_numeric(input('get.co_status'))) {
			$this->map[] = ['co_status', '=', input('get.co_status')];
		}

		$list = model('Courier')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array('list' => $list));
		return $this->fetch();
	}
	//添加配送员
	public function courier_add() {
		if (is_post()) {
			$data = input('post.');

			$validate = validate('Verify');
			if (!$validate->scene('addCour')->check($data)) {
				$this->error($validate->getError());
			}
			$rel = model("Courier")->tianjia($data);
			if ($rel) {
				$this->success('添加成功');
			} else {
				$this->error('添加失败');
			}
		}
		return $this->fetch();
	}

	//地址列表
	public function addr() {
		if (is_post()) {
			$rst = model('User_addr')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error('修改失败');
			}
			return $rst;
		}
		if (input('get.id')) {
			$this->map[] = ['us_id', '=', input('get.id')];
		} else {
			$this->error("非法操作");
		}
		$list = model('User_addr')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
			'name' => model('User')->where('id', input('get.id'))->value('us_account'),
		));
		return $this->fetch();

	}
	//地址修改
	public function addr_edit() {
		if (is_post()) {
			$data = input("post.");
			$validate = validate('Verify');
			$rst = $validate->scene('editAddr')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			unset($data['id']);
			$rel = model('Store')->xiugai($data, ['id' => input('post.id')]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
		}
		$info = model("User_addr")->get(input('get.id'));
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}
	public function position() {
		return $list = model("User_addr")->where('us_id', input('post.us_id'))->select();
	}
	public function is_jing() {
		$id = input('get.id');
		$info = model("User")->get($id);
		if ($info['us_is_jing'] != 1) {
			return [
				'code' => 0,
				'msg' => '该用户不是待进入节点图状态',
			];
		}
		if ($info['us_jibie'] == 0) {
			return [
				'code' => 0,
				'msg' => '该用户不是经销商',
			];
		}
		return [
			'code' => 1,
		];
	}
	public function tupu() {
		if (is_post()) {
			if (input('post.us_account')) {
				$info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->find();
				if (!$info || $info['us_is_jing'] != 2) {
					return [
						'code' => 0,
						'msg' => '该用户不存在或该用户不是经销商',
					];
				}
			}
			$znote = jiedian();
			




			$this->map[] = ['us_tree', 'like', $info['us_tree'] . "," . $info['id'] . "%"];
			$this->map[] = ['us_tree_long', '<=', $info['us_tree_long'] + 2];
			$this->map[] = ['us_is_jing', '=', 2];
			$list = db('user')->where($this->map)->select();
			
			array_push($list, $info);
			
			for ($i = 0; $i < 8; $i++) {
				if (isset($list[$i])) {
					$length = $list[$i]['us_tree_long'] - $info['us_tree_long'];
					if ($length == 0) {
						$key = 0;
					} elseif ($length == 1) {
						$key = 2 * $length + $list[$i]['us_qu'] - 1;
					} else {
						$key = 2 * $length + $list[$i]['us_qu'] + $list[$i]['us_aid_qu'] * 2 - 1;
					}
					$znote[$key]['name'] = $list[$i]['us_account'];
					$znote[$key]['tel'] = $list[$i]['us_tel'] . "(" . $list[$i]['us_real_name'] . ")";
					$znote[$key]['zuo'] = "左:" . $list[$i]['us_res_zuo'] . "," . $list[$i]['us_rel_zuo'] . "," . $list[$i]['us_per_zuo'];
					$znote[$key]['you'] = "右:" . $list[$i]['us_res_you'] . "," . $list[$i]['us_rel_you'] . "," . $list[$i]['us_per_you'];
					$znote[$key]['level'] = "级别:" . cache('calcu')[$list[$i]['us_jibie'] - 1]['cal_name'];
					$znote[$key]['k'] = $list[$i]['id'];
					$znote[$key]['p'] = $list[$i]['us_aid'];
					if ($list[$i]['us_head_pic']) {
						$znote[$key]['source'] = $list[$i]['us_head_pic'];
					}
				}
			}
			return [
				'code' => 1,
				'data' => $znote,
				'ptel' =>$info['atel'],
			];
		} else {
			//进入节点图的id
			$id = input('get.id');
			$type = input('get.type');
			$idd = 0;
			$us_account = 1;
			if ($id) {
				if($type){
					$us_account = model('User')->where('id',$id)->where('us_is_jing', 2)->value('us_account');
					$idd = 0;
				}else{
					$person = model("User")->get($id);
					if ($person) {
						$account = model('User')->where('id', $person['us_pid'])->where('us_is_jing', 2)->value('us_account');
						if ($account) {
							$us_account = $account;
						}
						$idd = $id;
					}
				}
			}
			$this->assign(array(
				'us_account' => $us_account,
				'id' => $idd,
			));
			return $this->fetch();
		}
	}
	//所有删除
	public function del() {
		if (input('post.id')) {
			$id = input('post.id');
		} else {
			$this->error('id不存在');
		}

		$info = model('User')->get($id);
		if ($info) {
			if ($info['us_is_jing'] == 2) {
				$this->error('已经成为经销商的会员无法删除');
			}
			$rel = model('User')->destroy($id);
			if ($rel) {
				$this->success('删除成功');
			} else {

				$this->error('请联系网站管理员');
			}
		} else {
			$this->error('数据不存在');
		}
	}
	//经销商进节点
	public function dealer() {
		$data = input('post.');
		$id = $data['tupu_id'];
		if (!$id) {
			$this->error('您没有选择进入图谱的用户');
		} else {
			$inf = model('User')->get($id);
			if (!$inf) {
				$this->error('您选择进入图谱的用户不存在');
			}
			if($inf['us_is_jing']<>1){
				$this->error('您选择进入图谱的用户不是待进入节点状态');
			}
			if($inf['us_jibie']==0){
				$this->error('您选择进入图谱的用户不是经销商');
			}
			if($inf['us_level']==0){
				$this->error('您选择进入图谱的用户不是会员');
			}
		}

		/*节点人id  左右区*/
		$info = model("User")->get($data['us_aid']);
		
		$arr = explode(',',$info['us_tree']);  //节点人tree

		if($info['id']<>$inf['us_pid'] && !in_array($inf['us_pid'],$arr)){
			$this->error('您选择进入图谱的用户不在推荐人图谱中');
		}
		if($data['qu']){
			$zuo = model("user")->where('us_aid',$info['id'])->where('us_qu',0)->find();
			if(!$zuo){
				$this->error('必须先放到左区');
			}
			$xiaji = model('User')->where('us_pid',$info['id'])->where('us_is_jing',2)->find();
			if(!$xiaji){
				$this->error('该节点人没有推荐经销商');
			}
		}
		$array = [
			'us_aid' => $info['id'],
			'us_aid_qu' => $info['us_qu'],
			'us_tree' => $info['us_tree'] . "," . $info['id'],
			'us_tree_long' => $info['us_tree_long'] + 1,
			'us_qu' => $data['qu'],
			'us_is_jing' => 2,
		];
		$rel = model("User")->xiugai($array, ['id' => $id]);
		// $rel['code'] = 1;
		if ($rel['code']) {

			$money = cache('calcu')[$inf['us_jibie'] - 1]['cal_money'];

			//业绩 对碰 管理 精英
			yeji($id, $money);
			//见点奖励
			jiandian($id, $money);
			
			//报单中心
			if($inf['us_center']){
				$num = $money * cache('setting')['center_calcu'] / 100;
				if(cache('setting')['switch_center']){
					$center = model('User')->get($inf['us_center']);
					if($center['us_is_center']==1){
						one_to_two($inf['us_center'],$num,6,6);
					}
				}
			}
		}
		return $rel;
	}
	protected function scerweima($url = '', $logo = '') {
		require_once __DIR__ . '\qrcode.php';
		$value = $url; //二维码内容
		$errorCorrectionLevel = 'H'; //容错级别
		$matrixPointSize = 7; //生成图片大小
		//生成二维码图片
		$path = '/uploads/erweima/' . date('YmdHis') . rand(1000, 9999) . '.png';
		$filename = $_SERVER['DOCUMENT_ROOT'] . $path;
		\QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		// $logo = $_SERVER['DOCUMENT_ROOT'] . '/static/admin/img/tou.jpg'; //准备好的logo图片
		$QR = $filename; //已经生成的原始二维码图
		if (file_exists($logo)) {
			$QR = imagecreatefromstring(file_get_contents($QR)); //目标图象连接资源。
			$logo = imagecreatefromstring(file_get_contents($logo)); //源图象连接资源。
			$QR_width = imagesx($QR); //二维码图片宽度
			$QR_height = imagesy($QR); //二维码图片高度
			$logo_width = imagesx($logo); //logo图片宽度
			$logo_height = imagesx($logo); //logo图片高度
			$logo_qr_width = $QR_width / 4; //组合之后logo的宽度(占二维码的1/5)
			$scale = $logo_width / $logo_qr_width; //logo的宽度缩放比(本身宽度/组合后的宽度)
			$logo_qr_height = $logo_height / $scale; //组合之后logo的高度
			$from_width = ($QR_width - $logo_qr_width) / 2; //组合之后logo左上角所在坐标点
			//重新组合图片并调整大小
			/*
	         *  imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
*/
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		// header('Content-Type: image/png');
		//输出图片
		$path1 = '/uploads/erweima/' . date('YmdHis') . rand(1000, 9999) . '.png';
		imagepng($QR, $_SERVER['DOCUMENT_ROOT'] . $path1);
		imagedestroy($QR);
		imagedestroy($logo);
		return $path1;
	}
}
