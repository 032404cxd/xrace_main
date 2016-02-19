<?php
/**
 * 用户激活相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Race extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_race_catalog';
	protected $table_race = 'config_race';
	protected $table_type = 'config_race_type';
	protected $table_group = 'config_race_group';
	protected $table_stage = 'config_race_stage';
	protected $table_timing = 'config_timing_point';
	protected $table_stage_group = 'config_race_stage_group';
	protected $maxRaceDetail = 5;

	protected $raceTimingType = array('chip'=>'芯片计时','gps'=>'gps定位');

	public function getTimingType()
	{
		return $this->raceTimingType;
	}
	public function getMaxRaceDetail()
	{
		return $this->maxRaceDetail;
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAllRaceCatalogList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY RaceCatalogId ASC";
		$return = $this->db->getAll($sql);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AllRaceCatalog[$value['RaceCatalogId']] = $value;
				$AllRaceCatalog[$value['RaceCatalogId']]['comment'] = json_decode($AllRaceCatalog[$value['RaceCatalogId']]['comment'],true);
			}
		}
		return $AllRaceCatalog;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceCatalog($RaceCatalogId, $fields = '*')
	{
		$RaceCatalogId = intval($RaceCatalogId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`RaceCatalogId` = ?', $RaceCatalogId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateRaceCatalog($RaceCatalogId, array $bind)
	{
		$RaceCatalogId = intval($RaceCatalogId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`RaceCatalogId` = ?', $RaceCatalogId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertRaceCatalog(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteRaceCatalog($RaceCatalogId)
	{
		$RaceCatalogId = intval($RaceCatalogId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`RaceCatalogId` = ?', $RaceCatalogId);
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAllRaceGroupList($RaceCatalogId,$fields = "*")
	{
		$RaceCatalogId = intval($RaceCatalogId);
		//初始化查询条件
		$whereCatalog = ($RaceCatalogId != 0)?" RaceCatalogId = $RaceCatalogId":"";
		$whereCondition = array($whereCatalog);

		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);

		$table_to_process = Base_Widget::getDbTable($this->table_group);
		$sql = "SELECT $fields FROM " . $table_to_process . "  where 1 ".$where." ORDER BY RaceCatalogId desc,RaceGroupId asc";
		$return = $this->db->getAll($sql);
		$AllRaceGroup = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AllRaceGroup[$value['RaceGroupId']] = $value;
			}
		}
		return $AllRaceGroup;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceGroup($RaceGroupId, $fields = '*')
	{
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->selectRow($table_to_process, $fields, '`RaceGroupId` = ?', $RaceGroupId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateRaceGroup($RaceGroupId, array $bind)
	{
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->update($table_to_process, $bind, '`RaceGroupId` = ?', $RaceGroupId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertRaceGroup(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteRaceGroup($RaceGroupId)
	{
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->delete($table_to_process, '`RaceGroupId` = ?', $RaceGroupId);
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAllRaceStageList($RaceCatalogId,$fields = "*")
	{
		$RaceCatalogId = trim($RaceCatalogId);
		//初始化查询条件
		$whereCatalog = ($RaceCatalogId != 0)?" RaceCatalogId = $RaceCatalogId":"";
		$whereCondition = array($whereCatalog);

		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);

		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		$sql = "SELECT $fields FROM " . $table_to_process . "  where 1 ".$where." ORDER BY RaceCatalogId,RaceStageId ASC";
		$return = $this->db->getAll($sql);
		$AllRaceGroup = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AllRaceStage[$value['RaceStageId']] = $value;
			}
		}
		return $AllRaceStage;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceStage($RaceStageId, $fields = '*')
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->selectRow($table_to_process, $fields, '`RaceStageId` = ?', $RaceStageId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateRaceStage($RaceStageId, array $bind)
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->update($table_to_process, $bind, '`RaceStageId` = ?', $RaceStageId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertRaceStage(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteRaceStage($RaceStageId)
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->delete($table_to_process, '`RaceStageId` = ?', $RaceStageId);
	}
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getAllRaceTypeList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY RaceTypeId ASC";
		$return = $this->db->getAll($sql);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$AllRaceType[$value['RaceTypeId']] = $value;
				$AllRaceType[$value['RaceTypeId']]['comment'] = json_decode($AllRaceType[$value['RaceTypeId']]['comment'],true);
			}
		}
		return $AllRaceType;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceType($RaceTypeId, $fields = '*')
	{
		$RaceTypeId = intval($RaceTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->selectRow($table_to_process, $fields, '`RaceTypeId` = ?', $RaceTypeId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateRaceType($RaceTypeId, array $bind)
	{
		$RaceTypeId = intval($RaceTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->update($table_to_process, $bind, '`RaceTypeId` = ?', $RaceTypeId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertRaceType(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteRaceType($RaceTypeId)
	{
		$RaceTypeId = intval($RaceTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->delete($table_to_process, '`RaceTypeId` = ?', $RaceTypeId);
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceStageGroupInfo($RaceStageId,$RaceGroupId,$fields = '*')
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage_group);
		return $this->db->selectRow($table_to_process, $fields, '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceInfo($RaceId,$fields = '*')
	{
		$RaceId = intval($RaceId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->selectRow($table_to_process, $fields, '`RaceId` = ?', $RaceId);
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceList($RaceStageId,$RaceGroupId,$fields = '*')
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->select($table_to_process, $fields, '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertRace(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateRace($RaceId, array $bind)
	{
		$RaceId = intval($RaceId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->update($table_to_process, $bind, '`RaceId` = ?', $RaceId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateRaceStageGroup($RaceStageId,$RaceGroupId,array $bind)
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage_group);
		return $this->db->update($table_to_process, $bind, '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertRaceStageGroup(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_stage_group);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getRaceStageGroupByStage($RaceStageId,$fields = '*')
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage_group);
		return $this->db->select($table_to_process, $fields, '`RaceStageId` = ?', $RaceStageId);
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function deleteRaceStageGroup($RaceStageId,$RaceGroupId)
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage_group);
		return $this->db->delete($table_to_process, '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getTimingDetail($TimingId, $fields = '*')
	{
		$TimingId = intval($TimingId);
		$table_to_process = Base_Widget::getDbTable($this->table_timing);
		return $this->db->selectRow($table_to_process, $fields, '`TimingId` = ?', $TimingId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertTimingDetail(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_timing);
		return $this->db->insert($table_to_process, $bind);
	}
	/**
	 * 更新计时点详情数据
	 * @param integer $TimingId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateTimingDetail($TimingId,array $bind)
	{
		$TimingId = intval($TimingId);
		$table_to_process = Base_Widget::getDbTable($this->table_timing);
		return $this->db->update($table_to_process, $bind,'`TimingId` = ?', $TimingId);
	}

	public function addTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$After,$bind)
	{
		//获取当前分站信息
		$oRaceStage = $this->getRaceStage($RaceStageId,'*');
		//解包压缩数组
		$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$oRaceGroup = $this->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				return false;
			}
			else
			{
				$RaceInfo = $this->getRaceInfo($RaceId);
				if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
				{
					$RaceInfo['comment'] = isset($RaceInfo['comment']) ? json_decode($RaceInfo['comment'], true) : array();
					//获取运动分段的数据
					$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
					//默认不新建数据
					$NewDetail = 0;
					//如果有存储对应计时点信息
					if(isset($SportsTypeInfo['TimingId']) && ($SportsTypeInfo['TimingId']>0))
					{
						$SportsTypeInfo['TimingDetailList'] = $this->getTimingDetail($SportsTypeInfo['TimingId']);
						if(!is_array($SportsTypeInfo['TimingDetailList']))
						{
							$NewDetail = 1;
						}
					}
					else
					{
						$NewDetail = 1;
					}
					$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
					$RaceStageGroupInfo['comment']['DetailList'] = isset($RaceStageGroupInfo['comment']['DetailList'])?$RaceStageGroupInfo['comment']['DetailList']:array();
					ksort($RaceStageGroupInfo['comment']['DetailList']);
					//如果添加在某个元素之后 且 元素下标不越界
					if($After>=0 && $After <= count($SportsTypeInfo['TimingDetailList']['comment']))
					{
						//添加元素
						$SportsTypeInfo['TimingDetailList']['comment'] = Base_Common::array_insert($SportsTypeInfo['TimingDetailList']['comment'],$bind,$After+1);
					}
					//如果在头部添加
					elseif($After == -1)
					{
						//添加元素
						$SportsTypeInfo['TimingDetailList']['comment'] = Base_Common::array_insert($SportsTypeInfo['TimingDetailList']['comment'],$bind,$After+1);
					}
					else
					{
						//默认为在表尾部添加元素
						$SportsTypeInfo['TimingDetailList']['comment'][count($SportsTypeInfo['TimingDetailList']['comment'])] = $bind;
					}
					$this->db->begin();
					//如果认为需要新建数据
					if($NewDetail == 1)
					{
						$insertBind['comment'] = json_encode($SportsTypeInfo['TimingDetailList']['comment']);
						$TimingId = $this->insertTimingDetail($insertBind);
						if($TimingId)
						{
							$RaceInfo['comment']['DetailList'][$SportsTypeId]['TimingId'] = $TimingId;
							$RaceInfo['comment'] = json_encode($RaceInfo['comment']);
							$RaceStageGroupModify = $this->updateRace($RaceId,$RaceInfo);
							if($RaceStageGroupModify)
							{
								$this->db->commit();
								return true;
							}
							else
							{
								$this->db->rollback();
								return false;
							}
						}
						else
						{
							$this->db->rollback();
							return false;
						}
					}
					else
					{
						$updateBind = array('comment' => json_encode($SportsTypeInfo['TimingDetailList']['comment']));
						$TimingDetailUpdate = $this->updateTimingDetail($SportsTypeInfo['TimingId'],$updateBind);
						if($TimingDetailUpdate)
						{
							$this->db->commit();
							return true;
						}
						else
						{
							$this->db->rollback();
							return false;
						}
					}
				}
				else
				{
					return false;
				}

			}
		}
	}
	//更新计时点数据
	public function updateTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$TimingId,$bind)
	{
		//获取当前分站信息
		$oRaceStage = $this->getRaceStage($RaceStageId,'*');
		//解包压缩数组
		$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$oRaceGroup = $this->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				return false;
			}
			else
			{
				$RaceInfo = $this->getRaceInfo($RaceId);
				if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
				{
					$RaceInfo['comment'] = isset($RaceInfo['comment']) ? json_decode($RaceInfo['comment'], true) : array();
					//获取运动分段的数据
					$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
					//如果有存储对应计时点信息
					if(isset($SportsTypeInfo['TimingId']) && ($SportsTypeInfo['TimingId']>0))
					{
						//获取计时点数据
						$SportsTypeInfo['TimingDetailList'] = $this->getTimingDetail($SportsTypeInfo['TimingId']);
						//如果有获取到计时点数据
						if(is_array($SportsTypeInfo['TimingDetailList']))
						{
							//解包数据
							$SportsTypeInfo['TimingDetailList']['comment'] = json_decode($SportsTypeInfo['TimingDetailList']['comment'],true);
							//如果需要被更新的计时点数据存在
							if(isset($SportsTypeInfo['TimingDetailList']['comment'][$TimingId]))
							{
								//替换内容
								$SportsTypeInfo['TimingDetailList']['comment'][$TimingId] = $bind;
								//重新打包计时点数据
								$updateBind = array('comment' => json_encode($SportsTypeInfo['TimingDetailList']['comment']));
								//更新计时点数据
								$TimingDetailUpdate = $this->updateTimingDetail($SportsTypeInfo['TimingId'],$updateBind);
								return $TimingDetailUpdate;
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
	//更新计时点数据
	public function deleteTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$TimingId)
	{
		//获取当前分站信息
		$oRaceStage = $this->getRaceStage($RaceStageId,'*');
		//解包压缩数组
		$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceGroupId]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$oRaceGroup = $this->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				return false;
			}
			else
			{
				$RaceInfo = $this->getRaceInfo($RaceId);
				if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
				{
					$RaceInfo['comment'] = isset($RaceInfo['comment']) ? json_decode($RaceInfo['comment'], true) : array();
					//获取运动分段的数据
					$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
					//如果有存储对应计时点信息
					if(isset($SportsTypeInfo['TimingId']) && ($SportsTypeInfo['TimingId']>0))
					{
						//获取计时点数据
						$SportsTypeInfo['TimingDetailList'] = $this->getTimingDetail($SportsTypeInfo['TimingId']);
						//如果有获取到计时点数据
						if(is_array($SportsTypeInfo['TimingDetailList']))
						{
							//解包数据
							$SportsTypeInfo['TimingDetailList']['comment'] = json_decode($SportsTypeInfo['TimingDetailList']['comment'],true);
							//如果需要被更新的计时点数据存在
							if(isset($SportsTypeInfo['TimingDetailList']['comment'][$TimingId]))
							{
								$deleted = 0;
								//循环检查数据
								foreach($SportsTypeInfo['TimingDetailList']['comment'] as $Key => $TimingPointInfo)
								{
									//如果遇到需要被删除的数据
									if($Key == $TimingId)
									{
										//删除
										unset($SportsTypeInfo['TimingDetailList']['comment'][$Key]);
										//标记为已删除
										$deleted = 1;
									}
									//如果已删除 且 后面的数据存在
									if($deleted == 1 && isset($SportsTypeInfo['TimingDetailList']['comment'][$Key+1]))
									{
										//数据向前复制
										$SportsTypeInfo['TimingDetailList']['comment'][($Key)] = $SportsTypeInfo['TimingDetailList']['comment'][$Key+1];
										//删除后面的数据
										unset($SportsTypeInfo['TimingDetailList']['comment'][$Key+1]);
									}
								}
								//重新打包计时点数据
								$updateBind = array('comment' => json_encode($SportsTypeInfo['TimingDetailList']['comment']));
								//更新计时点数据
								$TimingDetailUpdate = $this->updateTimingDetail($SportsTypeInfo['TimingId'],$updateBind);
								return $TimingDetailUpdate;
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
					else
					{
						return false;
					}
				}
			}
		}
	}
	public function addRace($RaceInfo)
	{
		//获取当前分站信息
		$oRaceStage = $this->getRaceStage($RaceInfo['RaceStageId'],'*');
		//解包压缩数组
		$oRaceStage['comment'] = json_decode($oRaceStage['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($oRaceStage['comment']['SelectedRaceGroup'][$RaceInfo['RaceGroupId']]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$oRaceGroup = $this->getRaceGroup($RaceInfo['RaceGroupId'],'*');
			//如果赛事分组尚未配置
			if(!$oRaceGroup['RaceGroupId'])
			{
				return false;
			}
			else
			{
				$RaceInfo['SingleUser'] = in_array($RaceInfo['SingleUser'],array(0,1))?$RaceInfo['SingleUser']:0;
				$RaceInfo['TeamUser'] = in_array($RaceInfo['TeamUser'],array(0,1))?$RaceInfo['TeamUser']:0;
				$RaceInfo['comment'] = "";
				$RaceInsert = $this->insertRace($RaceInfo);
				return $RaceInsert;
			}
		}
	}
}
