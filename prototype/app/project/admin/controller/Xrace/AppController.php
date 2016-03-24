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
	//APP系统列表页面
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
	//APP系统列表页面
	public function appVersionListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//APP类型ID
			$AppTypeId = isset($this->request->AppTypeId)?intval($this->request->AppTypeId):0;
			//APP系统ID
			$AppOSId = isset($this->request->AppOSId)?intval($this->request->AppOSId):0;
			//获取APP系统列表
			$AppVersionList = $this->oApp->getAppVersionList($AppTypeId,$AppOSId);
			//获取APP类型列表
			$AppTypeList = $this->oApp->getAppTypeList("AppTypeId,AppTypeName");
			//获取APP系统列表
			$AppOSList = $this->oApp->getAppOSList("AppOSId,AppOSName");
			//循环APP版本列表
			foreach($AppVersionList as $AppVersionId => $AppVersionInfo)
			{
				//如果当前尚未获取过APP类型
				if(isset($AppTypeList[$AppVersionInfo['AppTypeId']]))
				{
					//获取APP类型名称
					$AppVersionList[$AppVersionId]['AppTypeName'] = $AppTypeList[$AppVersionInfo['AppTypeId']]['AppTypeName'];
				}
				else
				{
					$AppVersionList[$AppVersionId]['AppTypeName'] = "未定义";
				}
				//如果当前尚未获取过APP系统
				if(isset($AppOSList[$AppVersionInfo['AppOSId']]))
				{
					//获取APP系统名称
					$AppVersionList[$AppVersionId]['AppOSName'] = $AppOSList[$AppVersionInfo['AppOSId']]['AppOSName'];
				}
				else
				{
					$AppVersionList[$AppVersionId]['AppOSName'] = "未定义";
				}
				//解压缩下载路径
				$AppVersionList[$AppVersionId]['AppDownloadUrl'] = urldecode($AppVersionInfo['AppDownloadUrl']);
				//数据解包
				$AppVersionList[$AppVersionId]['comment'] = json_decode($AppVersionInfo['comment']);
			}
			//渲染模版
			include $this->tpl('Xrace_App_AppVersionList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加APP版本信息填写配置页面
	public function appVersionAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppVersionInsert");
		if($PermissionCheck['return'])
		{
			//获取APP类型列表
			$AppTypeList = $this->oApp->getAppTypeList("AppTypeId,AppTypeName");
			//获取APP系统列表
			$AppOSList = $this->oApp->getAppOSList("AppOSId,AppOSName");
			//渲染模版
			include $this->tpl('Xrace_App_AppVersionAdd');
		}
		else
		{
			$home = $this->sign."&ac=app.version.list";
			include $this->tpl('403');
		}
	}
	//添加新APP版本
	public function appVersionInsertAction()
	{
		//检查权限
		$bind = $this->request->from('AppVersion','AppTypeId','AppOSId','AppDownloadUrl');
		//获取APP类型列表
		$AppTypeList = $this->oApp->getAppTypeList("AppTypeId");
		//获取APP系统列表
		$AppOSList = $this->oApp->getAppOSList("AppOSId");
		//APP类型必须有效
		if(!isset($AppTypeList[$bind['AppTypeId']]))
		{
			$response = array('errno' => 1);
		}
		//APP系统必须有效
		elseif(!isset($AppOSList[$bind['AppOSId']]))
		{
			$response = array('errno' => 2);
		}
		//APP下载链接过短
		elseif(strlen(trim($bind['AppDownloadUrl']))<=10)
		{
			$response = array('errno' => 3);
		}
		else
		{
			//下载路径编码
			$bind['AppDownloadUrl'] = urlencode($bind['AppDownloadUrl']);
			//获取并过滤版本说明文字
			$bind['comment']['VersionComment'] = htmlspecialchars(trim($this->request->VersionComment));
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//添加APP版本
			$res = $this->oApp->insertAppVersion($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//修改APP版本信息页面
	public function appVersionModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppVersionModify");
		if($PermissionCheck['return'])
		{
			//获取APP类型列表
			$AppTypeList = $this->oApp->getAppTypeList("AppTypeId,AppTypeName");
			//获取APP系统列表
			$AppOSList = $this->oApp->getAppOSList("AppOSId,AppOSName");
			//APP版本ID
			$AppVersionId = intval($this->request->AppVersionId);
			//获取APP版本信息
			$AppVersionInfo = $this->oApp->getAppVersion($AppVersionId,'*');
			//解压缩下载路径
			$AppVersionInfo['AppDownloadUrl'] = urldecode($AppVersionInfo['AppDownloadUrl']);
			//数据解包
			$AppVersionInfo['comment'] = json_decode($AppVersionInfo['comment'],true);
			//渲染模版
			include $this->tpl('Xrace_App_AppVersionModify');
		}
		else
		{
			$home = $this->sign."&ac=app.os.list";
			include $this->tpl('403');
		}
	}
	//添加新APP版本
	public function appVersionUpdateAction()
	{
		//检查权限
		$bind = $this->request->from('AppVersionId','AppVersion','AppTypeId','AppOSId','AppDownloadUrl');
		//获取APP类型列表
		$AppTypeList = $this->oApp->getAppTypeList("AppTypeId");
		//获取APP系统列表
		$AppOSList = $this->oApp->getAppOSList("AppOSId");
		//APP类型必须有效
		if(!isset($AppTypeList[$bind['AppTypeId']]))
		{
			$response = array('errno' => 1);
		}
		//APP系统必须有效
		elseif(!isset($AppOSList[$bind['AppOSId']]))
		{
			$response = array('errno' => 2);
		}
		//APP下载链接过短
		elseif(strlen(trim($bind['AppDownloadUrl']))<=10)
		{
			$response = array('errno' => 3);
		}
		else
		{
			//获取APP版本信息
			$AppVersionInfo = $this->oApp->getAppVersion($bind['AppVersionId'],'comment');
			//下载路径编码
			$bind['AppDownloadUrl'] = urlencode($bind['AppDownloadUrl']);
			//数据解包
			$bind['comment'] = json_decode($AppVersionInfo['comment'],true);
			//获取并过滤版本说明文字
			$bind['comment']['VersionComment'] = htmlspecialchars(trim($this->request->VersionComment));
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//添加APP版本
			$res = $this->oApp->updateAppVersion($bind['AppVersionId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//删除APP
	public function appVersionDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("AppVersionDelete");
		if($PermissionCheck['return'])
		{
			//APP版本ID
			$AppVersionId = trim($this->request->AppVersionId);
			//删除APP版本
			$this->oApp->deleteAppVersion($AppVersionId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign."&ac=app.version.list";
			include $this->tpl('403');
		}
	}
}
