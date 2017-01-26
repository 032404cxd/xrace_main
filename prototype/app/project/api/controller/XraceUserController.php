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

    //insert into xrace_user.UserInfo (UserId,Name,Birthday,Sex,WeChatId,Mobile) SELECT UserId,name,birth_day,sex,wx_open_id,phone FROM `user_profile` where phone != '' and phone != 'tbd' and phone not in (select Mobile from xrace_user.UserInfo) limit 50
    /**
     *获取所用户信息(缓存)
     */
    public function getUserInfoAction()
    {
        //是否调用缓存
        $Cache = isset($this->request->Cache) ? abs(intval($this->request->Cache)) : 1;
        //是否显示说明注释 默认为1
        $UserId = isset($this->request->UserId) ? abs(intval($this->request->UserId)) : 0;
        //获得用户信息
        $UserInfo = $this->oUser->getUserInfo($UserId,"*",$Cache);
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $result = array("return" => isset($UserInfo['UserId']) ? 1 : 0, "UserInfo" => $UserInfo);
        echo json_encode($result);
    }
    /**
     *通过Token获取所用户信息(缓存)
     */
    public function getUserInfoByTokenAction()
    {
        //是否调用缓存
        $Cache = isset($this->request->Cache) ? abs(intval($this->request->Cache)) : 1;
        //是否显示说明注释 默认为1
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->geToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获得用户信息
            $UserInfo = $this->oUser->getUserInfo($TokenInfo['UserId'],"*",$Cache);
            //结果数组 如果列表中有数据则返回成功，否则返回失败
            $result = array("return" => isset($UserInfo['UserId']) ? 1 : 0, "UserInfo" => $UserInfo);
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
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
    }
    /**
     *第三方登录
     */
    public function thirdPartyLoginAction()
    {
        //$LoginData  = '{"openid": "odLjsvnZwRS4lduV6D7DpS5hJoyY","nickname": "GUI Ling","headimgurl": "http://wx.qlogo.cn/mmopen/fl6pKMZtTyXGYHHVno0td0cv2VR9HHUEp2pz6p9qLAfTrOVtP07pgNSytgfKm4uBgGjXic0sGTkZKc7lFvFOKE999tY8jfEfj/0","sex": "2","province": "上海","city": "长宁"}';
        //$LoginData  = '{"openid": "odLjsvvYfXvkm9Rkrd4HAHXeqvA8","nickname": "JiMMy","headimgurl": "http://wx.qlogo.cn/mmopen/s6icJeKAt9X2zFZiafUjibkZhkibib8ickRZMDeoIwpfAeh04htIbSecdkU5uoW0AdAucU1kM4tEnKuw6uW6zeaWBYwLMYj9evlJvy/0","sex": "0","province": "","city": ""}';
        //$LoginData  = '{"openid": "odLjsvnl2cUkbbbM8EBvZmJOX7Sw","nickname": "栋辉tim","headimgurl": "http://wx.qlogo.cn/mmopen/fl6pKMZtTyXGYHHVno0td2q2q1K7U1r4Gx1Hib8mL7lVQiaCdux7ZrtAZicmeOu79ZOuhGicDmSUC9LiaqIRwIzQbVIzyvwbXmyn3/0","sex": "1","province": "上海","city": "浦东新区"}';

        $LoginData = isset($this->request->LoginData) ? trim($this->request->LoginData) : "";

        $LoginSource = isset($this->request->LoginSource) ? trim($this->request->LoginSource) : "WeChat";
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        $LoginData = json_decode($LoginData,true);
        $Login = $this->oUser->ThirdPartyLogin($LoginData,$LoginSource);
        if(isset($Login['UserId']))
        {
            //结果数组 返回用户信息
            $result = array("return" => 1, "UserInfo" => $Login,"Token"=>$this->oUser->makeToken($Login['UserId'],$IP),"comment" => "登录成功");
        }
        elseif(isset($Login['RegId']))
        {
            //结果数组 返回注册信息，引导绑定手机
            $result = array("return" => 1, "RegInfo" => $Login,"comment" => $Login['NeedMobile']?"请绑定手机":"请输入已经发往手机的验证码");
        }
        else
        {
            //结果数组 返回失败
            $result = array("return" => 0,"comment" => "登录失败，请重试");
        }
        echo json_encode($result);
    }
    /**
     *第三方登录时绑定手机
     */
    public function thirdPartyRegByMobileAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        $RegId = isset($this->request->RegId) ? abs(intval($this->request->RegId)) :0;
        //根据手机号码获取用户信息
        $UserInfo = $this->oUser->getUserByColumn("Mobile",$Mobile);
        if(isset($UserInfo['UserId']))
        {
            //返回错误
            $result = array("return" => 0,"comment" => "用户已存在，请输入其他的手机号码");
        }
        else
        {
            //获取正在用此手机号注册的用户
            $RegInfoList = $this->oUser->getUserRegByColumn("Mobile",$Mobile);
            //循环注册记录
            foreach($RegInfoList as $key => $value)
            {
                //如果发现尚未失效
                if(strtotime($value['ExceedTime'])>=time() && $value['RegId']!=$RegId)
                {
                    //返回错误
                    $result = array("return" => 0,"comment" => "此手机其他用户正在验证中");
                    echo json_encode($result);
                    return;
                }
            }
            //获取注册记录
            $RegInfo = $this->oUser->getRegInfo($RegId);
            //如果获取到注册记录
            if(isset($RegInfo['RegId']))
            {
                //尚未绑定手机
                if($RegInfo['Mobile']=="")
                {
                    //更新记录
                    $RegInfoUpdate = array('Mobile'=>$Mobile,'ExceedTime'=>date("Y-m-d H:i:s",time()+3600),'ValidateCode' => sprintf("%06d",rand(1,999999)));
                    $update = $this->oUser->updateRegInfo($RegInfo['RegId'],$RegInfoUpdate);
                    //如果更新成功
                    if($update)
                    {
                        $result = array("return" => 1,"comment" => "验证码已发送");
                    }
                    else
                    {
                        //返回错误
                        $result = array("return" => 0,"comment" => "更新失败，请重试");
                    }
                }
                else
                {
                    //如果已经发送该手机
                    if($RegInfo['Mobile']==$Mobile)
                    {
                        //如果已过有效期
                        if(strtotime($RegInfo['ExceedTime'])<=time())
                        {
                            //更新记录
                            $update = $this->oUser->thirdPartyRegMobile($RegId,$Mobile);
                            //如果更新成功
                            if($update)
                            {
                                $result = array("return" => 1,"comment" => "验证码已发送");
                            }
                            else
                            {
                                //返回错误
                                $result = array("return" => 0,"comment" => "更新失败，请重试");
                            }
                        }
                        else
                        {
                            $result = array("return" => 1,"comment" => "验证码之前已发送");
                        }
                    }
                    else
                    {
                        //如果已过有效期
                        if(strtotime($RegInfo['ExceedTime'])<=time())
                        {
                            //更新记录
                            $update = $this->oUser->thirdPartyRegMobile($RegId,$Mobile);
                            //如果更新成功
                            if($update)
                            {
                                $result = array("return" => 1,"comment" => "验证码已发送");
                            }
                            else
                            {
                                //返回错误
                                $result = array("return" => 0,"comment" => "更新失败，请重试");
                            }
                        }
                        else
                        {
                            $result = array("return" => 1,"comment" => "验证码之前已发送到尾号为".substr($RegInfo['Mobile'],-4)."的其他手机");
                        }
                    }
                }
            }
            else
            {
                //返回错误
                $result = array("return" => 0,"comment" => "无此记录，请重试");
            }
        }
        echo json_encode($result);
    }
    /**
     *注册时的短信验证
     */
    public function regMobileAuthAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        $ValidateCode = isset($this->request->ValidateCode) ? trim($this->request->ValidateCode) : "";
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        $Auth = $this->oUser->regMobileAuth($Mobile,$ValidateCode);
        if($Auth > 0)
        {
            //结果数组 返回用户信息
            $result = array("return" => 1, "UserInfo" => $this->oUser->getUserInfo($Auth),"Token"=>$this->oUser->makeToken($Auth,$IP),"comment" => "注册成功");
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
        echo json_encode($result);
    }
    public function tokenAction()
    {
        $UserId = isset($this->request->UserId) ? abs(intval($this->request->UserId)) : 0;
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        $token = $this->oUser->makeToken($UserId,$IP);
    }
}