<?php
/**
 * 积分相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Credit extends Base_Widget
{
	//声明所用到的表
	protected $table = 'config_credit';
    protected $table_credit_user = 'credit_user';

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
    public function Credit($CreditInfo,$UserId)
    {
        $suffix = substr(md5($UserId),0,1);
        $this->db->createTable($this->table_credit_user,$suffix);
    }
}
