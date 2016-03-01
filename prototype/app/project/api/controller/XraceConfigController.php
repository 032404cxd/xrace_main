<?php
/**
 *
 * 
 */
class XraceConfigController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oRace;
    protected $oSports;
    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oRace = new Xrace_Race();
        $this->oSports = new Xrace_Sports();
    }
    /**
     *获取所有赛事的列表
     */
    public function getRaceCatalogListAction()
    {
        //获得赛事列表
        $raceCatalogList = $this->oRace->getAllRaceCatalogList();
        //如果没有返回值,默认为空数组
        if(!is_array($raceCatalogList)) 
        {
           $raceCatalogList = array();
        }
        //循环赛事列表数组
        foreach($raceCatalogList as $raceCatalogId => $raceCatalogInfo)
        {
            //如果有输出赛事图标的绝对路径
            if(isset($raceCatalogInfo['comment']['RaceCatalogIcon']))
            {
                //删除
                unset($raceCatalogList[$raceCatalogId]['comment']['RaceCatalogIcon']);
            }
            //如果有输出赛事图标的相对路径
            if(isset($raceCatalogInfo['comment']['RaceCatalogIcon_root']))
            {
                //拼接上ADMIN站点的域名
                $raceCatalogList[$raceCatalogId]['comment']['RaceCatalogIcon_root'] = $this->config->adminUrl.$raceCatalogList[$raceCatalogId]['comment']['RaceCatalogIcon_root'];
            }
        }
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $result = array("return"=>count($raceCatalogList)?1:0,"raceCatalogList"=>$raceCatalogList);
        echo json_encode($result);
    }
    
    /*
     * 获取单个赛事信息
     */
    public function getRaceCatalogInfoAction() {
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId)?abs(intval($this->request->RaceCatalogId)):0;
        //赛事ID必须大于0
        if($RaceCatalogId)
        {
            //获取赛事信息
            $raceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId);
            //检测主键存在,否则值为空
            $raceCatalogInfo = isset($raceCatalogInfo['RaceCatalogId'])?$raceCatalogInfo:array();
            //解包数组
            $raceCatalogInfo['comment'] = isset($raceCatalogInfo['comment'])?json_decode($raceCatalogInfo['comment'],true):array();
            //如果有输出赛事图标的绝对路径
            if(isset($raceCatalogInfo['comment']['RaceCatalogIcon']))
            {
                //删除
                unset($raceCatalogInfo['comment']['RaceCatalogIcon']);
            }
            //如果有输出赛事图标的相对路径
            if(isset($raceCatalogInfo['comment']['RaceCatalogIcon_root']))
            {
                //拼接上ADMIN站点的域名
                $raceCatalogInfo['comment']['RaceCatalogIcon_root'] = $this->config->adminUrl.$raceCatalogInfo['comment']['RaceCatalogIcon_root'];
            }
            //根据赛事获取组别列表
            $raceGroupList = isset($raceCatalogInfo['RaceCatalogId'])?$this->oRace->getAllRaceGroupList($raceCatalogInfo['RaceCatalogId'],"RaceGroupId,RaceGroupName"):array();
            //根据赛事获取分站列表
            $raceStageList = isset($raceCatalogInfo['RaceCatalogId'])?$this->oRace->getAllRaceStageList($raceCatalogInfo['RaceCatalogId'],"RaceStageId,RaceStageName"):array();
            //结果数组
            $result = array("return"=>1,"raceCatalogInfo"=>$raceCatalogInfo,'raceGroupList'=>$raceGroupList,'raceStageList'=>$raceStageList);
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"raceCatalog"=>array(),'raceGroupList'=>array(),'raceStageList'=>array(),"comment"=>"请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }
    
    /*
     * 根据赛事获取所有分站列表
     */
    public function getRaceStageListAction() {
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId)?abs(intval($this->request->RaceCatalogId)):0;
        //赛事ID必须大于0
        if($RaceCatalogId)
        {
            //获得分站列表
            $raceStageList = $this->oRace->getAllRaceStageList($RaceCatalogId);
            //如果没有返回值,默认为空数组
            if(!is_array($raceStageList))
            {
                $raceStageList = array();
            }
            //解包数组
            foreach ($raceStageList as $raceStageId => $raceStageValue)
            {
                $raceStageList[$raceStageId]['comment'] = json_decode($raceStageValue['comment'],true);
            }
            //结果数组
            $result = array("return"=>1,"raceStageList"=>$raceStageList);            
        }
        else 
        {
            //全部置为空
            $result = array("return"=>0,"raceStageList"=>array(),"comment"=>"请指定一个有效的赛事ID"); 
        }
        echo json_encode($result);
    }
    /*
     * 获取单个赛事分站信息
     */
    public function getRaceStageInfoAction() {
        //格式化赛事分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId)?abs(intval($this->request->RaceStageId)):0;
        //赛事分站D必须大于0
        if($RaceStageId)
        {
            //获得分站信息
            $raceStageInfo = $this->oRace->getRaceStage($RaceStageId);
            //检测主键存在,否则值为空
            $raceStageInfo = isset($raceStageInfo['RaceStageId'])?$raceStageInfo:array();
            //解包数组
            $raceStageInfo['comment'] = isset($raceStageInfo['comment'])?json_decode($raceStageInfo['comment'],true):array();
            //如果有选择组别
            if(isset($raceStageInfo['comment']['SelectedRaceGroup']))
            {
                //循环已经选择的组别
                foreach($raceStageInfo['comment']['SelectedRaceGroup'] as $raceGroupId)
                {
                    //获取赛事分组基本信息
                    $raceGroupInfo = $this->oRace->getRaceGroup($raceGroupId,"RaceGroupId,RaceGroupName");
                    //如果有获取到分组信息
                    if($raceGroupInfo['RaceGroupId'])
                    {
                        //提取分组名称
                        $raceStageInfo['comment']['SelectedRaceGroup'][$raceGroupId] = $raceGroupInfo['RaceGroupName'];
                    }
                    else
                    {
                        //删除
                        unset($raceStageInfo['comment']['SelectedRaceGroup'][$raceGroupId]);
                    }
                }
            }
            else
            {
                $raceStageInfo['comment']['SelectedRaceGroup'] = array();
            }
            //结果数组
            $result = array("return"=>1,"raceStageInfo"=>$raceStageInfo);
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"raceStageInfo"=>array(),'raceGroupList'=>array(),"comment"=>"请指定一个有效的分站ID");
        }
        echo json_encode($result);
    }
    /*
     * 获取单个赛事组别的信息
     */
    public function getRaceGroupInfoAction() {
        //格式化赛事组别ID,默认为0
        $RaceGroupId = isset($this->request->RaceGroupId)?intval($this->request->RaceGroupId):0;
        //赛事组别必须大于0
        if($RaceGroupId)
        {   
            //获取赛事组别信息
            $raceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId);
            //检测主键存在,否则值为空
            $raceGroupInfo = isset($raceGroupInfo['RaceGroupId'])?$raceGroupInfo:array();
            //解包数组
            $raceGroupInfo['comment'] = isset($raceGroupInfo['comment'])?json_decode($raceGroupInfo['comment'],true):array();
            //如果有配置分组的审核规则
            if(isset($raceGroupInfo['comment']['LicenseList']))
            {
                //暂时先删除,其后版本再添加
                unset($raceGroupInfo['comment']['LicenseList']);
            }
            //结果数组
            $result = array("return"=>1,"raceGroupInfo"=>$raceGroupInfo);            
        }   
        else
        {
            //全部置为空
            $result = array("return"=>0,"raceGroupInfo"=>array(),"comment"=>"请指定一个有效的赛事ID");    
        }
        echo json_encode($result);
    }
    /*
     * 获取赛事分站和赛事组别获取比赛列表
     */
    public function getRaceListAction() {
        //格式化赛事分站和赛事组别ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId)?abs(intval($this->request->RaceStageId)):0;
        $RaceGroupId = isset($this->request->RaceGroupId)?abs(intval($this->request->RaceGroupId)):0;
        //赛事分站和赛事组别ID必须大于0
        if($RaceStageId && $RaceGroupId)
        {
            //获得比赛列表
            $raceList = $this->oRace->getRaceList($RaceStageId, $RaceGroupId,"RaceId,RaceName,PriceList,SingleUser,TeamUser,StartTime,EndTime,comment");
            if(!is_array($raceList))
            {
                $raceList =array();
            }
            $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
            //解包数组
            foreach ($raceList as $raceId => $raceInfo)
            {
               //初始化比赛里程
               $raceList[$raceId]['Distence'] = 0;
               //初始化比赛海拔提升
               $raceList[$raceId]['AltAsc'] = 0;
               //初始化比赛海拔下降
               $raceList[$raceId]['AltDec'] = 0;
                //如果有配置运动分段
               if(isset($raceInfo['comment']['DetailList']))
               {
                   //循环运动分段
                   foreach($raceInfo['comment']['DetailList'] as $detailId => $detailInfo)
                   {
                       //如果有配置过该运动分段
                       if(isset($SportsTypeList[$detailInfo['SportsTypeId']]))
                       {
                           //获取运动类型名称
                           $raceList[$raceId]['comment']['DetailList'][$detailId]['SportsTypeName'] =   $SportsTypeList[$detailInfo['SportsTypeId']]['SportsTypeName'];
                           //初始化运动分段的长度
                           $raceList[$raceId]['comment']['DetailList'][$detailId]['Distence'] = 0;
                           //初始化运动分段的海拔上升
                           $raceList[$raceId]['comment']['DetailList'][$detailId]['AltAsc'] = 0;
                           //初始化运动分段的海拔下降
                           $raceList[$raceId]['comment']['DetailList'][$detailId]['AltDec'] = 0;
                           //获取计时点信息
                           $timingInfo = isset($detailInfo['TimingId'])?$this->oRace->getTimingDetail($detailInfo['TimingId']):array();
                           //如果获取到计时点信息
                           if(isset($timingInfo['TimingId']))
                           {
                               //数据解包
                               $timingInfo['comment'] = isset($timingInfo['comment'])?json_decode($timingInfo['comment'],true):array();
                               //循环计时点信息
                               foreach($timingInfo['comment'] as $tid => $tInfo)
                               {
                                   //累加里程到运动分段
                                   $raceList[$raceId]['comment']['DetailList'][$detailId]['Distence'] += $tInfo['ToNext']*$tInfo['Round'];
                                   //累加里程到比赛
                                   $raceList[$raceId]['Distence'] += $tInfo['ToNext']*$tInfo['Round'];
                                   //累加海拔上升到运动分段
                                   $raceList[$raceId]['comment']['DetailList'][$detailId]['AltAsc'] += $tInfo['AltAsc']*$tInfo['Round'];
                                   //累加海拔上升到比赛
                                   $raceList[$raceId]['AltAsc'] += $tInfo['AltAsc']*$tInfo['Round'];
                                   //累加海拔下降到运动分段
                                   $raceList[$raceId]['comment']['DetailList'][$detailId]['AltDec'] += $tInfo['AltDec']*$tInfo['Round'];
                                   //累加海拔下降到比赛
                                   $raceList[$raceId]['AltDec'] += $tInfo['AltDec']*$tInfo['Round'];
                               }
                           }
                       }
                       else
                       {
                           unset($raceList[$raceId]['comment']['DetailList'][$detailId]);
                       }
                   }
               }
               else
               {
                   //初始化为空数组
                   $raceList[$raceId]['comment']['DetailList'] = array();
               }
            }
            //结果数组 如果列表有数据则为成功，否则为失败
            $result = array("return"=>count($raceList)?1:0,"raceList"=>$raceList);
        }
        else
        {
            $result = array("return"=>0,"raceList"=>array());
        }
        echo json_encode($result);
    }
    /*
     * 获得单个比赛信息
     */
    public function getRaceAction() {
        //格式化比赛ID,默认为0
        $RaceId = isset($this->request->RaceId)?abs(intval($this->request->RaceId)):0;
        //比赛ID必须大于0
        if($RaceId)
        {
            //获取比赛信息
            $raceInfo = $this->oRace->getRaceInfo($RaceId);
            //检测主键存在,否则值为空
            $raceInfo = isset($raceInfo['RaceId']) ? $raceInfo : array();
            //解包数组
            $raceInfo['comment'] = isset($raceInfo['comment']) ? json_decode($raceInfo['comment']) : array();
            //结果数组
            $result = array("return"=>1,"raceInfo"=>$raceInfo);
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"raceInfo"=>array(),"comment"=>"请指定一个有效的比赛ID");
        }
        echo json_encode($result); 
    }

}