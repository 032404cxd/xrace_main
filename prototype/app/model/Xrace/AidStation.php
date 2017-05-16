<?php
/**
 * 赛事配置相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_AidStation extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_aid_station';
	//更新单个补给点
	public function updateAidStation($AidStationId, array $bind)
	{
        $AidStationId = intval($AidStationId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`AidStationId` = ?', $AidStationId);
	}
	//添加单个补给点
	public function insertAidStation(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}
	//删除单个补给点
	public function deleteAidStation($AidStationId)
	{
		$AidStationId = intval($AidStationId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`AidStationId` = ?', $AidStationId);
	}
	//根据赛事获取所有组别列表
	//赛事ID为0则获取全部组别
	public function getAidStationIdList($params,$fields = "*")
	{
		//初始化查询条件
        $whereStage = (isset($params['RaceStageId']) && ($params['RaceStageId'] >0))?(" RaceStageId = '".$params['RaceStageId'])."'":"";
		$whereCondition = array($whereStage);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
        $table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . "  where 1 ".$where." ORDER BY RaceStageId desc,AidStationId asc";
		$return = $this->db->getAll($sql);
		$AidStationList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
			    $AidStationList[$value['AidStationId']] = $value;
			}
		}
		return $AidStationList;
	}
	//获取单个赛事组别的信息
	public function getAidStation($AidStationId, $fields = '*')
	{
		$AidStationId = intval($AidStationId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`AidStationId` = ?', $AidStationId);
	}
}
