<?php
/**
 * 赛事配置相关mod层
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
	protected $maxRaceDetail = 5;

	protected $raceTimingType = array('chip'=>'芯片计时','gps'=>'gps定位');
	protected $raceLicenseType = array('manager'=>'管理员审核','birthday'=>'生日','sex'=>'性别');

	public function getTimingType()
	{
		return $this->raceTimingType;
	}
	public function getMaxRaceDetail()
	{
		return $this->maxRaceDetail;
	}
	public function getRaceLicenseType()
	{
		return $this->raceLicenseType;
	}
	//获取所有赛事的列表
	public function getRaceCatalogList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY RaceCatalogId ASC";
		$return = $this->db->getAll($sql);
		$RaceCatalogList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceCatalogList[$value['RaceCatalogId']] = $value;
				$RaceCatalogList[$value['RaceCatalogId']]['comment'] = json_decode($RaceCatalogList[$value['RaceCatalogId']]['comment'],true);
			}
		}
		return $RaceCatalogList;
	}
	//获取单个赛事信息
	public function getRaceCatalog($RaceCatalogId, $fields = '*')
	{
		$RaceCatalogId = intval($RaceCatalogId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`RaceCatalogId` = ?', $RaceCatalogId);
	}
	//更新单个赛事信息
	public function updateRaceCatalog($RaceCatalogId, array $bind)
	{
		$RaceCatalogId = intval($RaceCatalogId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`RaceCatalogId` = ?', $RaceCatalogId);
	}
	//添加单个赛事
	public function insertRaceCatalog(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}
	//删除单个赛事
	public function deleteRaceCatalog($RaceCatalogId)
	{
		$RaceCatalogId = intval($RaceCatalogId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`RaceCatalogId` = ?', $RaceCatalogId);
	}
	//根据赛事获取所有组别列表
	//赛事ID为0则获取全部组别
	public function getRaceGroupList($RaceCatalogId,$fields = "*")
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
		$RaceGroupList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceGroupList[$value['RaceGroupId']] = $value;
			}
		}
		return $RaceGroupList;
	}
	//获取单个赛事组别的信息
	public function getRaceGroup($RaceGroupId, $fields = '*')
	{
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->selectRow($table_to_process, $fields, '`RaceGroupId` = ?', $RaceGroupId);
	}
	//更新单个赛事组别
	public function updateRaceGroup($RaceGroupId, array $bind)
	{
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->update($table_to_process, $bind, '`RaceGroupId` = ?', $RaceGroupId);
	}
	//添加单个赛事组别
	public function insertRaceGroup(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->insert($table_to_process, $bind);
	}
	//删除单个赛事组别
	public function deleteRaceGroup($RaceGroupId)
	{
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_group);
		return $this->db->delete($table_to_process, '`RaceGroupId` = ?', $RaceGroupId);
	}
	//根据赛事获取所有分站列表
	public function getRaceStageList($RaceCatalogId,$fields = "*")
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
		$RaceStageList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceStageList[$value['RaceStageId']] = $value;
			}
		}
		return $RaceStageList;
	}
	//获取单个赛事分站信息
	public function getRaceStage($RaceStageId, $fields = '*')
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->selectRow($table_to_process, $fields, '`RaceStageId` = ?', $RaceStageId);
	}
	//更新单个赛事分站
	public function updateRaceStage($RaceStageId, array $bind)
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->update($table_to_process, $bind, '`RaceStageId` = ?', $RaceStageId);
	}
	//新增单个赛事分站
	public function insertRaceStage(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->insert($table_to_process, $bind);
	}
	//删除单个赛事分站
	public function deleteRaceStage($RaceStageId)
	{
		$RaceStageId = intval($RaceStageId);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		return $this->db->delete($table_to_process, '`RaceStageId` = ?', $RaceStageId);
	}
	//获取所有比赛类型信息
	public function getRaceTypeList($fields = "*")
	{
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		$sql = "SELECT $fields FROM " . $table_to_process . " ORDER BY RaceTypeId ASC";
		$return = $this->db->getAll($sql);
		$RaceTypeList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceTypeList[$value['RaceTypeId']] = $value;
				$RaceTypeList[$value['RaceTypeId']]['comment'] = json_decode($RaceTypeList[$value['RaceTypeId']]['comment'],true);
			}
		}
		return $RaceTypeList;
	}
	//获取单个比赛类型信息
	public function getRaceType($RaceTypeId, $fields = '*')
	{
		$RaceTypeId = intval($RaceTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->selectRow($table_to_process, $fields, '`RaceTypeId` = ?', $RaceTypeId);
	}
	//更新单个比赛类型信息
	public function updateRaceType($RaceTypeId, array $bind)
	{
		$RaceTypeId = intval($RaceTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->update($table_to_process, $bind, '`RaceTypeId` = ?', $RaceTypeId);
	}
	//新增单个比赛类型信息
	public function insertRaceType(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->insert($table_to_process, $bind);
	}
	//删除单个比赛类型信息
	public function deleteRaceType($RaceTypeId)
	{
		$RaceTypeId = intval($RaceTypeId);
		$table_to_process = Base_Widget::getDbTable($this->table_type);
		return $this->db->delete($table_to_process, '`RaceTypeId` = ?', $RaceTypeId);
	}
	//获取单个比赛信息
	public function getRace($RaceId,$fields = '*')
	{
		$RaceId = intval($RaceId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->selectRow($table_to_process, $fields, '`RaceId` = ?', $RaceId);
	}
	//获取赛事分站和赛事组别获取比赛列表
	public function getRaceList($RaceStageId,$RaceGroupId,$fields = '*')
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		if(!$RaceGroupId)
		{
			$return = $this->db->select($table_to_process, $fields, '`RaceStageId` = ?', array($RaceStageId));
		}
		else
		{
			$return = $this->db->select($table_to_process, $fields, '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
		}
		$RaceList = array();
		foreach($return as $key => $value)
		{
			$RaceList[$value['RaceId']] = $value;
			if(isset($RaceList[$value['RaceId']]['comment']))
			{
				$RaceList[$value['RaceId']]['comment'] = json_decode($RaceList[$value['RaceId']]['comment'],true);
			}
		}
		return $RaceList;
	}
	//获取赛事分站和赛事组别获取比赛数量
	public function getRaceCount($RaceStageId,$RaceGroupId)
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->selectOne($table_to_process, "count(RaceId) as RaceCount", '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
	}
	//新增单个比赛信息
	public function insertRace(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->insert($table_to_process, $bind);
	}
	//更新单个比赛信息
	public function updateRace($RaceId, array $bind)
	{
		$RaceId = intval($RaceId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		return $this->db->update($table_to_process, $bind, '`RaceId` = ?', $RaceId);
	}
	//获取单个计时点信息
	public function getTimingDetail($TimingId, $fields = '*')
	{
		$TimingId = intval($TimingId);
		$table_to_process = Base_Widget::getDbTable($this->table_timing);
		return $this->db->selectRow($table_to_process, $fields, '`TimingId` = ?', $TimingId);
	}
	//新增单个计时点信息
	public function insertTimingDetail(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_timing);
		return $this->db->insert($table_to_process, $bind);
	}
	//更新单个计时点信息
	public function updateTimingDetail($TimingId,array $bind)
	{
		$TimingId = intval($TimingId);
		$table_to_process = Base_Widget::getDbTable($this->table_timing);
		return $this->db->update($table_to_process, $bind,'`TimingId` = ?', $TimingId);
	}
	//添加单个计时点信息
	public function addTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$After,$bind)
	{
		//获取当前分站信息
		$RaceStageInfo = $this->getRaceStage($RaceStageId,'*');
		//解包压缩数组
		$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$RaceGroupInfo = $this->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				return false;
			}
			else
			{
				//获取比赛信息
				$RaceInfo = $this->getRace($RaceId);
				//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
				if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
				{
					//数据解包
					$RaceInfo['comment'] = isset($RaceInfo['comment']) ? json_decode($RaceInfo['comment'], true) : array();
					//获取运动类型的数据
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
					//初始化运动类型下的计时点列表
					$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
					$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
					ksort($RaceInfo['comment']['DetailList']);
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
		$RaceStageInfo = $this->getRaceStage($RaceStageId,'*');
		//解包压缩数组
		$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$RaceGroupInfo = $this->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				return false;
			}
			else
			{
				//获取比赛信息
				$RaceInfo = $this->getRace($RaceId);
				//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
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
		$RaceStageInfo = $this->getRaceStage($RaceStageId,'*');
		//解包压缩数组
		$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$RaceGroupInfo = $this->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				return false;
			}
			else
			{
				//获取比赛信息
				$RaceInfo = $this->getRace($RaceId);
				//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
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
	//添加比赛
	public function addRace($RaceInfo)
	{
		//获取当前分站信息
		$RaceStageInfo = $this->getRaceStage($RaceInfo['RaceStageId'],'*');
		//解包压缩数组
		$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		//如果当前分站未配置了当前分组
		if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceInfo['RaceGroupId']]))
		{
			return false;
		}
		else
		{
			//获取赛事分组信息
			$RaceGroupInfo = $this->getRaceGroup($RaceInfo['RaceGroupId'],'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
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
	//根据当前时间获取比赛的状态
	public function getRaceTimeStatus($RaceInfo)
	{
		//获取当前时间
		$CurrentTime = time();
		//转化时间为时间戳
		$ApplyStartTime = strtotime(trim($RaceInfo['ApplyStartTime']));
		$ApplyEndTime = strtotime(trim($RaceInfo['ApplyEndTime']));
		$StartTime = strtotime(trim($RaceInfo['StartTime']));
		$EndTime = strtotime(trim($RaceInfo['EndTime']));
		if($CurrentTime < $ApplyStartTime)
		{
			$RaceStatus = array('RaceStatus'=>1,'RaceStatusName'=>'未开始报名');
		}
		elseif ($CurrentTime >= $ApplyStartTime && $CurrentTime < $ApplyEndTime)
		{
			$RaceStatus = array('RaceStatus'=>2,'RaceStatusName'=>'报名中');
		}
		elseif ($CurrentTime >= $ApplyEndTime && $CurrentTime < $StartTime)
		{
			$RaceStatus = array('RaceStatus'=>4,'RaceStatusName'=>'报名结束');
		}
		elseif ($CurrentTime >= $StartTime && $CurrentTime < $EndTime)
		{
			$RaceStatus = array('RaceStatus'=>8,'RaceStatusName'=>'比赛中');
		}
		else
		{
			$RaceStatus = array('RaceStatus'=>16,'RaceStatusName'=>'比赛结束');
		}
		return $RaceStatus;
	}
	//根据当前时间获取比赛的状态
	public function getRaceStageTimeStatus($RaceStageId,$RaceGroupId)
	{
		//获取当前时间
		$CurrentTime = time();
		//转化时间为时间戳
		$RaceList = $this->getRaceList($RaceStageId, $RaceGroupId, $fields = 'RaceId,ApplyStartTime,ApplyEndTime,StartTime,EndTime');
		//最小开始报名时间
		$MinApplyStartTime = 0;
		//最大结束报名时间
		$MaxApplyEndTime = 0;
		//最小开始比赛时间
		$MinStartTime = 0;
		//最大结束比赛时间
		$MaxEndTime = 0;
		//循环比赛列表
		foreach ($RaceList as $RaceId => $RaceInfo)
		{
			//如果开始报名时间有效
			if (strtotime($RaceInfo['ApplyStartTime']))
			{
				//放入备选数组
				$MinApplyStartTime = $MinApplyStartTime>0?min($MinApplyStartTime,strtotime($RaceInfo['ApplyStartTime'])):strtotime($RaceInfo['ApplyStartTime']);
			}
			//如果结束报名时间有效
			if (strtotime($RaceInfo['ApplyEndTime']))
			{
				//放入备选数组
				$MaxApplyEndTime = $MaxApplyEndTime>0?max($MaxApplyEndTime,strtotime($RaceInfo['ApplyEndTime'])):strtotime($RaceInfo['ApplyEndTime']);
			}

			//如果开始比赛时间有效
			if (strtotime($RaceInfo['StartTime']))
			{
				//放入备选数组
				$MinStartTime = $MinStartTime>0?min($MinStartTime,strtotime($RaceInfo['StartTime'])):strtotime($RaceInfo['StartTime']);
			}
			//如果结束比赛时间有效
			if (strtotime($RaceInfo['EndTime']))
			{
				//放入备选数组
				$MaxEndTime = $MaxEndTime>0?max($MaxEndTime,strtotime($RaceInfo['EndTime'])):strtotime($RaceInfo['EndTime']);
			}

		}

		if ($MinApplyStartTime == 0)
		{
			$StageTimeStatus = array('StageStatus' => 1, 'StageStatusName' => '报名即将开始');
		}
		//如果当前时间早于最小开始报名时间
		elseif ($CurrentTime < $MinApplyStartTime)
		{
			$StageTimeStatus = array('StageStatus' => 1, 'StageStatusName' => '报名即将开始');
		}
		//如果当前时间早于最大结束报名时间
		elseif ($CurrentTime <= $MaxApplyEndTime)
		{
			$StageTimeStatus = array('StageStatus' => 2, 'StageStatusName' => '报名中');
		}
		//如果当前时间晚于最大结束报名时间 且 早于最小报名时间
		elseif (($CurrentTime > $MaxApplyEndTime) && ($CurrentTime < $MinStartTime))
		{
			$StageTimeStatus = array('StageStatus' => 4, 'StageStatusName' => '报名结束');
		}
		//如果当前时间大于最小比赛开始时间 且 小于最大比赛结束时间
		elseif (($CurrentTime >= $MinStartTime) && ($CurrentTime <= $MaxEndTime))
		{
			$StageTimeStatus = array('StageStatus' => 8, 'StageStatusName' => '比赛中');
		}
		//如果当前时间大于最小比赛开始时间 且 小于最大比赛结束时间
		elseif ($CurrentTime > $MaxEndTime)
		{
			$StageTimeStatus = array('StageStatus' => 16, 'StageStatusName' => '比赛结束');
		}
		return $StageTimeStatus;
	}
	//把执照获得条件转化成HTML
	public function ParthRaceLicenseListToHtml($RaceLicenseList,$ReturnType=1,$delete=0,$array=0)
	{
		//如果已配置执照条件列表
		if(count($RaceLicenseList))
		{
			//获取条件列表
			$RaceLisenceTypeList = $this->getRaceLicenseType();
			//初始化空字符串
			$text = array();
			//循环条件列表
			foreach ($RaceLicenseList as $key => $LicenseInfo)
			{
				//如果已配置当前条件
				if(isset($RaceLisenceTypeList[$LicenseInfo['LicenseType']]))
				{
					$text[$key] = "".$RaceLisenceTypeList[$LicenseInfo['LicenseType']].": ";
					//根据不同的条件类型拼接不同的字符串
					$functionName = $LicenseInfo['LicenseType']."ConditionToHtml";
					$text[$key].= " ".$this->$functionName("LicenseList[".$key."]",$LicenseInfo,$ReturnType)."";
					if($delete)
					{
						$text[$key].= '<a href="javascript:void(0);" onclick="LicenseDelete('."'".$delete."'".','."'".$key."'".','."'".$RaceLisenceTypeList[$LicenseInfo['LicenseType']]."'".')"> 删除 </a>';
					}
				}
				else
				{
					//删除数据
					unset($RaceLicenseList[$key]);
				}
			}
			return $array==1?$text:("".implode("<br>",$text)."");
		}
		else
		{
			return "";
		}
	}
	//管理员赋予
	public function managerConditionToHtml($key,$LicenseInfo,$ReturnType = 1)
	{
		if(!count($LicenseInfo))
		{
			$LicenseInfo  = array("LicenseType"=>"manager","License"=>0);
		}
		if($ReturnType==1)
		{
			$text = '<input type="hidden" name="'.$key.'[LicenseType]" id="'.$key.'[LicenseType]" value="manager">
			<input type="radio" name="'.$key.'[License]" id="'.$key.'[License]" value="1" '.((isset($LicenseInfo['License'])&&$LicenseInfo['License']==1)?'checked':"").'>是
			<input type="radio" name="'.$key.'[License]" id="'.$key.'[License]" value="0" '.((!isset($LicenseInfo['License'])||$LicenseInfo['License']==0)?'checked':"").'>否';
		}
		else
		{
			$text = ((isset($LicenseInfo['License'])&&$LicenseInfo['License']==1)?"是":"否");
		}
		return $text;
	}
	//生日
	public function birthdayConditionToHtml($key,$LicenseInfo,$ReturnType = 1)
	{
		if(!count($LicenseInfo))
		{
			$LicenseInfo  = array("LicenseType"=>"birthday","License"=>array("equal"=>">=","Date"=>date("Y-m-d",time())));
		}
		if($ReturnType==1)
		{
			$text = '<input type="hidden" name="'.$key.'[LicenseType]" id="'.$key.'[LicenseType]" value="birthday"><select name="'.$key.'[License][equal]" size="1" class="span2">';
			$equalList = Base_common::equalList();
			foreach ($equalList as $value) {
				$text .= '<option value="' . $value . '" ' . ((isset($LicenseInfo['License']['equal']) && $LicenseInfo['License']['equal'] == $value) ? 'selected' : "") . '>' . $value . '</option>';
			}
			$text .= "</select>";
			$text .= '<input type="text" class="span2" name="'.$key.'[License][Date]" value="' . $LicenseInfo['License']['Date'] . '" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:' . "'yyyy-MM-dd'" . '})">';
		}
		else
		{
			$text = $LicenseInfo['License']['equal'].$LicenseInfo['License']['Date'];
		}
		return $text;
	}
	//管理员赋予
	public function sexConditionToHtml($key,$LicenseInfo,$ReturnType = 1)
	{
		$oUser = new Xrace_User();
		$sexList = $oUser -> getSexList();
		if(!count($LicenseInfo))
		{
			$LicenseInfo  = array("LicenseType"=>"sex","License"=>array_pop(array_keys($sexList)));
		}
		if($ReturnType==1)
		{

			$text = '<input type="hidden" name="'.$key.'[LicenseType]" id="'.$key.'[LicenseType]" value="sex">';
			foreach($sexList as $sex => $sex_name)
			{
				$text.='<input type="radio" name="'.$key.'[License]" id="'.$key.'[License]" value="'.$sex.'" '.((isset($LicenseInfo['License'])&&intval($LicenseInfo['License'])==$sex)?'checked':"").'>'.$sex_name;
			}
		}
		else
		{
			$text = isset($sexList[$LicenseInfo['License']])?$sexList[$LicenseInfo['License']]:$sexList[array_pop(array_keys($sexList))];
		}
		return $text;
	}
	//处理价格列表
	public function getPriceList($PriceList,$Revert = 0)
	{
		if(trim($PriceList)=="")
		{
			return ($Revert==1)?"":array();
		}
		//首层以|切割
		$P = explode("|",$PriceList);
		//初始化空数组
		$PriceList = array();
		foreach($P as $key => $value)
		{
			//以:切割
			$T = explode(":",$value);
			//如果切割数量大于等于2
			if(count($T)>=2)
			{
				if((abs(intval($T[1])))>0)
				{
					$PriceList[abs(intval($T[0]))] = abs(intval($T[1]));
				}
			}
			//如果只有1
			elseif(count($T)==1)
			{
				if((abs(intval($T[0])))>0)
				{
					$PriceList[1] = abs(intval($T[0]));
				}
			}
		}
		ksort($PriceList);
		if($Revert == 1)
		{
			foreach($PriceList as $num => $price)
			{
				$PriceList[$num] = intval($num).":".intval($price);
			}
			$PriceList = implode("|",$PriceList);
		}
		return  $PriceList;
	}
	//根据报名记录生成指定场次比赛选手的计时记录到配置文件
	public function genRaceLogToText($RaceId)
	{
		$RaceId = intval($RaceId);
		//获取比赛信息
		$RaceInfo = $this->getRace($RaceId);
		//如果获取到比赛信息
		if(isset($RaceInfo['RaceId']))
		{
			//查找到的计时点信息
			$TimingCount = 0;
			$i = 0;$TimingPointList = array();
			//数据解包
			$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
			//如果有配置赛段信息
			if(isset($RaceInfo['comment']['DetailList']) && count($RaceInfo['comment']['DetailList']))
			{
				//循环赛段信息
				foreach($RaceInfo['comment']['DetailList'] as $SportsType => $TimingList)
				{
					$TimingPointList['Sports'][$SportsType]['TimingPointList'] = array();
					//如果有配置计时点信息
					if(isset($TimingList['TimingId']))
					{
						//获取计时点详情信息
						$TimingInfo = $this->getTimingDetail($TimingList['TimingId']);
						if(isset($TimingInfo['TimingId']))
						{
							$TimingInfo['comment'] = json_decode($TimingInfo['comment'],true);
							if(count($TimingInfo['comment']))
							{
								foreach($TimingInfo['comment'] as $TimingPoint)
								{
									if(count($TimingPoint))
									{
										$TimingCount++;
										for($j = 0;$j<$TimingPoint['Round'];$j++)
										{
											$TimingPointList['Sports'][$SportsType]['TimingPointList'][] = $i+1;
											$t = $TimingPoint;
											$t['TName'].= "*".($j+1);
											$t['inTime'] = 0;
											$t['outTime'] = 0;
											$TimingPointList['Point'][$i+1] = $t;
											$i++;
										}
									}
								}
							}
						}
					}
				}
				//如果未检测到任何的计时点信息
				if($TimingCount==0)
				{
					return false;
				}
				else
				{
					//生成查询条件
					$params = array('RaceId'=>$RaceInfo['RaceId']);
					$oUser = new Xrace_User();
					//获取选手名单
					$RaceUserList = $oUser->getRaceUserList($params);
					//如果获取到选手名单
					if(count($RaceUserList))
					{
						//循环选手列表
						foreach($RaceUserList as $ApplyId => $ApplyInfo)
						{
							//获取用户信息
							$UserInfo = $oUser->getUserInfo( $ApplyInfo["UserId"],'user_id,name');
							//如果获取到用户
							if($UserInfo['user_id'])
							{
								$TimingPointList['UserInfo'] = array('UserName'=>$UserInfo['name'],'UserId' => $UserInfo['user_id']);
								$filePath = __APP_ROOT_DIR__."Timing"."\\".$RaceInfo['RaceId']."\\";
								$fileName = $UserInfo['user_id'].".php";
								Base_Common::rebuildConfig($filePath,$fileName,$TimingPointList,"Timing");
							}
						}
					}
					else
					{
						return fasle;
					}

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
