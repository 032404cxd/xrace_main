<?php
/**
 * 队伍相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Team extends Base_Widget
{
	//声明所用到的表
	protected $table = 'race_team';
	/**
	 * 获取单个队伍记录
	 * @param char $TeamId 队伍ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getRaceTeamInfo($TeamId, $fields = '*')
	{
		$TeamId = intval($TeamId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`TeamId` = ?', $TeamId);
	}
	/**
	 * 添加单个队伍记录
	 * @param array $bind 更新的数据列表
	 * @return boolean
	 */
	public function insertRaceTeamInfo(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 更新单个队伍记录
	 * @param char $TeamId 用户ID
	 * @param array $bind 更新的数据列表
	 * @return boolean
	 */
	public function updateRaceTeamInfo($TeamId, array $bind)
	{
		$TeamId = intval($TeamId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`TeamId` = ?', $TeamId);
	}
	/**
	 * 删除单个队伍记录
	 * @param integer $TeamId
	 * @return boolean
	 */
	public function deleteRaceTeam($TeamId)
	{
		$TeamId = intval($TeamId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`TeamId` = ?', $TeamId);
	}
	/**
	 * 获取队伍列表
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return array
	 */
	public function getRaceTeamList($params,$fields = array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//队伍名称
		$whereTeamName = (isset($params['RaceTeamName']) && trim($params['RaceTeamName']))?" RaceTeamName like '%".$params['RaceTeamName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereTeamName);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//获取用户数量
		if(isset($params['getCount'])&&$params['getCount']==1)
		{
			$RaceTeamCount = $this->getRaceTeamCount($params);
		}
		else
		{
			$TeamCount = 0;
		}
		$limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
		$order = " ORDER BY RaceTeamId desc";
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
		$return = $this->db->getAll($sql);
		$RaceTeamList = array('RaceTeamList'=>array(),'RaceTeamCount'=>$RaceTeamCount);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceTeamList['RaceTeamList'][$value['RaceTeamId']] = $value;
			}
		}
		else
		{
			return $RaceTeamList;
		}
		return $RaceTeamList;
	}
	/**
	 * 获取队伍数量
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return integer
	 */
	public function getRaceTeamCount($params)
	{
		//生成查询列
		$fields = Base_common::getSqlFields(array("RaceTeamCount"=>"count(RaceTeamId)"));

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//队伍名称
		$whereTeamName = (isset($params['RaceTeamName']) && trim($params['RaceTeamName']))?" RaceTeamName like '%".$params['RaceTeamName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereTeamName);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//生成条件列
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
		return $this->db->getOne($sql);
	}
}
