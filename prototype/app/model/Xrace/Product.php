<?php
/**
 * 用户激活相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Product extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_product_type';

	protected $maxParams = 5;

	public function getMaxParmas()
	{
		return $this->maxParams;
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAllProductTypeList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY ProductTypeId ASC";
		$return = $this->db->getAll($sql);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AllProductType[$value['ProductTypeId']] = $value;
			}
		}
		return $AllProductType;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getProductType($productTypeId, $fields = '*')
	{
		$productTypeId = intval($productTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`ProductTypeId` = ?', $productTypeId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateProductType($productTypeId, array $bind)
	{
		$productTypeId = intval($productTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`ProductTypeId` = ?', $productTypeId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertProductType(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteProductType($productTypeId)
	{
		$productTypeId = intval($productTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`ProductTypeId` = ?', $productTypeId);
	}

}
