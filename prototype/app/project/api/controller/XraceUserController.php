<?php
/**
 *
 * 
 */
class XraceUserController extends AbstractController
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
    }

    //insert into xrace_user.UserInfo (UserId,Name,Birthday,Sex,WeChatId,Mobile) SELECT user_id,name,birth_day,sex,wx_open_id,phone FROM `user_profile` where phone != '' and phone != 'tbd' and phone not in (select Mobile from xrace_user.UserInfo) limit 50
    /**
     *获取所用户信息(缓存)
     */
    public function getUserInfoAction()
    {
        //是否调用缓存
        $Cache = isset($this->request->Cache) ? abs(intval($this->request->Cache)) : 1;
        //是否显示说明注释 默认为1
        $UserId = isset($this->request->UserId) ? abs(intval($this->request->UserId)) : 0;
        //获得赛事列表
        $UserInfo = $this->oUser->getUserInfo($UserId,"Mobile",$Cache);
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $result = array("return" => isset($UserInfo['UserId']) ? 1 : 0, "UserInfo" => $UserInfo);
        echo json_encode($result);
    }

    /**
     *登录
     */
    public function loginAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        $Password = isset($this->request->Password) ? trim($this->request->Password) : "";
        $Mobile = "186217582371";
        $Password = md5("123");
        $Login = $this->oUser->Login($Mobile,$Password);
        print_R($Login);
    }
    /**
     *第三方登录
     */
    public function thirdPartyLoginAction()
    {
        $Text  = '{"openid":"o8Lb_t6lobJhEDrQcqwnTNRVpM58","nickname":"NICKNAME","sex":1,"province":"PROVINCE","city":"CITY","country":"COUNTRY","headimgurl":"http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfp b6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0","privilege":["PRIVILEGE1","PRIVILEGE2"],"unionid":"o6_bmasdasdsad6_2sgVt7hMZOPfL"}';
        $LoginSource = isset($this->request->LoginSource) ? trim($this->request->LoginSource) : "WeChat";
        $LoginData = json_decode($Text,true);
        $Login = $this->oUser->ThirdPartyLogin($LoginData,$LoginSource);
    }
    /**
     *注册时的短信验证
     */
    public function regMobileAuthAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        $ValidateCode = isset($this->request->ValidateCode) ? trim($this->request->ValidateCode) : "";
        $Mobile = "186217582371";
        $ValidateCode = "825988";
        $Auth = $this->oUser->regMobileAuth($Mobile,$ValidateCode);
        if($Auth > 0)
        {
            //结果数组 返回用户信息
            $result = array("return" => 1, "UserInfo" => $this->oUser->getUserInfo($Auth),"注册成功");
        }
        elseif($Auth = -1)
        {
            //结果数组 返回用户信息
            $result = array("return" => 0, "Auth" => 1, "comment" => "验证码过期，已重发请重新输入");
        }
        else
        {
            //结果数组 返回用户信息
            $result = array("return" => 0, "comment" => "验证失败");
        }
    }


    //{"openid":"OPENID",  "nickname":"NICKNAME",  "sex":1,  "province":"PROVINCE",  "city":"CITY",  "country":"COUNTRY",  "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfp b6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",  "privilege":["PRIVILEGE1",   "PRIVILEGE2"  ],  "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"}
}