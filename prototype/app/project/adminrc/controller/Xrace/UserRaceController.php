<?php
/**用户管理*/

class Xrace_UserRaceController extends AbstractController
{
	/**用户管理相关:User
	 * @var string
	 */
	protected $sign = '?ctl=xrace/user.race';
	/**
	 * game对象
	 * @var object
	 */
	protected $oUser;
	protected $oManager;
	protected $oUserRace;
    protected $oArena;


    /**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
        $this->oUserInfo = new Xrace_UserInfo();
        $this->oManager = new Widget_Manager();
        $this->oUserRace = new Xrace_UserRace();
        $this->oArena = new Xrace_Arena();

	}
	//用户对战列表
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
            //获取场地列表
		    $ArenaList = $this->oArena->getAllArenaList("ArenaName,ArenaId");
            //获取比赛状态列表
            $RaceStatusList = $this->oUserRace->getRaceStausList();
		    //页面参数预处理
			$params['RaceId'] = isset($this->request->RaceId)?intval($this->request->RaceId):0;
			$params['ArenaId'] = isset($this->request->ArenaId)?intval($this->request->ArenaId):0;
            $params['RaceStatus'] = isset($this->request->RaceStatus)?intval($this->request->RaceStatus):0;
            //分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 10;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户约战队列
			$UserAppliedRaceList = $this->oUserRace->getUserAppliedRaceList($params);
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/user.race','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($UserAppliedRaceList['UserRaceCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//初始化空的用户队列
			$UserList = array();
			foreach($UserAppliedRaceList['UserRaceList'] as $ApplyId => $ApplyInfo)
			{
			    //场地名称
                $UserAppliedRaceList['UserRaceList'][$ApplyId]['ArenaName'] = isset($ArenaList[$ApplyInfo['ArenaId']])?$ArenaList[$ApplyInfo['ArenaId']]['ArenaName']:"未知场地";
                //比赛状态
                $UserAppliedRaceList['UserRaceList'][$ApplyId]['RaceStatusName'] = isset($RaceStatusList[$ApplyInfo['RaceStatus']])?$RaceStatusList[$ApplyInfo['RaceStatus']]:"未知状态";
			}
            //模板渲染
			include $this->tpl('Xrace_UserRace_UserRaceList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
    //用户对战结果更新
    public function appliedRaceResultUpdateAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("UpdateAppliedRaceResult");
        if($PermissionCheck['return'])
        {
            //获取场地列表
            $ArenaList = $this->oArena->getAllArenaList("ArenaName,ArenaId");
            //获取比赛状态列表
            $RaceStatusList = $this->oUserRace->getRaceStausList();
            //获取比赛状态列表
            $UserRaceStatusList = $this->oUserRace->getUserRaceStausList();
            //比赛ID
            $RaceId = abs(intval($this->request->RaceId));
            //获取比赛信息
            $RaceInfo = $this->oUserRace->getAppliedRace($RaceId);
            //数据解包
            $RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
            if(isset($RaceInfo['comment']['Result']['manager']))
            {
                $managerInfo = $this->oManager->get($RaceInfo['comment']['Result']['manager']);
            }
            //如果获取到
            if($RaceInfo['RaceId'])
            {
                //获取场地信息
                $ArenaInfo = $this->oArena->getArena($RaceInfo['ArenaId']);
                $RaceInfo['ArenaName'] = $ArenaInfo['ArenaName'];
                //比赛状态
                $RaceInfo['RaceStatusName'] = isset($RaceStatusList[$RaceInfo['RaceStatus']])?$RaceStatusList[$RaceInfo['RaceStatus']]:"未知状态";
                $oChip = new Xrace_Chip();
                //获取对战的用户列表
                $UserList = $this->oUserRace->getUserRaceList(array("RaceId" => $RaceId));
                $oUser = new Xrace_UserInfo();
                //循环用户列表
                foreach ($UserList['UserRaceList'] as $UserRaceId => $ApplyInfo)
                {
                    //获得用户信息
                    $UserInfo = $oUser->getUserInfo($ApplyInfo['UserId'], "UserId,Name");
                    //保存用户姓名
                    $UserList['UserRaceList'][$UserRaceId]['Name'] = $UserInfo['Name'];
                    //获取芯片信息
                    $ChipInfo = $oChip->getChipInfo($ApplyInfo['ChipId'], "ChipId,NickName");
                    //保存用户姓名
                    $UserList['UserRaceList'][$UserRaceId]['ChipName'] = $ChipInfo['NickName'];
                    //保存用户状态
                    $UserList['UserRaceList'][$UserRaceId]['UserRaceStatusName'] = $UserRaceStatusList[$ApplyInfo['Result']];
                }
            }
            //模板渲染
            include $this->tpl('Xrace_UserRace_AppliedRaceResultUpdate');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //用户对战结果更新
    public function appliedRaceResultUpdateSubmitAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("UpdateAppliedRaceResult");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceId = abs(intval($this->request->RaceId));
            //胜方
            $Winner = abs(intval($this->request->Winner));
            //获取比赛信息
            $RaceInfo = $this->oUserRace->getAppliedRace($RaceId);
            //胜方
            $Force = isset($this->request->Force)?abs(intval($this->request->Force)):0;
            //如果获取到
            if($RaceInfo['RaceId'])
            {
                //更新比赛结果
                $update = $this->oUserRace->updateAppliedUserRaceResult(array("RaceId"=>$RaceInfo['RaceId'],"WinnerUser"=>$Winner,"manager"=>$this->manager->id,"Force"=>$Force));
            }
            //返回之前的页面
            $this->response->goBack();
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
}
