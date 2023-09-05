<?php
function is_options() {
	return request()->isOptions();
}
function is_post() {
	return request()->isPost();
}
function is_get() {
	return request()->isGet();
}

// 对象转数组
function object_array($array)
{
    if (is_object($array)) {
        $array = (array) $array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

/**
 * 加密函数
 * @param string 加密后字符串
 */
function mine_encrypt($data, $key = 'fes45dskfes45dsk') {
	$prep_code = serialize($data); //序列化

	$block = 8; // 获得加密算法的分组大小 8

	$pad = $block - (strlen($prep_code) % $block);
	if (($pad = $block - (strlen($prep_code) % $block)) < $block) {
		$prep_code .= str_repeat(chr($pad), $pad);
	}
	$encrypt = openssl_encrypt($prep_code, 'AES128', '55555555555', OPENSSL_RAW_DATA, $key);

	return base64_encode($encrypt);
}

/**
 *  解密函数
 * @param array 解密后数组
 */
function mine_decrypt($str, $key = 'fes45dskfes45dsk') {
	$str = base64_decode($str);
	$str = openssl_decrypt($str, 'AES128', '55555555555', OPENSSL_RAW_DATA, $key);
	$block = 8;
	$pad = ord($str[($len = strlen($str)) - 1]);
	if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
		$str = substr($str, 0, strlen($str) - $pad);
	}
	return unserialize($str);
}
/*------前后分离----------*/
/**
 * Md5合成
 */
function HmacMd5($data, $key) {
	//RFC 2104 HMAC implementation for php
	//Creates an md5 HMAC.
	//Eliminates the need to install mhash to compute a HMAC
	//Hacked by Lance Rushing(NOTE:Hacked means written)
	//需要配置环境支持iconv,否则中文参数不能正常处理
	$b = 64;
	if (strlen($key) > $b) {
		$key = pack("H*", md5($key));
	}
	$key = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad;
	$k_opad = $key ^ $opad;
	return md5($k_opad . pack("H*", md5($k_ipad . $data)));
}
//解密
function jsDecrypt($encryptedData, $privateKey, $iv = "O2%=!ExPCuY6SKX(") {
	$encryptedData = base64_decode($encryptedData);
	$decrypted = openssl_decrypt($encryptedData, 'AES128', $privateKey, OPENSSL_RAW_DATA, $iv);
	$decrypted = rtrim($decrypted, "\0");
	return $decrypted;
}

//加密
function jsEncode($encodeData, $privateKey, $iv = "O2%=!ExPCuY6SKX(") {
	$encode = base64_encode(openssl_encrypt($encodeData, 'AES128', $privateKey, OPENSSL_RAW_DATA, $iv));
	$encode = rtrim($encode, "\0");
	return $encode;
}

// ------------------------------------------------------------------------
/**
 * 生成一段随机字符串
 * @param int $len 几位数
 */
function GetRandStr($len) {
	$chars = array(
		"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
		"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
		"w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
		"H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
		"S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
		"3", "4", "5", "6", "7", "8", "9",
	);
	$charsLen = count($chars) - 1;
	shuffle($chars);
	$output = "";
	for ($i = 0; $i < $len; $i++) {
		$output .= $chars[mt_rand(0, $charsLen)];
	}
	return $output;
}

//上传图片
function base64_upload($base64) {
	$base64_image = str_replace(' ', '+', $base64);
	//post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
	if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
		$image_name = rand(100, 999) . time() . '.png';
		$path = "/uploads/" . date("Ymd") . '/' . $image_name;

		$image_file = env('ROOT_PATH') . 'public/' . $path;
		$rel = check_path(dirname($image_file));
		//服务器文件存储路径
		if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))) {
			return $path;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
function check_path($path) {
	if (is_dir($path)) {
		return true;
	}
	if (mkdir($path, 0755, true)) {
		return true;
	}
	return false;
}
/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1  起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2  终点纬度
 * @param  Int     $unit       单位 1:米 2:公里
 * @param  Int     $decimal    精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2) {

	$EARTH_RADIUS = 6370.996; // 地球半径系数
	$PI = 3.1415926;

	$radLat1 = $latitude1 * $PI / 180.0;
	$radLat2 = $latitude2 * $PI / 180.0;

	$radLng1 = $longitude1 * $PI / 180.0;
	$radLng2 = $longitude2 * $PI / 180.0;

	$a = $radLat1 - $radLat2;
	$b = $radLng1 - $radLng2;

	$distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
	$distance = $distance * $EARTH_RADIUS * 1000;

	if ($unit == 2) {
		$distance = $distance / 1000;
	}
	return round($distance, $decimal);

}

