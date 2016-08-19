<?php
/**
 * 队伍相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Team extends Base_Widget
{
	//声明所用到的表
	protected $table = 'team';
	/**
	 * 获取单个队伍记录
	 * @param char $TeamId 队伍ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getRaceTeamInfo($RaceTeamId, $fields = '*')
	{
		$TeamId = intval($RaceTeamId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`team_id` = ?', $RaceTeamId);
	}
	/**
	 * 获取单个队伍记录
	 * @param char $TeamId 队伍ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getRaceTeamInfoByName($RaceTeamName,$fields = '*')
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`name` = ?',array($RaceTeamName));
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
		$whereTeamName = (isset($params['RaceTeamName']) && trim($params['RaceTeamName']))?" name like '%".$params['RaceTeamName']."%' ":"";
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
			$RaceTeamCount = 0;
		}
		$limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
		$order = " ORDER BY team_id desc";
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
		$return = $this->db->getAll($sql);
		$RaceTeamList = array('RaceTeamList'=>array(),'RaceTeamCount'=>$RaceTeamCount);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceTeamList['RaceTeamList'][$value['team_id']] = $value;
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
		$fields = Base_common::getSqlFields(array("RaceTeamCount"=>"count(team_id)"));

		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//队伍名称
		$whereTeamName = (isset($params['RaceTeamName']) && trim($params['RaceTeamName']))?" name like '%".$params['RaceTeamName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereTeamName);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//生成条件列
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
		return $this->db->getOne($sql);
	}
	/**
	 * 获取队伍数量
	 * @param $RaceGroupInfo  分组信息
	 * @param $Cache 是否强制更新缓存
	 * @return array
	 */
	public function getRaceTeamListByGroup($RaceGroupInfo,$Cache = 1)
	{
		$oMemCache = new Base_Cache_Memcache("B5M");
		$CacheKey = "TeamList_".$RaceGroupInfo['RaceGroupId'];
		//如果需要获取缓存
		if($Cache == 1)
		{
			//获取缓存
			$m = $oMemCache->get($CacheKey);
			//缓存解开
			$RaceTeamList = json_decode($m,true);
			//如果数据为空
			if(count($RaceTeamList['RaceTeamList'])==0)
			{
				//需要从数据库获取
				$NeedDB = 1;
			}
			else
			{
				//echo "cached";
				return $RaceTeamList;
			}
		}
		else
		{
			//需要从数据库获取
			$NeedDB = 1;
		}
		if(isset($NeedDB))
		{
			//查询参数
			$params = array('RaceCatalogId'=>$RaceGroupInfo['RaceCatalogId'],'getCount'=>0);
			//获取指定赛事下的队伍列表
			$RaceTeamList = $this->getRaceTeamList($RaceGroupInfo);
			//如果有获取到队伍列表
			if(count($RaceTeamList['RaceTeamList']))
			{
				//循环队伍列表
				foreach($RaceTeamList['RaceTeamList'] as $RaceTeamId => $RaceTeamInfo)
				{
					//数据解包
					$RaceTeamInfo['comment'] = json_decode($RaceTeamInfo['comment'],true);
					//如果并未选择分组 或者 当前组别不在已经选择的分组当中
					if(!isset($RaceTeamInfo['comment']['SelectedRaceGroup']) || !in_array($RaceGroupInfo['RaceGroupId'],$RaceTeamInfo['comment']['SelectedRaceGroup']))
					{
						//删除当前分组
						unset($RaceTeamList['RaceTeamList'][$RaceTeamId]);
					}
					else
					{
						//保留数据
						$RaceTeamList['RaceTeamList'][$RaceTeamId] = $RaceTeamInfo;
					}
				}
				//如果有获取到队伍列表
				if(count($RaceTeamList['RaceTeamList']))
				{
					//写入缓存
					$oMemCache -> set($CacheKey,json_encode($RaceTeamList),86400);
					return $RaceTeamList;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
}
