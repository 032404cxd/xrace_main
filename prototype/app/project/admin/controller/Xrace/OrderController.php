<?php
/**用户管理*/

class Xrace_OrderController extends AbstractController
{
	/**用户管理相关:Order
	 * @var string
	 */
	protected $sign = '?ctl=xrace/order';
	/**
	 * game对象
	 * @var object
	 */
	protected $oOrder;
	protected $oUser;

        /**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oOrder = new Xrace_Order();
		$this->oUser = new Xrace_User();
	}
	//订单列表
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("OrderList");
		if($PermissionCheck['return'])
		{
			//获取支付状态
			$PayStatusList = $this->oOrder->getPayStatusList();
			//获取取消状态
			$CancelStatusList = $this->oOrder->getCancelStatusList();
			//页面参数预处理
			$params['IsPay'] = isset($PayStatusList[intval($this->request->IsPay)])?intval($this->request->IsPay):-1;
			$params['IsCancel'] = isset($CancelStatusList[intval($this->request->IsCancel)])?intval($this->request->IsCancel):-1;
			$params['OrderId'] = urldecode(trim($this->request->OrderId))?substr(urldecode(trim($this->request->OrderId)),0,30):"";
			$params['PayId'] = urldecode(trim($this->request->PayId))?substr(urldecode(trim($this->request->PayId)),0,30):"";
			$params['Name'] = urldecode(trim($this->request->Name))?substr(urldecode(trim($this->request->Name)),0,30):"";
			//分页参数
			$params['Page'] = abs(intval($this->request->Page))?abs(intval($this->request->Page)):1;
			$params['PageSize'] = 20;
			//获取用户列表时需要获得记录总数
			$params['getCount'] = 1;
			//如果有输入姓名
			if(strlen($params['Name']))
			{
				//模糊查询用户列表
				$UserList = $this->oUser->getUserList(array('Name'=>$params['Name'],'getCount'=>0),array("user_id"));
				//如果有查找到用户
				if(count($UserList['UserList']))
				{
					//生成用户ID列表
					$params['UserList'] = "(".implode(",",array_keys($UserList['UserList'])).")";
				}
				else
				{
					$params['UserList'] = "(0)";
				}
			}
			//获取用户列表
			$OrderList = $this->oOrder->getOrderList($params);
			//导出EXCEL链接
			$export_var = "<a href =".(Base_Common::getUrl('','xrace/user','user.list.download',$params))."><导出表格></a>";
			//翻页参数
			$page_url = Base_Common::getUrl('','xrace/order','index',$params)."&Page=~page~";
			$page_content =  base_common::multi($OrderList['OrderCount'], $page_url, $params['Page'], $params['PageSize'], 10, $maxpage = 100, $prevWord = '上一页', $nextWord = '下一页');
			//初始化空的用户列表
			$UserList = array();
			foreach($OrderList['OrderList'] as $OrderId => $OrderInfo)
			{
				//如果用户数据尚未获取
				if(!isset($UserList[$OrderInfo['member_id']]))
				{
					//获取用户数据
					$UserInfo = $this->oUser->getUserInfo($OrderInfo['member_id'],"user_id,name");
					//如果获取到用户数据
					if(isset($UserInfo['user_id']))
					{
						//保存到用户列表
						$UserList[$UserInfo['user_id']] = $UserInfo;
					}
				}
				//获取用户姓名
				$OrderList['OrderList'][$OrderId]['Name'] = isset($UserList[$OrderInfo['member_id']])?$UserList[$OrderInfo['member_id']]['name']:"未知用户";
				//获取订单支付状态
				$OrderList['OrderList'][$OrderId]['PayStatusName'] = isset($PayStatusList[$OrderInfo['isPay']])?$PayStatusList[$OrderInfo['isPay']]:"未定义";
				//获取订单取消状态
				$OrderList['OrderList'][$OrderId]['CancelStatusName'] = isset($CancelStatusList[$OrderInfo['isCancel']])?$CancelStatusList[$OrderInfo['isCancel']]:"未定义";
			}
			//模板渲染
			include $this->tpl('Xrace_Order_OrderList');
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
		$PermissionCheck = $this->manager->checkMenuPermission("OrderListDownload");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oOrder->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oOrder->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oOrder->getAuthIdType();

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
				$OrderList = $this->oOrder->getOrderList($params);
				$Count = count($OrderList['OrderList']);
				foreach($OrderList['OrderList'] as $OrderId => $OrderInfo)
				{
					//生成单行数据
					$t = array();
					$t['user_id'] = $OrderInfo['user_id'];
					$t['open_wx_id'] = $OrderInfo['wx_open_id'];
					$t['open_wx_id'] = $OrderInfo['wx_open_id'];
					$t['name'] = $OrderInfo['name'];
					$t['nick_name'] = $OrderInfo['nick_name'];
					$t['sex'] = isset($SexList[$OrderInfo['sex']])?$SexList[$OrderInfo['sex']]:"保密";
					$t['AuthStatus'] = isset($AuthStatusList[$OrderInfo['auth_state']])?$AuthStatusList[$OrderInfo['auth_state']]:"未知";

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
	//用户详情
	public function userDetailAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("OrderListDownload");
		if($PermissionCheck['return'])
		{
			//获取性别列表
			$SexList = $this->oOrder->getSexList();
			//获取实名认证状态列表
			$AuthStatusList = $this->oOrder->getAuthStatus();
			//获取实名认证证件类型列表
			$AuthIdTypesList = $this->oOrder->getAuthIdType();
			$OrderId = trim($this->request->OrderId);
			//获取用户信息
			$OrderInfo = $this->oOrder->getOrderInfo($OrderId);
			//用户性别
			$OrderInfo['sex'] = isset($SexList[$OrderInfo['sex']])?$SexList[$OrderInfo['sex']]:"保密";
			//实名认证状态
			$OrderInfo['AuthStatus'] = isset($AuthStatusList[$OrderInfo['auth_state']])?$AuthStatusList[$OrderInfo['auth_state']]:"未知";
			//证件有效期
			$OrderInfo['AuthExpireDate'] = !is_null($OrderInfo['expire_day'])?$OrderInfo['expire_day']:"未知";
			//证件有效期
			$OrderInfo['Birthday'] = !is_null($OrderInfo['birth_day'])?$OrderInfo['birth_day']:"未知";
			//用户头像
			$OrderInfo['thumb'] = urldecode($OrderInfo['thumb']);
			//实名认证证件类型
			$OrderInfo['AuthIdType'] = isset($AuthIdTypesList[intval($OrderInfo['id_type'])])?$AuthIdTypesList[intval($OrderInfo['id_type'])]:"未知";
			//获取用户实名认证记录
			$OrderInfo['OrderAuthLog'] = $this->oOrder->getOrderAuthLog($OrderId,'submit_time,op_time,op_uid,auth_result,auth_resp');
			if(count($OrderInfo['OrderAuthLog']))
			{
				//初始化一个空的后台管理员列表
				$ManagerList = array();
				//获取实名认证记录的状态列表
				$AuthLogIdStatusList = $this->oOrder->getAuthLogStatusTypeList();
				foreach($OrderInfo['OrderAuthLog'] as $LogId => $AuthLog)
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
					$OrderInfo['OrderAuthLog'][$LogId]['ManagerName'] = $ManagerInfo['name'];
					//认证结果
					$OrderInfo['OrderAuthLog'][$LogId]['AuthResult'] = $AuthLogIdStatusList[$AuthLog['auth_result']];
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Order_OrderDetail');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