// 节点图
function jiedian() {
	return [
		
		0 => [
			'key' => 0,
			'parent' => 0,
			'source' => '/static/admin/img/toutou.png',
		],
		1 => [
			'key' => 1,
			'parent' => 0,
			'source' => '/static/admin/img/toutou.png',
		],
		2 => [
			'key' => 2,
			'parent' => 0,
			'source' => '/static/admin/img/toutou.png',
		],
		3 => [
			'key' => 3,
			'parent' => 1,
			'source' => '/static/admin/img/toutou.png',
		],
		4 => [
			'key' => 4,
			'parent' => 1,
			'source' => '/static/admin/img/toutou.png',
		],
		5 => [
			'key' => 5,
			'parent' => 2,
			'source' => '/static/admin/img/toutou.png',
		],
		6 => [
			'key' => 6,
			'parent' => 2,
			'source' => '/static/admin/img/toutou.png',
		],
	];
}
//直推奖励
function direct_profit($id, $money,$jibie) {
	if(!cache('setting')['switch_direct']){
		return;
	}
	$info = model('User')->get($id);
	$calcu = cache('calcu')[$jibie - 1];
	if ($info['us_pid'] > 0) {
		$num = $money * $calcu['cal_direct'] / 100;
		one_to_two($info['us_pid'], $num, 9, 7);
	}
}

//业绩 剩余业绩 人数 对碰奖 升级精英 精英奖励
function yeji($id, $money,$type=1) {
	$info = model("User")->get($id);
	$brr = explode(',', $info['us_tree']);  /*所有上家*/
	array_push($brr, $info['id']);
	$arr = array_reverse($brr);
	$flag = 0;
	$count = count($arr);

	for ($i = 0; $i < $count - 2; $i++) {
		$id = $arr[$i+1];
		$child = model("User")->get($arr[$i]);
		$parent = model('User')->get($id);
		$calcu = cache('calcu')[$parent['us_jibie'] - 1];
		$num = 0; //金额
		$is_jy = 0;  //是否升级精英
		if ($child['us_qu']) { //子账号是右区
			$data = [
				'us_res_you'=>$parent['us_res_you']+$money,  //加右区业绩
				'id' => $id,
			];
			if($type==1){
				$data['us_per_you'] = $parent['us_per_you']+1;   //加右区人数
			}
			if ($parent['us_rel_you'] == 0) {  //小区是右 或两个区相同
				$nn = $parent['us_rel_zuo'] - $money;  
				if ($nn >= 0) {  //新小区右 
					$data['us_rel_zuo'] = $parent['us_rel_zuo']-$money; //左区剩余
					$num = $money;
					if($parent['us_is_center']==0 && $data['us_res_you']>cache('setting')['center_money']){
						$data['us_is_center']=1;
					}
					$is_jy = is_jy($data['us_res_you']);
				} else {		//新小区左
					$data['us_rel_zuo']  =  0;		  //左区剩余
					$data['us_rel_you']  =  abs($nn); //右区剩余 
					$num = $parent['us_rel_zuo'];
					if($parent['us_is_center']==0 && $parent['us_res_zuo']>cache('setting')['center_money']){
						$data['us_is_center']=1;
					}
				}
			}else{
				$data['us_rel_you'] = $parent['us_rel_you']+$money;
			}
		} else {  //子账号是左区
			$data = [
				'us_res_zuo'=>$parent['us_res_zuo']+$money,  //加左区业绩
				'id' => $id,
			];
			if($type==1){
				$data['us_per_zuo'] = $parent['us_per_zuo']+1;   //加左区人数
			}
			if ($parent['us_rel_zuo'] == 0) {  			//小区左 或左右相同

				$nn = $parent['us_rel_you'] - $money;
				if ($nn >= 0) {  											//新小区左
					$data['us_rel_you'] = $parent['us_rel_you']-$money;  
					$num = $money;
					if($parent['us_is_center']==0 && $data['us_res_zuo']>cache('setting')['center_money']){
						$data['us_is_center']=1;
					}
					$is_jy = is_jy($data['us_res_zuo']);
				} else {												    //新小区右
					$data['us_rel_you']  =  0;
					$data['us_rel_zuo']  =  $parent['us_rel_zuo']+abs($nn);
					$num = $parent['us_rel_you'];
					if($parent['us_is_center']==0 && $parent['us_res_you']>cache('setting')['center_money']){
						$data['us_is_center']=1;
					}
				}
			}else{
				$data['us_rel_zuo'] = $parent['us_rel_zuo']+$money;
			}
		}

		if($is_jy>$parent['us_is_jy']){ //升级精英

			$data['us_is_jy'] = $is_jy;
			$aa = $is_jy;
		}else{
			$aa = $parent['us_is_jy'];
		}
		
		model("User")->update($data);  //修改总业绩 剩余业绩 人数
		if ($num > 0) {   //没有添加对碰奖参数的num
 
			// 10%

			if($aa && $flag<10){ 		//精英奖励

				$jing = cache('jing')[$aa - 1]; 
				
				if($flag<$jing['jing_calcu']){

					$cal = $jing['jing_calcu']-$flag;
					
					$jy_num = $num * $cal / 100;

					if(cache('setting')['switch_jy']){
						one_to_two($id, $jy_num, 5, 5);
					}
					
					$flag = $jing['jing_calcu'];
				}
			}


			$sum1 = model('Wallet')->where('id',$id)->whereTime('wa_add_time', 'today')->sum('wa_num');
			$sum2 = model('Msc')->where('id',$id)->whereTime('msc_add_time', 'today')->sum('msc_num');
			/*---对碰奖励封顶*/
			$num = $num * $calcu['cal_distribution_profit'] / 100;
			if ($sum1 + $sum2 + $num <= $calcu['cal_distribution_top']) {
				$num = $num;
			} elseif ($num > $calcu['cal_distribution_top'] - $sum1 - $sum2) {
				$num = $calcu['cal_distribution_top'] - $sum1 - $sum2;
			} else {
				$num = 0;
			}
			if ($num > 0) {
				//对碰奖励 加开关
				if(cache('setting')['switch_peng']){
					one_to_two($id, $num, 1, 1);
				}
				//管理奖励 加开关
				if(cache('setting')['switch_manage'] && $id>0){
					manager($id, $num);
				}
			}
		}
	}
}
//管理奖励
function manager($id, $money) {
	$info = model("User")->get($id);
	$arr = explode(',', $info['us_path']);
	$brr = array_reverse($arr);
	if(key_exists(0,$brr)) {
		$num = $money * cache('setting')['manage_yi'] / 100;
		one_to_two($brr[0], $num, 4, 4);
		if (key_exists(1,$brr)) {
			if (direct_count($brr[1]) > 1) {
				$num1 = $money * cache('setting')['manage_er'] / 100;
				one_to_two($brr[1], $num1, 4, 4);
			}
			if (key_exists(2,$brr)) {
				if (direct_count($brr[2]) > 2) {
					$num2 = $money * cache('setting')['manage_san'] / 100;
					one_to_two($brr[2], $num2, 4, 4);
				}
			}
		}
	}
}

