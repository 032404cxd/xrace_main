<?php
/**用户管理*/

class Xrace_TeamController extends AbstractController
{
	/**用户管理相关:User
	 * @var string
	 */
	protected $sign = '?ctl=xrace/team';
	/**
	 * game对象
	 * @var object
	 */
	protected $oUser;
	protected $oTeam;
	protected $oManager;
	protected $oRace;

        /**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oUser = new Xrace_User();
		$this->oTeam = new Xrace_Team();
		$this->oManager = new Widget_Manager();
		$this->oRace = new Xrace_Race();

	}
	//用户列表
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//赛事ID
			$params['RaceCatalogId'] = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($params['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//页面参数预处理
			$params['RaceTeamName'] = urldecode(trim($this->request->RaceTeamName))?substr(urldecode(trim($this->request->RaceTeamName)),0,20):"";
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 20;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户列表
			$RaceTeamList = $this->oTeam->getRaceTeamList($params);
			//导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/team','team.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/team','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($RaceTeamList['RaceTeamCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//循环队伍列表
			foreach($RaceTeamList['RaceTeamList'] as $RaceTeamId => $RaceTeamInfo)
			{
				//如果存在于赛事列表
				if(isset($RaceCatalogList[$RaceTeamInfo['RaceCatalogId']]))
				{
					//获取赛事名称
					$RaceTeamList['RaceTeamList'][$RaceTeamId]['RaceCatalogName'] = $RaceCatalogList[$RaceTeamInfo['RaceCatalogId']]['RaceCatalogName'];
					//解包压缩数组
					$RaceTeamInfo['comment'] = json_decode($RaceTeamInfo['comment'],true);
					$t = array();
					//如果有已经选择的赛事组别
					if(isset($RaceTeamInfo['comment']['SelectedRaceGroup']) && is_array($RaceTeamInfo['comment']['SelectedRaceGroup']))
					{
						//循环各个组别
						foreach($RaceTeamInfo['comment']['SelectedRaceGroup'] as $k => $v)
						{
							//如果赛事组别配置有效
							if(isset($RaceGroupList[$v]))
							{
								//生成到比赛详情页面的链接
								$t[$k] = $RaceGroupList[$v]['RaceGroupName'];
							}
						}
					}
					//如果检查后有至少一个有效的赛事组别配置
					if(count($t))
					{
						//生成页面显示的数组
						$RaceTeamList['RaceTeamList'][$RaceTeamId]['SelectedGroupList'] = implode("/",$t);
					}
					else
					{
						//生成默认的入口
						$RaceTeamList['RaceTeamList'][$RaceTeamId]['SelectedGroupList'] = "尚未配置";
					}
				}
				else
				{
					$RaceTeamList['RaceTeamList'][$RaceTeamId]['RaceCatalogName'] = "未定义";
				}

			}
			//模板渲染
			include $this->tpl('Xrace_Team_RaceTeamList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加队伍填写配置页面
	public function teamAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceTeamInsert");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//渲染模板
			include $this->tpl('Xrace_Team_RaceTeamAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加新赛事分站
	public function teamInsertAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceTeamName','RaceCatalogId','RaceTeamComment');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogList  = $this->oRace->getRaceCatalogList();
		//队伍名称不能为空
		if(trim($bind['RaceTeamName'])=="")
		{
			$response = array('errno' => 1);
		}
		//必须选定一个有效的赛事ID
		elseif(!isset($RaceCatalogList[$bind['RaceCatalogId']]))
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
			//获取当前时间
			$CurrentTime = date("Y-m-d H:i:s",time());
			//记录分组信息
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			//对说明文字进行过滤和编码
			$bind['RaceTeamComment'] = urlencode(htmlspecialchars(trim($bind['RaceTeamComment'])));
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//获取创建时间和最后更新时间
			$bind['CreateTime'] = $CurrentTime;
			$bind['LastUpdateTime'] = $CurrentTime;
			//插入数据
			$res = $this->oTeam->insertRaceTeamInfo($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//添加队伍填写配置页面
	public function teamModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceTeamModify");
		if($PermissionCheck['return'])
		{
			//队伍ID
			$RaceTeamId = intval($this->request->RaceTeamId);
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//队伍数据
			$RaceTeamInfo = $this->oTeam->getRaceTeamInfo($RaceTeamId,'*');
			//分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceTeamInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//说明文字解码
			$RaceTeamInfo['RaceTeamComment'] = urldecode($RaceTeamInfo['RaceTeamComment']);
			//数据解包
			$RaceTeamInfo['comment'] = json_decode($RaceTeamInfo['comment'],true);
			//循环赛事分组列表
			foreach($RaceGroupList as $RaceGroupId => $RaceGroupInfo)
			{
				//如果出现在选定的分组列表当中
				if(in_array($RaceGroupId,$RaceTeamInfo['comment']['SelectedRaceGroup']))
				{
					$RaceGroupList[$RaceGroupId]['selected'] = 1;
				}
				else
				{
					$RaceGroupList[$RaceGroupId]['selected'] = 0;
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Team_RaceTeamModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新队伍
	public function teamUpdateAction()
	{
		//获取 页面参数
		$bind = $this->request->from('RaceTeamId','RaceTeamName','RaceCatalogId','RaceTeamComment');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogList  = $this->oRace->getRaceCatalogList();
		//分站名称不能为空
		if(trim($bind['RaceTeamName'])=="")
		{
			$response = array('errno' => 1);
		}
		//赛事分站ID必须大于0
		elseif(intval($bind['RaceTeamId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//必须选定一个有效的赛事ID
		elseif(!isset($RaceCatalogList[$bind['RaceCatalogId']]))
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
			$RaceTeamInfo = $this->oTeam->getRaceTeamInfo($bind['RaceTeamId']);
			//数据解包
			$bind['comment'] = json_decode($RaceTeamInfo['comment'],true);
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			//对说明文字进行过滤和编码
			$bind['RaceTeamComment'] = urlencode(htmlspecialchars(trim($bind['RaceTeamComment'])));
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//获取当前时间
			$CurrentTime = date("Y-m-d H:i:s",time());
			//最后更新时间
			$bind['LastUpdateTime'] = $CurrentTime;
			//更新数据
			$res = $this->oTeam->updateRaceTeamInfo($bind['RaceTeamId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//删除队伍
	public function teamDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceTeamDelete");
		if($PermissionCheck['return'])
		{
			//队伍ID
			$RaceTeamId = intval($this->request->RaceTeamId);
			//删除比赛类型
			$this->oTeam->deleteRaceTeam($RaceTeamId);
			//返回之前页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//获取队伍已经选择的分组列表
	public function getSelectedGroupAction()
	{
		//赛事ID
		$RaceCatalogId = intval($this->request->RaceCatalogId);
		//赛事分站ID
		$RaceTeamId = intval($this->request->RaceTeamId);
		//所有赛事分组列表
		$RaceGroupList = $this->oRace->getRaceGroupList($RaceCatalogId);
		//如果有传赛事分站ID
		if($RaceTeamId)
		{
			//队伍数据
			$RaceTeamInfo = $this->oTeam->getRaceTeamInfo($RaceTeamId,'*');
			//数据解包
			$RaceTeamInfo['comment'] = json_decode($RaceTeamInfo['comment'],true);
		}
		else
		{
			//置为空数组
			$RaceTeamInfo['comment']['SelectedRaceGroup'] = array();
		}
		//循环赛事分组列表
		foreach($RaceGroupList as $RaceGroupId => $RaceGroupInfo)
		{
			//如果有选择该赛事分组
			if(in_array($RaceGroupId,$RaceTeamInfo['comment']['SelectedRaceGroup']))
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
	//获取赛事对应的队伍列表
	public function getTeamByCatalogAction()
	{
		//赛事ID
		$RaceCatalogId = intval($this->request->RaceCatalogId);
		//分组ID
		$RaceTeamId = intval($this->request->RaceGroupId);
		//所有赛事分组列表
		$params = array('RaceCatalogId'=>$RaceCatalogId);
		$RaceTeamList = $this->oTeam->getRaceTeamList($params,array("RaceTeamId","RaceTeamName"));
		$text = '';
		//循环赛事分组列表
		foreach($RaceTeamList['RaceTeamList'] as $TeamId => $RaceTeamInfo)
		{
			//初始化选中状态
			$selected = "";
			//如果分组ID与传入的分组ID相符
			if($RaceTeamInfo['RaceTeamId'] == $RaceTeamId)
			{
				//选中拼接
				$selected = 'selected="selected"';
			}
			//字符串拼接
			$text .= '<option value="'.$RaceTeamInfo['RaceTeamId'].'">'.$RaceTeamInfo['RaceTeamName'].'</option>';
		}
		echo $text;
		die();
	}
	//获取赛事对应的队伍列表
	public function getGroupByTeamAction()
	{
		//分组ID
		$RaceTeamId = intval($this->request->RaceTeamId);
		//分组ID
		$RaceGroupId = intval($this->request->RaceGroupId);
		//所有赛事分组列表
		$TeamInfo = $this->oTeam->getRaceTeamInfo($RaceTeamId);
		//数据解包
		$TeamInfo['comment'] = json_decode($TeamInfo['comment'],true);
		$text = '';
		if(isset($TeamInfo['comment']['SelectedRaceGroup']))
		{
			//所有赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($TeamInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//循环赛事分组列表
			foreach($TeamInfo['comment']['SelectedRaceGroup'] as $GroupId)
			{
				if(isset($RaceGroupList[$GroupId]))
				{
					//初始化选中状态
					$selected = "";
					//如果分组ID与传入的分组ID相符
					if($GroupId == $RaceGroupId)
					{
						//选中拼接
						$selected = 'selected="selected"';
					}
					//字符串拼接
					$text .= '<option value="'.$GroupId.'">'.$RaceGroupList[$GroupId]['RaceGroupName'].'</option>';
				}

			}
		}
		echo $text;
		die();
	}
}
