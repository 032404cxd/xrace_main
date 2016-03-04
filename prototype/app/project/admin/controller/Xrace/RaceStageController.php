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
	//赛事分站列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
                        $RootUrl = "http://".$_SERVER['HTTP_HOST'];
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
                                
                                if(isset($value['comment']['RaceStageIcon']) && is_array($value['comment']['RaceStageIcon']))
                                {
                                    foreach ($value['comment']['RaceStageIcon'] as $k => $v) {
                                        $RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['RaceStageIconList'] .= "<a href='".$RootUrl.$v['RaceStageIcon_root']."' target='_blank'>图标".$k."</a>/";                                       
                                    }
                                    $RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['RaceStageIconList'] = rtrim($RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['RaceStageIconList'], "/");
                                }  else {
                                    $RaceStageList[$value['RaceCatalogId']]['RaceStageList'][$key]['RaceStageIconList'] = '未上传';  
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
	//添加赛事分站填写配置页面
	public function raceStageAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageInsert");
		if($PermissionCheck['return'])
		{
			//富文本编辑器
			include('Third/ckeditor/ckeditor.php');
			$editor =  new CKEditor();
			$editor->BasePath = '/js/ckeditor/';
			$editor->config['height'] = "50%";
			$editor->config['width'] ="80%";

			//赛事列表
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			//渲染模板
			include $this->tpl('Xrace_Race_RaceStageAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新赛事分站
	public function raceStageInsertAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceStageName','RaceCatalogId');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		//分站名称不能为空
		if(trim($bind['RaceStageName'])=="")
		{
			$response = array('errno' => 1);
		}
		//必须选定一个有效的赛事ID
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		//至少选定一个分组
		elseif(count($SelectedRaceGroup['SelectedRaceGroup'])==0)
		{
			$response = array('errno' => 4);
		}
		else
		{
                    //记录分组信息
                    $bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
                    //文件上传
                    $oUpload = new Base_Upload('RaceStageIcon');
                    $upload = $oUpload->upload('RaceStageIcon');
                    $res = $upload->resultArr;
                    foreach($upload->resultArr as $iconkey=>$iconvalue){
                        $path = $iconvalue;
                        //如果正确上传，就保存文件路径
                        if(strlen($path['path'])>2)
                        {
                            $bind['comment']['RaceStageIcon'][$iconkey]['RaceStageIcon'] = $path['path'];
                            $bind['comment']['RaceStageIcon'][$iconkey]['RaceStageIcon_root'] = $path['path_root'];
                        }
                    }
                    //数据压缩
                    $bind['comment'] = json_encode($bind['comment']);
                    //插入数据
                    $res = $this->oRace->insertRaceStage($bind);
                    $response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}

	//修改赛事分站填写配置页面
	public function raceStageModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事列表
			$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
			//分站数据
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//分组列表
			$RaceGroupArr = $this->oRace->getAllRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//循环赛事分组列表
			foreach($RaceGroupArr as $RaceGroupId => $value)
			{
				//如果出现在选定的分组列表当中
				if(in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
				{
					$RaceGroupArr[$RaceGroupId]['selected'] = 1;
				}
				else
				{
					$RaceGroupArr[$RaceGroupId]['selected'] = 0;
				}
			}
                        //获得赛事分组的图标
                        $RaceStageIconArr = array();
                        if(isset($RaceStageInfo['comment']['RaceStageIcon']) && is_array($RaceStageInfo['comment']['RaceStageIcon']))
                        {
                            $RaceStageIconArr = $RaceStageInfo['comment']['RaceStageIcon'];
                        }    
			//渲染模板
			include $this->tpl('Xrace_Race_RaceStageModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新赛事分站
	public function raceStageUpdateAction()
	{
		//获取 页面参数
		$bind = $this->request->from('RaceStageId','RaceStageName','RaceCatalogId','StageStartDate','StageEndDate');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogArr  = $this->oRace->getAllRaceCatalogList();
		//分站名称不能为空
		if(trim($bind['RaceStageName'])=="")
		{
			$response = array('errno' => 1);
		}
		//赛事分站ID必须大于0
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//必须选定一个有效的赛事ID
		elseif(!isset($RaceCatalogArr[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		//必须选定一个有效的赛事ID
		elseif(count($SelectedRaceGroup['SelectedRaceGroup'])==0)
		{
			$response = array('errno' => 4);
		}
		else
		{
                        //获取原有数据
			$oRaceStage = $this->oRace->getRaceStage($bind['RaceStageId']);
			$bind['comment'] = json_decode($oRaceStage['comment'],true);
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			//获取当前分站已经配置的分组列表
			$SelectedRacedGroup = $this->oRace->getRaceStageGroupByStage($bind['RaceStageId'],"RaceStageId,RaceGroupId");
			//循环分组列表
			foreach($SelectedRacedGroup as $key => $GroupInfo)
			{
				//如果未在已选择的分组列表中匹配到
				if(!isset($bind['comment']['SelectedRaceGroup'][$GroupInfo['RaceGroupId']]))
				{
					//删除该数据
					$this->oRace->deleteRaceStageGroup($GroupInfo['RaceStageId'],$GroupInfo['RaceGroupId']);
				}
			}
                        //文件上传
                        $oUpload = new Base_Upload('RaceStageIcon');
                        $upload = $oUpload->upload('RaceStageIcon');
                        $res = $upload->resultArr;
                        foreach($upload->resultArr as $iconkey=>$iconvalue){
                            $path = $iconvalue;
                            //如果正确上传，就保存文件路径
                            if(strlen($path['path'])>2)
                            {
                                $bind['comment']['RaceStageIcon'][$iconkey]['RaceStageIcon'] = $path['path'];
                                $bind['comment']['RaceStageIcon'][$iconkey]['RaceStageIcon_root'] = $path['path_root'];
                            }
                        }
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//更新数据
			$res = $this->oRace->updateRaceStage($bind['RaceStageId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//删除赛事分站
	public function raceStageDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageDelete");
		if($PermissionCheck['return'])
		{
			//赛事分赞ID
			$RaceStageId = intval($this->request->RaceStageId);
			//获取赛事分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//如果有获取到赛事分站信息
			if(isset($RaceStageInfo['RaceStageId']))
			{
				//删除
				$this->oRace->deleteRaceStage($RaceStageId);
			}
			//返回之前页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
        //删除赛事分站图标
	public function raceStageLogoDeleteAction()
	{
            //赛事分站ID
            $RaceStageId = intval($this->request->RaceStageId);
            //图标ID
            $LogoId = intval($this->request->LogoId);
            //获取原有数据
            $oRaceStage = $this->oRace->getRaceStage($RaceStageId);
            $bind['comment'] = json_decode($oRaceStage['comment'],true);
            foreach($bind['comment']['RaceStageIcon'] as $k => $v)
            {
                if($k == $LogoId) {
                    unset($bind['comment']['RaceStageIcon'][$k]);
                }
            }
            //数据压缩
            $bind['comment'] = json_encode($bind['comment']);
            //更新数据
            $res = $this->oRace->updateRaceStage($RaceStageId,$bind);
            //返回之前页面
            $this->response->goBack(); 
        }
	//获取赛事分站已经选择的分组列表
	public function getSelectedGroupAction()
	{
		//赛事ID
		$RaceCatalogId = intval($this->request->RaceCatalogId);
		//赛事分站ID
		$RaceStageId = intval($this->request->RaceStageId);
		//所有赛事分组列表
		$RaceGroupArr = $this->oRace->getAllRaceGroupList($RaceCatalogId);
		//如果有传赛事分站ID
		if($RaceStageId)
		{
			//获取赛事分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		}
		else
		{
			//置为空数组
			$RaceStageInfo['comment']['SelectedRaceGroup'] = array();
		}
		//循环赛事分组列表
		foreach($RaceGroupArr as $RaceGroupId => $RaceGroupInfo)
		{
			//如果有选择该赛事分组
			if(in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
			{
				//拼接单选框，并选中
				$t[$RaceGroupId] = '<input type="checkbox"  name="SelectedRaceGroup[]" value='.$RaceGroupId.' checked>'.$RaceGroupInfo['RaceGroupName'];
			}
			else
			{
				//拼接单选框，不选中
				$t[$RaceGroupId] = '<input type="checkbox"  name="SelectedRaceGroup[]" value='.$RaceGroupId.'>'.$RaceGroupInfo['RaceGroupName'];
			}
		}
		//字符串组合
		$text = implode("  ",$t);
		//如果当前没有已经选择的赛事分组列表
		$text = (trim($text!=""))?$text:"暂无分类";
		echo $text;
		die();
	}
	//比赛列表页面
	public function raceListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取比赛列表
			$RaceList = $this->oRace->getRaceList($RaceStageId,$RaceGroupId);
			//渲染模板
			include $this->tpl('Xrace_Race_RaceList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加比赛配置信息填写页面
	public function raceAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//初始化开始和结束时间
			$StartTime = date("Y-m-d H:i:s",time()+86400);
			$EndTime = date("Y-m-d H:i:s",time()+86400*2);
			//渲染模板
			include $this->tpl('Xrace_Race_RaceAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//修改比赛配置信息填写页面
	public function raceModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//渲染模板
				include $this->tpl('Xrace_Race_RaceModify');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加比赛
	public function raceInsertAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','StartTime','EndTime','SingleUser','TeamUser');
		//比赛名称不能为空
		if(trim($bind['RaceName'])=="")
		{
			$response = array('errno' => 1);
		}
		//分站ID必须大于0
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//分组ID必须大于0
		elseif(intval($bind['RaceGroupId'])<=0)
		{
			$response = array('errno' => 3);
		}
		//价格参数必须填写
		elseif(trim($bind['PriceList'])=="")
		{
			$response = array('errno' => 4);
		}
		//开始时间不能早于当前时间
		elseif(strtotime(trim($bind['StartTime']))<=time())
		{
			$response = array('errno' => 5);
		}
		//结束时间不能早于当前时间
		elseif(strtotime(trim($bind['EndTime']))<=time())
		{
			$response = array('errno' => 6);
		}
		//单人报名和团队报名至少要选择一个
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 7);
		}
		else
		{
			//新增比赛
			$AddRace = $this->oRace->addRace($bind);
			$response = $AddRace ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//修改比赛
	public function raceUpdateAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','StartTime','EndTime','SingleUser','TeamUser');
		//比赛ID
		$RaceId = intval($this->request->RaceId);
		//比赛名称不能为空
		if(trim($bind['RaceName'])=="")
		{
			$response = array('errno' => 1);
		}
		//分站ID必须大于0
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//分组ID必须大于0
		elseif(intval($bind['RaceGroupId'])<=0)
		{
			$response = array('errno' => 3);
		}
		//价格参数必须填写
		elseif(trim($bind['PriceList'])=="")
		{
			$response = array('errno' => 4);
		}
		//开始时间不能早于当前时间
		elseif(strtotime(trim($bind['StartTime']))<=time())
		{
			$response = array('errno' => 5);
		}
		//结束时间不能早于当前时间
		elseif(strtotime(trim($bind['EndTime']))<=time())
		{
			$response = array('errno' => 6);
		}
		//单人报名和团队报名至少要选择一个
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 7);
		}
		//比赛ID必须大于0
		elseif($RaceId<=0)
		{
			$response = array('errno' => 8);
		}
		else
		{
			//更新比赛
			$AddRace = $this->oRace->updateRace($RaceId,$bind);
			$response = $AddRace ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//比赛详情页面
	public function raceDetailAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取比赛信息
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
				//初始运动类型信息列表
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				$this->oSports = new Xrace_Sports();
				//获取运动类型列表
				$SportTypeArr = $this->oSports->getAllSportsTypeList();
				//循环运动类型列表
				foreach($RaceInfo['comment']['DetailList'] as $Key => $RaceSportsInfo)
				{
					//如果运动类型已经配置
					if(isset($SportTypeArr[$RaceSportsInfo['SportsTypeId']]))
					{
						//初始化统计信息
						$RaceInfo['comment']['DetailList'][$Key]['Total'] = array('Distence'=>0,'ChipCount'=>0,'AltAsc'=>0,'AltDec'=>0);
						//获取运动类型名称
						$RaceInfo['comment']['DetailList'][$Key]['SportsTypeName'] = $SportTypeArr[$RaceSportsInfo['SportsTypeId']]['SportsTypeName'];
						//如果有配置计时点ID 则获取计时点信息
						$RaceInfo['comment']['DetailList'][$Key]['TimingDetailList'] = isset($RaceInfo['comment']['DetailList'][$Key]['TimingId'])?$this->oRace->getTimingDetail($RaceInfo['comment']['DetailList'][$Key]['TimingId']):array();
						//数据解包
						$RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'] = isset($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'])?json_decode($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'],true):array();
						//计时点排序
						ksort($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment']);
						//循环计时点列表
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
						//从列表中删除
						unset($RaceInfo['comment']['DetailList'][$Key]);
					}
				}
				//渲染模板
				include $this->tpl('Xrace_Race_RaceDetail');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加运动类型分段
	public function raceSportsTypeInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				$response = array('errno' => 1);
			}
			else
			{
				//获取赛事分组信息
				$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
				//如果赛事分组尚未配置
				if(!$RaceGroupInfo['RaceGroupId'])
				{
					$response = array('errno' => 2);
				}
				else
				{
					$this->oSports = new Xrace_Sports();
					//获取运动类型信息
					$SportsTypeInfo = $this->oSports->getSportsType($SportsTypeId,'*');
					//如果未获取到有效的运动类型
					if(!isset($SportsTypeInfo['SportsTypeId']))
					{
						$response = array('errno' => 3);
					}
					else
					{
						//获取比赛信息
						$RaceInfo = $this->oRace->getRaceInfo($RaceId);
						//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
						if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
						{
							//数据解包
							$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
							//初始运动类型信息列表
							$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
							//运动类型列表排序
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
							//数据打包
							$RaceInfo['comment'] = json_encode($RaceInfo['comment']);
							//更新比赛
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
	//添加比赛提交页面
	public function raceSportsTypeAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//初始运动类型信息列表
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				//运动类型列表排序
				ksort($RaceInfo['comment']['DetailList']);
				//循环运动类型列表
				foreach($RaceInfo['comment']['DetailList'] as $Key => $SportsTypeInfo)
				{
					//获取运动类型名称
					$RaceInfo['comment']['DetailList'][$Key]['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				}
				//如果位置为负数
				if($After<0)
				{
					$After = -1;
				}
				//如果添加在某个元素之后 且 元素下标不越界
				elseif( $After >= count($RaceInfo['comment']['DetailList']))
				{
					$After = count($RaceInfo['comment']['DetailList'])-1;
				}
				//渲染模板
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
	public function raceSportsTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo ['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取比赛信息
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//初始运动类型信息列表
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				//运动类型列表排序
				ksort($RaceInfo['comment']['DetailList']);
				//已删除标签为0
				$deleted = 0;
				//循环运动类型列表
				foreach($RaceInfo['comment']['DetailList'] as $Key => $SportsTypeInfo)
				{
					//如果匹配到需要删除的数据
					if($Key == $SportsTypeId)
					{
						//删除数据
						unset($RaceInfo['comment']['DetailList'][$Key]);
						//已删除标签为1
						$deleted = 1;
					}
					//如果已删除，且有后续数据
					if($deleted == 1 && isset($RaceInfo['comment']['DetailList'][$Key+1]))
					{
						//后续数据复制到前一位
						$RaceInfo['comment']['DetailList'][($Key)] = $RaceInfo['comment']['DetailList'][$Key+1];
						//删除后续数据
						unset($RaceInfo['comment']['DetailList'][$Key+1]);
					}
				}
				//数据打包
				$RaceInfo['comment'] = json_encode($RaceInfo['comment']);
				//更新比赛
				$res = $this->oRace->updateRace($RaceId,$RaceInfo);
			}
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
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取 页面参数
			$bind = $this->request->from('TName','ToNext','AltAsc','AltDec','Round','ChipId');
			//添加计时点
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
	//添加比赛计时点提交页面
	public function timingPointAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//获取运动类型信息
				$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
				//获取运动类型名称
				$SportsTypeInfo['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				//初始化计时点列表
				$SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
				//解包数据
				$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
				//计时点信息排序
				ksort($SportsTypeInfo['TimingDetailList']['comment']);
				//如果计时点位置为负数
				if($After<0)
				{
					$After = -1;
				}
				//如果添加在某个元素之后 且 元素下标不越界
				elseif( $After >= count($SportsTypeInfo['TimingDetailList']['comment']))
				{
					$After = count($SportsTypeInfo['TimingDetailList']['comment'])-1;
				}
				//渲染模板
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
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRaceInfo($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//获取运动类型信息
				$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
				//获取运动类型名称
				$SportsTypeInfo['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				//初始化计时点列表
				$SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
				//解包数据
				$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
				//获取计时点信息
				$TimingInfo = $SportsTypeInfo['TimingDetailList']['comment'][$TimingId];
				//渲染模板
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
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//获取 页面参数
			$bind = $this->request->from('TName','ToNext','AltAsc','AltDec','Round','ChipId');

			//更新计时点
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
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//删除计时点
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