//见点奖励
function jiandian($id, $money) {
	$info = model("User")->get($id);
	$arr = explode(',', $info['us_tree']);
	if(!cache('setting')['switch_point']){
		return;
	}
	foreach ($arr as $k => $v) {
		if ($v > 0) {
			$inf = model('User')->get($v);
			$ceng = $info['us_tree_long'] - $inf['us_tree_long'];
			$calcu = cache('calcu')[$inf['us_jibie'] - 1];
			if ($ceng <= $calcu['cal_zel_point_ceng']) {
				$num = $money * $calcu['cal_zel_point_calcu'] / 1000;

				one_to_two($v, $num, 2, 2);
			}
		}
	}
}

/*-----------------分开发送金额*/
function one_to_two($id, $num, $type1, $type2) {

	$num1 = $num * (100 - cache('setting')['club_card_allot'] - cache('setting')['synthesize_manage_fee']) / 100;
	$num2 = $num * cache('setting')['club_card_allot'] / 100;
	if($num1>0){
		model('Msc')->tianjia($id, $num1, $type1);
	}
	if($num2>0){
		model('Wallet')->tianjia($id, $num2, $type2);
	}
	$inf = model('User')->get($id);
	if ($inf['us_is_tuo'] == 0 && $inf['us_jibie'] > 0) {
		$calcu = cache('calcu')[$inf['us_jibie'] - 1];
		$jine = $inf['us_tuo_num'] + $num;
		$nnnn = $calcu['cal_sta_top'] * $calcu['cal_money'] / 10;
		if ($jine > $nnnn) {
			model('User')->where('id', $id)->inc('us_is_tuo', 1)->exp('us_tuo_num', $nnnn)->update();
		} else {
			model("User")->where('id', $id)->setInc('us_tuo_num', $num);
		}
	}
}
//直推人数
function direct_count($id) {
	$count = model("User")->where('us_pid', $id)->count();
	return $count;
}
/**
 * 获取支付类型
 * @param  [int] $us_jibie [用户类型0,1,2,3,4]
 * @param  [int] $type     [0,1,2,3]
 * @return [int]           [支付类型]
 */
