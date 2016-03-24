<?php
/**
 * 用户激活相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_App extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_app_type';
	protected $table_os = 'config_app_os';
	protected $table_version = 'config_app_version';

	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAppTypeList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY AppTypeId ASC";
		$return = $this->db->getAll($sql);
		$AppTypeList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AppTypeList[$value['AppTypeId']] = $value;
			}
		}
		return $AppTypeList;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getAppType($AppTypeId, $fields = '*')
	{
		$AppTypeId = intval($AppTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`AppTypeId` = ?', $AppTypeId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateAppType($AppTypeId, array $bind)
	{
		$AppTypeId = intval($AppTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`AppTypeId` = ?', $AppTypeId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertAppType(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteAppType($AppTypeId)
	{
		$AppTypeId = intval($AppTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`AppTypeId` = ?', $AppTypeId);
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAppOsList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table_os);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY AppOSId ASC";
		$return = $this->db->getAll($sql);
		$AppOSList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AppOSList[$value['AppOSId']] = $value;
			}
		}
		return $AppOSList;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getAppOS($AppOSId, $fields = '*')
	{
		$AppOSId = intval($AppOSId);
		$table_to_process = Base_Widget::getDbTable($this->table_os);
		return $this->db->selectRow($table_to_process, $fields, '`AppOSId` = ?', $AppOSId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateAppOS($AppOSId, array $bind)
	{
		$AppOSId = intval($AppOSId);
		$table_to_process = Base_Widget::getDbTable($this->table_os);
		return $this->db->update($table_to_process, $bind, '`AppOSId` = ?', $AppOSId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertAppOS(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_os);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteAppOS($AppOSId)
	{
		$AppOSId = intval($AppOSId);
		$table_to_process = Base_Widget::getDbTable($this->table_os);
		return $this->db->delete($table_to_process, '`AppOSId` = ?', $AppOSId);
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAppVersionList($AppTypeId = 0,$AppOSId = 0,$fields = "*")
	{
		//APP类型ID
		$AppTypeId = intval($AppTypeId);
		//APP系统ID
		$AppOSId = intval($AppOSId);
		//初始化查询条件
		$whereAppType = ($AppTypeId != 0)?" AppTypeId = $AppTypeId":"";
		$whereAppOS = ($AppOSId != 0)?" AppOSId = $AppOSId":"";
		$whereCondition = array($whereAppType,$whereAppOS);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table_version);
		$sql = "SELECT $fields FROM " . $table_to_process . " where 1 ".$where." ORDER BY AppVersion,AppTypeId,AppOSId desc";
		$return = $this->db->getAll($sql);
		$AppVersionList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AppVersionList[$value['AppVersionId']] = $value;
			}
		}
		return $AppVersionList;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getAppVersionByVersion($AppVersion, $fields = '*')
	{
		//APP版本
		$AppVersion = trim($AppVersion);
		$table_to_process = Base_Widget::getDbTable($this->table_version);
		return $this->db->selectRow($table_to_process, $fields, '`AppVersion` = ?', $AppVersion);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertAppVersion(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_version);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getAppVersion($AppVersionId, $fields = '*')
	{
		//APP版本ID
		$AppVersionId = intval($AppVersionId);
		$table_to_process = Base_Widget::getDbTable($this->table_version);
		return $this->db->selectRow($table_to_process, $fields, '`AppVersionId` = ?', $AppVersionId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateAppVersion($AppVersionId, array $bind)
	{
		//APP版本ID
		$AppVersionId = intval($AppVersionId);
		$table_to_process = Base_Widget::getDbTable($this->table_version);
		return $this->db->update($table_to_process, $bind, '`AppVersionId` = ?', $AppVersionId);
	}
	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteAppVersion($AppVersionId)
	{
		//APP版本ID
		$AppVersionId = intval($AppVersionId);
		$table_to_process = Base_Widget::getDbTable($this->table_version);
		return $this->db->delete($table_to_process, '`AppVersionId` = ?', $AppVersionId);
	}

}
