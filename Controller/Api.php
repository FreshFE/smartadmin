<?php namespace Smartadmin\Controller;

use Think\Controller as Controller;
use Think\Debug as Debug;
use Think\Exception as Exception;
use Think\Lang as Lang;

/**
 * Api Controller
 * 后台开发CURD及页面控制器
 */
class Api extends Controller {

	public function _empty()
	{
		$method = array('get', 'post', 'put', 'delete', 'head');
		$current = strtolower($_SERVER['REQUEST_METHOD']);

		if(in_array($current, $method))
		{
			$class = $current . '_' . ACTION_NAME;
			$this->$class();
		}
		else {
			Debug::output(new Exception("不存在'$method'方法"));
		}
	}

	protected function errorJson(Exception $error)
	{
		$this->assign('success', false);
		$this->assign('error', $error->getMessage());
		$this->assign('error_msg', Lang::get($error->getMessage()));
		$this->json();
	}
}