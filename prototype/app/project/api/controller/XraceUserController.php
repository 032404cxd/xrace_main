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
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        "Token:"."<br>";;
        print_R($TokenInfo);
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
     *手机登录
     */
    public function mobileRegAction()
    {
        //手机号码
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        //密码
        $Password = isset($this->request->Password) ? trim($this->request->Password) : "";
        //客户端
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        //尝试账号登录
        $Login = $this->oUser->MobileReg($Mobile,$Password);
        //无用户，创建
        if(isset($Login['RegId']))
        {
            //结果数组 返回注册信息，引导绑定手机
            $result = array("return" => 1, "RegInfo" => $Login,"comment" => "请输入已经发往手机的验证码");
        }
        elseif($Login == -1)
        {
            //结果数组 返回失败
            $result = array("return" => 0,"comment" => "用户已存在，请更换其他手机号码注册");
        }
        else
        {
            //结果数组 返回失败
            $result = array("return" => 0,"comment" => "注册失败，请重试");
        }
        echo json_encode($result);
    }
    /**
     *手机登录
     */
    public function mobileLoginAction()
    {
        //手机号码
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        //密码
        $Password = isset($this->request->Password) ? trim($this->request->Password) : "";
        //客户端
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        //尝试账号登录
        $Login = $this->oUser->Login($Mobile,$Password);
        //登录成功
        if(isset($Login['UserId']))
        {
            //结果数组 返回用户信息
            $result = array("return" => 1, "UserInfo" => $Login,"Token"=>$this->oUser->makeToken($Login['UserId'],$IP,"Mobile"),"comment" => "登录成功");
        }
        //已有用户用第三方登陆过
        elseif($Login == -1)
        {
            //结果数组 返回注册信息，引导绑定手机
            $result = array("return" => -1, "comment" => "是否用其他方式登陆过？试试微信？");
        }
        //无用户，创建
        elseif($Login == -2)
        {
            //结果数组 返回失败
            $result = array("return" => 0,"comment" => "登录失败，请重试");
        }
        else
        {
            //结果数组 返回失败
            $result = array("return" => 0,"comment" => "登录失败，请重试");
        }
        echo json_encode($result);
    }
    /**
     *第三方登录
     */
    public function thirdPartyLoginAction()
    {
        //$text  = '{"openid": "odLjsvnZwRS4lduV6D7DpS5hJoyY","nickname": "GUI Ling","headimgurl": "http://wx.qlogo.cn/mmopen/fl6pKMZtTyXGYHHVno0td0cv2VR9HHUEp2pz6p9qLAfTrOVtP07pgNSytgfKm4uBgGjXic0sGTkZKc7lFvFOKE999tY8jfEfj/0","sex": "2","province": "上海","city": "长宁"}';
        $text  = '{"openid": "odLjsvvYfXvkm9Rkrd4HAHXeqvA8","nickname": "JiMMy","headimgurl": "http://wx.qlogo.cn/mmopen/s6icJeKAt9X2zFZiafUjibkZhkibib8ickRZMDeoIwpfAeh04htIbSecdkU5uoW0AdAucU1kM4tEnKuw6uW6zeaWBYwLMYj9evlJvy/0","sex": "0","province": "","city": ""}';
        //$LoginData  = '{"openid": "odLjsvnl2cUkbbbM8EBvZmJOX7Sw","nickname": "栋辉tim","headimgurl": "http://wx.qlogo.cn/mmopen/fl6pKMZtTyXGYHHVno0td2q2q1K7U1r4Gx1Hib8mL7lVQiaCdux7ZrtAZicmeOu79ZOuhGicDmSUC9LiaqIRwIzQbVIzyvwbXmyn3/0","sex": "1","province": "上海","city": "浦东新区"}';

        //身份数据
        $LoginData = isset($this->request->LoginData) ? trim($this->request->LoginData) : $text;
        //第三方来源
        $LoginSource = isset($this->request->LoginSource) ? trim($this->request->LoginSource) : "WeChat";
        //客户端
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        //身份数据解包
        $LoginData = json_decode($LoginData,true);
        //尝试第三方登录
        $Login = $this->oUser->ThirdPartyLogin($LoginData,$LoginSource);
        //登录成功
        if(isset($Login['UserId']))
        {
            //结果数组 返回用户信息
            $result = array("return" => 1, "UserInfo" => $Login,"Token"=>$this->oUser->makeToken($Login['UserId'],$IP,$LoginSource),"comment" => "登录成功");
        }
        //无用户，创建
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
                        //if(strtotime($RegInfo['ExceedTime'])<=time())
                        //{
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
                       //}
                        //else
                        //{
                            //$result = array("return" => 1,"comment" => "验证码之前已发送");
                        //}
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
        //手机号码
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) : "";
        //验证码
        $ValidateCode = isset($this->request->ValidateCode) ? trim($this->request->ValidateCode) : "";
        //客户端
        $IP = isset($this->request->IP) ?  trim($this->request->IP):"127.0.0.1";
        //短信验证
        $Auth = $this->oUser->regMobileAuth($Mobile,$ValidateCode);
        //如果验证成功
        if($Auth['UserId'] > 0)
        {
            //结果数组 返回用户信息
            $result = array("return" => 1, "UserInfo" => $this->oUser->getUserInfo($Auth['UserId'],"*",0),"Token"=>$this->oUser->makeToken($Auth['UserId'],$IP,$Auth['LoginSource']),"comment" => "注册成功");
        }
        elseif($Auth['RegId']>0)
        {
            //结果数组 返回用户信息
            $result = array("return" => 0, "NeedPassword" => 1, "comment" => "请输入密码");
        }
        elseif($Auth == -1)
        {
            //结果数组 返回用户信息
            $result = array("return" => 0, "Auth" => 1, "comment" => "验证失败，已重发请重新输入");
        }
        else
        {
            //结果数组 返回用户信息
            $result = array("return" => 0, "comment" => "验证失败");
        }
        echo json_encode($result);
    }
    //根据他人填写的信息选择用户
    public function getUserByOtherAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获取证件类型列表
            $IdTypeList = $this->oUser->getAuthIdType();
            //获取证件类型列表
            $SexList = $this->oUser->getSexList();
            //用户姓名
            $Name = trim($this->request->Name);
            //证件号码
            $IdNo = trim($this->request->IdNo);
            //证件类型
            $IdType = abs(intval($this->request->IdType));
            //如果不在证件类型范围内，默认为身份证
            $IdType = isset($IdTypeList[$IdType])?$IdType:1;
            //证件类型
            $Sex = abs(intval($this->request->Sex));
            //如果不在证件类型范围内，默认为身份证
            $Sex = isset($SexList[$Sex])?$Sex:0;
            //联系电话号码
            $ContactMobile = trim($this->request->ContactMobile);
            //如果证件号码长度不足
            if(strlen($IdNo) <=6)
            {
                //返回错误
                $result = array("return" => 0,"comment"=>"请输入合法的证件号");
            }
            else
            {
                //根据证件号码获取用户信息
                $UserInfo = $this->oUser->getUserByColumn("IdNo",$IdNo);
                //如果已经被占用
                if(isset($UserInfo['UserId']))
                {
                    //如果关联比赛用户
                    if($UserInfo['RaceUserId']>0)
                    {
                        //根据证件号码获取比赛用户信息
                        $RaceUserInfo = $this->oUser->getRaceUser($UserInfo['RaceUserId']);
                        //返回用户信息
                        $result = array("return" => 1,"RaceUserInfo"=>$RaceUserInfo);
                    }
                    else
                    {
                        //根据用户创建比赛用户
                        $RaceUserId = $this->oUser->createRaceUserByUserInfo($UserInfo['UserId']);
                        //如果创建成功
                        if($RaceUserId)
                        {
                            //根据证件号码获取比赛用户信息
                            $RaceUserInfo = $this->oUser->getRaceUser($RaceUserId);
                            //返回用户信息
                            $result = array("return" => 1,"UserInfo"=>$UserInfo);
                        }
                        else
                        {
                            $result = array("return" => 0,"comment"=>"用户数据错误");
                        }
                    }

                }
                else
                {
                    //根据证件号码获取比赛用户信息
                    $RaceUserInfo = $this->oUser->getRaceUserByColumn("IdNo",$IdNo);
                    //如果已经被占用
                    if(isset($RaceUserInfo['RaceUserId']))
                    {
                        //返回用户信息
                        $result = array("return" => 1,"RaceUserInfo"=>$RaceUserInfo);
                    }
                    else
                    {
                        //如果姓名长度不足
                        if(strlen($Name) <=2)
                        {
                            //返回错误
                            $result = array("return" => 0,"comment"=>"请输入合法的姓名");
                        }
                        else
                        {
                            //生成用户信息
                            $UserInfo = array('CreateUserId'=>$TokenInfo['UserId'],'Name'=>$Name,'Sex'=>$Sex,'ContactMobile'=>$ContactMobile,'IdNo'=>$IdNo,'IdType'=>$IdType,'Available'=>0,'RegTime'=>date("Y-m-d H:i:s",time()));
                            //创建用户
                            $CreateUser = $this->oUser->insertRaceUser($UserInfo);
                            //如果创建成功
                            if($CreateUser)
                            {
                                //强制获取用户信息
                                $UserInfo = $this->oUser->getRaceUser($CreateUser,"*");
                                //返回用户信息
                                $result = array("return" => 1,"UserInfo"=>$UserInfo);
                            }
                            else
                            {
                                //返回错误
                                $result = array("return" => 0,"comment"=>"创建失败");
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }

        echo json_encode($result);
    }
    //更新用户的身份信息（身份证，姓名）
    public function updateUserIdentityAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获取证件类型列表
            $IdTypeList = $this->oUser->getAuthIdType();
            //获取证件类型列表
            $SexList = $this->oUser->getSexList();
            //用户姓名
            $Name = trim($this->request->Name);
            //证件号码
            $IdNo = trim($this->request->IdNo);
            //证件类型
            $IdType = abs(intval($this->request->IdType));
            //如果不在证件类型范围内，默认为身份证
            $IdType = isset($IdTypeList[$IdType])?$IdType:1;
            //证件类型
            $Sex = abs(intval($this->request->Sex));
            //如果不在证件类型范围内，默认为身份证
            $Sex = isset($SexList[$Sex])?$Sex:0;
            //如果证件号码长度不足
            if(strlen($IdNo) <=6)
            {
                //返回错误
                $result = array("return" => 0,"comment"=>"请输入合法的证件号");
            }
            else
            {
                //根据证件号码获取用户信息
                $IdUserInfo = $this->oUser->getUserByColumn("IdNo",$IdNo);
                //如果已经被占用 且不是该用户本人
                if(isset($IdUserInfo['UserId']) && ($IdUserInfo['UserId']!=$TokenInfo['UserId']))
                {
                    //返回错误
                    $result = array("return" => 0,"commento"=>"证件号码已经被其他用户使用");
                }
                else
                {
                    //如果姓名长度不足
                    if(strlen($Name) <=2)
                    {
                        //返回错误
                        $result = array("return" => 0,"comment"=>"请输入合法的姓名");
                    }
                    else
                    {
                        //生成用户信息
                        $UserInfo = array('Name'=>$Name,'Sex'=>$Sex,'IdNo'=>$IdNo,'IdType'=>$IdType);
                        if($IdType==1)
                        {
                            $UserInfo['Birthday'] = substr($IdNo,6,4)."-".substr($IdNo,10,2)."-".substr($IdNo,12,2);
                            $UserInfo['Sex'] = $Sex==0?$Sex:(intval(substr($IdNo,16,1))%2==0?2:1);
                        }
                        //更新用户
                        $UpdateUser = $this->oUser->updateUser($TokenInfo['UserId'],$UserInfo);
                        //如果创建成功
                        if($UpdateUser)
                        {
                            //强制获取用户信息
                            $UserInfo = $this->oUser->getUserInfo($TokenInfo['UserId'],"*",0);
                            //返回用户信息
                            $result = array("return" => 1,"UserInfo"=>$UserInfo);
                        }
                        else
                        {
                            //返回错误
                            $result = array("return" => 0,"comment"=>"更新失败");
                        }
                    }
                }
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    //更新用户的身份信息（身份证，姓名）
    public function updateUserIceAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //紧急联系人姓名
            $Name = trim($this->request->Name);
            //证件号码
            $ContactMobile = trim($this->request->ContactMobile);
            //如果紧急联系人姓名和手机长度不足
            if((strlen($Name) <=2) || (strlen($ContactMobile) <=8))
            {
                //返回错误
                $result = array("return" => 0,"comment"=>"请输入合法紧急联系人姓名和联系方式");
            }
            else
            {
                //生成用户信息
                $UserInfo = array("ICE"=>json_encode(array("1"=>array('Name'=>$Name,'ContactMobile'=>$ContactMobile))));
                //更新用户
                $UpdateUser = $this->oUser->updateUser($TokenInfo['UserId'],$UserInfo);
                //如果创建成功
                if($UpdateUser)
                {
                    //强制获取用户信息
                    $UserInfo = $this->oUser->getUserInfo($TokenInfo['UserId'],"*",0);
                    //返回用户信息
                    $result = array("return" => 1,"UserInfo"=>$UserInfo);
                }
                else
                {
                    //返回错误
                    $result = array("return" => 0,"comment"=>"更新失败");
                }
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    //更新用户的身份信息（身份证，姓名）
    public function getCodeAction()
    {
        $RegId = isset($this->request->RegId) ? abs(intval($this->request->RegId)) :0;
        //获取注册记录
        $RegInfo = $this->oUser->getRegInfo($RegId);
        echo "here is the RegInfo";
        echo "<pre>";
        print_R($RegInfo);

    }
    //更新用户的身份信息（身份证，姓名）
    public function testDeleteAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) :"";
        $RegInfo = $this->oUser->getRegInfoByMobile($Mobile);
        if($RegInfo['RegId'])
        {
            $deleteRegInfo = $this->oUser->deleteRegInfo($RegInfo['RegId']);
        }
        $delete = $this->oUser->deleteUserByMobile($Mobile);
        echo "deleteReg:".$deleteRegInfo."<br>";
        echo "deleteUser:".$delete."<br>";

    }
    public function resetRegSmsByMobileAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) :"";
        $Reset = $this->oUser->resetRegSmsByMobile($Mobile);
        if($Reset)
        {
            $result = array("return" => 1,"comment"=>"重发成功");
        }
        else
        {
            $result = array("return" => 0,"comment"=>"发送失败，请重试");
        }
        echo json_encode($result);
    }
    public function resetRegSmsByRegAction()
    {
        $RegId = isset($this->request->RegId) ? abs(intval($this->request->RegId)) :0;
        $Reset = $this->oUser->resetRegSmsByReg($RegId);
        if($Reset)
        {
            $result = array("return" => 1,"comment"=>"重发成功");
        }
        else
        {
            $result = array("return" => 0,"comment"=>"发送失败，请重试");
        }
        echo json_encode($result);
    }
    public function checkMobileExistAction()
    {
        $Mobile = isset($this->request->Mobile) ? trim($this->request->Mobile) :"";
        $Check = $this->oUser->checkMobileExist($Mobile);
        print_R($Check);
        if($Check['Available']==1)
        {
            if(isset($Check['UserInfo']['UserId']))
            {
                $result = array("return" => 1,"UserInfo"=>$Check['UserInfo'],"comment"=>"可以继续注册");
            }
            else
            {
                $result = array("return" => 1,"comment"=>"可以继续注册");
            }
        }
        else
        {
            $result = array("return" => 0,"comment"=>"手机号码已经被占用");
        }
        echo json_encode($result);
    }
    public function updateRegPasswordAction()
    {
        $RegId = isset($this->request->RegId) ? abs(intval($this->request->RegId)) :0;
        $Password = isset($this->request->Password) ? trim($this->request->Password) :"";
        //获取注册记录
        $RegInfo = $this->oUser->getRegInfo($RegId);
        //如果获取到注册记录
        if(isset($RegInfo['RegId']))
        {
            //如果密码为空 且 是手机注册
            if(($RegInfo['Password'] == "") && ($RegInfo['RegPlatform'] == "Mobile"))
            {
                $NewRegInfo = array("Password" =>md5($Password));
                $Update = $this->oUser->updateRegInfo($RegInfo['RegId'],$NewRegInfo);
                if($Update)
                {
                    $result = array("return" => 1,"comment"=>"更新成功");
                }
                else
                {
                    $result = array("return" => 0,"comment"=>"更新失败");
                }
            }
            else
            {
                $result = array("return" => 0,"comment"=>"密码已存在，禁止更新");
            }
        }
        else
        {
            $result = array("return" => 0,"comment"=>"无此记录");
        }

        echo json_encode($result);
    }
}