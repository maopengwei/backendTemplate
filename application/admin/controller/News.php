<?php
namespace app\admin\controller;

/**
 * 消息控制器
 */
class News extends Common {
	protected $order;
	public function __construct() {
		parent::__construct();
		$this->order = 'id desc';
	}
	//消息列表
	public function index() {
		$map = [];
		$list = model('Message')->where($map)->order($this->order)->paginate(10);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	public function add() {
		if (is_Post()) {
			$request = input('post.');
			if ($request['title'] == "" || $request['content'] == "") {
				$this->error('标题和内容不能为空');
			}
			$data = array(
				'add_time' => date('Y-m-d H:i:s'),
				'title' => $request['title'],
				'content' => $request['content'],
			);
			$rel = model('Message')->save($data);
			if ($rel) {
				$this->success('添加成功');
			}
		}
		return $this->fetch();
	}
	public function edit() {
		$id = input('id');
		if (is_post()) {
			$data = array(
				'title' => input('title'),
				'content' => input('content'),
			);
			$rel = model('Message')->save($data, ['id' => $id]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('修改失败');
			}
		}
		$info = model("Message")->get($id);
		$this->assign('info', $info);
		return $this->fetch();
	}
	public function see() {
		$info = model("Message")->get(input('id'));
		$this->assign('info', $info);
		return $this->fetch();
	}
	public function del() {
		$id = input('post.id');
		if (!$id) {
			$this->error('非法操作');
		} else {
			$info = model('Message')->get($id);
		}
		if (!$info) {
			$this->error('非法操作');
		} else {
			$rel = model('Message')->destroy($id);
		}
		if ($rel) {
			$this->success('删除成功');
		}
	}
}
