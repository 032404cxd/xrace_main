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
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AllAppType[$value['AppTypeId']] = $value;
			}
		}
		return $AllAppType;
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

}
