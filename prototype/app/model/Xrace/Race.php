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
	//赛事ID为0则获取全部分站
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
	public function getRaceInfo($RaceId,$fields = '*')
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
				$RaceInfo = $this->getRaceInfo($RaceId);
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
				$RaceInfo = $this->getRaceInfo($RaceId);
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
				$RaceInfo = $this->getRaceInfo($RaceId);
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
			$RaceStatus = array('RaceStatus'=>3,'RaceStatusName'=>'报名结束');
		}
		elseif ($CurrentTime >= $StartTime && $CurrentTime < $EndTime)
		{
			$RaceStatus = array('RaceStatus'=>4,'RaceStatusName'=>'比赛中');
		}
		else
		{
			$RaceStatus = array('RaceStatus'=>5,'RaceStatusName'=>'比赛结束');
		}
		return $RaceStatus;
	}
	//根据当前时间获取比赛的状态
	public function getRaceStageTimeStatus($RaceStageId,$RaceGroupId)
	{
		//获取当前时间
		$CurrentTime = time();
		//转化时间为时间戳
		$RaceList = $this->getRaceList($RaceStageId, $RaceGroupId, $fields = 'RaceId,ApplyStartTime,ApplyEndTime');
		//最小开始报名时间数组
		$MinStartTime = array();
		//最大结束报名时间数组
		$MaxEndTime = array();
		//循环比赛列表
		foreach ($RaceList as $RaceId => $RaceInfo) {
			//如果开始报名时间有效
			if (strtotime($RaceInfo['ApplyStartTime'])) {
				//放入备选数组
				$MinStartTime[] = strtotime($RaceInfo['ApplyStartTime']);
			}
			//如果结束报名时间有效
			if (strtotime($RaceInfo['ApplyEndTime'])) {
				//放入备选数组
				$MaxEndTime[] = strtotime($RaceInfo['ApplyEndTime']);
			}
		}
		if (count($MinStartTime)) {
			//获取最小开始报名时间
			$MinStartTime = min($MinStartTime);
		} elseif (count($MaxEndTime)) {
			//获取最大报名时间
			$MaxEndTime = max($MaxEndTime);
		} else {
			$MinStartTime = 0;
			$MaxEndTime = 0;
		}
		if ($MinStartTime == 0) {
			$StageTimeStatus = array('StageStatus' => 1, 'StageStatusName' => '报名即将开始');
		} //如果当前时间早于最小开始报名时间
		elseif ($CurrentTime < $MinStartTime) {
			$StageTimeStatus = array('StageStatus' => 1, 'StageStatusName' => '报名即将开始');
		} //如果当前时间早于最大结束报名时间
		elseif ($CurrentTime <= $MaxEndTime) {
			$StageTimeStatus = array('StageStatus' => 2, 'StageStatusName' => '报名中');
		} //如果当前时间晚于最大结束报名时间
		elseif ($CurrentTime > $MaxEndTime) {
			$StageTimeStatus = array('StageStatus' => 3, 'StageStatusName' => '报名结束');
		}
		return $StageTimeStatus;
	}
	//把执照获得条件转化成HTML
	public function ParthRaceLicenseListToHtml($RaceLicenseList,$edit=1)
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
					$text[$key] = "<tr><td>".$RaceLisenceTypeList[$LicenseInfo['LicenseType']]."</td>";
					//根据不同的条件类型拼接不同的字符串
					$functionName = $LicenseInfo['LicenseType']."ConditionToHtml";
					$text[$key].= "<td>".$this->$functionName("LicenseList[".$key."]",$LicenseInfo,$edit)."</td>";
					$text[$key].="</tr>";
				}
				else
				{
					//删除数据
					unset($RaceLicenseList[$key]);
				}
			}
			return "<table>".implode("",$text)."</table>";
		}
		else
		{
			return "";
		}
	}
	//管理员赋予
	public function managerConditionToHtml($key,$LicenseInfo,$edit = 1)
	{
		if(!count($LicenseInfo))
		{
			$LicenseInfo  = array("LicenseType"=>"manager","License"=>0);
		}
		if($edit==1)
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
	public function birthdayConditionToHtml($key,$LicenseInfo,$edit = 1)
	{
		if(!count($LicenseInfo))
		{
			$LicenseInfo  = array("LicenseType"=>"birthday","License"=>array("equal"=>">=","Date"=>date("Y-m-d",time())));
		}
		if($edit==1)
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
	public function sexConditionToHtml($key,$LicenseInfo,$edit = 1)
	{
		if(!count($LicenseInfo))
		{
			$LicenseInfo  = array("LicenseType"=>"sex","License"=>"Male");
		}
		if($edit==1)
		{
			$text = '<input type="hidden" name="'.$key.'[LicenseType]" id="'.$key.'[LicenseType]" value="sex">
			<input type="radio" name="'.$key.'[License]" id="'.$key.'[License]" value="Male" '.((isset($LicenseInfo['License'])&&$LicenseInfo['License']=="Male")?'checked':"").'>男
			<input type="radio" name="'.$key.'[License]" id="'.$key.'[License]" value="Female" '.((!isset($LicenseInfo['License'])||$LicenseInfo['License']=="Female")?'checked':"").'>女';
		}
		else
		{
			$text = ((isset($LicenseInfo['License'])&&$LicenseInfo['License']=="Male")?"男":"女");
		}
		return $text;
	}
}
