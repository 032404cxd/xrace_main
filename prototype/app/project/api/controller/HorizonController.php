<?php
/**
 *
 * 
 */
class HorizonController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oRace;
    protected $oHorizon;
    protected $oUser;

    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oHorizon = new Xrace_Horizon();
        $this->oRace = new Xrace_Race();
        $this->oUser = new Xrace_UserInfo();
    }

    /**
     *获取某个分站的信息的列表
     */
    public function getRaceInfoAction()
    {
        //格式化分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        $RaceInfo = $this->oHorizon->getRaceStageInfo($RaceStageId);
        echo json_encode($RaceInfo);
    }
    /**
     *获取某个分站的选手列表
     */
    public function getAthleteListAction()
    {
        //格式化分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        $AthleteList = $this->oHorizon->getAthleteList($RaceStageId);
        echo json_encode($AthleteList);
    }
    /**
     *获取某个分站的计时点列表
     */
    public function getTimingPointListAction()
    {
        //格式化分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        $TimingPointList = $this->oHorizon->getTimingPointList($RaceStageId);
        echo json_encode($TimingPointList);
    }


}