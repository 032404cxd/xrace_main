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
    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oRace = new Xrace_Race();
    }
    public function indexAction() {
        echo 'index';
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
        //结果数组
        $result = array("return"=>1,"raceCatalogList"=>$raceCatalogList);
        echo json_encode($result);
    }
    
    /*
     * 获取单个赛事信息
     */
    public function getRaceCatalogAction() {
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
            foreach ($raceStageList as $raceStageId => $raceStageValue) {
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
    public function getRaceStageAction() {
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
            //根据赛事获取组别列表
            $raceGroupList = isset($raceStageInfo['RaceCatalogId'])?$this->oRace->getAllRaceGroupList($raceStageInfo['RaceCatalogId'],"RaceGroupId,RaceGroupName"):array();
            //结果数组
            $result = array("return"=>1,"raceStageInfo"=>$raceStageInfo,'raceGroupList'=>$raceGroupList);
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"raceStageInfo"=>array(),'raceGroupList'=>array(),"comment"=>"请指定一个有效的分站ID");
        }
        echo json_encode($result);
    }
    
    /*
     * 根据赛事获取所有赛事组别列表
     */
    public function getRaceGroupListAction() {
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId)?abs(intval($this->request->RaceCatalogId)):0;
        //赛事ID必须大于0
        if($RaceCatalogId)
        {
            //获得赛事组别列表
            $raceGroupList = $this->oRace->getAllRaceGroupList($RaceCatalogId);
            if(!is_array($raceGroupList))
            {
                $raceGroupList = array();
            }
            //解包数组
            foreach ($raceGroupList as $raceGroupId => $raceGroupValue) {
                $raceStageList[$raceGroupId]['comment'] = json_decode($raceGroupValue['comment'],true);
            }
            //结果数组
            $result = array("return"=>1,"raceGroupList"=>$raceGroupList); 
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"raceGroupList"=>array(),"comment"=>"请指定一个有效的赛事ID"); 
        }
        echo json_encode($result);
        
    }
    /*
     * 获取单个赛事组别的信息
     */
    public function getRaceGroupAction() {
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
            $raceList = $this->oRace->getRaceList($RaceStageId, $RaceGroupId);            
            if(!is_array($raceList))
            {
                $raceList =array();
            }
            //解包数组
            foreach ($raceList as $raceId => $raceValue) {
                $raceList[$raceId]['comment'] = json_decode($raceValue['comment'],true);
            }
            //结果数组
            $result = array("return"=>1,"raceList"=>$raceList);
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