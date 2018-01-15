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
    public function insertTiming($table,array $bind)
    {
        return $this->db->insert($table, $bind);
    }
    //新增一条计时记录
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
                //数据解包
                $RaceInfo["RouteInfo"] = json_decode($RaceInfo["RouteInfo"],true);
                //检测计时记录表是否存在
                $table_timing = $this->db->createTable($this->table,$RaceInfo["RouteInfo"]["TimePrefix"]);
                //初始化计时记录
                $TimingLog = array("time"=>$Timing["Time"],"Location"=>$Timing["Location"],"RaceUserId"=>$UserInfo['RaceUserId']?$UserInfo['RaceUserId']:$RaceUserId,"comment"=>json_encode(array("Position"=>array("X"=>$Timing["TencentX"],"Y"=>$Timing["TencentY"]))));
                //加入计时记录
                $Id = $this->insertTiming($table_timing,$TimingLog);
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
