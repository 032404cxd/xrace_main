<?php
/**
 *
 * 
 */
class XraceCreditController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oUser;
    protected $oAction;
    protected $oCredit;

    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oUser = new Xrace_UserInfo();
        $this->oAction = new Xrace_Action();
        $this->oCredit = new Xrace_Credit();
    }

    //insert into xrace_user.UserInfo (UserId,Name,Birthday,Sex,WeChatId,Mobile) SELECT UserId,name,birth_day,sex,wx_open_id,phone FROM `user_profile` where phone != '' and phone != 'tbd' and phone not in (select Mobile from xrace_user.UserInfo) limit 50
    /**
     *根据动作给用户更新积分
     */
    public function creditByActionAction()
    {
        //动作
        $Action = trim($this->request->Action) ? trim($this->request->Action) : "";
        //用户ID
        $UserId = isset($this->request->UserId) ? abs(intval($this->request->UserId)) : 0;
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $Credit = $this->oAction->CreditByAction($Action,$UserId);
        if($Credit>0)
        {
            $result = array("return" => 1,"comment" => "成功！");
        }
        else
        {
            $result = array("return" => 0,"comment" => "失败！");
        }
        echo json_encode($result);
    }
    /**
     *获取用户的积分类目详情
     */
    public function getUserCreditLogAction()
    {
        //用户ID
        $params['UserId'] = isset($this->request->UserId) ? abs(intval($this->request->UserId)) : 0;
        //页码，默认为1
        $params['Page'] = isset($this->request->Page) ? abs(intval($this->request->Page)) : 1;
        //每页数量
        $params['PageSize'] = isset($this->request->PageSize) ? abs(intval($this->request->PageSize)) : 5;
        //获取记录数量
        $params['getCount'] = 1;
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $CreditLog = $this->oCredit->getCreditLog($params);
        foreach($CreditLog['CreditLog'] as $Id => $LogInfo)
        {
            //如果在积分列表里面没有该记录
            if (!isset($CreditList[$LogInfo['CreditId']]))
            {
                //重新获取积分信息
                $CreditInfo = $this->oCredit->getCredit($LogInfo['CreditId'], "CreditId,CreditName");
                //如果获取到
                if (isset($CreditInfo['CreditId']))
                {
                    //保存到积分列表中
                    $CreditList[$LogInfo['CreditId']] = $CreditInfo;
                }
            }
            //保存积分名称
            $CreditLog['CreditLog'][$Id]['CreditName'] = isset($CreditList[$LogInfo['CreditId']])?$CreditList[$LogInfo['CreditId']]['CreditName']:"未知";
        }
        $result = array("return" => 1,"CreditLog" => $CreditLog);
        echo json_encode($result);
    }
}