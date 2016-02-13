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
	protected $sex = array('MALE'=>"男","FEMALE"=>"女");
	protected $auth_status = array('UNAUTH'=>"未审核","AUTHING"=>"审核中","AUTHED"=>"已审核");
	protected $auth_status_submit = array('UNAUTH'=>"不通过","AUTHED"=>"审核通过");
	protected $auth_id_type = array('IDCARD'=>"身份证","PASSPORT"=>"护照");
	public function getSexList()
	{
		return $this->sex;
	}
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
	public function getAuthIdType()
	{
		return $this->auth_id_type;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getUserInfo($UserId, $fields = '*')
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`user_id` = ?', $UserId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateUserInfo($UserId, array $bind)
	{
		$UserId = trim($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`user_id` = ?', $UserId);
	}
	/**
	 * 更新
	 * @param integer $AppId
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
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function insertUserAuthLog(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_auth_log);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
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
	 * @param $fields
	 * @param $params
	 * @return array
	 */
	public function getUserLst($params,$fields = array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//性别判断
		$whereSex = isset($this->sex[$params['Sex']])?" sex = '".$params['Sex']."' ":"";
		//实名认证判断
		$whereAuth = isset($this->auth_status[$params['AuthStatus']])?" auth_state = '".$params['AuthStatus']."' ":"";
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
	 * @param $fields
	 * @param $params
	 * @return array
	 */
	public function getUserCount($params)
	{
		//生成查询列
		$fields = Base_common::getSqlFields(array("UserCount"=>"count(user_id)"));

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//性别判断
		$whereSex = isset($this->sex[$params['Sex']])?" sex = '".$params['Sex']."' ":"";
		//实名认证判断
		$whereAuth = isset($this->auth_status[$params['AuthStatus']])?" auth_state = '".$params['AuthStatus']."' ":"";
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
	 * 获取用户数量
	 * @param $fields
	 * @param $params
	 * @return array
	 */
	public function UserAuth($UserId,$UserInfo,$AuthInfo)
	{
		$UserAuthInfo = $this->getUserAuthInfo($UserId);
		$UserAuthInfo['auth_resp'] = $AuthInfo['auth_resp'];
		$UserAuthInfo['auth_result'] = "ALLOWED";
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

}
