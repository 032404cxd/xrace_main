<?php
/**
 * 用户激活相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Product extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_product_type';
    protected $table_product = 'config_product';
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getProductTypeList($RaceCatalogId = 0,$fields = "*")
	{
		$RaceCatalogId = intval($RaceCatalogId);
		//初始化查询条件
		$whereCatalog = ($RaceCatalogId != 0)?" RaceCatalogId = $RaceCatalogId":"";
		$whereCondition = array($whereCatalog);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " where 1 ".$where." ORDER BY ProductTypeId ASC";
		$return = $this->db->getAll($sql);
		$ProductTypeList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$ProductTypeList[$value['ProductTypeId']] = $value;
			}
		}
		return $ProductTypeList;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getProductType($ProductTypeId, $fields = '*')
	{
		$ProductTypeId = intval($ProductTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`ProductTypeId` = ?', $ProductTypeId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateProductType($ProductTypeId, array $bind)
	{
		$ProductTypeId = intval($ProductTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`ProductTypeId` = ?', $ProductTypeId);
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
	public function deleteProductType($ProductTypeId)
	{
		$ProductTypeId = intval($ProductTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`ProductTypeId` = ?', $ProductTypeId);
	}
        
        /**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAllProductList($ProductTypeId = 0,$fields = "*")
	{
		$ProductTypeId = intval($ProductTypeId);
		//初始化查询条件
		$whereProductType = ($ProductTypeId != 0)?" ProductTypeId = $ProductTypeId":"";
		$whereCondition = array($whereProductType);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table_product);
		$sql = "SELECT $fields FROM " . $table_to_process . " where 1 ".$where." ORDER BY ProductTypeId ASC";
		$return = $this->db->getAll($sql);
		$ProductList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$ProductList[$value['ProductTypeId']][$value['ProductId']] = $value;
			}
		}
		return $ProductList;
	}

        /**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getProduct($ProductId, $fields = '*')
	{
		$ProductId = intval($ProductId);
		$table_to_process = Base_Widget::getDbTable($this->table_product);
		return $this->db->selectRow($table_to_process, $fields, '`ProductId` = ?', $ProductId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertProduct(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_product);
		return $this->db->insert($table_to_process, $bind);
	}
        /**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateProduct($ProductId, array $bind)
	{
		$ProductId = intval($ProductId);
		$table_to_process = Base_Widget::getDbTable($this->table_product);
		return $this->db->update($table_to_process, $bind, '`ProductId` = ?', $ProductId);
	}
	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteProduct($ProductId)
	{
		$ProductId = intval($ProductId);
		$table_to_process = Base_Widget::getDbTable($this->table_product);
		return $this->db->delete($table_to_process, '`ProductId` = ?', $ProductId);
	}
        

}
