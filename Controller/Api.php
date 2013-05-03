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

	protected function errorJson(Exception $error)
	{
		$this->assign('success', 0);
		$this->assign('error', $error->getMessage());
		$this->assign('error_msg', Lang::get($error->getMessage()));
		$this->json();
	}
}