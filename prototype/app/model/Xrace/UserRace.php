<?php
/**
 * 赛事配置相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_UserRace extends Base_Widget
{
	//声明所用到的表
	protected $table = 'RaceApplyQueue';
    protected $table_race = 'AppliedRaceList';
    protected $table_user_race = 'UserRaceList';
	protected $maxRaceAppplyCount = 5;

    public function getMaxRaceAppplyCount()
    {
        return $this->maxRaceAppplyCount;
    }
    /**
     * 新增约战
     * @param array $bind
     * @return boolean
     */
	public function insertRaceApply($bind)
	{
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->insert($table_to_process, $bind);
	}
    /**
     * 新增约战
     * @param array $bind
     * @return boolean
     */
    public function deleteRaceApply($ApplyId)
    {
        $ApplyId = intval($ApplyId);
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->delete($table_to_process, '`ApplyId` = ?', $ApplyId);
    }
    /**
     * 新增约战比赛
     * @param array $bind
     * @return boolean
     */
    public function insertAppliedRace($bind)
    {
        $table_to_process = Base_Widget::getDbTable($this->table_race);
        return $this->db->insert($table_to_process, $bind);
    }
    /**
     * 新增用户比赛记录
     * @param array $bind
     * @return boolean
     */
    public function insertUserRace($bind)
    {
        $table_to_process = Base_Widget::getDbTable($this->table_user_race);
        return $this->db->insert($table_to_process, $bind);
    }
    /**
     * 获取用户比赛记录
     * @param int $RaceId 比赛ID
     * @return boolean
     */
    public function getUserRace($RaceId, $fields = '*')
    {
        $RaceId = intval($RaceId);
        $table_to_process = Base_Widget::getDbTable($this->table_race);
        return $this->db->selectRow($table_to_process, $fields, '`RaceId` = ?', $RaceId);
    }
    /**
     * 获得约战信息
     * @param array $bind
     * @param char $ApplyId 约战记录ID
     * @return boolean
     */
    public function getUserRaceApply($ApplyId, $fields = '*')
    {
        $ApplyId = intval($ApplyId);
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->selectRow($table_to_process, $fields, '`ApplyId` = ?', $ApplyId);
    }

    //获取报名各状态约战记录的列表
    public function getUserRaceApplyList($params,$fields = array('*'))
    {
        $ReturnArr = array();
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        //获得用户ID
        $whereUser = (isset($params['UserId']) && $params['UserId']!="0")?" UserId = '".$params['UserId']."' ":"";
        //获得要忽略的用户ID
        $whereUserIgnore = (isset($params['UserIgnore']) && $params['UserIgnore']!="0")?" UserId != '".$params['UserIgnore']."' ":"";
        //获得场地ID
        $whereArena = (isset($params['ArenaId'])  && $params['ArenaId']!=0)?" ArenaId = '".$params['ArenaId']."' ":"";
        //获得芯片ID
        $whereChip = (isset($params['ChipId']) && strlen($params['ChipId'])>4)?" ChipId = '".$params['ChipId']."' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereUser,$whereArena,$whereChip,$whereUserIgnore);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        //分页参数
        $limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by ApplyId desc".$limit;
        $return = $this->db->getAll($sql);
        foreach($return as $key => $ApplyInfo)
        {
            $ReturnArr["UserRaceApplyList"][$ApplyInfo['ApplyId']] = $ApplyInfo;
        }
        //获取记录数量
        if(isset($params['getCount'])&&$params['getCount']==1)
        {
            $ReturnArr['UserRaceApplyCount'] = $this->getUserRaceApplyCount($params);
        }
        else
        {
            $ReturnArr['UserRaceApplyCount'] = 0;
        }
        return $ReturnArr;
    }
    //获取报名各状态约战记录的数量
    public function getUserRaceApplyCount($params)
    {
        $ReturnArr = array();
        //生成查询列
        $fields = Base_common::getSqlFields(array("ApplyCount"=>"count(1)"));
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        //获得用户ID
        $whereUser = (isset($params['UserId']) && $params['UserId']!="0")?" UserId = '".$params['UserId']."' ":"";
        //获得要忽略的用户ID
        $whereUserIgnore = (isset($params['UserIgnore']) && $params['UserIgnore']!="0")?" UserId != '".$params['UserIgnore']."' ":"";
        //获得场地ID
        $whereArena = (isset($params['ArenaId'])  && $params['ArenaId']!=0)?" ArenaId = '".$params['ArenaId']."' ":"";
        //获得芯片ID
        $whereChip = (isset($params['ChipId']) && strlen($params['ChipId'])>4)?" ChipId = '".$params['ChipId']."' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereUser,$whereArena,$whereChip,$whereUserIgnore);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
        return $this->db->getOne($sql);
    }
    /**
     * 用户直接加入某个约战
     * @param char $ApplyId 约战记录ID
     * @param int $Individual 是否单人对战
     * @param array $UserList 双方用户列表
     * @return boolean
     */
    public function insertUserAppliedRace($ApplyId,$Individual = 1,$UserList)
    {
        //获取约战信息
        $ApplyInfo = $this->getUserRaceApply($ApplyId);
        {
            //初始化比赛信息
            $RaceInfo = array("ApplyUserId"=>$ApplyInfo['UserId'],"RaceCreateTime"=>time(),"RaceStartTime"=>$ApplyInfo["ApplyStartTime"],"RaceEndTime"=>$ApplyInfo['ApplyEndTime'],"ArenaId"=>$ApplyInfo['ArenaId'],"Individual"=>$Individual);
            $this->db->begin();
            //新建比赛
            $RaceId = $this->insertAppliedRace($RaceInfo);
            //如果是单人对战
            if($Individual == 1)
            {
                //如果比赛创建成功
                if($RaceId)
                {
                    foreach($UserList as $key => $UserInfo)
                    {
                        //初始化用户参赛信息
                        $UserRaceInfo = array("RaceId"=>$RaceId,"UserId"=>$UserInfo['UserId'],"TeamId"=>0,"Result"=>0,"ApplyId"=>($ApplyInfo['UserId']==$UserInfo['UserId']?$ApplyId:0),"ChipId"=>$UserInfo['ChipId']);
                        //新建用户参赛信息
                        $UserRace = $this->insertUserRace($UserRaceInfo);
                        if(!$UserRace)
                        {
                            //事务回滚
                            $this->db->rollBack();
                            return false;
                        }
                    }
                    //删除原有的约战记录
                    $deleteApp = $this->deleteRaceApply($ApplyId);
                    if($deleteApp)
                    {
                        //事务提交
                        $this->db->commit();
                        return $RaceId;
                    }
                    else
                    {
                        //事务回滚
                        $this->db->rollBack();
                        return false;
                    }
                }
            }
            else
            {

            }
        }
    }
    //获取报名各状态约战记录的列表
    public function getUserRaceList($params,$fields = array('*'))
    {
        $ReturnArr = array();
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table_user_race);
        //获得用户ID
        $whereUser = (isset($params['UserId']) && $params['UserId']!="0")?" UserId = '".$params['UserId']."' ":"";
        //获得比赛ID
        $whereRace = (isset($params['RaceId']) && $params['RaceId']!="0")?" RaceId = '".$params['RaceId']."' ":"";
        //获得要忽略的用户ID
        $whereUserIgnore = (isset($params['UserIgnore']) && $params['UserIgnore']!="0")?" UserId != '".$params['UserIgnore']."' ":"";
        //获得场地ID
        $whereArena = (isset($params['ArenaId'])  && $params['ArenaId']!=0)?" ArenaId = '".$params['ArenaId']."' ":"";
        //获得芯片ID
        $whereChip = (isset($params['ChipId']) && strlen($params['ChipId'])>4)?" ChipId = '".$params['ChipId']."' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereUser,$whereRace,$whereUserIgnore,$whereArena,$whereChip);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        //分页参数
        $limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by UserRaceId desc".$limit;
        $return = $this->db->getAll($sql);
        foreach($return as $key => $ApplyInfo)
        {
            $ReturnArr["UserRaceList"][$ApplyInfo['UserRaceId']] = $ApplyInfo;
        }
        return $ReturnArr;
    }
}
