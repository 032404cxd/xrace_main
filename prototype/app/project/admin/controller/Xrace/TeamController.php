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
			//页面参数预处理
			$params['RaceTeamName'] = urldecode(trim($this->request->RaceTeamName))?substr(urldecode(trim($this->request->RaceTeamName)),0,8):"";
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 5;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户列表
			$RaceTeamList = $this->oTeam->getRaceTeamList($params);
			//导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/team','team.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/user','index',$params)."&Page=~page~";
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
			$bind['comment'] = json_encode($bind['comment']);
			//插入数据
			$res = $this->oTeam->insertRaceTeamInfo($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
}
