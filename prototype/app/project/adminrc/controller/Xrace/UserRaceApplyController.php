<?php
/**用户管理*/

class Xrace_UserRaceApplyController extends AbstractController
{
	/**用户管理相关:User
	 * @var string
	 */
	protected $sign = '?ctl=xrace/user.race.apply';
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
	//用户列表
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
            //获取场地列表
		    $ArenaList = $this->oArena->getAllArenaList("ArenaName,ArenaId");
            //页面参数预处理
			$params['ChipId'] = urldecode(trim($this->request->ChipId))?substr(urldecode(trim($this->request->ChipId)),0,8):"";
			$params['ArenaId'] = isset($this->request->ArenaId)?intval($this->request->ArenaId):-0;
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 10;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户约战队列
			$UserRaceApplyList = $this->oUserRace->getUserRaceApplyList($params);
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/user.race.apply','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($UserRaceApplyList['UserRaceApplyCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//初始化空的用户队列
			$UserList = array();
			foreach($UserRaceApplyList['UserRaceApplyList'] as $ApplyId => $ApplyInfo)
			{

			    //如果用户列表中未找到这个用户
			    if(!isset($UserList[$ApplyInfo['UserId']]))
                {
                    //获取用户信息
                    $UserList[$ApplyInfo['UserId']] = $this->oUserInfo->getUser($ApplyInfo['UserId'],"*");
                }

                //用户姓名
                $UserRaceApplyList['UserRaceApplyList'][$ApplyId]['Name'] = isset($UserList[$ApplyInfo['UserId']]['Name'])?$UserList[$ApplyInfo['UserId']]['Name']:"未知用户";
			    //场地名称
                $UserRaceApplyList['UserRaceApplyList'][$ApplyId]['ArenaName'] = isset($ArenaList[$ApplyInfo['ArenaId']])?$ArenaList[$ApplyInfo['ArenaId']]['ArenaName']:"未知场地";
			}
            //模板渲染
			include $this->tpl('Xrace_UserApplyRace_UserApplyRaceList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
