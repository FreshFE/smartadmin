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

	protected function successJson($output = null, $is_array = false, $merge = array())
	{
		// 成功
		$this->assign('success', 1);

		// 是否存在输出
		if(!is_null($output))
		{
			// 输出是否为复数
			if($is_array)
			{
				$this->assign('datas', $output);
			}
			else {
				$this->assign('data', $output);
			}
		}

		// 合并额外设置
		$this->vars = array_merge($this->vars, $merge);

		$this->json();
	}
}