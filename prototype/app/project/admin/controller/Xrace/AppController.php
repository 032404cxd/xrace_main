<?php
/**
 * 任务管理
 * @author Chen<cxd032404@hotmail.com>
 * $Id: LotoController.php 15195 2014-07-23 07:18:26Z 334746 $
 */

class Xrace_AppController extends AbstractController
{
	/**APP类型列表:AppTypeList
	 * 权限限制  ?ctl=xrace/app&ac=app.type.list
	 * @var string
	 */
	protected $sign = '?ctl=xrace/app';
	/**
	 * game对象
	 * @var object
	 */
	protected $oApp;

	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oApp = new Xrace_App();

	}
	//任务配置列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//获取APP类型列表
			$AppTypeList = $this->oApp->getAppTypeList();
			//渲染模版
			include $this->tpl('Xrace_App_AppTypeList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加APP类型填写配置页面
	public function appTypeAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppTypeInsert");
		if($PermissionCheck['return'])
		{
			//渲染模版
			include $this->tpl('Xrace_App_AppTypeAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加新APP类型
	public function appTypeInsertAction()
	{
		//检查权限
		$bind=$this->request->from('AppTypeName');
		//APP类型名称不能为空
		if(trim($bind['AppTypeName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//添加APP类型
			$res = $this->oApp->insertAppType($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//修改APP类型信息页面
	public function appTypeModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppTypeModify");
		if($PermissionCheck['return'])
		{
			//APP类型ID
			$AppTypeId = intval($this->request->AppTypeId);
			//获取APP类型信息
			$AppTypeInfo = $this->oApp->getAppType($AppTypeId,'*');
			//渲染模版
			include $this->tpl('Xrace_App_AppTypeModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}

	//更新APP类型信息
	public function appTypeUpdateAction()
	{
		//获取页面参数
		$bind=$this->request->from('AppTypeId','AppTypeName');
		//APP类型名称不能为空
		if(trim($bind['AppTypeName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//更新APP类型
			$res = $this->oApp->updateAppType($bind['AppTypeId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//删除APP类型
	public function appTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppTypeDelete");
		if($PermissionCheck['return'])
		{
			//APP类型ID
			$AppTypeId = trim($this->request->AppTypeId);
			//删除APP类型
			$this->oApp->deleteAppType($AppTypeId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//任务配置列表页面
	public function appOsListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//获取APP系统列表
			$AppOSList = $this->oApp->getAppOsList();
			//渲染模版
			include $this->tpl('Xrace_App_AppOSList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//修改APP系统信息页面
	public function appOsModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppOSModify");
		if($PermissionCheck['return'])
		{
			//APP类型ID
			$AppOSId = intval($this->request->AppOSId);
			//获取APP类型信息
			$AppOSInfo = $this->oApp->getAppOS($AppOSId,'*');
			//渲染模版
			include $this->tpl('Xrace_App_AppOSModify');
		}
		else
		{
			$home = $this->sign."&ac=app.os.list";
			include $this->tpl('403');
		}
	}

	//更新APP系统信息
	public function appOsUpdateAction()
	{
		//获取页面参数
		$bind=$this->request->from('AppOSId','AppOSName');
		//APP类型名称不能为空
		if(trim($bind['AppOSName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//更新APP类型
			$res = $this->oApp->updateAppOS($bind['AppOSId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//添加APP系统填写配置页面
	public function appOsAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppOSInsert");
		if($PermissionCheck['return'])
		{
			//渲染模版
			include $this->tpl('Xrace_App_AppOSAdd');
		}
		else
		{
			$home = $this->sign."&ac=app.os.list";
			include $this->tpl('403');
		}
	}
	//添加新APP系统
	public function appOsInsertAction()
	{
		//检查权限
		$bind=$this->request->from('AppOSName');
		//APP类型名称不能为空
		if(trim($bind['AppOSName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//添加APP类型
			$res = $this->oApp->insertAppOS($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//删除APP
	public function appOsDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppOSDelete");
		if($PermissionCheck['return'])
		{
			//APP类型ID
			$AppOSId = trim($this->request->AppOSId);
			//删除APP类型
			$this->oApp->deleteAppOS($AppOSId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign."&ac=app.os.list";
			include $this->tpl('403');
		}
	}
}
