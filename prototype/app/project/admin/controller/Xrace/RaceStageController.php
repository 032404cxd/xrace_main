<?php
class Xrace_RaceStageController extends AbstractController
{
	/**赛事分站:
	 * 权限限制  ?ctl=xrace/sports&ac=sports.stage
	 * @var string
	 */
	protected $sign = '?ctl=xrace/race.stage';
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
			//赛事ID
			$RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			//赛事列表
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			//赛事分站列表
			$RaceStageArr = $this->oRace->getAllRaceStageList($RaceCatalogId);
			//赛事分组列表
			$RaceGroupArr = $this->oRace->getAllRaceGroupList($RaceCatalogId,'RaceGroupId,RaceGroupName');
			//初始化一个空的赛事分站列表
			$RaceStageList = array();
			//循环赛事分站列表
			foreach($RaceStageArr as $key => $value)
			{
				$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key] = $value;
				//计算分站数量，用于页面跨行显示
				$RaceStageList[$value['RaceCatalogId']]['RaceStageCount'] = isset($RaceStageList[$value['RaceCatalogId']]['RaceStageCount'])?$RaceStageList[$value['RaceCatalogId']]['RaceStageCount']+1:1;
				$RaceStageList[$value['RaceCatalogId']]['RowCount'] = $RaceStageList[$value['RaceCatalogId']]['RaceStageCount']+1;
				//如果相关赛事ID有效
				if(isset($RaceCatalogArr[$value['RaceCatalogId']]))
				{
					//获取赛事ID
					$RaceStageList[$value['RaceCatalogId']]['RaceCatalogName'] = isset($RaceStageList[$value['RaceCatalogId']]['RaceCatalogName'])?$RaceStageList[$value['RaceCatalogId']]['RaceCatalogName']:$RaceCatalogArr[$value['RaceCatalogId']]['RaceCatalogName'];
					//解包压缩数组
					$value['comment'] = json_decode($value['comment'],true);
					$t = array();
					//如果有已经选择的赛事组别
					if(isset($value['comment']['SelectedRaceGroup']) && is_array($value['comment']['SelectedRaceGroup']))
					{
						//循环各个组别
						foreach($value['comment']['SelectedRaceGroup'] as $k => $v)
						{
							//获取各个组别的比赛场次数量
							$RaceCount = $this->oRace->getRaceCount($value['RaceStageId'],$v);
							//如果有配置比赛场次
							if($RaceCount>0)
							{
								//添加场次数量
								$Suffix = "(".$RaceCount.")";
							}
							else
							{
								$Suffix = "";
							}
							//如果赛事组别配置有效
							if(isset($RaceGroupArr[$v]))
							{
								//生成到比赛详情页面的链接
								$t[$k] = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$value['RaceStageId'],'RaceGroupId'=>$v)) ."'>".$RaceGroupArr[$v]['RaceGroupName'].$Suffix."</a>";
							}
						}
					}
					//如果检查后有至少一个有效的赛事组别配置
					if(count($t))
					{
						//生成页面显示的数组
						$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['SelectedGroupList'] = implode("/",$t);
						$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['GroupCount'] = count($t);
						$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['RowCount'] = $RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['GroupCount']+1;
					}
					else
					{
						//生成默认的入口
						$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['SelectedGroupList'] = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$key)) ."'>尚未配置</a>";
						$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['GroupCount'] = 0;
						$RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['RowCount'] = 1;
					}
				}
				else
				{
					$RaceStageList[$value['RaceCatalogId']]['RaceCatalogName'] = 	"未定义";
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceStageList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加任务填写配置页面
	public function raceStageAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageInsert");
		if($PermissionCheck['return'])
		{
			include('Third/ckeditor/ckeditor.php');

			$editor =  new CKEditor();
			$editor->BasePath = '/js/ckeditor/';
			$editor->config['height'] = "50%";
			$editor->config['width'] ="80%";

			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			include $this->tpl('Xrace_Race_RaceStageAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新任务
	public function raceStageInsertAction()
	{
		//检查权限
		$bind=$this->request->from('RaceStageName','RaceCatalogId');
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		if(trim($bind['RaceStageName'])=="")
		{
			$response = array('errno' => 1);
		}
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		elseif(count($SelectedRaceGroup['SelectedRaceGroup'])==0)
		{
			$response = array('errno' => 4);
		}
		else
		{
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			$bind['comment'] = json_encode($bind['comment']);
			$res = $this->oRace->insertRaceStage($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//修改任务信息页面
	public function raceStageModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			$RaceGroupArr = $this->oRace->getAllRaceGroupList($oRaceStage['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			foreach($RaceGroupArr as $RaceGroupId => $value)
			{
				if(in_array($RaceGroupId,$oRaceStage['comment']['SelectedRaceGroup']))
				{
					$RaceGroupArr[$RaceGroupId]['selected'] = 1;
				}
				else
				{
					$RaceGroupArr[$RaceGroupId]['selected'] = 0;
				}
			}
			include $this->tpl('Xrace_Race_RaceStageModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新任务信息
	public function raceStageUpdateAction()
	{
		$bind=$this->request->from('RaceStageId','RaceStageName','RaceCatalogId','StageStartDate','StageEndDate');
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		if(trim($bind['RaceStageName'])=="")
		{
			$response = array('errno' => 1);
		}
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		elseif(count($SelectedRaceGroup['SelectedRaceGroup'])==0)
		{
			$response = array('errno' => 4);
		}
		else
		{
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			$SelectedRacedGroup = $this->oRace->getRaceStageGroupByStage($bind['RaceStageId'],"RaceStageId,RaceGroupId");
			foreach($SelectedRacedGroup as $key => $GroupInfo)
			{
				if(!isset($bind['comment']['SelectedRaceGroup'][$GroupInfo['RaceGroupId']]))
				{
					$this->oRace->deleteRaceStageGroup($GroupInfo['RaceStageId'],$GroupInfo['RaceGroupId']);
				}
			}
			$bind['comment'] = json_encode($bind['comment']);
			$res = $this->oRace->updateRaceStage($bind['RaceStageId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//更新任务信息
	public function raceStageGroupListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果已选分组的数据不存在，用默认空数组替代
			$oRaceStage['comment']['SelectedRaceGroup'] = isset($oRaceStage['comment']['SelectedRaceGroup'])?$oRaceStage['comment']['SelectedRaceGroup']:array();
			foreach($oRaceStage['comment']['SelectedRaceGroup'] as $RaceGroupId => $RaceGroupInfo)
			{
				//获取赛事分组信息
				$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
				//如果获取到
				if($RaceGroupInfo['RaceGroupId'])
				{
					$oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId] = array('RaceGroupInfo' => $RaceGroupInfo, 'RaceStageGroupInfo' => array());
					$RaceStageGroupInfo = $this->oRace->getRaceStageGroup($RaceStageId,$RaceGroupId);
					$StartTime = date("Y-m-d H:i:s",time()+86400);
					$EndTime = date("Y-m-d H:i:s",time()+86400*2);
					$oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]['RaceStageGroupInfo'] = isset($RaceStageGroupInfo['RaceStageId'])?$RaceStageGroupInfo:array('PriceList'=>0,'SingleUser'=>1,'TeamUser'=>1,'StartTime'=>$StartTime,'EndTime'=>$EndTime);
				}
				else
				{
					//删除当前分组
					unset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]);
				}
			}
			include $this->tpl('Xrace_Race_RaceStageGroup');

		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function raceStageGroupUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$bind = $this->request->from('RaceGroupId','RaceStageId','StartTime','EndTime','PriceList','TeamUser','SingleUser');
			//获取数据库内存储的赛段详情
			$RaceStageGroupInfo = $this->oRace->getRaceStageGroup($bind['RaceStageId'],$bind['RaceGroupId']);
			//如果获取到
			if($RaceStageGroupInfo['RaceStageId'])
			{
				//更新
				$res = $this->oRace->updateRaceStageGroup($bind['RaceStageId'],$bind['RaceGroupId'],$bind);
			}
			else
			{
				//新建
				$res = $this->oRace->insertRaceStageGroup($bind);
			}

			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//删除任务
	public function raceStageDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageDelete");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			$this->oRace->deleteRaceStage($RaceStageId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//删除任务
	public function getSelectedGroupAction()
	{
		$RaceCatalogId = intval($this->request->RaceCatalogId);
		$RaceStageId = intval($this->request->RaceStageId);
		$RaceGroupArr = $this->oRace->getAllRaceGroupList($RaceCatalogId);
		if($RaceStageId)
		{
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
		}
		else
		{
			$oRaceStage['comment']['SelectedRaceGroup'] = array();
		}
		foreach($RaceGroupArr as $RaceGroupId => $RaceGroupInfo)
		{
			if(in_array($RaceGroupId,$oRaceStage['comment']['SelectedRaceGroup']))
			{
				$t[$RaceGroupId] = '<input type="checkbox"  name="SelectedRaceGroup[]" value='.$RaceGroupId.' checked>'.$RaceGroupInfo['RaceGroupName'];
			}
			else
			{
				$t[$RaceGroupId] = '<input type="checkbox"  name="SelectedRaceGroup[]" value='.$RaceGroupId.'>'.$RaceGroupInfo['RaceGroupName'];
			}
		}
		$text = implode("  ",$t);
		$text = (trim($text!=""))?$text:"暂无分类";
		echo $text;
		die();
	}
	//更新任务信息
	public function raceListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$RaceList = $this->oRace->getRaceList($RaceStageId,$RaceGroupId);
			include $this->tpl('Xrace_Race_RaceList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function raceAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			//初始化开始和结束时间
			$StartTime = date("Y-m-d H:i:s",time()+86400);
			$EndTime = date("Y-m-d H:i:s",time()+86400*2);
			include $this->tpl('Xrace_Race_RaceAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function raceModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//初始化开始和结束时间
				include $this->tpl('Xrace_Race_RaceModify');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function raceInsertAction()
	{
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','StartTime','EndTime','SingleUser','TeamUser');
		if(trim($bind['RaceName'])=="")
		{
			$response = array('errno' => 1);
		}
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		elseif(intval($bind['RaceGroupId'])<=0)
		{
			$response = array('errno' => 3);
		}
		elseif(trim($bind['PriceList'])=="")
		{
			$response = array('errno' => 4);
		}
		elseif(strtotime(trim($bind['StartTime']))<=time())
		{
			$response = array('errno' => 5);
		}
		elseif(strtotime(trim($bind['EndTime']))<=time())
		{
			$response = array('errno' => 6);
		}
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 6);
		}
		else
		{
			$AddRace = $this->oRace->addRace($bind);
			$response = $AddRace ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//更新任务信息
	public function raceUpdateAction()
	{
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','StartTime','EndTime','SingleUser','TeamUser');
		$RaceId = intval($this->request->RaceId);
		if(trim($bind['RaceName'])=="")
		{
			$response = array('errno' => 1);
		}
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		elseif(intval($bind['RaceGroupId'])<=0)
		{
			$response = array('errno' => 3);
		}
		elseif(trim($bind['PriceList'])=="")
		{
			$response = array('errno' => 4);
		}
		elseif(strtotime(trim($bind['StartTime']))<=time())
		{
			$response = array('errno' => 5);
		}
		elseif(strtotime(trim($bind['EndTime']))<=time())
		{
			$response = array('errno' => 6);
		}
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 7);
		}
		elseif($RaceId<=0)
		{
			$response = array('errno' => 8);
		}
		else
		{
			$AddRace = $this->oRace->updateRace($RaceId,$bind);
			$response = $AddRace ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//更新任务信息
	public function raceDetailAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				$this->oSports = new Xrace_Sports();
				$SportTypeArr = $this->oSports->getAllSportsTypeList();
				foreach($RaceInfo['comment']['DetailList'] as $Key => $RaceSportsInfo)
				{
					if(isset($SportTypeArr[$RaceSportsInfo['SportsTypeId']]))
					{
						$RaceInfo['comment']['DetailList'][$Key]['Total'] = array('Distence'=>0,'ChipCount'=>0,'AltAsc'=>0,'AltDec'=>0);
						$RaceInfo['comment']['DetailList'][$Key]['SportsTypeName'] = $SportTypeArr[$RaceSportsInfo['SportsTypeId']]['SportsTypeName'];
						$RaceInfo['comment']['DetailList'][$Key]['TimingDetailList'] = isset($RaceInfo['comment']['DetailList'][$Key]['TimingId'])?$this->oRace->getTimingDetail($RaceInfo['comment']['DetailList'][$Key]['TimingId']):array();
						$RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'] = isset($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'])?json_decode($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'],true):array();
						ksort($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment']);
						foreach($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'] as $tid => $tinfo)
						{
							//累加里程
							$RaceInfo['comment']['DetailList'][$Key]['Total']['Distence'] += $tinfo['ToNext']*	$tinfo['Round'];
							//累加计时点数量
							$RaceInfo['comment']['DetailList'][$Key]['Total']['ChipCount'] += $tinfo['Round'];
							//累加海拔上升
							$RaceInfo['comment']['DetailList'][$Key]['Total']['AltAsc'] += $tinfo['AltAsc']*	$tinfo['Round'];
							//累加海拔下降
							$RaceInfo['comment']['DetailList'][$Key]['Total']['AltDec'] += $tinfo['AltDec']*	$tinfo['Round'];
						}
					}
					else
					{
						unset($RaceInfo['comment']['DetailList'][$Key]);
					}
				}
				//初始化开始和结束时间
				include $this->tpl('Xrace_Race_RaceDetail');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function raceSportsTypeInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				$response = array('errno' => 1);
			}
			else
			{
				//获取赛事分组信息
				$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
				//如果赛事分组尚未配置
				if(!$oRaceGroup['RaceGroupId'])
				{
					$response = array('errno' => 2);
				}
				else
				{
					$this->oSports = new Xrace_Sports();
					$oSportsType = $this->oSports->getSportsType($SportsTypeId,'*');
					if(!isset($oSportsType['SportsTypeId']))
					{
						$response = array('errno' => 3);
					}
					else
					{
						$RaceInfo = $this->oRace->getRaceInfo($RaceId);
						if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
						{
							$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
							$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
							ksort($RaceInfo['comment']['DetailList']);
							//如果添加在某个元素之后 且 元素下标不越界
							if($After>=0 && $After <= count($RaceInfo['comment']['DetailList']))
							{
								//添加元素
								$RaceInfo['comment']['DetailList'] = Base_Common::array_insert($RaceInfo['comment']['DetailList'],array('SportsTypeId' => $SportsTypeId),$After+1);
							}
							//如果在头部添加
							elseif($After == -1)
							{
								//添加元素
								$RaceInfo['comment']['DetailList'] = Base_Common::array_insert($RaceInfo['comment']['DetailList'],array('SportsTypeId' => $SportsTypeId),$After+1);
							}
							else
							{
								//默认为在表尾部添加元素
								$RaceInfo['comment']['DetailList'][count($RaceInfo['comment']['DetailList'])] = array('SportsTypeId' => $SportsTypeId);
							}
							//生成修改后的元素列表
							$RaceInfo['RaceStageId'] = $RaceStageId;
							$RaceInfo['RaceGroupId'] = $RaceGroupId;
							$RaceInfo['comment'] = json_encode($RaceInfo['comment']);
							//更新数据
							$res = $this->oRace->updateRace($RaceId,$RaceInfo);
							$response = $res ? array('errno' => 0) : array('errno' => 9);
						}
					}
				}
			}
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加分站-分组的运动类型分段提交页面
	public function raceSportsTypeAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取分站分组配置详情
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				ksort($RaceInfo['comment']['DetailList']);
				foreach($RaceInfo['comment']['DetailList'] as $Key => $SportsTypeInfo)
				{
					$RaceInfo['comment']['DetailList'][$Key]['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				}
				//如果添加在某个元素之后 且 元素下标不越界
				if($After<0)
				{
					$After = -1;
				}
				elseif( $After >= count($RaceInfo['comment']['DetailList']))
				{
					$After = count($RaceInfo['comment']['DetailList'])-1;
				}
				include $this->tpl('Xrace_Race_RaceSportsTypeAdd');
			}

		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加分站-分组的运动类型分段提交页面
	public function raceStageGroupSportsTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取分站分组配置详情
			$RaceStageGroupInfo = $this->oRace->getRaceStageGroup($RaceStageId,$RaceGroupId);
			$RaceStageGroupInfo['comment'] = isset($RaceStageGroupInfo['comment'])?json_decode($RaceStageGroupInfo['comment'],true):array();
			$RaceStageGroupInfo['comment']['DetailList'] = isset($RaceStageGroupInfo['comment']['DetailList'])?$RaceStageGroupInfo['comment']['DetailList']:array();
			ksort($RaceStageGroupInfo['comment']['DetailList']);
			$deleted = 0;
			foreach($RaceStageGroupInfo['comment']['DetailList'] as $Key => $SportsTypeInfo)
			{
				if($Key == $SportsTypeId)
				{
					unset($RaceStageGroupInfo['comment']['DetailList'][$Key]);
					$deleted = 1;
				}
				if($deleted == 1 && isset($RaceStageGroupInfo['comment']['DetailList'][$Key+1]))
				{
					$RaceStageGroupInfo['comment']['DetailList'][($Key)] = $RaceStageGroupInfo['comment']['DetailList'][$Key+1];
					unset($RaceStageGroupInfo['comment']['DetailList'][$Key+1]);
				}
			}
			$RaceStageGroupInfo['comment'] = json_encode($RaceStageGroupInfo['comment']);
			//更新数据
			$res = $this->oRace->updateRaceStageGroup($RaceStageId,$RaceGroupId,$RaceStageGroupInfo);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function timingPointInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			$RaceId = intval($this->request->RaceId);
			$After = isset($this->request->After)?intval($this->request->After):-1;
			$bind['TName'] = trim($this->request->TName);
			$bind['ToNext'] = abs(intval($this->request->ToNext));
			$bind['AltAsc'] = abs(intval($this->request->AltAsc));
			$bind['AltDec'] = abs(intval($this->request->AltDec));
			$bind['Round'] = abs(intval($this->request->Round));
			$bind['ChipId'] = trim($this->request->ChipId);

			$AddTimingPoint = $this->oRace->addTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$After,$bind);
			$response = $AddTimingPoint ? array('errno' => 0) : array('errno' => $AddTimingPoint);
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加分站-分组的运动类型分段提交页面
	public function timingPointAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取分站分组配置详情
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				$RaceInfo['comment'] = isset($RaceInfo['comment']) ? json_decode($RaceInfo['comment'], true) : array();
				$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
				$SportsTypeInfo['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				$SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
				$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
				ksort($SportsTypeInfo['TimingDetailList']['comment']);
				//如果添加在某个元素之后 且 元素下标不越界
				if($After<0)
				{
					$After = -1;
				}
				elseif( $After >= count($SportsTypeInfo['TimingDetailList']['comment']))
				{
					$After = count($SportsTypeInfo['TimingDetailList']['comment'])-1;
				}
				include $this->tpl('Xrace_Race_TimingPointAdd');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加计时点数据提交页面
	public function timingPointModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//获取当前分站信息
			$oRaceStage = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$oRaceGroup = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取分站分组配置详情
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				$RaceInfo['comment'] = isset($RaceInfo['comment']) ? json_decode($RaceInfo['comment'], true) : array();
				$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
				$SportsTypeInfo['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				$SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
				$SportsTypeInfo['TimingDetailList']['comment'] = json_decode($SportsTypeInfo['TimingDetailList']['comment'],true);
				$TimingInfo = $SportsTypeInfo['TimingDetailList']['comment'][$TimingId];
				include $this->tpl('Xrace_Race_TimingPointModify');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//修改计时点数据
	public function timingPointUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			$bind['TName'] = trim($this->request->TName);
			$bind['ToNext'] = abs(intval($this->request->ToNext));
			$bind['AltAsc'] = abs(intval($this->request->AltAsc));
			$bind['AltDec'] = abs(intval($this->request->AltDec));
			$bind['Round'] = abs(intval($this->request->Round));
			$bind['ChipId'] = trim($this->request->ChipId);

			$UpdateTimingPoint = $this->oRace->updateTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$TimingId,$bind);
			$response = $UpdateTimingPoint ? array('errno' => 0) : array('errno' => $UpdateTimingPoint);
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//删除计时点数据
	public function timingPointDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			$RaceStageId = intval($this->request->RaceStageId);
			$RaceGroupId = intval($this->request->RaceGroupId);
			$RaceId = intval($this->request->RaceId);
			$SportsTypeId = intval($this->request->SportsTypeId);
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;

			$DeleteTimingPoint = $this->oRace->deleteTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$TimingId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
