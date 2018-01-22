<?php
/**
 *
 * 
 */
class WechatController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oWechatTiming;
    protected $oUser;
    protected $oRace;

    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oWechatTiming = new Xrace_WechatTiming();
        $this->oRace = new Xrace_Race();
        $this->oUser = new Xrace_UserInfo();
    }

    /**
     *获取打卡记录并入库
     */
    public function insertTimingAction()
    {
        //比赛ID
        $Timing['RaceId'] = abs(intval($this->request->RaceId));
        //微信openID
        $Timing['OpenId'] = trim(urldecode($this->request->OpenId));
        //计时点标识
        $Timing['Location'] = trim(urldecode($this->request->Location));
        //时间
        $Timing['Time'] = abs(intval($this->request->Time));
        //如果时间非法或与当前时差超过60秒，则以当前时间为准
        $Timing['Time'] = $Timing['Time'] > 0 || (abs($Timing['Time'] - time()) >= 60) ? $Timing['Time'] : time();
        $Timing['TencentX'] = trim(urldecode($this->request->TencentX));
        $Timing['TencentY'] = trim(urldecode($this->request->TencentY));
        //插入记录
        $LogId = $this->oWechatTiming->insertTimingLog($Timing);
        if ($LogId)
        {
            //全部置为空
            $result = array("return" => 1, "comment" => "打卡成功");
        }
        else
        {
            //全部置为空
            $result = array("return" => 0, "comment" => "打卡失败");
        }
        echo json_encode($result);
    }
}