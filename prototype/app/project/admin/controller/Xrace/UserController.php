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
	protected $oSportsType;

	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oUser = new Xrace_User();

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
			$params['Sex'] = isset($SexList[strtoupper(trim($this->request->Sex))])?substr(strtoupper(trim($this->request->Sex)),0,8):"";
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,8):"";
			$params['NickName'] = urldecode(trim($this->request->NickName))?substr(urldecode(trim($this->request->NickName)),0,8):"";
			$params['AuthStatus'] = isset($AuthStatusList[strtoupper(trim($this->request->AuthStatus))])?substr(strtoupper(trim($this->request->AuthStatus)),0,8):"";

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
				$UserList['UserList'][$UserId]['AuthStatus'] = ($UserInfo['auth_state'] == "AUTHED" && isset($AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))]))?$UserList['UserList'][$UserId]['AuthStatus']."/".$AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))]:$UserList['UserList'][$UserId]['AuthStatus'];
				//用户生日
				$UserList['UserList'][$UserId]['Birthday'] = is_null($UserInfo['birth_day'])?"未知":$UserInfo['birth_day'];
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
			$params['Sex'] = isset($SexList[strtoupper(trim($this->request->Sex))])?substr(strtoupper(trim($this->request->Sex)),0,8):"";
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,8):"";
			$params['NickName'] = urldecode(trim($this->request->NickName))?substr(urldecode(trim($this->request->NickName)),0,8):"";
			$params['AuthStatus'] = isset($AuthStatusList[strtoupper(trim($this->request->AuthStatus))])?substr(strtoupper(trim($this->request->AuthStatus)),0,8):"";

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
			//用户头像
			$UserInfo['thumb'] = urldecode($UserInfo['thumb']);
			//实名认证证件类型
			$UserInfo['AuthIdType'] = isset($AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))])?$AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))]:"未知";
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
			$UserInfo['sex'] = isset($SexList[strtoupper(trim($AuthInfo['UserSex']))])?substr(strtoupper(trim($AuthInfo['UserSex'])),0,8):"";
			$UserInfo['id_type'] = isset($AuthIdTypesList[strtoupper(trim($AuthInfo['UserAuthIdType']))])?substr(strtoupper(trim($AuthInfo['UserAuthIdType'])),0,8):"";
			$UserInfo['auth_state'] = isset($AuthStatusList[strtoupper(trim($AuthInfo['UserAuthStatus']))])?substr(strtoupper(trim($AuthInfo['UserAuthStatus'])),0,8):"";
			$UserInfo['id_number'] = substr(strtoupper(trim($AuthInfo['UserAuthIdNo'])),0,30);
			$UserInfo['birth_day'] = $AuthInfo['UserBirthDay'];
			$UserInfo['expire_day'] = $AuthInfo['UserAuthExpireDay'];
			$UserAuthInfo['auth_resp'] = substr((trim(urldecode($AuthInfo['UserAuthReason']))),0,30);
			//执行操作的管理员ID
			$UserAuthInfo['op_uid'] = $this->manager->id;
			//通过认证要求选择性别
			if($UserInfo['auth_state'] == "AUTHED" && $UserInfo['sex']=="")
			{
				$response = array('errno' => 2);
			}
			//通过认证要求选择证件类型
			elseif($UserInfo['auth_state'] == "AUTHED" && $UserInfo['id_type']=="")
			{
				$response = array('errno' => 3);
			}
			//要求选择一个操作 通过/拒绝
			elseif( $UserInfo['auth_state']=="")
			{
				$response = array('errno' => 4);
			}
			//通过认证要求填写证件号码
			elseif($UserInfo['auth_state'] == "AUTHED" && $UserInfo['id_number']=="" )
			{
				$response = array('errno' => 5);
			}
			//通过认证要求填写有效的生日 不小于 今天
			elseif($UserInfo['auth_state'] == "AUTHED" && strtotime($UserInfo['birth_day']) < time())
			{
				$response = array('errno' => 6);
			}
			//通过认证要求填写有效的证件有效期 不小于 今天
			elseif($UserInfo['auth_state'] == "AUTHED" && strtotime($UserInfo['expire_day']) < time())
			{
				$response = array('errno' => 7);
			}
			//拒绝认证需要填写理由
			elseif($UserInfo['auth_state'] == "UNAUTH" && $UserAuthInfo['auth_resp'] == "")
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
				elseif($User['auth_state'] == "AUTHED")
				{
					$response = array('errno' => 10);
				}
				else
				{
					//执行认证操作
					if($UserInfo['auth_state'] == "AUTHED")
					{
						$Auth = $this->oUser->UserAuth($UserId,$UserInfo,$UserAuthInfo);
						$response = $Auth ? array('errno' => 0) : array('errno' => 9);
					}
					//执行认证拒绝操作
					elseif($UserInfo['auth_state'] == "UNAUTH")
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
}
