<?php
/**
 * 积分相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Credit extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_credit';

	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getCreditList($RaceCatalogId = 0,$fields = "*")
	{
		$RaceCatalogId = intval($RaceCatalogId);
		//初始化查询条件
		$whereCatalog = ($RaceCatalogId != 0)?" RaceCatalogId = $RaceCatalogId":"";
		$whereCondition = array($whereCatalog);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " where 1 ".$where." ORDER BY CreditId ASC";
		$return = $this->db->getAll($sql);
		$CreditList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$CreditList[$value['CreditId']] = $value;
			}
		}
		return $CreditList;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getCredit($CreditId, $fields = '*')
	{
		$CreditId = intval($CreditId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`CreditId` = ?', $CreditId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateCredit($CreditId, array $bind)
	{
		$CreditId = intval($CreditId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`CreditId` = ?', $CreditId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertCredit(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteCredit($CreditId)
	{
		$CreditId = intval($CreditId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`CreditId` = ?', $CreditId);
	}
}
