<?php
/**
 * 地平线对接相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Horizon extends Base_Widget
{
	protected $ApiUrl = 'api.dpxian.cc';
    //声明所用到的表
	protected $table_race = 'config_race';

    //获取单个分站的比赛配置信息
    public function getRaceStageInfo($RaceStageId)
    {
        $oRace = new Xrace_Race();
        //获取分站信息
        $RaceStageInfo = $oRace->getRaceStage($RaceStageId);
        //如果找到
        if(isset($RaceStageInfo["RaceStageId"]))
        {
            $ReturnArr = array();
            $ReturnArr["startDate"] = $RaceStageInfo["StageStartDate"];
            $ReturnArr["endDate"] = $RaceStageInfo["StageEndDate"];
            $ReturnArr["name"] = $RaceStageInfo["RaceStageName"];
            //数组解包
            $RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
            //比赛分组模式
            if($RaceStageInfo['comment']['RaceStructure']=="race")
            {
                //初始化空的比赛列表
                $RaceArr = array();
                //初始化空的分组列表
                $RaceGroupList = array();
                //获取比赛列表
                $RaceList = $oRace->getRaceList(array("RaceStageId"=>$RaceStageId));
                //循环比赛列表
                foreach($RaceList as $RaceId => $RaceInfo)
                {
                    //数据解包
                    $RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo']);
                    foreach($RaceInfo['comment']["SelectedRaceGroup"] as $RaceGroupId => $GroupInfo)
                    {
                        if (!isset($RaceGroupList[$RaceGroupId]))
                        {
                            $RaceGroupList[$RaceGroupId] = $oRace->getRaceGroup($RaceGroupId, "RaceGroupId,RaceGroupName");
                        }
                        $RaceArr[] = array("hasDepartureTP" => $RaceInfo['comment']["NoStart"] == 0 ? "T" : "F",
                            "id" => $RaceId."_".$RaceGroupId,
                            "name" => $RaceGroupList[$RaceGroupId]["RaceGroupName"],
                            "raceCategoryName" => $RaceInfo["RaceName"],
                            "raceCategoryUnitType" => $RaceInfo['comment']['RaceResult'] == "Individual" ? "INDIVIDUAL" : "TEAM",
                            "startDate"=>date("Y-m-d", strtotime($GroupInfo["StartTime"])),
                            "startTime"=>date("H:i:s", strtotime($GroupInfo["StartTime"])),
                        );
                    }
                }
            }
            $ReturnArr["matches"] = $RaceArr;
            return $ReturnArr;
        }
        else
        {
            return false;
        }
    }
    //获取单个分站的比赛配置信息
    public function getAthleteList($RaceStageId)
    {
        $oRace = new Xrace_Race();
        $oUser = new Xrace_UserInfo();
        $oTeam = new Xrace_Team();

        //获取分站信息
        $RaceStageInfo = $oRace->getRaceStage($RaceStageId);
        //如果找到
        if(isset($RaceStageInfo["RaceStageId"]))
        {
            $ReturnArr = array();
            //数组解包
            $RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
            //比赛分组模式
            if($RaceStageInfo['comment']['RaceStructure']=="race")
            {
                //初始化空的比赛列表
                $AthleteArr = array();
                //初始化空的分组列表
                $RaceGroupList = array();
                //获取比赛列表
                $RaceList = $oRace->getRaceList(array("RaceStageId"=>$RaceStageId));
                $SexList = $oUser->getSexList();
                $TeamList = array();
                $TeamArr = array();
                //循环比赛列表
                foreach($RaceList as $RaceId => $RaceInfo)
                {
                    //数据解包
                    $RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo']);
                    $RaceUserList = $oUser->getRaceUserList(array("RaceStageId"=>$RaceStageId,"RaceId"=>$RaceId));
                    foreach($RaceUserList as $key => $ApplyInfo)
                    {
                        if (!isset($RaceGroupList[$ApplyInfo["RaceGroupId"]]))
                        {
                            $RaceGroupList[$ApplyInfo["RaceGroupId"]] = $oRace->getRaceGroup($ApplyInfo["RaceGroupId"], "RaceGroupId,RaceGroupName");
                        }
                        $RaceUserInfo = $oUser->getRaceUser($ApplyInfo['RaceUserId']);
                        //单人模式
                        if($RaceInfo['comment']['ResultType'] == "Individual")
                        {
                            if($ApplyInfo['TeamId']>0)
                            {
                                //如果在队伍列表中有获取到队伍信息
                                if(!isset($TeamInfo['TeamId']))
                                {
                                    $TeamList[$ApplyInfo['TeamId']] = $oTeam->getTeamInfo($ApplyInfo['TeamId'],'TeamId,TeamName');
                                }
                            }
                            $AthleteArr[] = array("gender"=>$SexList[$RaceUserInfo["Sex"]],
                                "name"=>$RaceUserInfo["Name"],
                                "id"=>$RaceUserInfo['RaceUserId'],
                                "matchName"=>$RaceGroupList[$ApplyInfo["RaceGroupId"]]["RaceGroupName"],
                                "startNum"=>$ApplyInfo["BIB"],
                                "mobile"=>$RaceUserInfo['ContactMobile'],
                                "club"=>$ApplyInfo['TeamId']>0?$TeamList[$ApplyInfo['TeamId']]["TeamName"]:"");
                        }
                        else
                        {
                            $RaceUserInfo = $oUser->getRaceUser($ApplyInfo['RaceUserId']);
                            if(isset($TeamArr[$ApplyInfo["TeamId"]]))
                            {
                                $TeamArr[$ApplyInfo["TeamId"]]["name"] .= ",".$RaceUserInfo["Name"];
                            }
                            else
                            {
                                //如果在队伍列表中有获取到队伍信息
                                if(!isset($TeamInfo['TeamId']))
                                {
                                    $TeamList[$ApplyInfo['TeamId']] = $oTeam->getTeamInfo($ApplyInfo['TeamId'],'TeamId,TeamName');
                                }
                                $TeamArr[$ApplyInfo["TeamId"]] =
                                    array(
                                    "name"=>$RaceUserInfo["Name"],
                                    "id"=>$RaceUserInfo['RaceUserId'],
                                    "matchName"=>$RaceGroupList[$ApplyInfo["RaceGroupId"]]["RaceGroupName"],
                                    "startNum"=>$ApplyInfo["BIB"],
                                    "mobile"=>$RaceUserInfo['ContactMobile'],
                                    "club"=>$TeamList[$ApplyInfo['TeamId']]["TeamName"],
                                    "teamName"=>$TeamList[$ApplyInfo['TeamId']]["TeamName"]);
                            }
                        }
                    }
                }
                foreach($TeamArr as $key => $TeamInfo)
                {
                    $AthleteArr[] =  $TeamInfo;
                }
            }
            $ReturnArr = array(
                "raceId"=>$RaceStageId,
                "raceName"=>$RaceStageInfo['RaceStageName'],
                "athletes"=>$AthleteArr
            );
            return $ReturnArr;
        }
        else
        {
            return false;
        }

    }
}
