<?php
/**
 * 订单管理相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Mylaps extends Base_Widget
{
	//声明所用到的表
	#protected $table = 'zs_times';
	protected $table = 'times';

	/**
	 * 获取单条记录
	 * @param integer $OrderId
	 * @param string $fields
	 * @return array
	 */
	public function getTimingData($params,$fields=array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		$table_to_process = str_replace($this->table,$params['prefix'].$this->table,$table_to_process);
		echo "table_to_process:".$table_to_process."<br>";
		//获得芯片ID
		$whereChip = isset($params['Chip'])?" Chip = '".$params['Chip']."' ":"";
		$whereChipList = isset($params['ChipList'])?" Chip in (".$params['ChipList'].") ":"";
		$Limit = isset($params['page'])?(" limit ".($params['page']-1)*$params['pageSize'].",".$params['page']*$params['pageSize']):"";
		//所有查询条件置入数组
		$whereCondition = array($whereChip,$whereChipList);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by Chip,ChipTime,Millisecs asc".$Limit;
		echo $sql."<br>";
		$return = $this->db->getAll($sql);
		return $return;
	}
}
