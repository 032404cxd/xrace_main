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
        if ($LogId > 0)
        {
            //全部置为空
            $result = array("return" => 1, "comment" => "打卡成功");
        }
        else
        {
            if($LogId == -1)
            {
                //全部置为空
                $result = array("return" => 0, "comment" => "用户信息有误");
            }
            elseif($LogId == -2)
            {
                //全部置为空
                $result = array("return" => 0, "comment" => "用户未找到");
            }
            elseif($LogId == -3)
            {
                //全部置为空
                $result = array("return" => 0, "comment" => "比赛未找到");
            }
            elseif($LogId == -4)
            {
                //全部置为空
                $result = array("return" => 0, "comment" => "好像没报名哦");
            }
            elseif($LogId == -5)
            {
                //全部置为空
                $result = array("return" => 0, "comment" => "计时点好像不存在哦");
            }
            elseif($LogId == -6)
            {
                //全部置为空
                $result = array("return" => 0, "comment" => "离开打卡点的距离有点远哦");
            }
        }
        echo json_encode($result);
    }
}