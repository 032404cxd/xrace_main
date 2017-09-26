<?php
/**
 * 赛事配置相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_UserRace extends Base_Widget
{
	//声明所用到的表
	protected $table = 'RaceApplyQueue';
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
        echo $table_to_process;
        return $this->db->insert($table_to_process, $bind);
	}
    //获取报名各状态的名单
    public function getUserRaceApplyList($params,$fields = array('*'))
    {
        print_R($params);
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
        //获取用户数量
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
    //获取报名各状态的名单
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
}
