<?php
/**
 * 订单管理相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Order extends Base_Widget
{
	//声明所用到的表
	protected $table = 'hs_order';

	/**
	 * 获取单条记录
	 * @param integer $OrderId
	 * @param string $fields
	 * @return array
	 */
	public function getOrder($OrderId, $fields = '*')
	{
		$OrderId = trim($OrderId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`order_no` = ?', $OrderId);
	}
}
