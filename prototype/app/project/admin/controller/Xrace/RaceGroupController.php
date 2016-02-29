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
			//如果有填写审核类型列表
			if(isset($RaceGroupInfo['comment']['LicenseList']))
			{
				//将执照判断条件转化为HTML
				$RaceLicenseListHtml = $this->oRace->ParthRaceLicenseListToHtml($RaceGroupInfo['comment']['LicenseList']);
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
		$bind=$this->request->from('RaceGroupId','RaceGroupName','RaceCatalogId','LicenseList');
		if(is_array($bind['LicenseList']))
		{
			//获取条件类型列表
			$RaceLisenceTypeList = $this->oRace->getRaceLicenseType();
			//循环条件列表
			foreach($bind['LicenseList'] as $k => $v)
			{
				//如果条件类型不符合
				if(!isset($RaceLisenceTypeList[$v['LicenseType']]))
				{
					//删除数据
					unset($bind['LicenseList'][$k]);
				}
				//如果设置为不需要管理员赋予
				elseif(($v['LicenseType']=="manager") && ($v['License'] == "0"))
				{
					//删除数据
					unset($bind['LicenseList'][$k]);
				}
			}
		}
		else
		{
			//置为空数组
			$bind['LicenseList'] = array();
		}
		//获取赛事列表
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		//分组名称不能为空
		if(trim($bind['RaceGroupName'])=="")
		{
			$response = array('errno' => 1);
		}
		//分组ID必须填写
		elseif(intval($bind['RaceGroupId'])==0)
		{
			$response = array('errno' => 2);
		}
		//分组必须属于某个已配置的赛事
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		else
		{
			//赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($bind['RaceGroupId'],'*');
			//数据解包
			$bind['comment'] = json_decode($RaceGroupInfo['comment'],true);
			//移动条件列表到comment数组下
			$bind['comment']['LicenseList'] = $bind['LicenseList'];
			//删除原有数组
			unset($bind['LicenseList']);
			//数据打包
			$bind['comment'] = json_encode($bind['comment']);
			//更新数据
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
	//修改任务信息页面
	public function groupLicenseAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceGroupModify");
		if($PermissionCheck['return'])
		{
			//赛事分组ID
			$raceGroupId = trim($this->request->raceGroupId);
			//赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($raceGroupId,'*');
			//数据解包
			$RaceGroupInfo['comment'] = json_decode($RaceGroupInfo['comment'],true);
			//获取条件类型列表
			$RaceLisenceTypeList = $this->oRace->getRaceLicenseType();
			//模板渲染
			include $this->tpl('Xrace_Race_GroupLicenseAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	public function getLicenseConditionAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceGroupModify");
		if($PermissionCheck['return'])
		{
			//赛事分组ID
			$LicenseType = trim($this->request->LicenseType);
			//获取条件类型列表
			$RaceLisenceTypeList = $this->oRace->getRaceLicenseType();
			if(isset($RaceLisenceTypeList[$LicenseType]))
			{
				echo  $this->oRace->$LicenseType("",array(),$edit = 1);
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}

}
