<?php
/**
 * 用户数据相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_UserInfo extends Base_Widget
{
	//声明所用到的表
    protected $table = 'UserInfo';
    protected $table_reg_info = 'UserReg';
    protected $table_race = 'user_race';

    //性别列表
	protected $sex = array('0'=>"保密",'1'=>"男",'2'=>"女");
    //性别列表
    protected $raceApplySourceList = array('0'=>"未知",'1'=>"线上","2"=>"线下");
    //实名认证状态
    protected $authStatuslist = array('0'=>"未认证",'1'=>"待认证",'2'=>"已认证");
    //提交时对应的实名认证状态名
    protected $authStatusListSubmit = array('0'=>"不通过",'2'=>"审核通过");
    //认证记录中对应的实名认证状态名
    protected $authStatus_log = array('0'=>"拒绝","2"=>"通过");
    //实名认证用到的证件类型列表
    protected $authIdType = array('1'=>"身份证","2"=>"护照");
    //用户执照状态
    protected $user_license_status = array('1'=>"生效中",'2'=>"已过期",'3'=>"即将生效",'4'=>"已删除");
    //用户签到状态
    protected $user_checkin_status = array('0'=>'全部','2'=>"未签到",'1'=>"已签到");
    //用户签到短信发送状态
    protected $user_checkin_sms_sent_status = array('3'=>"不需发送",'1'=>"待发送",'2'=>"已发送");
        //获取性别列表
	public function getSexList()
	{
		return $this->sex;
	}
    //获取实名认证状态
    public function getAuthStatus($type = "display")
    {
        if($type=="display")
        {
            return $this->authStatuslist;
        }
        else
        {
            return $this->authStatusListSubmit;
        }
    }
    //获取实名认证的证件列表
    public function getAuthIdType()
    {
        return $this->authIdType;
    }
    //获取实名认证的记录的状态列表
    public function getAuthLogStatusTypeList()
    {
        return $this->auth_status_log;
    }
    //获得用户执照状态
    public function getUserLicenseStatusList()
    {
        return $this->user_license_status;
    }
    //获得用户签到状态列表
    public function getUserCheckInStatus()
    {
        return $this->user_checkin_status;
    }
    //获得用户签到短信发送状态列表
    public function getUserCheckInSmsSentStatus()
    {
        return $this->user_checkin_sms_sent_status;
    }
    //获得用户签到短信发送状态列表
    public function getRaceApplySourceList()
    {
        return $this->raceApplySourceList;
    }
    /**
     * 新增单个用户注册中间记录
     * @param array $bind 所要添加的数据列
     * @return boolean
     */
    public function insertRegInfo($bind)
    {
        $table_to_process = Base_Widget::getDbTable($this->table_reg_info);
        return $this->db->insert($table_to_process, $bind);
    }
    /**
     * 更新单个用户注册中间记录
     * @param string $RegId 注册记录ID
     * @param array $bind 所要更新的数据列
     * @return boolean
     */
    public function updateRegInfo($RegId,$bind)
    {
        $RegId = intval($RegId);
        $table_to_process = Base_Widget::getDbTable($this->table_reg_info);
        return $this->db->update($table_to_process, $bind, '`RegId` = ?', $RegId);
    }
    /**
     * 获取单个用户注册中间记录
     * @param string $RegId 注册记录ID
     * @return array
     */
    public function getRegInfo($RegId)
    {
        $RegId = intval($RegId);
        $table_to_process = Base_Widget::getDbTable($this->table_reg_info);
        return $this->db->selectRow($table_to_process, "*", '`RegId` = ?', $RegId);
    }
    /**
     * 通过手机和短信验证码获取单个用户注册中间记录
     * @param string $Mobile 用户手机号码
     * @param string $Code 短信验证码
     * @return array
     */
    public function getRegInfoByBobile($Mobile,$ValidateCode)
    {
        $Mobile = trim($Mobile);
        $ValidateCode = trim($ValidateCode);
        $table_to_process = Base_Widget::getDbTable($this->table_reg_info);
        return $this->db->selectRow($table_to_process, '*', '`Mobile` = ? and `ValidateCode` = ?', array($Mobile,$ValidateCode));
    }
    /**
    /**
     * 获取单个用户记录
     * @param char $UserId 用户ID
     * @param string $fields 所要获取的数据列
     * @return array
     */
    public function getUserInfo($UserId, $fields = '*',$Cache=1)
    {
        $oMemCache = new Base_Cache_Memcache("xrace");
        //获取缓存
        if($Cache == 1)
        {
            //获取缓存
            $m = $oMemCache->get("UserInfo_".$UserId);
            //缓存解开
            $UserInfo = json_decode($m,true);
            //如果结果集不有效
            if(!isset($UserInfo['UserId']))
            {
                //缓存置为0
                $Cache = 0;
            }
            else
            {
                //echo "UserInfo cahced";
            }
        }
        if($Cache == 0)
        {
            //从数据库中获取
            $UserInfo = $this->getUser($UserId, "*");
            //如果结果集有效
            if(isset($UserInfo['UserId']))
            {
                //写入缓存
                $oMemCache -> set("UserInfo_".$UserId,json_encode($UserInfo),86400);
            }
        }
        //如果结果集有效，并且获取的字段列表不是全部
        if(isset($UserInfo['UserId']) && $fields != "*")
        {
            //分解字段列表
            $fieldsList = explode(",",$fields);
            //循环结果集
            foreach($UserInfo as $key => $value)
            {
                //如果不在字段列表中且不是主键
                if(!in_array($key,$fieldsList) && $key != "UserId")
                {
                    //删除
                    unset($UserInfo[$key]);
                }
            }
        }
        //返回结果
        return $UserInfo;
    }
	/**
	 * 获取单个用户记录
	 * @param char $UserId 用户ID
	 * @param string $fields 所要获取的数据列
	 * @return array
	 */
	public function getUser($UserId, $fields = '*')
	{
		$UserId = intval($UserId);
		$table_to_process = Base_Widget::getDbTable($this->table);
		return $this->db->selectRow($table_to_process, $fields, '`UserId` = ?', $UserId);
	}
    /**
     * 新增单个用户记录
     * @param array $bind 所要添加的数据列
     * @return boolean
     */
    public function insertUser($bind)
    {
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->insert($table_to_process, $bind);
    }
    /**
     * 更新单个用户记录
     * @param char $UserId 用户ID
     * @param array $bind 更新的数据列表
     * @return boolean
     */
    public function updateUser($UserId, array $bind)
    {
        $UserId = trim($UserId);
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->update($table_to_process, $bind, '`UserId` = ?', $UserId);
    }
    /**
     * 根据指定字段获取单个用户记录
     * @param char $UserId 用户ID
     * @param string $fields 所要获取的数据列
     * @return array
     */
    public function getUserByColumn($Column,$Value, $fields = '*')
    {
        $table_to_process = Base_Widget::getDbTable($this->table);
        return $this->db->selectRow($table_to_process, $fields, '`'.$Column.'` = ?', $Value);
    }
    /**
     * 获取用户列表
     * @param $fields  所要获取的数据列
     * @param $params 传入的条件列表
     * @return array
     */
    public function getUserList($params,$fields = array("*"))
    {
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        //性别判断
        $whereSex = isset($this->sex[$params['Sex']])?" Sex = '".$params['Sex']."' ":"";
        //实名认证判断
        $whereAuth = isset($this->authStatuslist[$params['AuthStatus']])?" AuthStatus = ".$params['AuthStatus']." ":"";
        //姓名
        $whereName = (isset($params['Name']) && trim($params['Name']))?" name like '%".$params['Name']."%' ":"";
        //昵称
        $whereNickName = (isset($params['NickName']) && trim($params['NickName']))?" NickName like '%".$params['NickName']."%' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereSex,$whereName,$whereNickName,$whereAuth);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        //获取用户数量
        if(isset($params['getCount'])&&$params['getCount']==1)
        {
            $UserCount = $this->getUserCount($params);
        }
        else
        {
            $UserCount = 0;
        }
        $limit  = isset($params['Page'])&&$params['Page']?" limit ".($params['Page']-1)*$params['PageSize'].",".$params['PageSize']." ":"";
        $order = " ORDER BY RegTime desc";
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." ".$order." ".$limit;
        $return = $this->db->getAll($sql);
        $UserList = array('UserList'=>array(),'UserCount'=>$UserCount);
        if(count($return))
        {
            foreach($return as $key => $value)
            {
                $UserList['UserList'][$value['UserId']] = $value;
            }
        }
        else
        {
            return $UserList;
        }
        return $UserList;
    }
    /**
     * 获取用户数量
     * @param $fields  所要获取的数据列
     * @param $params 传入的条件列表
     * @return integer
     */
    public function getUserCount($params)
    {
        //生成查询列
        $fields = Base_common::getSqlFields(array("UserCount"=>"count(UserId)"));

        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        //性别判断
        $whereSex = isset($this->sex[$params['Sex']])?" Sex = '".$params['Sex']."' ":"";
        //实名认证判断
        $whereAuth = isset($this->authStatuslist[$params['AuthStatus']])?" AuthStatus = ".$params['AuthStatus']." ":"";
        //姓名
        $whereName = (isset($params['Name']) && trim($params['Name']))?" name like '%".$params['Name']."%' ":"";
        //昵称
        $whereNickName = (isset($params['NickName']) && trim($params['NickName']))?" NickName like '%".$params['NickName']."%' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereSex,$whereName,$whereNickName,$whereAuth);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);

        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where;
        return $this->db->getOne($sql);
    }
    /**
     * 登录
     * @param string $Mobile 用户手机号码
     * @param string $Password 已经经过一次MD5的用户密码
     * @return array
     */
    public function Login($Mobile,$Password)
    {
        echo "Mobile:".$Mobile;
        $oMemCache = new Base_Cache_Memcache("xrace");
        //获取缓存
        $m = $oMemCache->get("Mobile_".$Mobile);
        //如果获取到的数据为0
        if(intval($m)==0)
        {
            //根据手机号码查询用户
            $UserInfo = $this->getUserByColumn("Mobile",$Mobile);
            //如果查询到
            if(isset($UserInfo['UserId']))
            {
                //写入缓存
                $oMemCache -> set("Mobile_".$Mobile,$UserInfo['UserId'],86400);
                return $UserInfo;
            }
            else
            {
                $ValidateCode = sprintf("%06d",rand(1,999999));
                $Time = time();
                //创建用户
                $UserRegInfo = array("RegPlatform"=>"Mobile","RegKey"=>$Mobile,"RegTime"=>date("Y-m-d H:i:s",$Time),"ValidateCode"=>$ValidateCode,'ExceedTime'=>date("Y-m-d H:i:s",$Time+3600));
                $RegId = $this->insertRegInfo($UserRegInfo);
            }
        }
        else
        {
            echo "cached";
            //根据手机号码查询用户
            $UserInfo = $this->getUserByColumn("Mobile",$Mobile);
            //如果查询到
            if(isset($UserInfo['UserId']))
            {
                //缓存检查
                return $UserInfo;
            }
            else
            {
                //创建用户
            }
        }
    }
    /**
     * 第三方登录
     * @param array $LoginData 登陆用的数据集
     * @param string $LoginSource 第三方登录的来源
     * @return array
     */
    public function ThirdPartyLogin($LoginData,$LoginSource)
    {
        $oMemCache = new Base_Cache_Memcache("xrace");
        switch ($LoginSource)
        {
            case "WeChat":
                if(isset($LoginData['openid']))
                {
                    //获取缓存
                    $m = $oMemCache->get("ThirdParty_".$LoginSource."_".$LoginData['openid']);
                    //缓存数据解包
                    $m = json_decode($m,true);
                    //如果获取到的数据为0
                    if(intval($m['UserId'])==0)
                    {
                        //根据第三方平台ID查询用户
                        $UserInfo = $this->getUserByColumn("WeChatId",$LoginData['openid'],"UserId,WeChatInfo");
                        //如果查询到
                        if(isset($UserInfo['UserId']))
                        {
                            //微信数据解包
                            $UserInfo['WeChatInfo'] = json_decode($UserInfo['WeChatInfo'],true);
                            //用户数据比对
                            if(($UserInfo['WeChatInfo']['UserImg'] = $LoginData['headimgurl']) && ($UserInfo['WeChatInfo']['NickName'] = $LoginData['nickname']))
                            {
                                echo "UserInfo Cached";
                            }
                            else
                            {
                                //更新用户数据
                                $WeChatInfo = array('NickName' => $LoginData['nickname'],'UserImg' => $LoginData['headimgurl']);
                                $UserInfoUpdate = array('WeChatInfo' => json_encode($WeChatInfo));
                                $this->updateUser($UserInfo['UserId'], $UserInfoUpdate);
                            }
                            //写缓存
                            $UserInfoCache = array("UserId"=>$UserInfo['UserId'],'NickName'=>$LoginData['nickname'],'UserImg'=>$LoginData['headimgurl']);
                            $oMemCache->set("ThirdParty_".$LoginSource."_".$LoginData['openid'],json_encode($UserInfoCache),86400);
                            return $UserInfoCache;
                        }
                        else
                        {
                            //获取当前时间
                            $Time = time();
                            //创建用户注册记录
                            $UserRegInfo = array("RegPlatform"=>"WeChat","RegKey"=>$LoginData['openid'],"RegTime"=>date("Y-m-d H:i:s",$Time),"ValidateCode"=>"");
                            $RegId = $this->insertRegInfo($UserRegInfo);
                            //如果写入成功
                            if($RegId)
                            {
                                //通知前端需要进一步获取手机
                                return array('RegId'=>$RegId,'NeedMobile'=>1);
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                    else//没拿到缓存
                    {
                        //获取用户信息
                        $UserInfo = $this->getUserInfo($m['UserId']);
                        if(isset($UserInfo['UserId']))
                        {
                            //微信数据解包
                            $UserInfo['WeChatInfo'] = json_decode($UserInfo['WeChatInfo'],true);
                            //用户数据比对
                            if(($UserInfo['WeChatInfo']['UserImg'] = $LoginData['headimgurl']) && ($UserInfo['WeChatInfo']['NickName'] = $LoginData['nickname']))
                            {
                                //echo "UserInfo Cached";
                            }
                            else
                            {
                                //更新用户数据
                                $WeChatInfo = array('NickName' => $LoginData['nickname'],'UserImg' => $LoginData['headimgurl']);
                                $UserInfoUpdate = array('WeChatInfo' => json_encode($WeChatInfo));
                                $this->updateUser($UserInfo['UserId'], $UserInfoUpdate);
                            }
                            //写缓存
                            $UserInfoCache = array("UserId"=>$UserInfo['UserId'],'NickName'=>$LoginData['nickname'],'UserImg'=>$LoginData['headimgurl']);
                            $oMemCache->set("ThirdParty_".$LoginSource."_".$LoginData['openid'],json_encode($UserInfoCache),86400);
                            return $UserInfoCache;
                        }
                        else
                        {
                            //获取当前时间
                            $Time = time();
                            //创建用户注册记录
                            $UserRegInfo = array("RegPlatform"=>"WeChat","RegKey"=>$LoginData['openid'],"RegTime"=>date("Y-m-d H:i:s",$Time),"ValidateCode"=>"");
                            $RegId = $this->insertRegInfo($UserRegInfo);
                            //如果写入成功
                            if($RegId)
                            {
                                //通知前端需要进一步获取手机
                                return array('RegId'=>$RegId,'NeedMobile'=>1);
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                }
                else
                {
                    return false;
                }
            case "Weibo":
                if(isset($LoginData['openid']))
                {
                    //获取缓存
                    $m = $oMemCache->get("ThirdParty_".$LoginSource."_".$LoginData['UID']);
                    //缓存数据解包
                    $m = json_decode($m,true);
                    //如果获取到的数据为0
                    if(intval($m['UserId'])==0)
                    {
                        //根据第三方平台ID查询用户
                        $UserInfo = $this->getUserByColumn("WeiboId",$LoginData['openid'],"UserId,WeiboInfo");
                        //如果查询到
                        if(isset($UserInfo['UserId']))
                        {
                            //微信数据解包
                            $UserInfo['WeiboInfo'] = json_decode($UserInfo['WeiboInfo'],true);
                            //用户数据比对
                            if(($UserInfo['WeiboInfo']['UserImg'] = $LoginData['headimgurl']) && ($UserInfo['WeiboInfo']['NickName'] = $LoginData['nickname']))
                            {
                                echo "UserInfo Cached";
                            }
                            else
                            {
                                //更新用户数据
                                $WeChatInfo = array('NickName' => $LoginData['nickname'],'UserImg' => $LoginData['headimgurl']);
                                $UserInfoUpdate = array('WeiboInfo' => json_encode($WeChatInfo));
                                $this->updateUser($UserInfo['UserId'], $UserInfoUpdate);
                            }
                            //写缓存
                            $UserInfoCache = array("UserId"=>$UserInfo['UserId'],'NickName'=>$LoginData['nickname'],'UserImg'=>$LoginData['headimgurl']);
                            $oMemCache->set("ThirdParty_".$LoginSource."_".$LoginData['UID'],json_encode($UserInfoCache),86400);
                            return $UserInfoCache;
                        }
                        else
                        {
                            //获取当前时间
                            $Time = time();
                            //创建用户注册记录
                            $UserRegInfo = array("RegPlatform"=>"Weibo","RegKey"=>$LoginData['UID'],"RegTime"=>date("Y-m-d H:i:s",$Time),"ValidateCode"=>"");
                            $RegId = $this->insertRegInfo($UserRegInfo);
                            //如果写入成功
                            if($RegId)
                            {
                                //通知前端需要进一步获取手机
                                return array('RegId'=>$RegId,'NeedMobile'=>1);
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                    else//没拿到缓存
                    {
                        //获取用户信息
                        $UserInfo = $this->getUserInfo($m['UserId']);
                        if(isset($UserInfo['UserId']))
                        {
                            //微信数据解包
                            $UserInfo['WeiboInfo'] = json_decode($UserInfo['WeChatInfo'],true);
                            //用户数据比对
                            if(($UserInfo['WeiboInfo']['UserImg'] = $LoginData['headimgurl']) && ($UserInfo['WeiboInfo']['NickName'] = $LoginData['nickname']))
                            {
                                //echo "UserInfo Cached";
                            }
                            else
                            {
                                //更新用户数据
                                $WeChatInfo = array('NickName' => $LoginData['nickname'],'UserImg' => $LoginData['headimgurl']);
                                $UserInfoUpdate = array('WeiboInfo' => json_encode($WeChatInfo));
                                $this->updateUser($UserInfo['UserId'], $UserInfoUpdate);
                            }
                            //写缓存
                            $UserInfoCache = array("UserId"=>$UserInfo['UserId'],'NickName'=>$LoginData['nickname'],'UserImg'=>$LoginData['headimgurl']);
                            $oMemCache->set("ThirdParty_".$LoginSource."_".$LoginData['UID'],json_encode($UserInfoCache),86400);
                            return $UserInfoCache;
                        }
                        else
                        {
                            //获取当前时间
                            $Time = time();
                            //创建用户注册记录
                            $UserRegInfo = array("RegPlatform"=>"Weibo","RegKey"=>$LoginData['UID'],"RegTime"=>date("Y-m-d H:i:s",$Time),"ValidateCode"=>"");
                            $RegId = $this->insertRegInfo($UserRegInfo);
                            //如果写入成功
                            if($RegId)
                            {
                                //通知前端需要进一步获取手机
                                return array('RegId'=>$RegId,'NeedMobile'=>1);
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                }
                else
                {
                    return false;
                }
        }
    }
    /**
     * 第三方登录时绑定手机
     * @param string $Mobile 用户手机号码
     * @param string $RegId 注册ID
     * @return array
     */
    public function thirdPartyRegMobile($RegId,$Mobile)
    {
        //获取注册记录
        $RegInfo = $this->getRegInfo($RegId);
        //如果找到记录
        if(isset($RegInfo['RegId']))
        {
            //如果在有效期内
            if(strtotime($RegInfo['ExceedTime'])>=time())
            {
                //验证通过，注册
                $UserInfo = array('Mobile'=>$Mobile,'RegTime'=>$RegInfo['RegTime'],'LastLoginTime'=>date("Y-m-d H:i:s"));
                if($RegInfo['RegPlatform'] == "WeChat")
                {
                    $UserInfo['WeChatId'] = $RegInfo['RegPlatform'];
                }
                elseif($RegInfo['RegPlatform'] == "Weibo")
                {
                    $UserInfo['WeiboId'] = $RegInfo['RegPlatform'];
                }
                elseif($RegInfo['RegPlatform'] == "QQ")
                {
                    $UserInfo['QQ'] = $RegInfo['RegPlatform'];
                }
                elseif($RegInfo['RegPlatform'] == "Mobile")
                {
                    //手机注册只保留密码
                    $UserInfo['Password'] = md5($RegInfo['Password']);
                }
                //创建用户
                $UserId = $this->insertUser($UserInfo);
                return $UserId;
            }
            else
            {
                //更新注册记录，生成新的验证码和过期时间
                $bind = array('ExceedTime'=>date("Y-m-d H:i:s",time()+3600),'ValidateCode' => sprintf("%06d",rand(1,999999)));
                $Update = $this->updateRegInfo($RegInfo['RegId'],$bind);
                //更新成功
                if($Update)
                {
                    return -1;
                }
                else
                {
                    return 0;
                }
            }
        }
        else
        {
            return 0;
        }
    }
    /**
     * 注册时的短信验证码校验
     * @param string $Mobile 用户手机号码
     * @param string $ValidateCode 短信验证码
     * @return array
     */
    public function regMobileAuth($Mobile,$ValidateCode)
    {
        //获取注册记录
        $RegInfo = $this->getRegInfoByBobile($Mobile,$ValidateCode);
        //如果找到记录
        if(isset($RegInfo['RegId']))
        {
            //如果在有效期内
            if(strtotime($RegInfo['ExceedTime'])>=time())
            {
                //验证通过，注册
                $UserInfo = array('Mobile'=>$Mobile,'RegTime'=>$RegInfo['RegTime'],'LastLoginTime'=>date("Y-m-d H:i:s"));
                if($RegInfo['RegPlatform'] == "WeChat")
                {
                    $UserInfo['WeChatId'] = $RegInfo['RegPlatform'];
                }
                elseif($RegInfo['RegPlatform'] == "Weibo")
                {
                    $UserInfo['WeiboId'] = $RegInfo['RegPlatform'];
                }
                elseif($RegInfo['RegPlatform'] == "QQ")
                {
                    $UserInfo['QQ'] = $RegInfo['RegPlatform'];
                }
                elseif($RegInfo['RegPlatform'] == "Mobile")
                {
                    //手机注册只保留密码
                    $UserInfo['Password'] = md5($RegInfo['Password']);
                }
                //创建用户
                $UserId = $this->insertUser($UserInfo);
                return $UserId;
            }
            else
            {
                //更新注册记录，生成新的验证码和过期时间
                $bind = array('ExceedTime'=>date("Y-m-d H:i:s",time()+3600),'ValidateCode' => sprintf("%06d",rand(1,999999)));
                $Update = $this->updateRegInfo($RegInfo['RegId'],$bind);
                //更新成功
                if($Update)
                {
                    return -1;
                }
                else
                {
                    return 0;
                }
            }
        }
        else
        {
            return 0;
        }
    }
    //获取报名记录
    public function getRaceUserList($params,$fields = array('*'))
    {
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table_race);
        //获得比赛ID
        $whereStage = isset($params['RaceStageId'])?" RaceStageId = '".$params['RaceStageId']."' ":"";
        //获得比赛ID
        $whereRace = isset($params['RaceId'])?" RaceId = '".$params['RaceId']."' ":"";
        //获得用户ID
        $whereUser = (isset($params['UserId']) && $params['UserId']!="0")?" UserId = '".$params['UserId']."' ":"";
        //获得组别ID
        $whereGroup = (isset($params['RaceGroupId'])  && $params['RaceGroupId']!=0)?" RaceGroupId = '".$params['RaceGroupId']."' ":"";
        //获得赛事ID
        $whereCatalog = isset($params['RaceCatalogId'])?" RaceCatalogId = '".$params['RaceCatalogId']."' ":"";
        //所有查询条件置入数组
        $whereCondition = array($whereCatalog,$whereUser,$whereGroup,$whereRace,$whereStage);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by BIB,RaceTeamId,ApplyId desc";
        $return = $this->db->getAll($sql);
        return $return;
    }
    //获取某场比赛的报名名单
    public function getRaceUserListByRace($RaceId,$RaceGroupId,$TeamId=0,$Cache = 1)
    {
        $oMemCache = new Base_Cache_Memcache("xrace");
        //如果需要获取缓存
        if($Cache == 1)
        {
            //获取缓存
            $m = $oMemCache->get("RaceUserList_".$RaceId);
            //缓存解开
            $RaceUserList = json_decode($m,true);
            //如果数据为空
            if(count($RaceUserList['RaceUserList'])==0)
            {
                //需要从数据库获取
                $NeedDB = 1;
            }
            else
            {
                //echo "cached";
            }
        }
        else
        {
            //需要从数据库获取
            $NeedDB = 1;
        }
        if(isset($NeedDB))
        {
            //生成查询条件
            $params = array('RaceId'=>$RaceId,'RaceGroupId'=>$RaceGroupId);
            //获取选手名单
            $UserList = $this->getRaceUserList($params);
            //初始化空的返回值列表
            $RaceTeamList = array('RaceUserList'=>array(),'RaceTeamList'=>array());
            //如果获取到选手名单
            if(count($UserList))
            {
                $oTeam = new Xrace_Team();
                $oRace = new Xrace_Race();
                $RaceApplySourceList = $this->getRaceApplySourceList();
                //初始化空的分组列表
                $RaceGroupList = array();
                foreach($UserList as $ApplyId => $ApplyInfo)
                {
                    //获取用户信息
                    $UserInfo = $this->getUser( $ApplyInfo["UserId"],'UserId,Name');
                    //如果获取到用户
                    if($UserInfo['UserId'])
                    {
                        //存储报名数据
                        $RaceUserList['RaceUserList'][$ApplyId] = $ApplyInfo;
                        //如果列表中没有分组信息
                        if(!isset($RaceGroupList[$ApplyInfo['RaceGroupId']]))
                        {
                            //获取分组信息
                            $RaceGroupInfo = $oRace->getRaceGroup($ApplyInfo['RaceGroupId'],"RaceGroupId,RaceGroupName");
                            //如果合法则保存
                            if(isset($RaceGroupInfo['RaceGroupId']))
                            {
                                $RaceGroupList[$ApplyInfo['RaceGroupId']] = $RaceGroupInfo;
                            }
                        }
                        //保存分组信息
                        $RaceUserList['RaceUserList'][$ApplyId]['RaceGroupName'] = $RaceGroupList[$ApplyInfo['RaceGroupId']]['RaceGroupName'];
                        //获取用户名
                        $RaceUserList['RaceUserList'][$ApplyId]['Name'] = $UserInfo['Name'];
                        if(!isset($RaceUserList['RaceTeamList'][$ApplyInfo['RaceTeamId']]))
                        {
                            //队伍信息
                            $RaceTeamInfo = $oTeam->getRaceTeamInfo($ApplyInfo['RaceTeamId'],'team_id as RaceTeamId,name as RaceTeamName');
                            //如果在队伍列表中有获取到队伍信息
                            if(isset($RaceTeamInfo['RaceTeamId']))
                            {
                                $RaceUserList['RaceTeamList'][$ApplyInfo['RaceTeamId']] = $RaceTeamInfo;
                            }
                        }
                        //格式化用户的队伍名称和队伍ID
                        $RaceUserList['RaceUserList'][$ApplyId]['RaceTeamName'] = isset($RaceUserList['RaceTeamList'][$ApplyInfo['RaceTeamId']])?$RaceUserList['RaceTeamList'][$ApplyInfo['RaceTeamId']]['RaceTeamName']:"个人";
                        $RaceUserList['RaceUserList'][$ApplyId]['RaceTeamId'] = isset($RaceUserList['RaceTeamList'][$ApplyInfo['RaceTeamId']])?$ApplyInfo['RaceTeamId']:0;
                        $RaceUserList['RaceUserList'][$ApplyId]['comment'] = json_decode($ApplyInfo['comment'],true);
                        $RaceUserList['RaceUserList'][$ApplyId]['ApplySourceName'] = $RaceApplySourceList[$ApplyInfo['ApplySource']];
                    }
                }
                //如果有获取到最新版本信息
                if(count($RaceUserList['RaceUserList']))
                {
                    //写入缓存
                    $oMemCache -> set("RaceUserList_".$RaceId,json_encode($RaceUserList),86400);
                }
            }
        }
        //如果需要筛选的队伍ID在队伍列表中
        if(isset($RaceUserList['RaceTeamList'][$TeamId]))
        {
            //循环名单
            foreach($RaceUserList['RaceUserList'] as $ApplyId => $ApplyInfo)
            {

                //如果不是想要的队伍
                if($ApplyInfo['RaceTeamId'] != $TeamId)
                {
                    //删除数据
                    unset($RaceUserList['RaceUserList'][$ApplyId]);
                }
            }
        }
        //如果只要个人报名选手
        elseif($TeamId == -1)
        {
            //循环名单
            foreach($RaceUserList['RaceUserList'] as $ApplyId => $ApplyInfo)
            {
                //如果不是想要的队伍
                if($ApplyInfo['RaceTeamId'] != 0)
                {
                    //删除数据
                    unset($RaceUserList['RaceUserList'][$ApplyId]);
                }
            }
        }
        return $RaceUserList;
    }
}