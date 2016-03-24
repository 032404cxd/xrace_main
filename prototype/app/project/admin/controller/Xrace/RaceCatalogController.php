<?php
/**
 * 任务管理
 * @author Chen<cxd032404@hotmail.com>
 * $Id: LotoController.php 15195 2014-07-23 07:18:26Z 334746 $
 */

class Xrace_RaceCatalogController extends AbstractController
{
	/**运动类型列表:RaceCatalogList
	 * 权限限制  ?ctl=xrace/sports&ac=sports.type
	 * @var string
	 */
	protected $sign = '?ctl=xrace/race.catalog';
	/**
	 * race对象
	 * @var object
	 */
	protected $oRace;

	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oRace = new Xrace_Race();

	}
	//任务配置列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//当前站点根域名
			$RootUrl = "http://".$_SERVER['HTTP_HOST'];
			//获取赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//渲染模板
			include $this->tpl('Xrace_Race_RaceCatalogList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加任务填写配置页面
	public function raceCatalogAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceCatalogInsert");
		if($PermissionCheck['return'])
		{
			//渲染模板
			include $this->tpl('Xrace_Race_RaceCatalogAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新任务
	public function raceCatalogInsertAction()
	{
		//检查权限
		$bind=$this->request->from('RaceCatalogId','RaceCatalogName');
		//赛事名称不能为空
		if(trim($bind['RaceCatalogName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//文件上传
			$oUpload = new Base_Upload('RaceCatalogIcon');
			$upload = $oUpload->upload('RaceCatalogIcon');
			$res[1] = $upload->resultArr;
			$path = $res[1][1];

			//如果正确上传，就保存文件路径
			if(strlen($path['path'])>2)
			{
				$bind['comment']['RaceCatalogIcon'] = $path['path'];
				$bind['comment']['RaceCatalogIcon_root'] = $path['path_root'];
			}
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//添加赛事记录
			$res = $this->oRace->insertRaceCatalog($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//修改任务信息页面
	public function raceCatalogModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceCatalogModify");
		if($PermissionCheck['return'])
		{
			//赛事ID
			$RaceCatalogId = trim($this->request->RaceCatalogId);
			//获取赛事信息
			$RaceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId,'*');
			//渲染模板
			include $this->tpl('Xrace_Race_RaceCatalogModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新任务信息
	public function raceCatalogUpdateAction()
	{
		//获取页面参数
		$bind=$this->request->from('RaceCatalogId','RaceCatalogName');
		//赛事名称不能为空
		if(trim($bind['RaceCatalogName'])=="")
		{
			$response = array('errno' => 1);
		}
		//赛事ID必须为正数
		elseif(intval($bind['RaceCatalogId'])<=0)
		{
			$response = array('errno' => 2);
		}
		else
		{
			//获取原有数据
			$RaceCatalogInfo = $this->oRace->getRaceCatalog($bind['RaceCatalogId'],'*');
			//数据解包
			$bind['comment'] = json_decode($RaceCatalogInfo['comment'],true);
			//文件上传
			$oUpload = new Base_Upload('RaceCatalogIcon');
			$upload = $oUpload->upload('RaceCatalogIcon');
			$res[1] = $upload->resultArr;
			$path = isset( $res[1][1] ) ? $res[1][1]:array('path'=>"");
			//如果正确上传，就保存文件路径
			if(strlen($path['path'])>2)
			{
				$bind['comment']['RaceCatalogIcon'] = $path['path'];
				$bind['comment']['RaceCatalogIcon_root'] = $path['path_root'];
			}
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//修改赛事记录
			$res = $this->oRace->updateRaceCatalog($bind['RaceCatalogId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}

	//删除任务
	public function raceCatalogDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceCatalogDelete");
		if($PermissionCheck['return'])
		{
			//赛事ID
			$RaceCatalogId = intval($this->request->RaceCatalogId);
			//删除赛事记录
			$this->oRace->deleteRaceCatalog($RaceCatalogId);
			//返回原有页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
