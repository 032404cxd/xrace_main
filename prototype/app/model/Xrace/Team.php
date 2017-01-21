<?php
/**
 * 队伍相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Team extends Base_Widget
{
	//声明所用到的表
	protected $table = 'race_team';
    protected $table_user = 'user_team';
	/**
	 * 获取单个队伍记录
	 * @param char $TeamId 队伍ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getTeam($TeamId, $fields = '*')
	{
		$TeamId = intval($TeamId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`TeamId` = ?', $TeamId);
	}
    //更新单个队伍
    public function updateTeam($TeamId,array $bind)
    {
        $TeamId = intval($TeamId);
        $bind['LastUpdateTime'] = date("Y-m-d H:i:s",time());
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->update($table_to_process, $bind, '`TeamId` = ?',array($TeamId));
    }
    //添加单个队伍
    public function insertTeam(array $bind)
    {
        $Time = time();
        $bind['CreateTime'] = date("Y-m-d H:i:s",$Time);
        $bind['LastUpdateTime'] = date("Y-m-d H:i:s",$Time);
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->insert($table_to_process, $bind);
    }
    //添加单个用户队伍
    public function insertTeamUser(array $bind)
    {
        $Time = time();
        $bind['InTime'] = date("Y-m-d H:i:s",$Time);
        $bind['LastUpdateTime'] = date("Y-m-d H:i:s",$Time);
        $table_to_process = Base_Widget::getDbTable($this->table_user);
        return $this->db->insert($table_to_process, $bind);
    }
    /**
     * 获取单个用户加入队伍的信息
     * @param char $TeamId 队伍ID
     * @param char $UserId 用户ID
     * @param string $fields 所要获取的数据列
     * @return array
     */
    public function getTeamUser($UserId,$TeamId, $fields = '*')
    {
        $TeamId = intval($TeamId);
        $UserId = intval($UserId);
        $table_to_process = Base_Widget::getDbTable($this->table_user);
        return $this->db->selectRow($table_to_process, $fields, '`TeamId` = ? and `UserId` = ?', array($TeamId,$UserId));
    }
    /**
     * 删除单个用户加入队伍的信息
     * @param char $TeamId 队伍ID
     * @param char $UserId 用户ID
     * @return array
     */
    public function deleteTeamUser($UserId,$TeamId)
    {
        $TeamId = intval($TeamId);
        $UserId = intval($UserId);
        $table_to_process = Base_Widget::getDbTable($this->table_user);
        return $this->db->delete($table_to_process,'`TeamId` = ? and `UserId` = ?', array($TeamId,$UserId));
    }
	/**
	 * 获取单个队伍记录
	 * @param char $TeamId 队伍ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getTeamInfoByName($TeamName,$fields = '*')
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`TeamName` = ?',array($TeamName));
	}
	/**
	 * 获取队伍列表
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return array
	 */
	public function getTeamList($params,$fields = array("*"))
	{
	    //生成查询列
		$fields = Base_common::getSqlFields($fields);
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//队伍名称
		$whereTeamName = (isset($params['TeamName']) && trim($params['TeamName']))?" TeamName like '%".$params['TeamName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereTeamName);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//获取用户数量
		if(isset($params['getCount'])&&$params['getCount']==1)
		{
			$TeamCount = $this->getTeamCount($params);
		}
		else
		{
			$TeamCount = 0;
		}
		$limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
		$order = " ORDER BY LastUpdateTime desc";
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
		$return = $this->db->getAll($sql);
		$TeamList = array('TeamList'=>array(),'TeamCount'=>$TeamCount);
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$TeamList['TeamList'][$value['TeamId']] = $value;
			}
		}
		else
		{
			return $TeamList;
		}
		return $TeamList;
	}
	/**
	 * 获取队伍数量
	 * @param $fields  所要获取的数据列
	 * @param $params 传入的条件列表
	 * @return integer
	 */
	public function getTeamCount($params)
	{
		//生成查询列
		$fields = Base_common::getSqlFields(array("TeamCount"=>"count(TeamId)"));
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		//队伍名称
		$whereTeamName = (isset($params['TeamName']) && trim($params['TeamName']))?" TeamName like '%".$params['TeamName']."%' ":"";
		//所有查询条件置入数组
		$whereCondition = array($whereTeamName);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		//生成条件列
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
		return $this->db->getOne($sql);
	}
    /**
    /**
     * 获取单个用户记录
     * @param char $TeamId 用户ID
     * @param string $fields 所要获取的数据列
     * @return array
     */
    public function getTeamInfo($TeamId, $fields = '*',$Cache=1)
    {
        $oMemCache = new Base_Cache_Memcache("xrace");
        //获取缓存
        if($Cache == 1)
        {
            //获取缓存
            $m = $oMemCache->get("TeamInfo_".$TeamId);
            //缓存解开
            $TeamInfo = json_decode($m,true);
            //如果结果集不有效
            if(!isset($TeamInfo['TeamId']))
            {
                //缓存置为0
                $Cache = 0;
            }
            else
            {
                //echo "TeamInfo cahced";
            }
        }
        if($Cache == 0)
        {
            //从数据库中获取
            $TeamInfo = $this->getTeam($TeamId, "*");
            //如果结果集有效
            if(isset($TeamInfo['TeamId']))
            {
                //写入缓存
                $oMemCache -> set("TeamInfo_".$TeamId,json_encode($TeamInfo),86400);
            }
        }
        //如果结果集有效，并且获取的字段列表不是全部
        if(isset($TeamInfo['TeamId']) && $fields != "*")
        {
            //分解字段列表
            $fieldsList = explode(",",$fields);
            //循环结果集
            foreach($TeamInfo as $key => $value)
            {
                //如果不在字段列表中且不是主键
                if(!in_array($key,$fieldsList) && $key != "TeamId")
                {
                    //删除
                    unset($TeamInfo[$key]);
                }
            }
        }
        //返回结果
        return $TeamInfo;
    }
	/**
	 * 获取队伍数量
	 * @param $RaceGroupInfo  分组信息
	 * @param $Cache 是否强制更新缓存
	 * @return array
	 */
	public function getTeamListByGroup($RaceGroupInfo,$Cache = 1)
	{
		$oMemCache = new Base_Cache_Memcache("xrace");
		$CacheKey = "TeamList_".$RaceGroupInfo['RaceGroupId'];
		//如果需要获取缓存
		if($Cache == 1)
		{
			//获取缓存
			$m = $oMemCache->get($CacheKey);
			//缓存解开
			$TeamList = json_decode($m,true);
			//如果数据为空
			if(count($TeamList['TeamList'])==0)
			{
				//需要从数据库获取
				$NeedDB = 1;
			}
			else
			{
				//echo "cached";
				return $TeamList;
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
			$TeamList = $this->getTeamList($RaceGroupInfo);
			//如果有获取到队伍列表
			if(count($TeamList['TeamList']))
			{
				//循环队伍列表
				foreach($TeamList['TeamList'] as $TeamId => $TeamInfo)
				{
					//数据解包
					$TeamInfo['comment'] = json_decode($TeamInfo['comment'],true);
					//如果并未选择分组 或者 当前组别不在已经选择的分组当中
					if(!isset($TeamInfo['comment']['SelectedRaceGroup']) || !in_array($RaceGroupInfo['RaceGroupId'],$TeamInfo['comment']['SelectedRaceGroup']))
					{
						//删除当前分组
						unset($TeamList['TeamList'][$TeamId]);
					}
					else
					{
						//保留数据
						$TeamList['TeamList'][$TeamId] = $TeamInfo;
					}
				}
				//如果有获取到队伍列表
				if(count($TeamList['TeamList']))
				{
					//写入缓存
					$oMemCache -> set($CacheKey,json_encode($TeamList),86400);
					return $TeamList;
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
	//获取用户参与的队伍列表
	public function getTeamUserList($params)
    {
        //生成查询列
        $fields = Base_common::getSqlFields(array("*"));
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table_user);
        //队伍
        $whereTeam = (isset($params['TeamId']) && intval($params['TeamId']))?" TeamId = ".$params['TeamId']." ":"";
        //用户
        $whereUser = (isset($params['UserId']) && intval($params['UserId']))?" UserId = ".$params['UserId']." ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereTeam,$whereUser);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        //生成条件列
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
        return $this->db->getAll($sql);
    }
}
