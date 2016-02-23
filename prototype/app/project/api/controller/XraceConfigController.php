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
        $raceCatalogList = $this->oRace->getAllRaceCatalogList();
        if(!is_array($raceCatalogList)) 
        {
           $raceCatalogList = array();
        }
        $result = array("return"=>1,"raceCatalogList"=>$raceCatalogList);
        echo json_encode($result);
    }
    
    /*
     * 获取单个赛事信息
     */
    public function getRaceCatalogAction() {
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
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
        $RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
        $raceStageList = $this->oRace->getAllRaceStageList($RaceCatalogId);
//        foreach ($raceStageList as $key => $value) {
//            $raceStageList[$key]['comment'] = json_decode($value['comment'],true);
//        }
        $result = array("return"=>0,"raceStageList"=>$raceStageList);
        echo json_encode($result);
    }
    
    /*
     * 获取单个赛事分站信息
     */    
    public function getRaceStageAction() {
        $RaceStageId = isset($this->request->RaceStageId)?intval($this->request->RaceStageId):0;
        $raceStage = $this->oRace->getRaceStage($RaceStageId);
        $result = array("return"=>0,"raceStage"=>$raceStage);
        echo json_encode($result);
    }
    
    /*
     * 根据赛事获取所有赛事组别列表
     */
    public function getAllRaceGroupListAction() {
        $RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
        $raceGroupList = $this->oRace->getAllRaceGroupList($RaceCatalogId);
        $result = array("return"=>0,"raceGroupList"=>$raceGroupList);
        echo json_encode($result);
        
    }
    /*
     * 获取单个赛事组别的信息
     */
    public function getRaceGroupAction() {
        $RaceGroupId = isset($this->request->RaceGroupId)?intval($this->request->RaceGroupId):0;
        $raceGroup = $this->oRace->getRaceGroup($RaceGroupId);
        $result = array("return"=>0,"raceGroup"=>$raceGroup);
        echo json_encode($result);
    }
    /*
     * 获取赛事分站和赛事组别获取比赛列表
     */
    public function getRaceListAction() {
        $RaceStageId = isset($this->request->RaceStageId)?intval($this->request->RaceStageId):0;
        $RaceGroupId = isset($this->request->RaceGroupId)?intval($this->request->RaceGroupId):0;
        $raceList = $this->oRace->getRaceList($RaceStageId, $RaceGroupId);
        $result = array("return"=>0,"raceList"=>$raceList);
        echo json_encode($result);
        
    }
    
    /*
     * 获得单个比赛信息
     */
    public function getRaceAction() {
        $RaceId = isset($this->request->RaceId)?intval($this->request->RaceId):0;
        $race = $this->oRace->getRaceInfo($RaceId);
        $result = array("return"=>0,"race"=>$race);
        echo json_encode($result); 
    }

}