<?php
/**
 * 场地管理
 * @author Chen<cxd032404@hotmail.com>
 */

class Xrace_ArenaController extends AbstractController
{
	/**场地:Arena
	 * @var string
	 */
	protected $sign = '?ctl=xrace/arena';
	/**
	 * game对象
	 * @var object
	 */
	protected $oArena;

	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oArena = new Xrace_Arena();

	}
	//场地配置列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//获取场地列表
			$ArenaList = $this->oArena->getAllArenaList();
			foreach($ArenaList as $ArenaId => $ArenaInfo)
            {
                $ArenaList[$ArenaId]['RaceTimeListUrl'] = 	"<a href='".Base_Common::getUrl('','xrace/arena','arena.race.time.list',array('ArenaId'=>$ArenaId)) ."'>可选时间段</a>";
            }
			//渲染模版
			include $this->tpl('Xrace_Arena_ArenaList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加场地填写配置页面
	public function arenaAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ArenaInsert");
		if($PermissionCheck['return'])
		{
			//渲染模版
			include $this->tpl('Xrace_Arena_ArenaAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新场地
	public function arenaInsertAction()
	{
		//检查权限
		$bind=$this->request->from('ArenaName');
		//场地名称不能为空
		if(trim($bind['ArenaName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//添加场地
			$res = $this->oArena->insertArena($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//修改场地信息页面
	public function arenaModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
		if($PermissionCheck['return'])
		{
			//场地ID
			$ArenaId = intval($this->request->ArenaId);
			//获取场地信息
			$ArenaInfo = $this->oArena->getArena($ArenaId,'*');
            //渲染模版
			include $this->tpl('Xrace_Arena_ArenaModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新场地信息
	public function arenaUpdateAction()
	{
		//接收页面参数
		$bind=$this->request->from('ArenaId','ArenaName');
        //场地名称不能为空
		if(trim($bind['ArenaName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//修改场地
			$res = $this->oArena->updateArena($bind['ArenaId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//删除场地
	public function arenaDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ArenaDelete");
		if($PermissionCheck['return'])
		{
			//场地ID
			$ArenaId = trim($this->request->ArenaId);
			//删除场地
			$this->oArena->deleteArena($ArenaId);
			//返回之前的页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
    //场地比赛时间段列表页面
    public function arenaRaceTimeListAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
        if($PermissionCheck['return'])
        {
            //场地ID
            $ArenaId = intval($this->request->ArenaId);
            //获取场地信息
            $ArenaInfo = $this->oArena->getArena($ArenaId,'*');
            //数据解包
            $ArenaInfo['comment'] = json_decode($ArenaInfo['comment'],true);
            //获取每日列表
            $WeekdayList = $this->oArena->getWeekdayList();
            foreach($ArenaInfo['comment']['RaceTimeList'] as  $tid => $TimeInfo)
            {
                //分解时间
                $ArenaInfo['comment']['RaceTimeList'][$tid]['StartHour'] = sprintf("%02d",intval($TimeInfo['StartTime']/3600));
                $ArenaInfo['comment']['RaceTimeList'][$tid]['StartMinute'] = sprintf("%02d",intval(($TimeInfo['StartTime']%3600)/60));
                //分解时间
                $ArenaInfo['comment']['RaceTimeList'][$tid]['EndHour'] = sprintf("%02d",intval($TimeInfo['EndTime']/3600));
                $ArenaInfo['comment']['RaceTimeList'][$tid]['EndMinute'] = sprintf("%02d",intval($TimeInfo['EndTime']%3600/60-1));
            }
            //返回页面
            $returnUrl =  "<a href='".Base_Common::getUrl('','xrace/arena','index',array()) ."'>返回</a>";
            //渲染模版
            include $this->tpl('Xrace_Arena_ArenaRaceTimeList');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //场地比赛时间段列表页面
    public function arenaRaceTimeAddAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
        if($PermissionCheck['return'])
        {
            //场地ID
            $ArenaId = intval($this->request->ArenaId);
            //获取场地信息
            $ArenaInfo = $this->oArena->getArena($ArenaId,'*');
            $HourList = array();
            $MinuteList = array();
            for($i=0;$i<24;$i++)
            {
                $HourList[] = sprintf("%02d",$i);
            }
            for($i=0;$i<60;$i++)
            {
                $MinuteList[] = sprintf("%02d",$i);
            }
            //获取每日列表
            $WeekdayList = $this->oArena->getWeekdayList();
            //数据解包
            $ArenaInfo['comment'] = json_decode($ArenaInfo['comment'],true);
            //返回页面
            $returnUrl =  "<a href='".Base_Common::getUrl('','xrace/arena','index',array()) ."'>返回</a>";
            //渲染模版
            include $this->tpl('Xrace_Arena_ArenaRaceTimeAdd');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //场地比赛时间段列表页面
    public function arenaRaceTimeInsertAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
        if($PermissionCheck['return'])
        {
            //页面参数
            $bind=$this->request->from('ArenaId','name','StartHour','StageMinute','EndHour','EndMinute','WeekdayList');
            $WeekdayList = $this->oArena->getWeekdayList();
            //初始化空的时间数组
            $TimeInfo = array();
            //循环每日列表
            foreach($bind['WeekdayList'] as $day => $select)
            {
                //如果选定的每日列表有效
                if(isset($WeekdayList[$day]))
                {
                    //保留
                    $TimeInfo['Weekday'][] = $day;
                }
            }
            //如果有选定至少一个有效的天
            if(count($TimeInfo['Weekday'])>0)
            {
                //保存数据
                $TimeInfo['name'] = trim(urldecode($bind['name']));
                $TimeInfo['StartTime'] = intval($bind['StartHour'])*3600+intval($bind['StartMinute'])*60;
                $TimeInfo['EndTime'] = intval($bind['EndHour'])*3600+intval($bind['StartMinute'])*60+60;
                //获取场地信息
                $ArenaInfo = $this->oArena->getArena($bind['ArenaId'],'ArenaId,comment');
                //数据解包
                $ArenaInfo['comment'] = json_decode($ArenaInfo['comment'],true);
                //如果没有则初始化数组
                $ArenaInfo['comment']['RaceTimeList'] = isset($ArenaInfo['comment']['RaceTimeList'])?$ArenaInfo['comment']['RaceTimeList']:array();
                //保存到指定位置
                $ArenaInfo['comment']['RaceTimeList'][count($ArenaInfo['comment']['RaceTimeList'])] = $TimeInfo;
                //数据打包
                $ArenaInfo['comment'] = json_encode($ArenaInfo['comment']);
                //修改场地
                $res = $this->oArena->updateArena($bind['ArenaId'],$ArenaInfo);
                $response = $res ? array('errno' => 0) : array('errno' => 9);
            }
            else
            {
                $response = array('errno' => 1);
            }
            echo json_encode($response);
            return true;
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //场地比赛时间段列表页面
    public function arenaRaceTimeModifyAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
        if($PermissionCheck['return'])
        {
            //场地ID
            $ArenaId = intval($this->request->ArenaId);
            //场地ID
            $id = intval($this->request->id);
            //获取场地信息
            $ArenaInfo = $this->oArena->getArena($ArenaId,'*');
            $HourList = array();
            $MinuteList = array();
            for($i=0;$i<24;$i++)
            {
                $HourList[] = sprintf("%02d",$i);
            }
            for($i=0;$i<60;$i++)
            {
                $MinuteList[] = sprintf("%02d",$i);
            }
            //获取每日列表
            $WeekdayList = $this->oArena->getWeekdayList();
            //数据解包
            $ArenaInfo['comment'] = json_decode($ArenaInfo['comment'],true);
            //获取时间段数据
            $TimeInfo = $ArenaInfo['comment']['RaceTimeList'][$id];
            //分解时间
            $TimeInfo['StartHour'] = sprintf("%02d",intval($TimeInfo['StartTime']/3600));
            $TimeInfo['StartMinute'] = sprintf("%02d",intval($TimeInfo['StartTime']%3600/60));
            //分解时间
            $TimeInfo['EndHour'] = sprintf("%02d",intval($TimeInfo['EndTime']/3600));
            $TimeInfo['EndMinute'] = sprintf("%02d",intval($TimeInfo['EndTime']%3600/60-1));
            //返回页面
            $returnUrl =  "<a href='".Base_Common::getUrl('','xrace/arena','index',array()) ."'>返回</a>";
            //渲染模版
            include $this->tpl('Xrace_Arena_ArenaRaceTimeModify');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //场地比赛时间段列表页面
    public function arenaRaceTimeUpdateAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
        if($PermissionCheck['return'])
        {
            //页面参数
            $bind=$this->request->from('ArenaId','id','name','StartHour','StartMinute','EndHour','EndMinute','WeekdayList');
            $WeekdayList = $this->oArena->getWeekdayList();
            //获取场地信息
            $ArenaInfo = $this->oArena->getArena(intval($bind['ArenaId']),'*');
            //数据解包
            $ArenaInfo['comment'] = json_decode($ArenaInfo['comment'],true);
            //获取时间段数据
            $TimeInfo = $ArenaInfo['comment']['RaceTimeList'][intval($bind['id'])];
            //初始化空的每日数组
            $TimeInfo['Weekday'] = array();
            //循环每日列表
            foreach($bind['WeekdayList'] as $day => $select)
            {
                //如果选定的每日列表有效
                if(isset($WeekdayList[$day]))
                {
                    //保留
                    $TimeInfo['Weekday'][] = $day;
                }
            }
            //如果有选定至少一个有效的天
            if(count($TimeInfo['Weekday'])>0)
            {
                //保存数据
                $TimeInfo['name'] = trim(urldecode($bind['name']));
                $TimeInfo['StartTime'] = intval($bind['StartHour'])*3600+intval($bind['StartMinute'])*60;
                $TimeInfo['EndTime'] = intval($bind['EndHour'])*3600+intval($bind['EndMinute'])*60+60;
                //保存到指定位置
                $ArenaInfo['comment']['RaceTimeList'][intval($bind['id'])] = $TimeInfo;
                //数据打包
                $ArenaInfo['comment'] = json_encode($ArenaInfo['comment']);
                //修改场地
                $res = $this->oArena->updateArena($bind['ArenaId'],$ArenaInfo);
                $response = $res ? array('errno' => 0) : array('errno' => 9);
            }
            else
            {
                $response = array('errno' => 1);
            }
            echo json_encode($response);
            return true;
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //场地比赛时间段列表页面
    public function arenaRaceTimeDeleteAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("ArenaModify");
        if($PermissionCheck['return'])
        {
            //页面参数
            $bind=$this->request->from('ArenaId','id');
            $WeekdayList = $this->oArena->getWeekdayList();
            //获取场地信息
            $ArenaInfo = $this->oArena->getArena(intval($bind['ArenaId']),'*');
            //数据解包
            $ArenaInfo['comment'] = json_decode($ArenaInfo['comment'],true);
            //如果数据存在
            if(isset($ArenaInfo['comment']['RaceTimeList'][intval($bind['id'])]))
            {
                //删除数据
                unset($ArenaInfo['comment']['RaceTimeList'][intval($bind['id'])]);
                //数组重新排序
                $ArenaInfo['comment']['RaceTimeList'] = array_values($ArenaInfo['comment']['RaceTimeList']);
                //数据打包
                $ArenaInfo['comment'] = json_encode($ArenaInfo['comment']);
                //修改场地
                $this->oArena->updateArena($bind['ArenaId'],$ArenaInfo);
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
}
