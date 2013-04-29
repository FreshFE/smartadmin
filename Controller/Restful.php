<?php namespace Smartadmin\Controller;

use Think\Controller as Controller;
use Think\Debug as Debug;
use Think\Exception as Exception;
use Think\Lang as Lang;

/**
 * Restful Controller
 */
class Restful extends Controller
{
	/**
	 * 允许检测的方法
	 *
	 * @var array
	 */
	protected $_method = array('get', 'post', 'put', 'delete', 'head');

	/**
	 * 空方法执行请求方法属性的检查
	 *
	 * @return void
	 */
	public function _empty()
	{
		try
		{
			$method = $this->_method;

			// 当前http请求方法
			$current = strtolower($_SERVER['REQUEST_METHOD']);

			if(in_array($current, $method))
			{
				$class = $current . '_' . ACTION_NAME;

				// 是否存在this/get_login
				if(method_exists($this, $class))
				{
					$this->$class();
				}
				// 是否存在this/get
				else if(method_exists($this, $current))
				{
					$this->$current();
				}
				// 不存在输出
				else {
					throw new Exception("不存在指定的控制器方法");
				}
			}
			// 方法错误输出
			else {
				throw new Exception("不存在'$method'方法");
			}

		}
		catch (Exception $e) {
			Debug::output($e);
		}
	}
}