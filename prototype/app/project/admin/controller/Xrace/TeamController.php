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
		$this->oUser = new Xrace_UserInfo();
		$this->oTeam = new Xrace_Team();
		$this->oManager = new Widget_Manager();
		$this->oRace = new Xrace_Race();

	}
	//队伍列表
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			$params['TeamName'] = urldecode(trim($this->request->TeamName))?substr(urldecode(trim($this->request->TeamName)),0,20):"";
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 20;
			//获取队伍列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户列表
			$TeamList = $this->oTeam->getTeamList($params);
            //初始化空的用户列表
            $UserList = array();
            $RaceCatalogList = array();
            $RaceStageList = array();
            //循环队伍列表
            foreach($TeamList['TeamList'] as $TeamId => $TeamInfo)
            {
                //如果没有获取过创建者信息
                if(!isset($UserList[$TeamInfo['CreateUserId']]))
                {
                    //获取用户数据
                    $UserInfo = $this->oUser->getUserInfo($TeamInfo['CreateUserId'],"UserId,UserName",1);
                    //如果获取到
                    if(isset($UserInfo['UserId']))
                    {
                        //存入用户列表
                        $UserList[$TeamInfo['CreateUserId']] = $UserInfo;
                    }
                }
                //保存到创建者信息
                $CreateUserInfo = isset($UserList[$TeamInfo['CreateUserId']])?$UserList[$TeamInfo['CreateUserId']]:array();
                if($TeamInfo['IsTemp'] ==1)
                {
                    //如果没有获取过赛事信息
                    if(!isset($RaceCatalogList[$TeamInfo['RaceCatalogId']]))
                    {
                        //获取赛事数据
                        $RaceCatalogInfo = $this->oRace->getRaceCatalog($TeamInfo['RaceCatalogId'],"RaceCatalogId,RaceCatalogName");
                        //如果获取到
                        if(isset($RaceCatalogInfo['RaceCatalogId']))
                        {
                            //存入赛事列表
                            $RaceCatalogList[$TeamInfo['RaceCatalogId']] = $RaceCatalogInfo;
                        }
                    }
                    //保存到赛事信息
                    $RaceCatalogInfo = isset($RaceCatalogList[$TeamInfo['RaceCatalogId']])?$RaceCatalogList[$TeamInfo['RaceCatalogId']]:array();
                    //如果没有获取过分站信息
                    if(!isset($RaceStageList[$TeamInfo['RaceStageId']]))
                    {
                        //获取分站数据
                        $RaceStageInfo = $this->oRace->getRaceStage($TeamInfo['RaceStageId'],"RaceStageId,RaceStageName");
                        //如果获取到
                        if(isset($RaceStageInfo['RaceStageId']))
                        {
                            //存入分站列表
                            $RaceStageList[$TeamInfo['RaceStageId']] = $RaceStageInfo;
                        }
                    }
                    //保存到分站信息
                    $RaceStageInfo = isset($RaceStageList[$TeamInfo['RaceStageId']])?$RaceStageList[$TeamInfo['RaceStageId']]:array();
                }

                //保存赛事名称
                $TeamList['TeamList'][$TeamId]['RaceCatalogName'] =  isset($RaceCatalogInfo['RaceCatalogName'])?$RaceCatalogInfo['RaceCatalogName']:"未知赛事";
                //保存分站名称
                $TeamList['TeamList'][$TeamId]['RaceStageName'] =  isset($RaceStageInfo['RaceStageName'])?$RaceStageInfo['RaceStageName']:"未知分站";
                //保存创建者姓名
                $TeamList['TeamList'][$TeamId]['CreateUserName'] =  isset($CreateUserInfo['UserName'])?$CreateUserInfo['UserName']:"未知用户";

            }
            //导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/team','team.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/team','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($TeamList['TeamCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//模板渲染
			include $this->tpl('Xrace_Team_TeamList');
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
		$TeamId = intval($this->request->RaceGroupId);
		//所有赛事分组列表
		$params = array('RaceCatalogId'=>$RaceCatalogId);
		$TeamList = $this->oTeam->getTeamList($params,array("TeamId","TeamName"));
		$text = '';
		//循环赛事分组列表
		foreach($TeamList['TeamList'] as $TeamId => $TeamInfo)
		{
			//初始化选中状态
			$selected = "";
			//如果分组ID与传入的分组ID相符
			if($TeamInfo['TeamId'] == $TeamId)
			{
				//选中拼接
				$selected = 'selected="selected"';
			}
			//字符串拼接
			$text .= '<option value="'.$TeamInfo['TeamId'].'">'.$TeamInfo['TeamName'].'</option>';
		}
		echo $text;
		die();
	}
	//获取赛事对应的队伍列表
	public function getGroupByTeamAction()
	{
		//分组ID
		$TeamId = intval($this->request->TeamId);
		//分组ID
		$RaceGroupId = intval($this->request->RaceGroupId);
		//所有赛事分组列表
		$TeamInfo = $this->oTeam->getTeamInfo($TeamId);
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
