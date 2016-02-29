<?php
/**
 * 任务管理
 * @author Chen<cxd032404@hotmail.com>
 * $Id: LotoController.php 15195 2014-07-23 07:18:26Z 334746 $
 */

class Xrace_RaceGroupController extends AbstractController
{
	/**运动类型列表:RaceGroupList
	 * 权限限制  ?ctl=xrace/sports&ac=sports.type
	 * @var string
	 */
	protected $sign = '?ctl=xrace/race.group';
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
			$RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			$RaceGroupArr = $this->oRace->getAllRaceGroupList($RaceCatalogId);
			$RaceGroupList = array();
			foreach($RaceGroupArr as $key => $value)
			{
				$RaceGroupList[$value['RaceCatalogId']]['RaceGroupList'][$key] = $value;
				$RaceGroupList[$value['RaceCatalogId']]['RaceGroupCount'] = isset($RaceGroupList[$value['RaceCatalogId']]['RaceGroupCount'])?$RaceGroupList[$value['RaceCatalogId']]['RaceGroupCount']+1:1;
				$RaceGroupList[$value['RaceCatalogId']]['RowCount'] = $RaceGroupList[$value['RaceCatalogId']]['RaceGroupCount']+1;
				if(isset($RaceCatalogArr[$value['RaceCatalogId']]))
				{
					$RaceGroupList[$value['RaceCatalogId']]['RaceCatalogName'] = isset($RaceGroupList[$value['RaceCatalogId']]['RaceCatalogName'])?$RaceGroupList[$value['RaceCatalogId']]['RaceCatalogName']:$RaceCatalogArr[$value['RaceCatalogId']]['RaceCatalogName'];
				}
				else
				{
					$RaceGroupList[$value['RaceCatalogId']]['RaceCatalogName'] = 	"未定义";
				}
				$RaceGroupList[$value['RaceCatalogId']]['RaceGroupList'][$key]['comment'] = json_decode($RaceGroupList[$value['RaceCatalogId']]['RaceGroupList'][$key]['comment']);
			}
			include $this->tpl('Xrace_Race_RaceGroupList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加任务填写配置页面
	public function raceGroupAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceGroupInsert");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			//模板渲染
			include $this->tpl('Xrace_Race_RaceGroupAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新任务
	public function raceGroupInsertAction()
	{
		//检查权限
		$bind=$this->request->from('RaceGroupName','RaceCatalogId');
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		if(trim($bind['RaceGroupName'])=="")
		{
			$response = array('errno' => 1);
		}
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		else
		{
			$res = $this->oRace->insertRaceGroup($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//修改任务信息页面
	public function raceGroupModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceGroupModify");
		if($PermissionCheck['return'])
		{
			//赛事分组ID
			$raceGroupId = trim($this->request->raceGroupId);
			//赛事列表
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			//赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($raceGroupId,'*');
			//数据解包
			$RaceGroupInfo['comment'] = json_decode($RaceGroupInfo['comment'],true);
			//获取执照审核方式列表
			//$RaceLisenceTypeList = $this->oRace->getRaceLicenseType();
			$RaceGroupInfo['comment']['LicenseList'] = array('1'=>array('LicenseType'=>"manager",'License'=>1),'2'=>array('LicenseType'=>"birthday",'License'=>array('equal'=>">=",'Date'=>"2015-01-01")),3=>array("LicenseType"=>"manager"));
			if(isset($RaceGroupInfo['comment']['LicenseList']))
			{
				$RaceLicenseListHtml = $this->oRace->ParthRaceLicenseListToHtml($RaceGroupInfo['comment']['LicenseList']);
				/*
				foreach ($RaceGroupInfo['comment']['LicenseList'] as $key => $LicenseInfo)
				{

					if(isset($RaceLisenceTypeList[$LicenseInfo['LicenseType']]))
					{
						$RaceGroupInfo['comment']['LicenseList'][$key]['LicenseTypeName'] = $RaceLisenceTypeList[$LicenseInfo['LicenseType']];
					}
					else
					{
						unset($RaceGroupInfo['comment']['LicenseList'][$key]);
					}
				}
				*/
			}
			//模板渲染
			include $this->tpl('Xrace_Race_RaceGroupModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新任务信息
	public function raceGroupUpdateAction()
	{
		$bind=$this->request->from('RaceGroupId','RaceGroupName','RaceCatalogId');
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		if(trim($bind['RaceGroupName'])=="")
		{
			$response = array('errno' => 1);
		}
		elseif(intval($bind['RaceGroupId'])=="")
		{
			$response = array('errno' => 2);
		}
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		else
		{
			$res = $this->oRace->updateRaceGroup($bind['RaceGroupId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//删除任务
	public function raceGroupDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceGroupDelete");
		if($PermissionCheck['return'])
		{
			$raceGroupId = intval($this->request->raceGroupId);
			$this->oRace->deleteRaceGroup($raceGroupId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
