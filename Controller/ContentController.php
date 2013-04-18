<?php namespace Smartadmin\Controller;

use Think\Url as Url;
use Think\Request as Request;
use Think\Response as Response;
use Think\Redirect as Redirect;
use Think\Import as Import;
use Think\Controller as Controller;

use Think\Library\Category as Category;
use Think\Library\Upload\Upload as Upload;

/**
 * ContentExtendAction
 * 后台开发CURD及页面控制器
 */
class ContentController extends Controller {

	// Model Config
	/**
	 * 主模型
	 * 默认由构造函数通过D或M方法声明
	 *
	 * var object
	 */
	protected $model;

	/**
	 * 主模型名称
	 * 通过D()或M()方法声明
	 * 在构造函数内使用，require
	 *
	 * var string
	 */
	protected $model_name;

	// Thumb Config
	/**
	 * 上传图片匹配的尺寸名称
	 *
	 * var string
	 */
	protected $image_thumb_name;

	/**
	 * 缩略图匹配的尺寸名称
	 *
	 * var string
	 */
	protected $cover_thumb_name;

	// Category Config
	/**
	 * 在category中的分类编号
	 *
	 * var int
	 */
	protected $category_id;

	/**
	 * 分类Model的名称
	 * 默认为'category'表
	 *
	 * var string
	 */
	protected $category_model = 'Category';

	/**
	 * 每页显示多少行
	 *
	 * var int
	 */
	protected $list_rows = 20;

	/**
	 * 主键名称
	 *
	 * var string
	 */
	protected $pk_name = 'id';

	/**
	 * 获得的主键的值
	 *
	 * var int
	 */
	protected $pk_id;

	/**
	 * 查询条件
	 *
	 * var array
	 */
	protected $condition = array();

	/**
	 * 构造函数
	 * 构建常用参数内容
	 *
	 * @return void
	 */
	public function __construct() {

		parent::__construct();

		// 创建主模型
		$this->model = D($this->model_name);

		// 保存id
		$this->pk_id = $_GET[$this->pk_name];

		// 输出缩略图名称
		if($this->cover_thumb_name) {
			$this->assign('coverThumbSize', $this->cover_thumb_name);
		}
	}

	/**
	 * Read List Action
	 * 用于显示主模型列表
	 * 具有页码筛选功能
	 *
	 * @return void
	 */
	public function index() {

		// 获得页码
		$page = $_GET['page'] ? $_GET['page'] : 1;

		// cid排序
		if($_GET['cid']) $this->condition['cid'] = $_GET['cid'];

		// 获得内容并输出页码数组
		$datas = $this->model->where($this->condition)->page($page, $this->list_rows)->select();
		$this->assign('datas', $datas);

		$pager = $this->model->where($this->condition)->pager($page, $this->list_rows);
		$this->assign('pager', $pager);

		$this->display();
	}

	/**
	 * Create Action
	 * 用于创建新的数据行
	 *
	 * post请求下执行创建工作
	 * get请求下执行创建页面
	 *
	 * @return void
	 */
	public function create() {

		// Post提交后的执行
		if(Request::is('post')) {

			$data = $this->model->create();
			$this->model->add($data);
			Redirect::success('创建成功', Url::make('index'));

		// 未执行Post时的默认行为
		} else {

			$this->assign('category', $this->category());
			$this->display('edit');
		}
	}

	/**
	 * Create Action by default data
	 * 通过默认方式创建数据行
	 * 创建完成后重定向至编辑操作
	 *
	 * @return void
	 */
	public function add() {

		$id = $this->model->createDefault(array('cid' => $this->category_id));
		Redirect::success('创建成功，请编辑', Url::make('edit', array('id' => $id)));
	}

	/**
	 * Update Action
	 * 编辑
	 *
	 * @return void
	 */
	public function edit() {

		if(Request::is('post')) {

			$data = $this->model->create();
			$this->model->save($data);

			Redirect::success('编辑成功', Url::make('index'));

		}

		else if($this->pk_id) {

			$data = $this->model->find($this->pk_id);
			$this->assign('data', $data);
			$this->assign('category', $this->category());

			$this->display();
		}
	}

	/**
	 * Read Action
	 * 读取主模型数据
	 *
	 * @return void
	 */
	public function detail() {

		if($this->pk_id) {

			$data = $this->model->find($this->pk_id);
			$this->assign('data', $data);
			$this->display();
		}
	}

	/**
	 * Update model hidden
	 * 编辑model的hidden字段
	 *
	 * @return void
	 */
	public function enable() {

		if($this->pk_id) {

			$this->model->find($this->pk_id);
			$this->model->hidden = !$this->model->hidden;
			$this->model->save();

			Redirect::success('状态发布成功');
		}
	}

	/**
	 * Delete
	 * 删除
	 *
	 * @return void
	 */
	public function delete() {

		if($this->pk_id) {

			$this->model->delete($this->pk_id);
			Redirect::success('删除成功');
		}
	}

	/**
	 * Read category for sidebar widget
	 * 侧边栏调用小组件
	 *
	 * @return void
	 */
	public function sidebar() {

		// 侧边分栏
		$this->assign('category', $this->category());
		return $this->fetch('sidebar');
	}

	/**
	 * 文章内图片上传接口
	 *
	 * @return void
	 */
	public function image() {

		$info = Upload::image($_FILES['uploadify_file'], $image_thumb_name);
		Response::json($info);
		// $this->ajaxReturn($info);
	}

	/**
	 * Upload cover image and update 'coverpath'
	 * 封面上传并写入主模型
	 *
	 * @return void
	 */
	public function cover() {

		// 上传图片
		$info = Upload::image($_FILES['uploadify_file'], $this->cover_thumb_name);

		// 建立数据表
		$this->model->where(array('id' => $_POST['id']))->save(array('coverpath' => $info['name']));

		// 输出JSON
		Response::json($info);
		// $this->ajaxReturn($info);
	}

	/**
	 * Read category
	 *
	 * @return void
	 */
	protected function category() {

		$Category = new Category($this->category_model);
		return $Category->getList('', $this->category_id, 'priority ASC');
	}

}