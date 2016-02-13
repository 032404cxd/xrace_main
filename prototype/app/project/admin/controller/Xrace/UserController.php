<?php
/**
 * 任务管理
 * @author Chen<cxd032404@hotmail.com>
 * $Id: LotoController.php 15195 2014-07-23 07:18:26Z 334746 $
 */

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
			$SexList = $this->oUser->getSexList();
			$AuthStatusList = $this->oUser->getAuthStatus();
			$AuthIdTypesList = $this->oUser->getAuthIdType();

			$params['Sex'] = isset($SexList[strtoupper(trim($this->request->Sex))])?substr(strtoupper(trim($this->request->Sex)),0,8):"";
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,8):"";
			$params['NickName'] = urldecode(trim($this->request->NickName))?substr(urldecode(trim($this->request->NickName)),0,8):"";
			$params['AuthStatus'] = isset($AuthStatusList[strtoupper(trim($this->request->AuthStatus))])?substr(strtoupper(trim($this->request->AuthStatus)),0,8):"";

			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 5;
			$params['getCount'] = 1;
			$UserList = $this->oUser->getUserLst($params);
			//导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/user','user.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/user','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($UserList['UserCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			foreach($UserList['UserList'] as $UserId => $UserInfo)
			{
				$UserList['UserList'][$UserId]['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
				$UserList['UserList'][$UserId]['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";
				$UserList['UserList'][$UserId]['AuthStatus'] = isset($AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))])?$UserList['UserList'][$UserId]['AuthStatus']."/".$AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))]:$UserList['UserList'][$UserId]['AuthStatus'];
				$UserList['UserList'][$UserId]['Birthday'] = is_null($UserInfo['birth_day'])?"未知":$UserInfo['birth_day'];

			}
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
			$SexList = $this->oUser->getSexList();
			$AuthStatusList = $this->oUser->getAuthStatus();
			$params['Sex'] = isset($SexList[strtoupper(trim($this->request->Sex))])?substr(strtoupper(trim($this->request->Sex)),0,8):"";
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,8):"";
			$params['NickName'] = urldecode(trim($this->request->NickName))?substr(urldecode(trim($this->request->NickName)),0,8):"";
			$params['AuthStatus'] = isset($AuthStatusList[strtoupper(trim($this->request->AuthStatus))])?substr(strtoupper(trim($this->request->AuthStatus)),0,8):"";

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
			$SexList = $this->oUser->getSexList();
			$AuthStatusList = $this->oUser->getAuthStatus();
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			$UserId = trim($this->request->UserId);
			$UserInfo = $this->oUser->getUserInfo($UserId);
			$UserInfo['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
			$UserInfo['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";
			$UserInfo['thumb'] = urldecode($UserInfo['thumb']);
			$UserInfo['AuthIdType'] = isset($AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))])?$AuthIdTypesList[strtoupper(trim($UserInfo['id_type']))]:"未知";
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
			$SexList = $this->oUser->getSexList();
			$AuthStatusList = $this->oUser->getAuthStatus();
			$UserId = trim($this->request->UserId);
			$UserInfo = $this->oUser->getUserInfo($UserId);
			$UserInfo['thumb'] = urldecode($UserInfo['thumb']);
			$UserInfo['AuthStatus'] = isset($AuthStatusList[$UserInfo['auth_state']])?$AuthStatusList[$UserInfo['auth_state']]:"未知";
			$UserInfo['sex'] = isset($SexList[$UserInfo['sex']])?$SexList[$UserInfo['sex']]:"保密";
			$UserAuthInfo = $this->oUser->getUserAuthInfo($UserId);
			$UserAuthInfo = $this->oUser->getUserAuthInfo($UserId);
			$UserAuthInfo['submit_img1'] = urldecode($UserAuthInfo['submit_img1']);
			$UserAuthInfo['submit_img2'] = urldecode($UserAuthInfo['submit_img2']);
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
			$SexList = $this->oUser->getSexList();
			$AuthStatusList = $this->oUser->getAuthStatus("submit");
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			$UserId = trim($this->request->UserId);
			$UserInfo = $this->oUser->getUserInfo($UserId);
			$UserInfo['birth_day'] =  is_null($UserInfo['birth_day'])?date("Y-m-d",time()):$UserInfo['birth_day'];
			$UserInfo['expire_day'] =  is_null($UserInfo['expire_day'])?date("Y-m-d",time()):$UserInfo['expire_day'];
			$UserAuthInfo = $this->oUser->getUserAuthInfo($UserId);
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
			$SexList = $this->oUser->getSexList();
			$AuthIdTypesList = $this->oUser->getAuthIdType();
			$AuthStatusList = $this->oUser->getAuthStatus("submit");

			$AuthInfo=$this->request->from('UserId','UserRealName','UserSex','UserAuthStatus','UserAuthIdType','UserAuthIdNo','UserBirthDay','UserAuthReason','UserAuthExpireDay');
			$UserId = trim($this->request->UserId);
			$UserInfo['sex'] = isset($SexList[strtoupper(trim($AuthInfo['UserSex']))])?substr(strtoupper(trim($AuthInfo['UserSex'])),0,8):"";
			$UserInfo['id_type'] = isset($AuthIdTypesList[strtoupper(trim($AuthInfo['UserAuthIdType']))])?substr(strtoupper(trim($AuthInfo['UserAuthIdType'])),0,8):"";
			$UserInfo['auth_state'] = isset($AuthStatusList[strtoupper(trim($AuthInfo['UserAuthStatus']))])?substr(strtoupper(trim($AuthInfo['UserAuthStatus'])),0,8):"";
			$UserInfo['id_number'] = substr(strtoupper(trim($AuthInfo['UserAuthIdNo'])),0,30);
			$UserInfo['birth_day'] = $AuthInfo['UserBirthDay'];
			$UserInfo['expire_day'] = $AuthInfo['UserAuthExpireDay'];
			$UserAuthInfo['auth_resp'] = substr((trim(urldecode($AuthInfo['UserAuthReason']))),0,30);
			$UserAuthInfo['op_uid'] = $this->manager->id;
			if($UserInfo['sex']=="")
			{
				$response = array('errno' => 2);
			}
			elseif($UserInfo['id_type']=="")
			{
				$response = array('errno' => 3);
			}
			elseif($UserInfo['auth_state']=="")
			{
				$response = array('errno' => 4);
			}
			elseif($UserInfo['auth_state'] == "AUTHED" && $UserInfo['id_number']=="" )
			{
				$response = array('errno' => 5);
			}
			elseif($UserInfo['auth_state'] == "AUTHED" && strtotime($UserInfo['birth_day']) < time())
			{
				$response = array('errno' => 6);
			}
			elseif($UserInfo['auth_state'] == "AUTHED" && strtotime($UserInfo['expire_day']) < time())
			{
				$response = array('errno' => 7);
			}
			elseif($UserInfo['auth_state'] == "UNAUTH" && $UserAuthInfo['auth_resp'] == "")
			{
				$response = array('errno' => 8);
			}
			else
			{
				$User = $this->oUser->getUserInfo($UserId);
				if(!isset($User['user_id']))
				{
					$response = array('errno' => 1);
				}
				elseif($User['auth_state'] == "AUTHED")
				{
					$response = array('errno' => 10);
				}
				else
				{
					if($UserInfo['auth_state'] == "AUTHED")
					{
						$Auth = $this->oUser->UserAuth($UserId,$UserInfo,$UserAuthInfo);
						$response = $Auth ? array('errno' => 0) : array('errno' => 9);
					}
					elseif($UserInfo['auth_state'] == "UNAUTH")
					{
						$Auth = $this->oUser->UserUnAuth($UserId,$UserInfo,$UserAuthInfo);
						$response = $Auth ? array('errno' => 0) : array('errno' => 9);

					}

				}
				die();

			}
			//elseif(if($UserInfo['id_type']==""))
			//elseif(intval($bind['RaceCatalogId'])<=0)
			{
			//	$response = array('errno' => 2);
			}
			//print_r($UserInfo);
			//print_r($response);
			//die();

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
