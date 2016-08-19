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
			$params['RaceTeamName'] = urldecode(trim($this->request->RaceTeamName))?substr(urldecode(trim($this->request->RaceTeamName)),0,20):"";
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 20;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户列表
			$RaceTeamList = $this->oTeam->getRaceTeamList($params);
			//print_R($RaceTeamList);
            //导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/team','team.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/team','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($RaceTeamList['RaceTeamCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//模板渲染
			include $this->tpl('Xrace_Team_RaceTeamList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
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