function get_type($us_jibie, $type) {
	switch (true) {

	case $us_jibie > $type: //我不能购买我等级一下的级别
		return [
			'code' => 0,
			'msg' => '您选中的级别不大于您的级别',
		];
		break;
	case $us_jibie == 0: //我不是
		$tt = $type + 5;
		break;
	case $us_jibie == 1: //我是普卡
		$tt = $type + 8;
		break;

	case $us_jibie == 2: //我是银卡
		$tt = $type + 10;
		break;
	case $us_jibie = 3; //我是金卡
		$tt = $type + 11;
	default:
		break;
	}
	return [
		'code' => 1,
		'type' => $tt,
	];
}

//购买类型
function buy_type($type) {

	switch ($type) {
	case $type == 1:
		$money = cache('setting')['vip'];
		break;
	case $type == 2:
		$money = cache('setting')['vip'];
		break;
	case $type == 3:
		$money = cache('setting')['vipplus'];
		break;
	case $type == 4:
		$money = cache('setting')['vipplus'];
		break;
	case $type == 5:
		$money = cache('calcu')[0]['cal_money']-cache('setting')['vip'];
		break;
	case $type == 6:
		$money = cache('calcu')[1]['cal_money']-cache('setting')['vip'];
		break;
	case $type == 7:
		$money = cache('calcu')[2]['cal_money']-cache('setting')['vip'];
		break;
	case $type == 8:
		$money = cache('calcu')[3]['cal_money']-cache('setting')['vip'];
		break;
	case $type == 9:
		$money = cache('calcu')[1]['cal_money'] - cache('calcu')[0]['cal_money'];
		break;
	case $type == 10:
		$money = cache('calcu')[2]['cal_money'] - cache('calcu')[0]['cal_money'];
		break;
	case $type == 11:
		$money = cache('calcu')[3]['cal_money'] - cache('calcu')[0]['cal_money'];
		break;
	case $type == 12:
		$money = cache('calcu')[2]['cal_money'] - cache('calcu')[1]['cal_money'];
		break;
	case $type == 13:
		$money = cache('calcu')[3]['cal_money'] - cache('calcu')[1]['cal_money'];
		break;
	case $type == 14:
		$money = cache('calcu')[3]['cal_money'] - cache('calcu')[2]['cal_money'];
		break;
	case $type == 15:
		$money = cache('setting')['vipplus'] - cache('setting')['vip'];
		break;
	default:
		# code...
		break;
	}
	return $money;
}

function buy_note($type) {
	$arr = [
		0 => '充值',
		1 => '开通vip',
		2 => '续费vip',
		3 => '开通vipplus',
		4 => '续费vipplus',
		5 => '普卡',
		6 => '银卡',
		7 => '金卡',
		8 => '钻卡',
		9 => '普卡升银卡',
		10 => '普卡升金卡',
		11 => '普卡升钻卡',
		12 => '银卡升金卡',
		13 => '银卡升钻卡',
		14 => '金卡升钻卡',
		15 => 'vip升vipplus',
	];
	return $arr[$type];
}

/**
 * 根据金额 升级精英
 * @param  [type]  $yeji [小区业绩]
 */
function is_jy($yeji){
	switch ($yeji) {
	case cache('jing')[0]['jing_money']<=$yeji && $yeji<cache('jing')[1]['jing_money']:
		$is_jy = 1;
		break;
	case cache('jing')[1]['jing_money']<=$yeji && $yeji<cache('jing')[2]['jing_money']:
		$is_jy = 2; 
		break;
	case cache('jing')[2]['jing_money']<=$yeji && $yeji<cache('jing')[3]['jing_money']:
		$is_jy = 3; 
		break;
	case cache('jing')[3]['jing_money']<=$yeji && $yeji<cache('jing')[4]['jing_money']:
		$is_jy = 4; 
		break;
	case cache('jing')[4]['jing_money']<=$yeji:
		$is_jy = 5; 
		break;
	default:
		$is_jy = 0;
		break;
	}
	return $is_jy;
}

/**
 * 返回新的会员卡编号
 */
function level_number(){
	// $wallet =db("wallet")->where('wa_type','in',[9,11])->order('id desc')->limit(2)->select();
	// if(count($wallet)<2){
	// 	$new = "ZAVIP-000001";
	// }else{
	// 	$str =  db("user")->where('id',$wallet[1]['us_id'])->value('us_level_number');
	// 	if($str){
	// 		$aa = substr($str,0,6);
	// 		$bb = "1".substr($str,-6);
	// 		$cc = $bb+1;
	// 		$dd = ltrim($cc,1);
	// 		$new = $aa.$dd;
	// 	}else{
	// 		$new = "ZAVIP-000001";
	// 	}
	// }
	$new = 'ZAVIP'.rand(100,999).date('ds').rand(100,999);
	return $new;
}

