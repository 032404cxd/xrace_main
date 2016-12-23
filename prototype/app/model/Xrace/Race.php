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
	protected $table_combination = 'config_race_combination';
	protected $maxRaceDetail = 5;

	protected $raceStructure = array('race'=>'比赛-分组','group'=>'分组-比赛');
	protected $raceTimingType = array('mylaps'=>'myLaps芯片计时');
	protected $raceTimingResultType = array('gunshot'=>'发枪时间','net'=>'净时间');
	protected $raceLicenseType = array('manager'=>'管理员审核','birthday'=>'生日','sex'=>'性别','age'=>"年龄");
    protected $applySourceList = array(1=>"线上",2=>"线下");
	public function getRaceStructure()
	{
		return $this->raceStructure;
	}
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
	public function getRaceTimingResultType($RaceResultType)
	{
		//获取预定义数组
		$raceTimingResultTypeList = $this->raceTimingResultType;
		//如果有指定分类
		if(isset($raceTimingResultTypeList[$RaceResultType]))
		{
			return $raceTimingResultTypeList[$RaceResultType];
		}
		else
		{
			//否则返回整个列表
			return $raceTimingResultTypeList;
		}
	}
    public function getAppSourceList()
    {
        return $this->raceLicenseType;
    }

	//获取所有赛事的列表(已缓存)
	public function getRaceCatalogList($Display = 0,$fields = "*",$Cache = 0)
	{
        $oMemCache = new Base_Cache_Memcache("xrace");
        //如果需要获取缓存
        if($Cache == 1)
        {
            //获取缓存
            $m = $oMemCache->get("RaceCatalogList");
            //缓存解开
            $RaceCatalogList = json_decode($m,true);
            //如果数据为空
            if(count($RaceCatalogList)==0)
            {
                //需要从数据库获取
                $NeedDB = 1;
            }
            else
            {
                //echo "cached";
            }
        }
        else
        {
            //需要从数据库获取
            $NeedDB = 1;
        }
        if(isset($NeedDB))
        {
            $table_to_process = Base_Widget::getDbTable($this->table);
            $whereDisplay = $Display == 1?" and Display = '1'":"";
            $sql = "SELECT $fields FROM " . $table_to_process . " where 1 ".$whereDisplay." ORDER BY RaceCatalogId ASC";
            $return = $this->db->getAll($sql);
            $RaceCatalogList = array();
            if(count($return))
            {
                foreach($return as $key => $value)
                {
                    $RaceCatalogList[$value['RaceCatalogId']] = $value;
                    if(isset($RaceCatalogList[$value['RaceCatalogId']]['comment']))
                    {
                        $RaceCatalogList[$value['RaceCatalogId']]['comment'] = json_decode($RaceCatalogList[$value['RaceCatalogId']]['comment'],true);
                    }
                }
            }
        }
        //如果有获取到最新赛事列表
        if(count($RaceCatalogList))
        {
            //写入缓存
            $oMemCache -> set('RaceCatalogList',json_encode($RaceCatalogList),3600);
        }
		return $RaceCatalogList;
	}
	//获取单个赛事信息
	public function getRaceCatalog($RaceCatalogId, $fields = '*',$Cache = 0)
	{
        $RaceCatalogId = intval($RaceCatalogId);
        $oMemCache = new Base_Cache_Memcache("xrace");
        //如果需要获取缓存
        if($Cache == 1)
        {
            //获取缓存
            $m = $oMemCache->get("RaceCatalogInfo_".$RaceCatalogId);
            //缓存解开
            $RaceCatalogInfo = json_decode($m,true);
            //如果数据为空
            if(count($RaceCatalogInfo)==0)
            {
                //需要从数据库获取
                $NeedDB = 1;
            }
            else
            {
                //echo "cached";
            }
        }
        else
        {
            //需要从数据库获取
            $NeedDB = 1;
        }
        if(isset($NeedDB))
        {
            $table_to_process = Base_Widget::getDbTable($this->table);
            $RaceCatalogInfo =  $this->db->selectRow($table_to_process, $fields, '`RaceCatalogId` = ?', $RaceCatalogId);
        }
        //如果有获取到最新赛事信息
        if($RaceCatalogInfo['RaceCatalogId'])
        {
            //写入缓存
            $oMemCache -> set("RaceCatalogInfo_".$RaceCatalogId,json_encode($RaceCatalogInfo),3600);
        }
        return $RaceCatalogInfo;
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
    //根据分组名称获取单个赛事组别的信息
    public function getRaceGroupByName($RaceGroupName, $fields = '*')
    {
        $RaceGroupName = trim($RaceGroupName);
        $table_to_process = Base_Widget::getDbTable($this->table_group);
        return $this->db->selectRow($table_to_process, $fields, '`RaceGroupName` = ?', $RaceGroupName);
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
	public function getRaceStageList($RaceCatalogId,$fields = "*",$Display = 0)
	{
		$RaceCatalogId = trim($RaceCatalogId);
		//初始化查询条件
		$whereCatalog = ($RaceCatalogId != 0)?" RaceCatalogId = $RaceCatalogId":"";
        $whereDisplay = $Display == 1?" Display = '1'":"";

		$whereCondition = array($whereCatalog,$whereDisplay);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table_stage);
		$sql = "SELECT $fields FROM " . $table_to_process . "  where 1 ".$where." ORDER BY RaceCatalogId,RaceStageId DESC";
        $return = $this->db->getAll($sql);
		$RaceStageList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				if(isset($value['comment']))
				{
					$value['comment'] = json_decode($value['comment'],true);
				}
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
				$RaceTypeList[$value['RaceTypeId']]['comment'] = isset($RaceTypeList[$value['RaceTypeId']]['comment'])?json_decode($RaceTypeList[$value['RaceTypeId']]['comment'],true):array();
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
	public function getRaceList($params,$fields = '*')
	{
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		//初始化查询条件
		$whereGroup = (isset($params['RaceGroupId']) && ($params['RaceGroupId'] >0))?(" RaceGroupId = ".$params['RaceGroupId']):"";
		$whereStage = (isset($params['RaceStageId']) && ($params['RaceStageId'] >0))?(" RaceStageId = ".$params['RaceStageId']):"";
		//$whereStartTime = (isset($params['RaceStartTime']) && ($params['RaceStartTime'] >0))?(" unix_timestamp(StartTime) >= ".$params['RaceStartTime']):"";
		//$whereEndTime = (isset($params['RaceEndTime']) && ($params['RaceEndTime'] >0))?(" unix_timestamp(EndTime) <= ".$params['RaceEndTime']):"";
		$whereIn = (isset($params['inRun']) && ($params['inRun']==1))?("((EndTime >= '".date("Y-m-d H:i:s",time()-1800)."') and (StartTime <= '".date("Y-m-d H:i:s",time()+1800). "'))"):"";
		$whereCondition = array($whereGroup,$whereStage,$whereIn);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$sql = "SELECT $fields FROM " . $table_to_process . "  where 1 ".$where." ORDER BY RaceId asc";
        $return = $this->db->getAll($sql);
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
	
	//获取赛事分站和赛事组别获取比赛列表
	public function getRaceListBak($RaceStageId,$RaceGroupId,$fields = '*')
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
	public function getRaceCount($RaceStageId,$RaceGroupId=0)
	{
		$RaceStageId = intval($RaceStageId);
		$RaceGroupId = intval($RaceGroupId);
		$table_to_process = Base_Widget::getDbTable($this->table_race);
		if($RaceGroupId)
		{
			return $this->db->selectOne($table_to_process, "count(RaceId) as RaceCount", '`RaceStageId` = ? and `RaceGroupId` = ?', array($RaceStageId,$RaceGroupId));
		}
		else
		{
			return $this->db->selectOne($table_to_process, "count(RaceId) as RaceCount", '`RaceStageId` = ?', $RaceStageId);
		}
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
    //更新单个比赛信息
    public function deleteRace($RaceId)
    {
        $RaceId = intval($RaceId);
        $table_to_process = Base_Widget::getDbTable($this->table_race);
        return $this->db->delete($table_to_process, '`RaceId` = ?', $RaceId);
    }
    //删除单个赛事
    public function deleteRaceInfo($RaceId)
    {
        $RaceId = intval($RaceId);
        $table_to_process = Base_Widget::getDbTable($this->table_race);
        //获取比赛信息
        $RaceInfo = $this->getRace($RaceId,"RaceId,comment");
        //如果获取到比赛
        if($RaceInfo['RaceId'])
        {
            //数据解包
            $RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
            foreach($RaceInfo['comment']['DetailList'] as $key => $TimingPointInfo)
            {
                if(isset($TimingPointInfo['TimingId']))
                {
                    //删除计时点配置
                    $this->deleteTimingDetail($TimingPointInfo['TimingId']);
                }
            }
            $oUser = new Xrace_User();
            //删除报名记录
            $deleteApply = $oUser->deleteRaceApplyUserInfo($RaceInfo['RaceId']);
            //删除比赛
            $deleteRace = $this->deleteRace($RaceInfo['RaceId']);
            return true;
        }
        else
        {
            return false;
        }
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
    //删除单个计时点信息
    public function deleteTimingDetail($TimingId)
    {
        $table_to_process = Base_Widget::getDbTable($this->table_timing);
        return $this->db->delete($table_to_process,'`TimingId` = ?', $TimingId);
    }
	//添加单个计时点信息
	public function addTimingPoint($RaceId,$SportsTypeId,$After,$bind)
	{
		//对计时点等待时间进行处理
		$bind['TolaranceTime'] = abs(intval($bind['TolaranceTime']));
		//获取比赛信息
		$RaceInfo = $this->getRace($RaceId);
		//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
		if(isset($RaceInfo['RaceId']))
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
	//更新计时点数据
	public function updateTimingPoint($RaceId,$SportsTypeId,$TimingId,$bind)
	{
		//对计时点等待时间进行处理
		$bind['TolaranceTime'] = abs(intval($bind['TolaranceTime']));
		//获取比赛信息
		$RaceInfo = $this->getRace($RaceId);
		//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
		if(isset($RaceInfo['RaceId']))
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
	}
	//更新计时点数据
	public function deleteTimingPoint($RaceId,$SportsTypeId,$TimingId)
	{
		//获取比赛信息
		$RaceInfo = $this->getRace($RaceId);
		//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
		if(isset($RaceInfo['RaceId']))
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
    //更新计时点数据
    public function insertTimingPointCredit($RaceId,$SportsTypeId,$TimingId,$bind)
    {
        //获取比赛信息
        $RaceInfo = $this->getRace($RaceId);
        //如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
        if(isset($RaceInfo['RaceId']))
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
                        if(isset($SportsTypeInfo['TimingDetailList']['comment'][$TimingId]))
                        {
                            //替换内容
                            $SportsTypeInfo['TimingDetailList']['comment'][$TimingId]['CreditList'][$bind['CreditId']] = $bind;
                        }
                        else
                        {
                            return false;
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
    //更新计时点数据
    public function deleteTimingPointCredit($RaceId,$SportsTypeId,$TimingId,$CreditId)
    {
        //获取比赛信息
        $RaceInfo = $this->getRace($RaceId);
        //如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
        if(isset($RaceInfo['RaceId']))
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
                        if(isset($SportsTypeInfo['TimingDetailList']['comment'][$TimingId]))
                        {
                            //替换内容
                            unset($SportsTypeInfo['TimingDetailList']['comment'][$TimingId]['CreditList'][$CreditId]);
                        }
                        else
                        {
                            return false;
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
	//添加比赛
	public function addRace($RaceInfo)
	{
		//获取当前分站信息
		$RaceStageInfo = $this->getRaceStage($RaceInfo['RaceStageId'],'RaceStageId,comment');
		//解包压缩数组
		$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		//比赛-分组的层级规则
		$RaceStructureList  = $this->getRaceStructure();
		//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
		if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
		{
			//默认为分组优先
			$RaceStageInfo['comment']['RaceStructure'] = "group";
		}
		//如果当前分站未配置了当前分组
		if(!in_array($RaceInfo['RaceGroupId'],$RaceStageInfo['comment']['SelectedRaceGroup']) && ($RaceStageInfo['comment']['RaceStructure'] =="group") )
		{
			return false;
		}
		else
		{
			$RaceInfo['SingleUser'] = in_array($RaceInfo['SingleUser'],array(0,1))?$RaceInfo['SingleUser']:0;
			$RaceInfo['TeamUser'] = in_array($RaceInfo['TeamUser'],array(0,1))?$RaceInfo['TeamUser']:0;
			$RaceInsert = $this->insertRace($RaceInfo);
			return $RaceInsert;
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
			$RaceStatus = array('RaceStatus'=>1,'RaceStatusName'=>'即将报名');
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
		$RaceList = $this->getRaceList(array("RaceStageId"=>$RaceStageId, "RaceGroupId"=>$RaceGroupId), $fields = 'RaceId,ApplyStartTime,ApplyEndTime,StartTime,EndTime');
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
    //年龄
    public function ageConditionToHtml($key,$LicenseInfo,$ReturnType = 1)
    {
        if(!count($LicenseInfo))
        {
            $LicenseInfo  = array("LicenseType"=>"age","License"=>array("equal"=>">=","age"=>18));
        }
        if($ReturnType==1)
        {
            $text = '<input type="hidden" name="'.$key.'[LicenseType]" id="'.$key.'[LicenseType]" value="age"><select name="'.$key.'[License][equal]" size="1" class="span2">';
            $equalList = Base_common::equalList();
            foreach ($equalList as $value) {
                $text .= '<option value="' . $value . '" ' . ((isset($LicenseInfo['License']['equal']) && $LicenseInfo['License']['equal'] == $value) ? 'selected' : "") . '>' . $value . '</option>';
            }
            $text .= "</select>";
            $text .= '<input type="text" class="span1" name="'.$key.'[License][Age]" value="' . $LicenseInfo['License']['Age'] . '">';
        }
        else
        {
            $text = $LicenseInfo['License']['equal'].$LicenseInfo['License']['Age'];
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
				if((abs(sprintf("%10.2f",$T[1])))>0)
				{
					$PriceList[abs(intval($T[0]))] = abs(sprintf("%10.2f",$T[1]));
				}
			}
			//如果只有1
			elseif(count($T)==1)
			{
				if((abs(intval($T[0])))>0)
				{
					$PriceList[1] = abs(sprintf("%10.2f",$T[0]));
				}
			}
		}
		ksort($PriceList);
		if($Revert == 1)
		{
			foreach($PriceList as $num => $price)
			{
				$PriceList[$num] = intval($num).":".trim(sprintf("%10.2f",$price));
			}
			$PriceList = implode("|",$PriceList);
		}
		return  $PriceList;
	}
	//根据报名记录生成指定场次比赛选手的计时记录到配置文件
	public function genRaceLogToText($RaceId,$UserId = 0)
	{
		$RaceId = intval($RaceId);
		//获取比赛信息
		$RaceInfo = $this->getRace($RaceId,"RaceId,RaceTypeId,StartTime,EndTime,RaceStageId,RaceGroupId,RaceName,comment,RouteInfo");
		//如果获取到比赛信息
		if(isset($RaceInfo['RaceId']))
		{
			//查找到的计时点信息
			$TimingCount = 0;
			$i = 0;$TimingPointList = array();
			//数据解包
			$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
			//地图相关数据解包
			$RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
			//如果有配置赛段信息
			if(isset($RaceInfo['comment']['DetailList']) && count($RaceInfo['comment']['DetailList']))
			{
				//循环赛段信息
				foreach($RaceInfo['comment']['DetailList'] as $SportsType => $TimingList)
				{
					//每个分段的距离初始化为0
					$SectionDistence = 0;
					//初始化空数组
					$TimingPointList['Sports'][$SportsType]['TimingPointList'] = array();
					//如果有配置计时点信息
					if(isset($TimingList['TimingId']))
					{
						//获取计时点详情信息
						$TimingInfo = $this->getTimingDetail($TimingList['TimingId']);
						//如果计时点数据获取成功
						if(isset($TimingInfo['TimingId']))
						{
							unset($RaceInfo['comment']['DetailList']);
							$TimingPointList['RaceInfo'] = $RaceInfo;
							//数据解包
							$TimingInfo['comment'] = json_decode($TimingInfo['comment'],true);
							//如果解包后有计时点数据
							if(count($TimingInfo['comment']))
							{
								//循环计时点数据
								foreach($TimingInfo['comment'] as $TimingPoint)
								{
									//如果有计时点
									if(count($TimingPoint))
									{
										//依次累加
										$TimingCount++;
										$oSports = new Xrace_Sports();
										//获取运动信息
										$SportsTypeInfo = $oSports->getSportsType($TimingList['SportsTypeId'],"SportsTypeId,SportsTypeName,SpeedDisplayType");
										if(isset($SportsTypeInfo['SportsTypeId']))
										{
											$SportsTypeInfo['TimingId']=$TimingList['TimingId'];
											//保存运动本身信息
											$TimingPointList['Sports'][$SportsType]['SportsTypeInfo'] = $SportsTypeInfo;
											// 按照计时点通过测次数进行循环
											for($j = 0;$j<$TimingPoint['Round'];$j++)
											{
												$TimingPointList['Sports'][$SportsType]['TimingPointList'][] = $i+1;
												$t = $TimingPoint;
												//第一次通过不需要下标
												$t['TName'].= ($j==0)?"":"*".($j+1);
												$t['inTime'] = 0;
												//每个分段内距离相互累加 如果距离为正数
                                                $t['ToNext'] = intval($t['ToNext']);
                                                $SectionDistence += ($TimingPointList['Point'][$i]['ToNext']>0)?$TimingPointList['Point'][$i]['ToNext']:0;
												$t['CurrentDistense'] = $SectionDistence;
												$t['SportsTypeName'] = $SportsTypeInfo['SportsTypeName'];
                                                $t['SpeedDisplayType'] = $SportsTypeInfo['SpeedDisplayType'];
												//初始化通过的用户列表
												$t['UserList'] = array();
												$TimingPointList['Point'][$i+1] = $t;
												$i++;
											}
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
					$params = array('RaceId'=>$RaceInfo['RaceId'],'UserId'=>$UserId);
					if($UserId==0)
					{
						$filePath = __APP_ROOT_DIR__."Timing"."/".$RaceInfo['RaceId']."/";
						$fileName = "Total".".php";
						//生成配置文件
						Base_Common::rebuildConfig($filePath,$fileName,$TimingPointList,"Timing");
					}
					$oUser = new Xrace_User();
					$oTeam = new Xrace_Team();
					//获取选手名单
					$RaceUserList = $oUser->getRaceUserList($params);
					//如果获取到选手名单
					if(count($RaceUserList))
					{
						//循环选手列表
						foreach($RaceUserList as $ApplyId => $ApplyInfo)
						{
							//获取用户信息
							$UserInfo = $oUser->getUserInfo($ApplyInfo["UserId"],'user_id,name');
							//如果获取到用户
							if($UserInfo['user_id'])
							{
								$RaceTeamInfo = $oTeam->getRaceTeamInfo($ApplyInfo['RaceTeamId']);
								if(!isset($RaceTeamInfo['RaceTeamId']))
								{
									$RaceTeamInfo = array('RaceTeamName'=>"个人");
								}
								//存储用户信息
								$TimingPointList['UserInfo'] = array('UserName'=>$UserInfo['name'],'UserId' => $UserInfo['user_id'],'RaceTeamId'=> $ApplyInfo['RaceTeamId'],'RaceTeamName'=>$RaceTeamInfo['RaceTeamName'],'BIB'=>$ApplyInfo['BIB'],'ChipId'=>$ApplyInfo['ChipId'],'ApplyComment'=>json_decode($ApplyInfo['comment'],true));
								//数据解包
								$ApplyInfo['comment'] = json_decode($ApplyInfo['comment'],true);
								//如果有关联的订单数据
								if(isset($ApplyInfo['comment']['order_id']))
								{
									$oOrder = new Xrace_Order();
									//获取订单信息
									$OrderInfo = $oOrder->getOrder($ApplyInfo['comment']['order_id'],'id,order_no,trade_no,amount_total,createDateTime,payDateTime,isCancel,status');
									//如果有获取到订单信息
									if(isset($OrderInfo['order_no']))
									{
										//保存订单信息
										$TimingPointList['OrderInfo'] = $OrderInfo;
									}
								}
								if(isset($ApplyInfo['comment']['BDDeviceId']) && (strlen($ApplyInfo['comment']['BDDeviceId'])>4))
								{
									$TimingPointList['UserInfo']['ApplyComment']['BDLocationUrl'] = "http://182.92.140.26:8000/rest/sdk.location.queryLocation/226/0?deviceId=".$ApplyInfo['comment']['BDDeviceId']."&beginTime=beginTime&endTime=endTime";
								}
								//存储报名信息
								//$TimingPointList['ApplyInfo'] = $ApplyInfo;
								$filePath = __APP_ROOT_DIR__."Timing"."/".$RaceInfo['RaceId']."/"."UserList"."/";
								$fileName = $UserInfo['user_id'].".php";
								//生成配置文件
								Base_Common::rebuildConfig($filePath,$fileName,$TimingPointList,"Timing");
							}
						}
					}
					else
					{
						return false;
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
	//根据分组信息判断用户是否符合组别的报名规则
	public function raceLicenseCheck($RaceLicenseList,$UserId,$RaceStageInfo,$RaceGroupInfo)
	{
		$oUser = new Xrace_User();
		$oMemCache = new Base_Cache_Memcache("xrace");
		{
			//获取缓存
			$m = $oMemCache->get("UserInfo_".$UserId);
			//缓存解开
			$m = json_decode($m,true);
			//如果获取到的用户信息有效
			if(isset($m['user_id']))
			{
				$UserInfo = $m;
			}
			else
			{
				//从数据库获取用户信息你
				$UserInfo = $oUser->getUserInfo($UserId);
				//如果获取到的用户信息有效
				if(isset($UserInfo['user_id']))
				{
					//写入缓存
					$oMemCache -> set("UserInfo_".$UserId,json_encode($UserInfo),86400);
				}
			}
		}
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
					$functionName = $LicenseInfo['LicenseType'] . "ConditionCheck";
					//依次检查用户信息的审核状态
					$RaceLicenseList[$key]['checked'] = $this->$functionName($LicenseInfo, $UserInfo,$RaceStageInfo,$RaceGroupInfo);
				}
				else
				{
					//删除数据
					unset($RaceLicenseList[$key]);
				}
			}
			return $RaceLicenseList;
		}
		else
		{
			return $RaceLicenseList;
		}
	}
	//根据 用户生日判断是否符合执照条件
	public function birthdayConditionCheck($LicenseInfo,$UserInfo)
	{
		//如果获取到的用户信息有效
		if(isset($UserInfo['user_id']) && ($LicenseInfo['LicenseType']=="birthday"))
		{
			//判断结果
			$text = '$return=$UserInfo['."'birth_day'".']'.$LicenseInfo['License']['equal']."'".$LicenseInfo['License']['Date']."'?true:false;";
			eval($text);
			return $return;
		}
		else
		{
			return true;
		}
	}
	//根据 用户性比判断是否符合执照条件
	public function sexConditionCheck($LicenseInfo,$UserInfo)
	{
		//如果获取到的用户信息有效
		if(isset($UserInfo['user_id']) && ($LicenseInfo['LicenseType']=="sex"))
		{
			//判断结果
			$text = '$return=$UserInfo['."'sex'".']=='.$LicenseInfo['License']."?true:false;";
			eval($text);
			return $return;
		}
		else
		{
			return true;
		}
	}
	//根据 用户性比判断是否符合执照条件
	public function managerConditionCheck($LicenseInfo,$UserInfo,$RaceStageInfo,$RaceGroupInfo)
	{
		//如果获取到的用户信息有效
		if(isset($UserInfo['user_id']) && ($LicenseInfo['LicenseType']=="manager"))
		{
			//判断结果
			$oUser = new Xrace_User();
			//检查指定时间段，指定分组范围内有没有执照
			$params = array('UserId'=>$UserInfo['user_id'],'RaceGroupId'=>$RaceGroupInfo['RaceGroupId'],'DuringDate'=>array('StartDate'=>$RaceStageInfo['StageStartDate'],'EndDate'=>$RaceStageInfo['StageEndDate']));
			//获取用户的有效执照的数量
			$UserLicenseCount = $oUser->getUserLicenseCount($params);
			return $UserLicenseCount>0?true:false;
		}
		else
		{
			return true;
		}
	}
	//根据用户ID和比赛ID获取用户该场比赛的详情
	public function getUserRaceInfo($RaceId,$UserId)
	{
		$filePath = __APP_ROOT_DIR__."Timing"."/".$RaceId."/"."UserList"."/";
		$fileName = $UserId.".php";
		//载入预生成的配置文件
		return Base_Common::loadConfig($filePath,$fileName);
	}
	//根据用户ID和比赛ID获取用户该场比赛的详情
	public function getUserRaceInfoList($RaceId)
	{
		$filePath = __APP_ROOT_DIR__."Timing"."/".$RaceId."/";
		$fileName = "Total".".php";
		//载入预生成的配置文件
		return Base_Common::loadConfig($filePath,$fileName);
	}

	//根据用户ID和比赛ID获取用户该场比赛的详情
	public function GetUserRaceTimingInfo($RaceId)
	{
		$filePath = __APP_ROOT_DIR__."Timing"."/".$RaceId."/";
		$fileName = "Total.php";
		//载入预生成的配置文件
		return Base_Common::loadConfig($filePath,$fileName);
	}

	public function getUserRaceStatus($UserRaceInfo)
	{
		$CurrentTime = time();
		//比赛尚未开始
		if($CurrentTime<strtotime($UserRaceInfo['RaceInfo']['StartTime']))
		{
			$RaceStatus = array("RaceStatus"=>1,"RaceStatusName"=>"即将参赛");
		}
		//比赛进行中
		elseif(($CurrentTime>=strtotime($UserRaceInfo['RaceInfo']['StartTime']))&& $CurrentTime<=strtotime($UserRaceInfo['RaceInfo']['EndTime']))
		{
			$RaceStatus = array("RaceStatus"=>2,"RaceStatusName"=>"比赛进行中");
		}
		else
		{
			//计算进过的计时点
			$passedPoint = 0;
			foreach($UserRaceInfo['Point'] as $p => $pInfo)
			{
				//如果有计时点过线信息，进过的计时点数量累加
				if($pInfo['inTime']>0)
				{
					$passedPoint++;
				}
			}
			//如果没通过任何一个计时点
			if($passedPoint==0)
			{
				$RaceStatus = array("RaceStatus"=>3,"RaceStatusName"=>"DNS");
			}
			else
			{
				//如果通过了终点
				if($UserRaceInfo['Point'][count($UserRaceInfo['Point'])]['inTime']>0)
				{
					$RaceStatus = array("RaceStatus"=>4,"RaceStatusName"=>"完赛");
				}
				else
				{
					$RaceStatus = array("RaceStatus"=>5,"RaceStatusName"=>"DNF");
				}
			}
		}
		return $RaceStatus;
	}
	//获取某场比赛的成绩列表
	public function getRaceResult($RaceId,$RaceGroupId = 0)
	{
		if($RaceGroupId>0)
        {
            $url = $this->config->apiUrl.Base_Common::getUrl('','xrace.config','get.race.user.list.by.bib',array('RaceId'=>$RaceId,'RaceGroupId'=>$RaceGroupId));
        }
        else
        {
            $url = $this->config->apiUrl.Base_Common::getUrl('','xrace.config','get.race.user.list.by.bib',array('RaceId'=>$RaceId));
        }
        $return = Base_Common::do_post($url);
		return json_decode($return,true);
	}
	//添加单个套餐
	public function insertRaceCombination(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table_combination);
		return $this->db->insert($table_to_process, $bind);
	}
	//获取单个套餐信息
	public function getRaceCombination($RaceCombinationId, $fields = '*')
	{
		$RaceCombinationId = intval($RaceCombinationId);
		$table_to_process = Base_Widget::getDbTable($this->table_combination);
		return $this->db->selectRow($table_to_process, $fields, '`RaceCombinationId` = ?', $RaceCombinationId);
	}
	//更新单个赛事分站
	public function updateRaceCombination($RaceCombinationId, array $bind)
	{
		$RaceCombinationId = intval($RaceCombinationId);
		$table_to_process = Base_Widget::getDbTable($this->table_combination);
		return $this->db->update($table_to_process, $bind, '`RaceCombinationId` = ?', $RaceCombinationId);
	}
	//获取所有套餐列表
	public function getRaceCombinationList($params,$fields = "*")
	{
		//初始化查询条件
		$whereCatalog = (isset($params['RaceCatalogId']) && ($params['RaceCatalogId'] >0))?(" RaceCatalogId = ".$params['RaceCatalogId']):"";
		$whereStage = (isset($params['RaceStageId']) && ($params['RaceStageId'] >0))?(" RaceStageId = ".$params['RaceStageId']):"";
		$whereCondition = array($whereCatalog,$whereStage);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table_combination);
		$sql = "SELECT $fields FROM " . $table_to_process . "  where 1 ".$where." ORDER BY RaceCatalogId,RaceStageId desc,RaceCombinationId asc";
		$return = $this->db->getAll($sql);
		$RaceCombinationList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$RaceCombinationList[$value['RaceCombinationId']] = $value;
			}
		}
		return $RaceCombinationList;
	}
	public function CheckIn($RaceStageId,$CheckInCode)
    {
        //分解签到码
        $t = explode("|",$CheckInCode);
        //用户ID
        $U = hexdec($t[1]);
        //分站ID
        $S = hexdec($t[0]);
        $oUser = new Xrace_User();
        //获取签到信息
        $UserCheckInInfo = $oUser->getUserCheckInInfo($U,$S);
        //检查签到码
        if(trim($CheckInCode) == trim($UserCheckInInfo['CheckinCode']))
        {
            //如果已经签到，直接返回成功
            if($UserCheckInInfo['CheckinStatus'] == 1)
            {
                return $U;
            }
            else
            {
                //更新
                $oUser->updateUserCheckInInfo($U,$S,array('CheckinStatus'=>1,'CheckInTime'=>date("Y-m-d H:i:s",time())));
                //复查数据
                $UserCheckInInfo = $oUser->getUserCheckInInfo($U,$S);
                if($UserCheckInInfo['CheckinStatus'] == 1)
                {
                    return $U;
                }
                else
                {
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
