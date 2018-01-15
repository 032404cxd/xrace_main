<?php
/**
 * 微信打卡计时相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_WechatTiming extends Base_Widget
{
	//声明所用到的表
	protected $table = 'wechat_times';
    //新增单个计时记录
    public function insertTiming(array $bind)
    {
        $table_to_process = Base_Widget::getDbTable($this->table);
        echo $table_to_process;
        return $this->db->insert($table_to_process, $bind);
    }
    //
    public function insertTimingLog(array $Timing)
    {
        $oRace = new Xrace_Race();
        //获取比赛信息
        $RaceInfo = $oRace->getRace($Timing['RaceId']);
        //如果获取到比赛信息
        if(isset($RaceInfo['RaceId']))
        {
            $oUser = new Xrace_UserInfo();
            //获取用户信息
            $UserInfo = $oUser->getUserByColumn("WechatId",$Timing['OpenId']);
            //如果获取到用户信息
            if(isset($UserInfo['UserId']))
            {
                //如果有关联的比赛用户信息
                if($UserInfo['RaceUserId']>0)
                {

                }
                else
                {

                    //根据用户创建比赛用户
                    $RaceUserId = $oUser->createRaceUserByUserInfo($UserInfo['UserId']);
                    //如果创建成功
                    if($RaceUserId)
                    {

                    }
                    else
                    {
                        //返回错误
                        return false;
                    }
                }
                $TimingLog = array("time"=>$Timing["Time"],"Location"=>$Timing["Location"],"RaceUserId"=>$UserInfo['RaceUserId']?$UserInfo['RaceUserId']:$RaceUserId,"comment"=>json_encode(array("Position"=>array("X"=>$Timing["TencentX"],"Y"=>$Timing["TencentY"]))));
                print_r($TimingLog);
                $Id = $this->insertTiming($TimingLog);
                echo "Id:".$Id;
            }
            else
            {
                //返回错误
                return false;
            }

        }
        else
        {
            //返回错误
            return false;
        }
    }
}
