<?php
/**
 * 积分相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Credit extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_credit';
    protected $table_credit_update_log = 'user_credit_log';
    protected $table_credit_update_log_total = 'user_credit_log_total';
    protected $table_credit_user = 'user_credit_sum';

    protected $creditFrequenceList = array("day"=>array("Name"=>"每日","ParamList"=>array()),
        "week"=>array("Name"=>"每周","ParamList"=>array()),
        "month"=>array("Name"=>"每月","ParamList"=>array()),
        "year"=>array("Name"=>"每年","ParamList"=>array()),
        "total"=>array("Name"=>"总计","ParamList"=>array()));
        //"dateRange"=>array("Name"=>"日期","ParamList"=>array("StartDate"=>array("Name"=>"开始日期","Type"=>"Date"),"EndDate"=>array("Name"=>"结束日期","Type"=>"Date"))));
    public function getCreditFrequenceList()
    {
        return $this->creditFrequenceList;
    }
	/**
	 * 查询全部
	 * @param $fields
	 * @return array
	 */
	public function getCreditList($RaceCatalogId = 0,$fields = "*")
	{
		$RaceCatalogId = intval($RaceCatalogId);
		//初始化查询条件
		$whereCatalog = ($RaceCatalogId != 0)?" RaceCatalogId = $RaceCatalogId":"";
		$whereCondition = array($whereCatalog);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$table_to_process = Base_Widget::getDbTable($this->table);
		$sql = "SELECT $fields FROM " . $table_to_process . " where 1 ".$where." ORDER BY CreditId ASC";
		$return = $this->db->getAll($sql);
		$CreditList = array();
		if(count($return))
		{
			foreach($return as $key => $value)
			{
				$CreditList[$value['CreditId']] = $value;
			}
		}
		return $CreditList;
	}
	/**
	 * 获取单条记录
	 * @param integer $AppId
	 * @param string $fields
	 * @return array
	 */
	public function getCredit($CreditId, $fields = '*')
	{
		$CreditId = intval($CreditId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`CreditId` = ?', $CreditId);
	}
	/**
	 * 更新
	 * @param integer $AppId
	 * @param array $bind
	 * @return boolean
	 */
	public function updateCredit($CreditId, array $bind)
	{
		$CreditId = intval($CreditId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->update($table_to_process, $bind, '`CreditId` = ?', $CreditId);
	}
	/**
	 * 插入
	 * @param array $bind
	 * @return boolean
	 */
	public function insertCredit(array $bind)
	{
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->insert($table_to_process, $bind);
	}

	/**
	 * 删除
	 * @param integer $AppId
	 * @return boolean
	 */
	public function deleteCredit($CreditId)
	{
		$CreditId = intval($CreditId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->delete($table_to_process, '`CreditId` = ?', $CreditId);
	}
    //把执照获得条件转化成HTML
    public function ParthCreditListToHtml($CreditList,$ActionId,$modify=0,$delete=0)
    {
        //如果已配置执照条件列表
        if (count($CreditList)) {
            //获得积分频率列表
            $CreditFrequenceList = $this->getCreditFrequenceList();
            //循环积分列表
            foreach ($CreditList as $key => $Credit)
            {
                //获取积分信息
                $CreditInfo = $this->getCredit($Credit['CreditId'], "CreditId,CreditName");
                if (!count($Credit['ParamList']))
                {
                    $t = $Credit['StartTime']."~".$Credit['EndTime']." ".$CreditFrequenceList[$Credit['Frequency']]['Name'];
                }
                else
                {
                    $t2 = array();
                    foreach ($Credit['ParamList'] as $k => $Param)
                    {
                        $t2[$k] = $CreditFrequenceList[$Credit['Frequency']]["ParamList"][$k]['Name'] . ":" . $Param;
                    }
                    $t = $Credit['StartTime']."~".$Credit['EndTime']." ".("" . implode(",", $t2) . "");
                }
                $t .= "<br>" . "每次获得：" . $CreditInfo['CreditName'] . " " . $Credit['Credit'] . "/次数：" . ($Credit['CreditCount'] > 0 ? ($Credit['CreditCount'] . "次") : "不限");
                $text[$key] = $t;
                if ($modify) {
                    $text[$key] .= '<a href="javascript:void(0);" onclick="CreditModify(' . "'" . $ActionId . "'" . ',' . "'" . $key . "'" . ')"> 修改 </a>';
                }
                if ($delete) {
                    $text[$key] .= '<a href="javascript:void(0);" onclick="CreditDelete(' . "'" . $ActionId . "'" . ',' . "'" . $key . "'" . ',' . "'" . $CreditInfo['CreditName'] . "'" . ')"> 删除 </a>';
                }
            }
            return ("" . implode("<br>", $text) . "");
        }
    }
    public function parthFrequenceConditioToHtml($CreditFrequence,$params)
    {
        $t = array();
        foreach($CreditFrequence['ParamList'] as $k => $ParamInfo)
        {
            switch ($ParamInfo['Type'])
            {
                case "Date":
                    $t[$k] = $ParamInfo['Name']." ".'<input type="text" class="span2" name="ParamList['.$k.']" value="' . $params[$k] . '" class="input-medium" onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:' . "'yyyy-MM-dd'" . '})">';
            }
        }
        return implode(" ",$t);
    }
    public function Credit($CreditInfo,$params,$UserId)
    {
        //获取当前时间
        $Time = date("Y-m-d H:i:s",time());
        //事务开始
        $this->db->begin();
        //计算所在用户分表的后缀
        $suffix = substr(md5($UserId),0,1);
        //检测用户积分变更表是否存在
        $table_user_log = $this->db->createTable($this->table_credit_update_log,$suffix);
        //检测用户积分汇总表是否存在
        $table_user = $this->db->createTable($this->table_credit_user,$suffix);
        //检测总表
        $table_total = Base_Widget::getDbTable($this->table_credit_update_log_total);
        //积分变更表的数据
        $bindLog = array("UserId"=>$UserId,"CreditId"=>$CreditInfo['CreditId'],"Time"=>$Time,"Credit"=>$CreditInfo['Credit']);
        //循环传入的参数列表
        foreach($params as $key => $value)
        {
            //依次赋值
            $bindLog[$key] = $value;
        }
        //积分表的变更数据
        $bindUpdate = array("LastUpdateTime"=>$Time,"Credit"=>"_Credit".($CreditInfo['Credit']>0?("+".$CreditInfo['Credit']):$CreditInfo['Credit']));
        //积分表的新增数据
        $bind = array("LastUpdateTime"=>$Time,"UserId"=>$UserId,"CreditId"=>$CreditInfo['CreditId'],"Credit"=>$CreditInfo['Credit']);
        //更新汇总表
        $CreditSum = $this->db->insert_update($table_user,$bind,$bindUpdate);
        //新增记录表
        $CreditLog = $this->db->insert($table_user_log, $bindLog);
        //组合新的ID
        $bindLog['Id'] = $suffix."_".$CreditLog;
        echo $bindLog['Id'] ."<br>";
        //新增记录表
        $CreditLogTotal = $this->db->insert($table_total, $bindLog);
        //如果同时成功
        if($CreditSum && $CreditLog && $CreditLogTotal)
        {
            //提交
            $this->db->commit();
            return true;
        }
        else
        {
            //回滚
            $this->db->rollBack();
            return false;
        }
    }
    /**
     * 获取用户列表
     * @param $fields  所要获取的数据列
     * @param $params 传入的条件列表
     * @return array
     */
    public function getCreditLog($params,$fields = array("*"))
    {
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table_credit_update_log_total);
        //动作
        $whereAction = (isset($params['ActionId']) && $params['ActionId']>0)?" ActionId = '".$params['ActionId']."' ":"";
        //积分
        $whereCredit = (isset($params['CreditId']) && $params['CreditId']>0)?" CreditId = '".$params['CreditId']."' ":"";
        //比赛
        $whereRace = (isset($params['RaceId']) && $params['RaceId']>0)?" RaceId = '".$params['RaceId']."' ":"";
        //昵称
        //$whereNickName = (isset($params['NickName']) && trim($params['NickName']))?" NickName like '%".$params['NickName']."%' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereAction,$whereCredit,$whereRace);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        //获取用户数量
        if(isset($params['getCount'])&&$params['getCount']==1)
        {
            $CreditLogCount = $this->getCreditLogCount($params);
        }
        else
        {
            $CreditLogCount = 0;
        }
        $limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
        $order = " ORDER BY Time desc";
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
        $return = $this->db->getAll($sql);
        $CreditLog = array('CreditLog'=>array(),'CreditLogCount'=>$CreditLogCount);
        if(count($return))
        {
            foreach($return as $key => $value)
            {
                $CreditLog['CreditLog'][$value['Id']] = $value;
            }
        }
        else
        {
            return $CreditLog;
        }
        return $CreditLog;
    }
    /**
     * 获取用户数量
     * @param $fields  所要获取的数据列
     * @param $params 传入的条件列表
     * @return integer
     */
    public function getCreditLogCount($params)
    {
        //生成查询列
        $fields = Base_common::getSqlFields(array("CreditLogCount"=>"count(Id)"));

        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table_credit_update_log_total);
        //动作
        $whereAction = (isset($params['ActionId']) && $params['ActionId']>0)?" ActionId = '".$params['ActionId']."' ":"";
        //积分
        $whereCredit = (isset($params['CreditId']) && $params['CreditId']>0)?" CreditId = '".$params['CreditId']."' ":"";
        //比赛
        $whereRace = (isset($params['RaceId']) && $params['RaceId']>0)?" RaceId = '".$params['RaceId']."' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereAction,$whereCredit,$whereRace);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
        echo $sql."<br>";
        return $this->db->getOne($sql);
    }
}
