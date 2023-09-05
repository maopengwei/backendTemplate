<?php
namespace app\admin\controller;
use app\common\controller\Base;


/**
 * 测试控制器
 */
class Cron extends Base {

	public function ceshi() {
		
		$list = model("Admin")->select();
		var_dump($list);
	}

	//拓展奖励
	public function expand() {
		if(!cache('setting')['switch_static']){
			return;
		}
		$num = 0;
		$num += model("PayRecord")->where('pay_lei', 'in', [5, 6, 7, 8, 9, 10, 11, 12, 13, 14])->whereTime('pay_add_time', 'today')->sum('pay_num');
		$count = model('PayRecord')->where('pay_lei','in',[5,6,7,8])->whereTime('pay_add_time','today')->count();
		/*$num 总金额*/ 
	
		$nnn = cache('setting')['vip']*$count;
		$num = $nnn + $num;
		// dump($num);

		if ($num > 0) {
			//总金额$money
			$money = $num * cache('setting')['static_money'] / 100;

			$list1 = model('User')->where('us_is_jing', 2)->where('us_wei_jibie', 1)->where('us_is_tuo', 0)->select();
			$list2 = model('User')->where('us_is_jing', 2)->where('us_wei_jibie', 2)->where('us_is_tuo', 0)->select();
			$list3 = model('User')->where('us_is_jing', 2)->where('us_wei_jibie', 3)->where('us_is_tuo', 0)->select();
			$list4 = model('User')->where('us_is_jing', 2)->where('us_wei_jibie', 4)->where('us_is_tuo', 0)->select();
			$count1 = count($list1);
			$count2 = count($list2);
			$count3 = count($list3);
			$count4 = count($list4);
			// dump($count1);
			// dump($count2);
			// dump($count3);
			// dump($count4);
			if($count1>0){
				$count1 = count($list1);
				$num1 = $money * cache('calcu')[0]['cal_sta_expand'] / 100 / $count1;
				// dump($num1);
				if($num1>0){
					foreach ($list1 as $v1) {
						one_to_two($v1['id'], $num1, 3, 3);
					}
				}
			}
			if($count2>0){
				$num2 = $money * cache('calcu')[1]['cal_sta_expand'] / 100 / $count2;
				// dump($num2);
				if($num2>0){
					foreach ($list2 as $v2) {
						one_to_two($v2['id'], $num2, 3, 3);
					}
				}
			}
			if($count3>0){
				$num3 = $money * cache('calcu')[2]['cal_sta_expand'] / 100 / $count3;
				// dump($num3);
				if($num3>0){
					foreach ($list3 as $v3) {
						one_to_two($v3['id'], $num3, 3, 3);
					}
				}
			}
			if($count4>0){
				$num4 = $money * cache('calcu')[3]['cal_sta_expand'] / 100 / $count4;
				// dump($num4);
				if($num4>0){
					foreach ($list4 as $v4) {
						one_to_two($v4['id'], $num4, 3, 3);
					}
				}
			}
			
			// $num2 = $money * cache('calcu')[1]['cal_sta_expand'] / 100 / count($list2);
			// $num3 = $money * cache('calcu')[2]['cal_sta_expand'] / 100 / count($list3);
			// $num4 = $money * cache('calcu')[3]['cal_sta_expand'] / 100 / count($list4);

			
			// foreach ($list2 as $v2) {
			// 	one_to_two($v2['id'], $num2, 3, 3);
			// }
			// foreach ($list3 as $v3) {
			// 	one_to_two($v3['id'], $num3, 3, 3);
			// }
			// foreach ($list4 as $v4) {
			// 	one_to_two($v4['id'], $num4, 3, 3);
			// }
		}
		halt(123);

	}



	//
// function ceshi(){
// 	// $id   $money,
// 	// $info
// 	//$arr   
// 	// $arr = explode('');
// 	// 
// 	rsort($arr)
// 	foreach ($arr as $k => $v) {
// 		if($k>0){
// 			if($k=1){
				
				
// 			}else{
// 				$k_wei=$info['wei']%pow(2,$k); 
// 				if($k_wei == 1){
// 					$x_wei = pow(2,$k)-1;
// 				}elseif($k_wei == pow(2,$k)-1){
// 					$x_wei = 1;
// 				}else{
// 					continue;
// 				}
// 			}
			

// 			$wei = $info['wei']+$x_wei-$k_wei;

// 			if($wei){

// 			}
			
// 		}
// 	}
// }
// 
	// public function cc(){
	// 	$id = 'pid' ;
	// 	$data = input('post.');

	// 	$parent = model('user')->get($data['pid']);
	// 	// unserialize();
	// 	// serialize();
	// 	$arr = unserialize();
	// 	$data = [
	// 		'us_tree' = $parent['us_tree'].','.$data['pid'].
	// 	]
	// }
// function ceshi(){
// 	// $id   $money,
// 	// $info  我的信息
// 	//$arr   
// 	// $arr = explode('');
// 	// 
// 	rsort($arr)
// 	foreach ($arr as $k => $v) {
		
// 		$k_wei=$info['wei']%pow(2,$k);
// 		if($k_wei==0){
// 			$k_wei = pow(2,$k);
// 		}
// 		if($k_wei <= pow(2,$k-1)){
// 			$x_wei = $k_wei+pow(2,$k-1); 
// 		}elseif{
// 			$x_wei = $k_wei-pow(2,$k-1); 
// 		}
// 		$ceng = $info['ceng'];
// 	}
// function ceshi(){
	// $id   $money,
	// $info  我的信息
	//$arr   
	// $arr = explode('');
	// 
	// ksort($arr)
	// foreach ($arr as $k => $v) {
		

	// 	$zhong = pow(2,$k-1);

	// 	$zuo = model('')->where('ceng',)->where('wei','<=',$zhong)->sum();
	// 	$you = model('')->where('ceng',)->where('wei','>',$zhong)->sum();


	// 	$k_wei=$info['wei']%pow(2,$k);
		// $k = 5
		// $info['wei'] == 4;
		// $k_wei = 4
		// $zhongzhong = 32 						
		// $zhong_zhong = $wei-($k_wei-$zhong); 
	// 	if($k_wei <= $zhong){
	// 		if($zuo<$you){
	// 			$cha = $you-$zuo;
	// 			if($cha>$money){
	// 				$num = $money;
	// 			}else{
	// 				$num = $cha;
	// 			}
	// 		}				
			
	// 	}else{
	// 		if($you<$zuo){
	// 			$cha = $zuo-$you;
	// 			if($cha>$money){
	// 				$num = $money;
	// 			}else{
	// 				$num = $cha;
	// 			}
	// 		}	 
	// 	}
	// }
									
}


