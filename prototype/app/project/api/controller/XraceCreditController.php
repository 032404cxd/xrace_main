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
    }

    //insert into xrace_user.UserInfo (UserId,Name,Birthday,Sex,WeChatId,Mobile) SELECT user_id,name,birth_day,sex,wx_open_id,phone FROM `user_profile` where phone != '' and phone != 'tbd' and phone not in (select Mobile from xrace_user.UserInfo) limit 50
    /**
     *获取所用户信息(缓存)
     */
    public function creditByActionAction()
    {
        //是否调用缓存
        $Action = trim($this->request->Action) ? trim($this->request->Action) : "";
        //是否显示说明注释 默认为1
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
}