<?php namespace Smartadmin\Model;

use Think\Model as Model;

class Api extends Model {

	public function parseMaps($data, $level = 1)
	{
		if($level === 1)
		{
			return $this->parseFieldsMap($data);
		}
		else if($level === 2)
		{
			foreach ($data as $key => &$value) {
				$value = $this->parseFieldsMap($value);
			}
			return $data;
		}
	}

	/**
	 * 解析查询条件
	 *
	 * @param array $array 处理内容数组
	 * @param array &$conditon 输出的数组
	 * @param string $method 查询query的方法，get, post or both
	 */
	public function createCondition(array $array, &$condition, $method = 'both')
	{
		// 计算请求的方法
		if(strtolower($method) == 'both')
		{
			$query = array_merge($_GET, $_POST);
		}
		else if(strtolower($method) == 'post') {
			$query = $_POST;
		}
		else {
			$query = $_GET;
		}

		// 遍历赋值
		foreach ($array as $key => $value)
		{
			// 不存在别名设置, array('', '');
			if(is_numeric($key))
			{
				// 检查预定义项目，是否存在
				$map = $this->_map;

				// 存在，则使用map中对应的名称
				if(isset($map[$value]))
				{
					$alias = $value;
					$doing = $map[$value];
				}
				// 不存在，则使用当前默认
				else {
					$alias = $value;
					$doing = $value;
				}
			}
			// 存在别名设置, array('' => '', '' => '');
			else {
				$alias = $key;
				$doing = $value;
			}

			// 检查query内是否存在
			if(isset($query[$alias]))
			{
				$condition[$doing] = $query[$alias];
			}
		}
	}
}