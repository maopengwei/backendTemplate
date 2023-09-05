<?php
namespace app\admin\controller;

use Cache;

/**
 * @todo 配置信息管理
 */
class Setting extends Common {
	
	//
	public function index() {
		if (is_post()) {
			$data = input('post.');
			model('Config')->xiugai($data);
			$this->success('修改成功');
		}
		
		return $this->fetch();
	}
	//系统参数
	public function system() {

		if(is_post()){
			$data = input('post.');
			if($data['type']==1){
				$rel = db('calcu')->where('id',$data['i'])->setfield($data['key'],$data['val']);
				
			}else{
				$rel = db('jing')->where('id',$data['i'])->setfield($data['key'],$data['val']);
			}
			if($rel){
				Cache::clear();
			}
		}

		$this->assign(array(
			'list'=> cache('calcu'),
			'jing'=> cache('jing'),
		));
		return $this->fetch();
	}
	//修改经销商级别优惠内容
	public function edit() {
		if (is_post()) {
			$data = input('post.');
			$rel = model('Calcu')->xiugai($data);
			return $rel;
		}

		$k = input('id') - 1;
		dump($k);
		$this->assign(array(
			'k' => $k,
		));
		return $this->fetch();
	}
	//
	public function label() {
		if (is_post()) {
			$data = input('post.');
			$datb = $data;
			$datc = array_pop($data);
			if ($datc['name'] == "") {
				$datd = $data;
			} else {
				$datd = $datb;
			}
			$datd = serialize($datd);
			$rel = model("Config")->where('key', 'label')->update(['value' => $datd]);
			Cache::clear();
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您并没有做出修改');
			}
		}
		$label = cache('setting')['label'];
		$array = [
			'name' => "",
			'pic' => "",
		];
		// dump(cache('setting')['label']);
		$list = unserialize(cache('setting')['label']);
		// halt($list);
		array_push($list, $array);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	public function shuffling() {
		if (is_post()) {
			$data = input('post.');
			$datb = $data;
			$datc = array_pop($data);
			// dump($data);
			// dump($datb);
			// halt($datc);
			if ($datc == "") {
				$datd = $data;
			} else {
				$datd = $datb;
			}
			$datd = implode(',', $datd);
			$rel = model("Config")->where('key', 'shuffling_figure')->update(['value' => $datd]);
			Cache::clear();
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您并没有做出修改');
			}
		}

		$shuffling = cache('setting')['shuffling_figure'];
		$array = explode(',', $shuffling);
		array_push($array, '');
		$this->assign(array(
			'array' => $array,
		));
		return $this->fetch();
	}

	//项目文档
	public function api() {
		return $this->fetch();
	}
	public function document() {
		$path = env('ROUTE_PATH');
		$swagger = \Swagger\scan($path);
		header('Content-Type: application/json');
		echo $swagger;
	}
}
