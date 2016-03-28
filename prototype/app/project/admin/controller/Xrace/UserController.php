<?php
/**用户管理*/

class Xrace_UserController extends AbstractController
{
	/**用户管理相关:User
	 * 权限限制  ?ctl=xrace/user
	 * @var string
	 */
	protected $sign = '?ctl=xrace/user';
	/**
	 * game对象
	 * @var object
	 */
	protected $oUser;

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
			//获取性别列表
			$SexList = $this->oUser->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oUser->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			//页面参数预处理
			$params['Sex'] = isset($SexList[intval($this->request->Sex)])?intval($this->request->Sex):0;
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,8):"";
			$params['NickName'] = urldecode(trim($this->request->NickName))?substr(urldecode(trim($this->request->NickName)),0,8):"";
			$params['AuthStatus'] = isset($AuthStatusList[$this->request->AuthStatus])?intval($this->request->AuthStatus):-1;
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 5;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//获取用户列表
			$UserList = $this->oUser->getUserLst($params);
			//导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/user','user.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/user','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($UserList['UserCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			foreach($UserList['UserList'] as $UserId => $UserInfo)
			{
				//用户性别
				$UserList['UserList'][$UserId]['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
				//实名认证状态
				$UserList['UserList'][$UserId]['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";
				//如果当前已经认证，则同时拼接上认证的证件类型
				$UserList['UserList'][$UserId]['AuthStatus'] = ($UserInfo['auth_state'] == 2 && isset($AuthIdTypesList[intval($UserInfo['id_type'])]))?$UserList['UserList'][$UserId]['AuthStatus']."/".$AuthIdTypesList[intval($UserInfo['id_type'])]:$UserList['UserList'][$UserId]['AuthStatus'];
				//用户生日
				$UserList['UserList'][$UserId]['Birthday'] = is_null($UserInfo['birth_day'])?"未知":$UserInfo['birth_day'];
                                //用户执照
				$UserList['UserList'][$UserId]['License'] = "<a href='".Base_Common::getUrl('','xrace/user','license.list',array('UserId'=>$UserId)) ."'>执照</a>";
			}
			//模板渲染
			include $this->tpl('Xrace_User_UserList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//用户列表下载
	public function userListDownloadAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("UserListDownload");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oUser->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oUser->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oUser->getAuthIdType();

			//页面参数预处理
			$params['Sex'] = isset($SexList[intval($this->request->Sex)])?intval($this->request->Sex):0;
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,8):"";
			$params['NickName'] = urldecode(trim($this->request->NickName))?substr(urldecode(trim($this->request->NickName)),0,8):"";
			$params['AuthStatus'] = isset($AuthStatusList[$this->request->AuthStatus])?intval($this->request->AuthStatus):-1;

			//分页参数
			$params['PageSize'] = 500;

			$oExcel = new Third_Excel();
			$FileName= ($this->manager->name().'用户列表');
			$oExcel->download($FileName)->addSheet('用户');
			//标题栏
			$title = array("用户ID","微信openId","姓名","昵称","性别","出生年月","实名认证状态");
			$oExcel->addRows(array($title));
			$Count = 1;$params['Page'] =1;
			do
			{
				$UserList = $this->oUser->getUserLst($params);
				$Count = count($UserList['UserList']);
				foreach($UserList['UserList'] as $UserId => $UserInfo)
				{
					//生成单行数据
					$t = array();
					$t['user_id'] = $UserInfo['user_id'];
					$t['open_wx_id'] = $UserInfo['wx_open_id'];
					$t['open_wx_id'] = $UserInfo['wx_open_id'];
					$t['name'] = $UserInfo['name'];
					$t['nick_name'] = $UserInfo['nick_name'];
					$t['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
					$t['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";

					$oExcel->addRows(array($t));
					unset($t);
				}
				$params['Page']++;
				$oExcel->closeSheet()->close();
			}
			while($Count>0);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//用户列表下载
	public function userDetailAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("UserListDownload");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oUser->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oUser->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			$UserId = trim($this->request->UserId);
			//获取用户信息
			$UserInfo = $this->oUser->getUserInfo($UserId);
			//用户性别
			$UserInfo['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
			//实名认证状态
			$UserInfo['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";
			//证件有效期
			$UserInfo['AuthExpireDate'] = !is_null($UserInfo['expire_day'])?$UserInfo['expire_day']:"未知";
			//证件有效期
			$UserInfo['Birthday'] = !is_null($UserInfo['birth_day'])?$UserInfo['birth_day']:"未知";
			//用户头像
			$UserInfo['thumb'] = urldecode($UserInfo['thumb']);
			//实名认证证件类型
			$UserInfo['AuthIdType'] = isset($AuthIdTypesList[intval($UserInfo['id_type'])])?$AuthIdTypesList[intval($UserInfo['id_type'])]:"未知";
			//获取用户实名认证记录
			$UserInfo['UserAuthLog'] = $this->oUser->getUserAuthLog($UserId,'submit_time,op_time,op_uid,auth_result,auth_resp');
			if(count($UserInfo['UserAuthLog']))
			{
				//初始化一个空的后台管理员列表
				$ManagerList = array();
				//获取实名认证记录的状态列表
				$AuthLogIdStatusList = $this->oUser->getAuthLogStatusTypeList();
				foreach($UserInfo['UserAuthLog'] as $LogId => $AuthLog)
				{
					// 如果管理员记录已经获取到
					if(isset($ManagerList[$AuthLog['op_uid']]))
					{
						$ManagerInfo = $ManagerList[$AuthLog['op_uid']];
					}
					//否则重新获取
					else
					{
						$ManagerInfo = $this->manager->get($AuthLog['op_uid'], "name");
					}
					//记录管理员账号
					$UserInfo['UserAuthLog'][$LogId]['ManagerName'] = $ManagerInfo['name'];
					//认证结果
					$UserInfo['UserAuthLog'][$LogId]['AuthResult'] = $AuthLogIdStatusList[$AuthLog['auth_result']];
				}
			}
			//渲染模板
			include $this->tpl('Xrace_User_UserDetail');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//用户实名认证信息
	public function userAuthInfoAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("UserAuth");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oUser->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oUser->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			$UserId = trim($this->request->UserId);
			//获取用户信息
			$UserInfo = $this->oUser->getUserInfo($UserId);
			//用户头像
			$UserInfo['thumb'] = urldecode($UserInfo['thumb']);
			//实名认证状态
			$UserInfo['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";
			//用户性别
			$UserInfo['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
			//获取用户实名认证状态记录
			$UserAuthInfo = $this->oUser->getUserAuthInfo($UserId);
			//实名认证提交时间
			$UserAuthInfo['submit_time'] = isset($UserAuthInfo['submit_time'])?urldecode($UserAuthInfo['submit_time']):"未知";
			//实名认证提交的照片
			$UserAuthInfo['submit_img1'] = isset($UserAuthInfo['submit_img1'])?urldecode($UserAuthInfo['submit_img1']):"";
			$UserAuthInfo['submit_img2'] = isset($UserAuthInfo['submit_img2'])?urldecode($UserAuthInfo['submit_img2']):"";
			//模板渲染
			include $this->tpl('Xrace_User_UserAuth');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//用户实名认证信息
	public function userAuthSubmitAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("UserAuth");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oUser->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oUser->getAuthStatus("submit");
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			$UserId = trim($this->request->UserId);
			//获取用户信息
			$UserInfo = $this->oUser->getUserInfo($UserId);
			//用户生日
			$UserInfo['birth_day'] =  is_null($UserInfo['birth_day'])?date("Y-m-d",time()):$UserInfo['birth_day'];
			//证件有效期
			$UserInfo['expire_day'] =  is_null($UserInfo['expire_day'])?date("Y-m-d",time()):$UserInfo['expire_day'];
			//获取用户实名认证状态记录
			$UserAuthInfo = $this->oUser->getUserAuthInfo($UserId);
			//模板渲染
			include $this->tpl('Xrace_User_UserAuthSubmit');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//用户实名认证信息
	public function userAuthAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("UserAuth");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oUser->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oUser->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oUser->getAuthIdType("submit");
			//批量获取页面参数
			$AuthInfo=$this->request->from('UserId','UserRealName','UserSex','UserAuthStatus','UserAuthIdType','UserAuthIdNo','UserBirthDay','UserAuthReason','UserAuthExpireDay');
			$UserId = trim($this->request->UserId);
			//页面参数预处理
			$UserInfo['sex'] = isset($SexList[intval($AuthInfo['UserSex'])])?intval($AuthInfo['UserSex']):0;
			$UserInfo['id_type'] = isset($AuthIdTypesList[intval($AuthInfo['UserAuthIdType'])])?intval($AuthInfo['UserAuthIdType']):0;
			$UserInfo['auth_state'] = isset($AuthStatusList[intval($AuthInfo['UserAuthStatus'])])?intval($AuthInfo['UserAuthStatus']):0;
			$UserInfo['id_number'] = substr(strtoupper(trim($AuthInfo['UserAuthIdNo'])),0,30);
			$UserInfo['birth_day'] = $AuthInfo['UserBirthDay'];
			$UserInfo['expire_day'] = $AuthInfo['UserAuthExpireDay'];
			$UserAuthInfo['auth_resp'] = substr((trim(urldecode($AuthInfo['UserAuthReason']))),0,30);
			//执行操作的管理员ID
			$UserAuthInfo['op_uid'] = $this->manager->id;
			//通过认证要求选择性别
			if($UserInfo['auth_state'] == 2 && $UserInfo['sex']== 0)
			{
				$response = array('errno' => 2);
			}
			//通过认证要求选择证件类型
			elseif($UserInfo['auth_state'] == 2 && $UserInfo['id_type']== 0)
			{
				$response = array('errno' => 3);
			}
			//要求选择一个操作 通过/拒绝
			elseif( $UserInfo['auth_state']=="")
			{
				$response = array('errno' => 4);
			}
			//通过认证要求填写证件号码
			elseif($UserInfo['auth_state'] == 2 && $UserInfo['id_number']=="" )
			{
				$response = array('errno' => 5);
			}
			//通过认证要求填写有效的生日 不小于 今天
			elseif($UserInfo['auth_state'] == 2 && strtotime($UserInfo['birth_day']) < time())
			{
				$response = array('errno' => 6);
			}
			//通过认证要求填写有效的证件有效期 不小于 今天
			elseif($UserInfo['auth_state'] == 2 && strtotime($UserInfo['expire_day']) < time())
			{
				$response = array('errno' => 7);
			}
			//拒绝认证需要填写理由
			elseif($UserInfo['auth_state'] == 2 && $UserAuthInfo['auth_resp'] == "")
			{
				$response = array('errno' => 8);
			}
			else
			{
				//获取用户信息
				$User = $this->oUser->getUserInfo($UserId);
				//未获取到用户信息
				if(!isset($User['user_id']))
				{
					$response = array('errno' => 1);
				}
				//如果用户已经被实名认证
				elseif($User['auth_state'] == 2)
				{
					$response = array('errno' => 10);
				}
				else
				{
					//执行认证操作
					if($UserInfo['auth_state'] == 2)
					{
						$Auth = $this->oUser->UserAuth($UserId,$UserInfo,$UserAuthInfo);
						$response = $Auth ? array('errno' => 0) : array('errno' => 9);
					}
					//执行认证拒绝操作
					elseif($UserInfo['auth_state'] == 0)
					{
						//只保存认证状态
						$UInfo = array('auth_state'=>$UserInfo['auth_state']);
						$Auth = $this->oUser->UserUnAuth($UserId,$UInfo,$UserAuthInfo);
						$response = $Auth ? array('errno' => 0) : array('errno' => 9);
					}
				}
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
	//实名认证记录
	public function authLogAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//页面参数预处理
			$params['StartDate'] = isset($this->request->StartDate)?substr(strtoupper(trim($this->request->StartDate)),0,10):date("Y-m-d",time());
			$params['EndDate'] = isset($this->request->EndDate)?substr(strtoupper(trim($this->request->EndDate)),0,10):date("Y-m-d",time());
			$params['AuthResult'] = isset($this->request->AuthResult)?substr(strtoupper(trim($this->request->AuthResult)),0,8):"";
			$params['ManagerId'] = isset($this->request->ManagerId)?intval($this->request->ManagerId):0;

			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 2;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;

			//获取实名认证记录的状态列表
			$AuthLogIdStatusList = $this->oUser->getAuthLogStatusTypeList();
			//获取所有管理员列表
			$ManagerList = $this->manager->getAll('id,name');
			//获取实名认证记录
			$AuthLog = $this->oUser->getAuthLog($params);
			//导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/user','auth.log.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/user','auth.log',$params)."&Page=~page~";
			$page_content =  base_common::multi($AuthLog['AuthLogCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//初始化一个空的用户数组
			$UserList = array();
			foreach($AuthLog['AuthLog'] as $AuthId => $LogInfo)
			{
				//管理员账号
				$AuthLog['AuthLog'][$AuthId]['ManagerName'] = isset($ManagerList[$LogInfo['op_uid']])? $ManagerList[$LogInfo['op_uid']]['name']:"未知";
				$AuthLog['AuthLog'][$AuthId]['AuthResultName'] = isset($AuthLogIdStatusList[$LogInfo['auth_result']])?$AuthLogIdStatusList[$LogInfo['auth_result']]:"未知";
				// 如果管理员记录已经获取到
				if(isset($UserList[$LogInfo['user_id']]))
				{
					$ManagerInfo = $UserList[$LogInfo['user_id']];
				}
				//否则重新获取
				else
				{
					$ManagerInfo = $this->oUser->getUserInfo($LogInfo['user_id'], "name");
				}
				$AuthLog['AuthLog'][$AuthId]['UserName'] = $ManagerInfo['name'];
				//实名认证提交的照片
				$AuthLog['AuthLog'][$AuthId]['submit_img1'] = isset($AuthLog['AuthLog'][$AuthId]['submit_img1'])?urldecode($AuthLog['AuthLog'][$AuthId]['submit_img1']):"";
				$AuthLog['AuthLog'][$AuthId]['submit_img2'] = isset($AuthLog['AuthLog'][$AuthId]['submit_img2'])?urldecode($AuthLog['AuthLog'][$AuthId]['submit_img2']):"";
			}
			//模板渲染
			include $this->tpl('Xrace_User_AuthLog');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
        
	//用户执照列表
	public function licenseListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//获得需要添加执照的用户ID
			$UserId = trim($this->request->UserId);
			//获取用户信息
			$UserInfo = $this->oUser->getUserInfo($UserId,"user_id,name");
			$params = array('UserId' => $UserId);
			//获得用户执照列表
			$UserLicenseList = $this->oUser->getUserLicenseList($params,array('UserId','RaceCatalogId','RaceGroupId','LicenseId','LicenseStartDate','LicenseEndDate','ManagerId','LicenseAddTime','LastUpdateTime','LicenseStatus'));
			//获得用户执照状态列表
			$UserLicenseStatusList = $this->oUser->getUserLicenseStatusList();
			//循环拼接用户执照显示数组
			foreach($UserLicenseList['UserLicenseList'] as $UserLicenseId => $UserLicenseInfo)
			{
				if($UserLicenseInfo['LicenseStatus'] == 4)
				{

				}
				//如果执照结束时间小于当前时间,更新执照状态为过期
				elseif (strtotime(trim($UserLicenseInfo['LicenseStartDate'])) > time())
				{
					$UserLicenseInfo['LicenseStatus'] = 3;
				}
				elseif(strtotime(trim($UserLicenseInfo['LicenseStartDate'])) <= time() && (strtotime(trim($UserLicenseInfo['LicenseEndDate']))+86400) >= time())
				{
					$UserLicenseInfo['LicenseStatus'] = 1;
				}
				elseif((strtotime(trim($UserLicenseInfo['LicenseEndDate']))+86400) < time())
				{
					$UserLicenseInfo['LicenseStatus'] = 2;
				}
				$UserLicenseList['UserLicenseList'][$UserLicenseId]['LicenseStatusName'] = $UserLicenseStatusList[$UserLicenseInfo['LicenseStatus']];
				//获得赛组信息
				$RaceGroupInfo = $this->oRace->getRaceGroup($UserLicenseInfo['RaceGroupId'], 'RaceGroupName');
				//获得赛组名称
				$UserLicenseList['UserLicenseList'][$UserLicenseId]['RaceGroupName'] = $RaceGroupInfo['RaceGroupName'];
				//获得赛组信息
				$RaceCatalogInfo = $this->oRace->getRaceCatalog($UserLicenseInfo['RaceCatalogId'], 'RaceCatalogName');
				//获得赛组名称
				$UserLicenseList['UserLicenseList'][$UserLicenseId]['RaceCatalogName'] = $RaceCatalogInfo['RaceCatalogName'];
				//获得管理员信息
				$UserLicenseList['UserLicenseList'][$UserLicenseId]['ManagerName'] = $this->oManager->getOne($UserLicenseInfo['ManagerId'], 'name');
				//获得用户执照状态名称
				$UserLicenseList['UserLicenseList'][$UserLicenseId]['LicenseStatusName'] = $this->oUser->getUserLicenseStatus($UserLicenseInfo);
				//复写执照状态
				$UserLicenseList['UserLicenseList'][$UserLicenseId]['LicenseStatus'] = $UserLicenseInfo['LicenseStatus'];
			}
			//模板渲染
			include $this->tpl('Xrace_User_LicenseList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}

	//执照添加页面
	public function licenseAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseInsert");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList('RaceCatalogId,RaceCatalogName');
			//获得需要添加执照的用户ID
			$UserId = trim($this->request->UserId);
			//模板渲染
			include $this->tpl('Xrace_User_LicenseAdd');
		}
		else
	   {
			$home = $this->sign;
			include $this->tpl('403');
	   }
	}

	//执照添加操作
	public function licenseInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseInsert");
		if($PermissionCheck['return'])
		{
			//获取当前时间
			$currentTime = time();
			//获取 页面参数
			$bind=$this->request->from('UserId','RaceCatalogId','RaceGroupId','LicenseStartDate','LicenseEndDate','comment');
			//获取管理员ID
			$bind['ManagerId'] = $this->manager->id;
			//获得执照添加时间
			$bind['LicenseAddTime'] = date('Y-m-d H:i:s',$currentTime) ;
			//获得执照更新时间
			$bind['LastUpdateTime'] = date('Y-m-d H:i:s',$currentTime) ;
			//转化时间为时间戳
			$LicenseStartDate = strtotime(trim($bind['LicenseStartDate']));
			$LicenseEndDate = strtotime(trim($bind['LicenseEndDate']));
			//执照所属分组不能为空
			if(intval($bind['RaceGroupId'])<=0)
			{
				$response = array('errno' => 1);
			}
			//执照结束时间不能小于当前时间
			elseif(strtotime($bind["LicenseEndDate"]) < $currentTime)
			{
				$response = array('errno' => 2);
			}
			//执照开始时间不能小于执照结束时间
			elseif((strtotime($bind["LicenseStartDate"])+86400)  > strtotime($bind["LicenseEndDate"]) )
			{
				$response = array('errno' => 3);
			}
			//添加的执照的理由不能为空
			elseif(trim($bind['comment'])=="")
			{
				$response = array('errno' => 4);
			}
			else
			{
				//获取执照状态
				$bind['LicenseStatus'] = $currentTime > strtotime($bind['LicenseStartDate'])?1:2;
				//初始化压缩数组
				$bind['comment'] = array('LicenseUpdateLog'=>array('0' => array("action"=>"add","time" => date('Y-m-d H:i:s',time()),"reason"=>trim($bind['comment']),'manager'=>$this->manager->id)));
				//数据压缩
				$bind['comment'] = json_encode($bind['comment']);
				//查询同一用户在同赛组是否有重复的有效执照
				$params = array(
					'UserId' => $bind['UserId'],
					'GroupId' => $bind['GroupId'],
					//选中生效中 和 尚未生效的记录
					'LicenseStatus' => '1|3',
					//不选出时间有冲突的记录
					'ExceptionDate' => array("StartDate"=>$bind['LicenseStartDate'],"EndDate"=>$bind['LicenseEndDate']),
				);
				//获得在目标范围内有冲突的记录
				$UserLicenseCount = $this->oUser->getUserLicenseCount($params);
				if($UserLicenseCount>=1)
				{
					$response = array('errno' => 5);
				}
				else
				{
					//添加执照数据
					$AddLicense = $this->oUser->insertUserLicense($bind);
					$response = $AddLicense ? array('errno' => 0) : array('errno' => 9);
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
	//执照修改页面
	public function licenseModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseModify");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList('RaceCatalogId,RaceCatalogName');
			//获得执照ID
			$LicenseId = trim($this->request->LicenseId);
			//获得执照信息
			$UserLicenseInfo = $this->oUser->getUserLicense($LicenseId,'*');
			//数据解包
			$UserLicenseInfo['comment'] = json_decode($UserLicenseInfo['comment'],true);
			//获得常用的操作符号列表
			$actionList = Base_Common::actionList();
			//初始化一个空的管理员列表
			$ManagerList = array();
			//循环操作记录
			foreach($UserLicenseInfo['comment']['LicenseUpdateLog'] as $key => $LogInfo)
			{
				$LogInfo['actionName'] = isset($actionList[$LogInfo['action']])?$actionList[$LogInfo['action']]:"未定义";
				// 如果管理员记录已经获取到
				if(isset($ManagerList[$LogInfo['ManagerId']]))
				{
					$ManagerInfo = $ManagerList[$LogInfo['ManagerId']];
				}
				//否则重新获取
				else
				{
					$ManagerInfo = $this->manager->get($LogInfo['ManagerId'], "name");
				}
				$UserLicenseInfo['comment']['LicenseUpdateLog'][$key]['LogText'] = $ManagerInfo['name']." 执行".$LogInfo['actionName']."操作<br>操作理由:".$LogInfo['reason'];
			}
			//所有赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($UserLicenseInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//模板渲染
			include $this->tpl('Xrace_User_LicenseModify');
		}
		else
	   {
			$home = $this->sign;
			include $this->tpl('403');
	   }
	}
	//执照修改操作
	public function licenseUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseModify");
		if($PermissionCheck['return'])
		{
			//获取当前时间
			$currentTime = time();
			//获取 页面参数
			$bind = $this->request->from('LicenseId', 'UserId', 'RaceCatalogId', 'RaceGroupId', 'LicenseStartDate', 'LicenseEndDate', 'comment');
			//获取管理员ID
			$bind['ManagerId'] = $this->manager->id;
			//获得执照更新时间
			$bind['LastUpdateTime'] = date("Y-m-d H:i:s",$currentTime);
			//转化时间为时间戳
			$LicenseStartDate = strtotime(trim($bind['LicenseStartDate']));
			$LicenseEndDate = strtotime(trim($bind['LicenseEndDate']));
			//获得执照ID
			$LicenseId = $bind['LicenseId'];
			//获得用户ID
			$UserId = $bind['UserId'];
			//执照ID和用户ID不用存表
			unset($bind['LicenseId']);
			unset($bind['UserId']);
			//执照所属分组不能为空
			if (intval($bind['RaceGroupId']) <= 0)
			{
				$response = array('errno' => 1);
			}
			//执照结束时间不能小于当前时间
			elseif (strtotime($bind["LicenseEndDate"]) < $currentTime)
			{
				$response = array('errno' => 2);
			}
			//执照开始时间不能小于执照结束时间
			elseif (strtotime($bind["LicenseStartDate"]) > strtotime($bind["LicenseEndDate"]))
			{
				$response = array('errno' => 3);
			}
			//修改的执照的理由不能为空
			elseif (trim($bind['comment']) == "")
			{
				$response = array('errno' => 4);
			}
			else
			{
				//查询同一用户在同赛组是否有重复的有效执照
				$params = array(
					'UserId' => $UserId,
					'GroupId' => $bind['RaceGroupId'],
					//排除记录本身
					'ExceptionId' => $LicenseId,
					//选中生效中 和 尚未生效的记录
					'LicenseStatus' => '1|3',
					//不选出时间有冲突的记录
					'ExceptionDate' => array("StartDate"=>$bind['LicenseStartDate'],"EndDate"=>$bind['LicenseEndDate']),
					//不选出已删除的记录
					'ExceptionStatus' => 4,
				);
				//获得在目标范围内有冲突的记录
				$UserLicenseCount = $this->oUser->getUserLicenseCount($params);
				if ($UserLicenseCount >= 1)
				{
					$response = array('errno' => 5);
				}
				else
				{
					//获得执照信息
					$UserLicenseInfo = $this->oUser->getUserLicense($LicenseId,'*');
					//数据解包
					$UserLicenseInfo['comment'] = json_decode($UserLicenseInfo['comment'],true);
					$UserLicenseInfo['comment']['LicenseUpdateLog'][count($UserLicenseInfo['comment']['LicenseUpdateLog'])] = array("action" => "update", "time" => date('Y-m-d H:i:s', time()), "reason" => trim($bind['comment']), 'manager' => $this->manager->id);
					$bind['comment'] = $UserLicenseInfo['comment'];
					//封装理由的数据
					$bind['comment'] = json_encode($bind['comment']);
					//更新执照数据
					$UpdateLicense = $this->oUser->updateUserLicense($LicenseId,$bind);
					$response = $UpdateLicense ? array('errno' => 0) : array('errno' => 9);
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

	//执照删除界面
	public function licenseUnsetAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseDelete");
		if($PermissionCheck['return'])
		{
			//获得执照ID
			$LicenseId = trim($this->request->LicenseId);
			//获得执照信息
			$UserLicenseInfo = $this->oUser->getUserLicense($LicenseId,'*');
			$RaceCatalogInfo = $this->oRace->getRaceCatalog($UserLicenseInfo['RaceCatalogId'],'RaceCatalogId,RaceCatalogName');
			$UserLicenseInfo['RaceCatalogName'] = $RaceCatalogInfo['RaceCatalogName'];
			$RaceGroupInfo = $this->oRace->getRaceGroup($UserLicenseInfo['RaceGroupId'],'RaceGroupId,RaceGroupName');
			$UserLicenseInfo['RaceGroupName'] = $RaceGroupInfo['RaceGroupName'];
			//数据解包
			$UserLicenseInfo['comment'] = json_decode($UserLicenseInfo['comment'],true);
			//获得常用的操作符号列表
			$actionList = Base_Common::actionList();
			//初始化一个空的管理员列表
			$ManagerList = array();
			//循环操作记录
			foreach($UserLicenseInfo['comment']['LicenseUpdateLog'] as $key => $LogInfo)
			{
				$LogInfo['actionName'] = isset($actionList[$LogInfo['action']])?$actionList[$LogInfo['action']]:"未定义";
				// 如果管理员记录已经获取到
				if(isset($ManagerList[$LogInfo['ManagerId']]))
				{
					$ManagerInfo = $ManagerList[$LogInfo['ManagerId']];
				}
				//否则重新获取
				else
				{
					$ManagerInfo = $this->manager->get($LogInfo['ManagerId'], "name");
				}
				$UserLicenseInfo['comment']['LicenseUpdateLog'][$key]['LogText'] = $ManagerInfo['name']." 执行".$LogInfo['actionName']."操作<br>操作理由:".$LogInfo['reason'];
			}
			//模板渲染
			include $this->tpl('Xrace_User_LicenseUnset');
		}
		else
	   {
			$home = $this->sign;
			include $this->tpl('403');
	   }
	}

	//执照删除操作
	public function licenseDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseDelete");
		if($PermissionCheck['return'])
		{
			//获取 页面参数
			$bind = $this->request->from('LicenseId','comment');
			//获得执照ID
			$LicenseId = $bind['LicenseId'];
			//执照ID和用户ID不用存表
			unset($bind['LicenseId']);
			//删除执照变更执照状态
			$bind['LicenseStatus'] = 4;
			//修改的执照的理由不能为空
			if (trim($bind['comment']) == "")
			{
				$response = array('errno' => 1);
			}
			else
			{
				//获得执照信息
				$UserLicenseInfo = $this->oUser->getUserLicense($LicenseId,'*');
				//数据解包
				$UserLicenseInfo['comment'] = json_decode($UserLicenseInfo['comment'],true);
				//解析执照已有的理由
				$UserLicenseInfo['comment']['LicenseUpdateLog'][count($UserLicenseInfo['comment']['LicenseUpdateLog'])] = array("action" => "delete", "time" => date('Y-m-d H:i:s', time()), "reason" => trim($bind['comment']), 'manager' => $this->manager->id);
				$bind['comment'] = $UserLicenseInfo['comment'];
				//封装数据
				$bind['comment'] = json_encode($bind['comment']);
				//删除执照 更新执照数据
				$UpdateLicense = $this->oUser->updateUserLicense($LicenseId,$bind);
				$response = $UpdateLicense ? array('errno' => 0) : array('errno' => 9);
			}
			echo json_encode($response);
		}
		else
	   {
			$home = $this->sign;
			include $this->tpl('403');
	   }
	}
	//执照修改页面
	public function licenseUpdateLogAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("LicenseModify");
		if($PermissionCheck['return'])
		{
			//获得执照ID
			$LicenseId = trim($this->request->LicenseId);
			//获得执照信息
			$UserLicenseInfo = $this->oUser->getUserLicense($LicenseId,'*');
			$RaceCatalogInfo = $this->oRace->getRaceCatalog($UserLicenseInfo['RaceCatalogId'],'RaceCatalogId,RaceCatalogName');
			$UserLicenseInfo['RaceCatalogName'] = $RaceCatalogInfo['RaceCatalogName'];
			$RaceGroupInfo = $this->oRace->getRaceGroup($UserLicenseInfo['RaceGroupId'],'RaceGroupId,RaceGroupName');
			$UserLicenseInfo['RaceGroupName'] = $RaceGroupInfo['RaceGroupName'];
			//数据解包
			$UserLicenseInfo['comment'] = json_decode($UserLicenseInfo['comment'],true);
			//获得常用的操作符号列表
			$actionList = Base_Common::actionList();
			//初始化一个空的管理员列表
			$ManagerList = array();
			//循环操作记录
			foreach($UserLicenseInfo['comment']['LicenseUpdateLog'] as $key => $LogInfo)
			{
				$LogInfo['actionName'] = isset($actionList[$LogInfo['action']])?$actionList[$LogInfo['action']]:"未定义";
				// 如果管理员记录已经获取到
				if(isset($ManagerList[$LogInfo['ManagerId']]))
				{
					$ManagerInfo = $ManagerList[$LogInfo['ManagerId']];
				}
				//否则重新获取
				else
				{
					$ManagerInfo = $this->manager->get($LogInfo['ManagerId'], "name");
				}
				$UserLicenseInfo['comment']['LicenseUpdateLog'][$key]['LogText'] = $ManagerInfo['name']." 执行".$LogInfo['actionName']."操作<br>操作理由:".$LogInfo['reason'];
			}
			//模板渲染
			include $this->tpl('Xrace_User_LicenseUpdateLog');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
