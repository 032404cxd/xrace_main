<?php
/**
 * 用户激活相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_User extends Base_Widget
{
	//声明所用到的表
	protected $table = 'user_profile';
	protected $table_auth = 'user_auth';
	protected $table_auth_log = 'user_auth_log';
	protected $table_license = 'user_license';
	protected $table_race = 'user_race';
        //性别列表
	protected $sex = array('1'=>"男","2"=>"女");
	//实名认证状态
	protected $auth_status = array('0'=>"未审核",'1'=>"审核中",'2'=>"已审核");
	//提交时对应的实名认证状态名
	protected $auth_status_submit = array('0'=>"不通过",'2'=>"审核通过");
	//认证记录中对应的实名认证状态名
	protected $auth_status_log = array('0'=>"拒绝","2"=>"通过");
	//实名认证用到的证件类型列表
	protected $auth_id_type = array('1'=>"身份证","2"=>"护照");
        //用户执照状态
	protected $user_license_status = array('1'=>"生效中",'2'=>"已过期",'3'=>"即将生效",'4'=>"已删除");
        //获取性别列表
	public function getSexList()
	{
		return $this->sex;
	}
	//获取实名认证状态
	public function getAuthStatus($type = "display")
	{
		if($type=="display")
		{
			return $this->auth_status;
		}
		else
		{
			return $this->auth_status_submit;
		}
	}
	//获取实名认证的证件列表
	public function getAuthIdType()
	{
		return $this->auth_id_type;
	}
	//获取实名认证的记录的状态列表
	public function getAuthLogStatusTypeList()
	{
		return $this->auth_status_log;
	}

    //获得用户执照状态
	public function getUserLicenseStatusList()
	{
		return $this->user_license_status;
	}
	/**
	 * 获取单个用户记录
	 * @param char $UserId 用户ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getUserInfo($UserId, $fields = '*')
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`user_id` = ?', $UserId);
	}
	/**
	 * 更新单个用户记录
	 * @param char $UserId 用户ID
	 * @param array $bind 更新的数据列表
	 * @return boolean
	 */
	public function updateUserInfo($UserId, array $bind)
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`user_id` = ?', $UserId);
	}
	/**
	 * 更新单个用户的实名认证状态记录
	 * @param char $UserId 用户ID
	 * @param array $bind
	 * @return boolean
	 */
	public function updateUserAuthInfo($UserId, array $bind)
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table_auth);
		return $this->db->update($table_to_process, $bind, '`user_id` = ?', $UserId);
	}
	/**
	 * 插入一条用户的实名认证记录
	 * @param array $bind 更新的数据列表
	 * @return boolean
	 */
	public function insertUserAuthLog(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_auth_log);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 获取单条用户实名认证的状态记录
	 * @param char $UserId 用户ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getUserAuthInfo($UserId, $fields = '*')
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table_auth);
		return $this->db->selectRow($table_to_process, $fields, '`user_id` = ?', $UserId);
	}
	/**
	 * 获取用户列表
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return array
	 */
	public function getUserLst($params,$fields = array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//性别判断
		$whereSex = isset($this->sex[$params['Sex']])?" sex = ".$params['Sex']." ":"";
		//实名认证判断
		$whereAuth = isset($this->auth_status[$params['AuthStatus']])?" auth_state = ".$params['AuthStatus']." ":"";
		//姓名
		$whereName = (isset($params['Name']) && trim($params['Name']))?" name like '%".$params['Name']."%' ":"";
		//昵称
		$whereNickName = (isset($params['NickName']) && trim($params['NickName']))?" nick_name like '%".$params['NickName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereSex,$whereName,$whereNickName,$whereAuth);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//获取用户数量
		if(isset($params['getCount'])&&$params['getCount']==1)
		{
			$UserCount = $this->getUserCount($params);
		}
		else
		{
			$UserCount = 0;
		}
		$limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
		$order = " ORDER BY crt_time desc";
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
		$return = $this->db->getAll($sql);
		$UserList = array('UserList'=>array(),'UserCount'=>$UserCount);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$UserList['UserList'][$value['user_id']] = $value;
			}
		}
		else
		{
			return $UserList;
		}
		return $UserList;
	}
	/**
	 * 获取用户数量
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return integer
	 */
	public function getUserCount($params)
	{
		//生成查询列
		$fields = Base_common::getSqlFields(array("UserCount"=>"count(user_id)"));

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//性别判断
		$whereSex = isset($this->sex[$params['Sex']])?" sex = ".$params['Sex']." ":"";
		//实名认证判断
		$whereAuth = isset($this->auth_status[$params['AuthStatus']])?" auth_state = ".$params['AuthStatus']." ":"";
		//姓名
		$whereName = (isset($params['Name']) && trim($params['Name']))?" name like '%".$params['Name']."%' ":"";
		//昵称
		$whereNickName = (isset($params['NickName']) && trim($params['NickName']))?" nick_name like '%".$params['NickName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereSex,$whereName,$whereNickName,$whereAuth);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);

		//生成条件列
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
		return $this->db->getOne($sql);
	}
	/**
	 * 用户实名名认证通过
	 * @param $UserId 用户ID
	 * @param $UserInfo 更新的用户信息
	 * @param $AuthInfo 更新的用户实名认证状态数据
	 * @return boolean
	 */
	public function UserAuth($UserId,$UserInfo,$AuthInfo)
	{
		$UserAuthInfo = $this->getUserAuthInfo($UserId);
		$UserAuthInfo['auth_resp'] = $AuthInfo['auth_resp'];
		$UserAuthInfo['auth_result'] = 2;
		$UserAuthInfo['op_time'] = date("Y-m-d H:i:s",time());
		$UserAuthInfo['op_uid'] = $AuthInfo['op_uid'];
		//事务开始
		$this->db->begin();
		$UserProfileUpdate = $this->updateUserInfo($UserId,$UserInfo);
		$UserAuthInfoUpdate = $this->updateUserAuthInfo($UserId,$UserAuthInfo);
		$UserAuthLogInsert = $this->insertUserAuthLog($UserAuthInfo+array('auth_id'=>rand(111,999)));
		if($UserProfileUpdate && $UserAuthInfoUpdate && $UserAuthLogInsert)
		{
			$this->db->commit();
			return true;
		}
		else
		{
			$this->db->rollBack();
			return false;
		}
	}
	/**
	 * 用户实名名认证通过
	 * @param $UserId 用户ID
	 * @param $UserInfo 更新的用户信息
	 * @param $AuthInfo 更新的用户实名认证状态数据
	 * @return boolean
	 */
	public function UserUnAuth($UserId,$UserInfo,$AuthInfo)
	{

		$UserAuthInfo = $this->getUserAuthInfo($UserId);
		$UserAuthInfo['auth_resp'] = $AuthInfo['auth_resp'];
		$UserAuthInfo['auth_result'] = 2;
		$UserAuthInfo['op_time'] = date("Y-m-d H:i:s",time());
		$UserAuthInfo['op_uid'] = $AuthInfo['op_uid'];
		//事务开始
		$this->db->begin();
		$UserProfileUpdate = $this->updateUserInfo($UserId,$UserInfo);
		$UserAuthInfoUpdate = $this->updateUserAuthInfo($UserId,$UserAuthInfo);
		$UserAuthLogInsert = $this->insertUserAuthLog($UserAuthInfo+array('auth_id'=>rand(111,999)));
		if($UserProfileUpdate && $UserAuthInfoUpdate && $UserAuthLogInsert)
		{
			$this->db->commit();
			return true;
		}
		else
		{
			$this->db->rollBack();
			return false;
		}
	}

	/**
	 * 获取单个用户记录
	 * @param char $UserId 用户ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getUserAuthLog($UserId, $fields = '*')
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table_auth_log);
		$sql = "select $fields from $table_to_process where  `user_id` = ? order by op_time desc";
		return $this->db->getAll($sql,  $UserId);
	}
	/**
	 * 获取实名认证记录
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return array
	 */
	public function getAuthLog($params,$fields = array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table_auth_log);
		//开始时间
		$whereStartDate = isset($params['StartDate'])?" op_time >= '".$params['StartDate']."' ":"";
		//开始时间
		$whereEndDate = isset($params['EndDate'])?" op_time <= '".date("Y-m-d",strtotime($params['EndDate'])+86400)."' ":"";
		//审核结果
		$whereAuthResult = isset($this->auth_status_log[$params['AuthResult']])?" auth_result = ".$params['AuthResult']." ":"";
		//操作人
		$whereManager = $params['ManagerId']?" op_uid = ".$params['ManagerId']." ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereStartDate,$whereEndDate,$whereAuthResult,$whereManager);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//获取用户数量
		if(isset($params['getCount'])&&$params['getCount']==1)
		{
			$AuthLogCount = $this->getAuthLogCount($params);
		}
		else
		{
			$AuthLogCount = 0;
		}
		$limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
		$order = " ORDER BY op_time desc";
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
		$return = $this->db->getAll($sql);
		$AuthLog = array('AuthLog'=>array(),'AuthLogCount'=>$AuthLogCount);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AuthLog['AuthLog'][$value['auth_id']] = $value;
			}
		}
		return $AuthLog;
	}
	/**
	 * 获取用户数量
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return integer
	 */
	public function getAuthLogCount($params)
	{
		//生成查询列
		$fields = Base_common::getSqlFields(array("AuthLOgCount"=>"count(auth_id)"));

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table_auth_log);
		//开始时间
		$whereStartDate = isset($params['StartDate'])?" op_time >= '".$params['StartDate']."' ":"";
		//开始时间
		$whereEndDate = isset($params['EndDate'])?" op_time <= '".date("Y-m-d",strtotime($params['EndDate'])+86400)."' ":"";
		//审核结果
		$whereAuthResult = isset($this->auth_status_log[$params['AuthResult']])?" auth_result = ".$params['AuthResult']." ":"";
		//操作人
		$whereManager = $params['ManagerId']?" op_uid = ".$params['ManagerId']." ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereStartDate,$whereEndDate,$whereAuthResult,$whereManager);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);

		//生成条件列
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
		return $this->db->getOne($sql);
	}
        
	/*
	 * 获得用户执照
	 */
	public function getUserLicenseList($params,$fields = array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table_license);
		//获得执照ID
		$whereLicenseId = isset($params['LicenseId'])?" LicenseId = '".$params['LicenseId']."' ":"";
		//获得用户ID
		$whereUserId = isset($params['UserId'])?" UserId = '".$params['UserId']."' ":"";
		//获得组别ID
		$whereGroupId = isset($params['RaceGroupId'])?" RaceGroupId = '".$params['RaceGroupId']."' ":"";
		//获得执照状态
		if(isset($params['LicenseStatus']) && $params['LicenseStatus'] != 0)
		{
			$currentTime = date("Y-m-d",time());
			$p = explode("|",$params['LicenseStatus']);
			foreach($p as $LicenseStatus)
			{
				//生效中
				if($LicenseStatus == 3)
				{
					$t[] = "(LicenseStartDate > '$currentTime')";
				}
				//失效
				elseif($LicenseStatus == 1)
				{
					$t[] = "(LicenseStartDate <= '$currentTime' and LicenseEndDate >= '$currentTime')";
				}
				//已删除
				elseif($LicenseStatus == 2)
				{
					$t[] = "(LicenseEndDate < '$currentTime')";
				}
				else
				{
					$t[] = "(LicenseStatus = ".$params['LicenseStatus'].")";
				}
			}
			$whereLicenseStatus = implode(" or ",$t);
		}
		//需要排除的时间
		if(isset($params['ExceptionDate']))
		{
			$whereExceptionDate = "((LicenseStartDate <= '".$params['ExceptionDate']['StartDate']."' and LicenseEndDate >= '".$params['ExceptionDate']['StartDate']."')".
			" or "."(LicenseStartDate <= '".$params['ExceptionDate']['EndDate']."' and LicenseEndDate >= '".$params['ExceptionDate']['EndDate']."')"." or "."(LicenseStartDate <= '".$params['ExceptionDate']['StartDate']."' and LicenseEndDate >= '".$params['ExceptionDate']['EndDate']."'))";
		}
		else
		{
			$whereExceptionDate = "";
		}
		//排除数据
		$whereExceptionId = isset($params['ExceptionId'])?" LicenseId != '".$params['ExceptionId']."' ":"";
		//排除数据
		$whereExceptionStatus = isset($params['ExceptionStatus'])?" LicenseStatus != '".$params['ExceptionStatus']."' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereLicenseId,$whereUserId,$whereGroupId,$whereLicenseStatus,$whereExceptionId,$whereExceptionStatus,$whereExceptionDate);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//获取用户数量
		if(isset($params['getCount'])&&$params['getCount']==1)
		{
			$UserLicenseCount = $this->getUserLicenseCount($params);
		}
		else
		{
			$UserLicenseCount = 0;
		}
		//存储的数据结构
		$UserLicense = array('UserLicenseList'=>array(),'UserLicenseCount'=>$UserLicenseCount);
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by LicenseId,RaceGroupId";
		$return = $this->db->getAll($sql);
		if($return)
		{
			$UserLicense['UserLicenseList'] = $return;
		}
		return $UserLicense;
	}
        
        /*
         * 获得用户执照数量
         */
        public function getUserLicenseCount($params)
		{
            //生成查询列
            $fields = Base_common::getSqlFields(array("UserLicenseCount"=>"count(LicenseId)"));
			//获取需要用到的表名
			$table_to_process = Base_Widget::getDbTable($this->table_license);
			//获得执照ID
			$whereLicenseId = isset($params['LicenseId'])?" LicenseId = '".$params['LicenseId']."' ":"";
			//获得用户ID
			$whereUserId = isset($params['UserId'])?" UserId = '".$params['UserId']."' ":"";
			//获得组别ID
			$whereGroupId = isset($params['RaceGroupId'])?" RaceGroupId = '".$params['RaceGroupId']."' ":"";
			//获得执照状态
			if(isset($params['LicenseStatus']) && $params['LicenseStatus'] != 0)
			{
				$currentTime = date("Y-m-d",time());
				$p = explode("|",$params['LicenseStatus']);
				foreach($p as $LicenseStatus)
				{
					//生效中
					if($LicenseStatus == 3)
					{
						$t[] = "(LicenseStartDate > '$currentTime')";
					}
					//失效
					elseif($LicenseStatus == 1)
					{
						$t[] = "(LicenseStartDate <= '$currentTime' and LicenseEndDate >= '$currentTime')";
					}
					//已删除
					elseif($LicenseStatus == 2)
					{
						$t[] = "(LicenseEndDate < '$currentTime')";
					}
					else
					{
						$t[] = "(LicenseStatus = ".$params['LicenseStatus'].")";
					}
				}
				$whereLicenseStatus = implode(" or ",$t);
			}
			//需要排除的时间
			if(isset($params['ExceptionDate']))
			{
				$whereExceptionDate = "((LicenseStartDate <= '".$params['ExceptionDate']['StartDate']."' and LicenseEndDate >= '".$params['ExceptionDate']['StartDate']."')".
					" or "."(LicenseStartDate <= '".$params['ExceptionDate']['EndDate']."' and LicenseEndDate >= '".$params['ExceptionDate']['EndDate']."')".
					" or "."(LicenseStartDate <= '".$params['ExceptionDate']['StartDate']."' and LicenseEndDate >= '".$params['ExceptionDate']['EndDate']."'))";
			}
			else
			{
				$whereExceptionDate = "";
			}
			//排除数据
			$whereExceptionId = isset($params['ExceptionId'])?" LicenseId != '".$params['ExceptionId']."' ":"";
			//排除数据
			$whereExceptionStatus = isset($params['ExceptionStatus'])?" LicenseStatus != '".$params['ExceptionStatus']."' ":"";
			//所有查询条件置入数组
			$whereCondition = array($whereLicenseId,$whereUserId,$whereGroupId,$whereLicenseStatus,$whereExceptionId,$whereExceptionStatus,$whereExceptionDate);
			//生成条件列
            $where = Base_common::getSqlWhere($whereCondition);
			$sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
			return $this->db->getOne($sql);
        }
	/**
	 * 获取单个用户记录
	 * @param char $UserId 用户ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getUserLicense($LicenseId, $fields = '*')
	{
		$LicenseId = trim($LicenseId);
		$table_to_process = Base_Widget::getDbTable($this->table_license);
		return $this->db->selectRow($table_to_process, $fields, '`LicenseId` = ?', $LicenseId);
	}
        /*
         * 获得用户执照状态
         */
        public function getUserLicenseStatus($UserLicenseInfo)
		{
			return $this->user_license_status[$UserLicenseInfo['LicenseStatus']];
        }
        
        //新增单个用户执照信息
	public function insertUserLicense(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_license);
		return $this->db->insert($table_to_process, $bind);
	}
	//更新单个用户执照信息
	public function updateUserLicense($LicenseId,array $bind)
	{
		$LicenseId = intval($LicenseId);
		$table_to_process = Base_Widget::getDbTable($this->table_license);
		return $this->db->update($table_to_process, $bind, '`LicenseId` = ?', $LicenseId);
	}
	public function getRaceUserList($params,$fields = array('*'))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		//获得比赛ID
		$whereRace = isset($params['RaceId'])?" RaceId = '".$params['RaceId']."' ":"";
		//获得用户ID
		$whereUser = isset($params['UserId'])?" UserId = '".$params['UserId']."' ":"";
		//获得组别ID
		$whereGroup = isset($params['RaceGroupId'])?" RaceGroupId = '".$params['RaceGroupId']."' ":"";
		//获得赛事ID
		$whereCatalog = isset($params['RaceCatalogId'])?" RaceCatalogId = '".$params['RaceCatalogId']."' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereCatalog,$whereUser,$whereGroup,$whereRace);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by ApplyId";
		$return = $this->db->getAll($sql);
		return $return;
	}
	//更新用户报名信息
	public function updateRaceUser($RaceId,$UserId, array $bind)
	{
		$RaceId = intval($RaceId);
		$UserId = intval($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->update($table_to_process, $bind, '`RaceId` = ? and `UserId` = ?', array($RaceId,$UserId));
	}
}
