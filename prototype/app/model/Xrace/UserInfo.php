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

	//性别列表
	protected $sex = array('0'=>"保密",'1'=>"男",'2'=>"女");

        //获取性别列表
	public function getSexList()
	{
		return $this->sex;
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
                //echo "cahced";
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
                print_R($UserRegInfo);
                $RegId = $this->insertRegInfo($UserRegInfo);
                echo "RegId:".$RegId;
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
        echo "LoginSource:".$LoginSource;
        $oMemCache = new Base_Cache_Memcache("xrace");
        switch ($LoginSource)
        {
            case "WeChat":
                if(isset($LoginData['openid']))
                {
                    echo "openId:".$LoginData['openid']."<br>";
                    //获取缓存
                    $m = $oMemCache->get("ThirdParty_".$LoginSource."_".$LoginData['openid']);
                    //如果获取到的数据为0
                    if(intval($m)==0)
                    {
                        //根据第三方平台ID查询用户
                        $UserInfo = $this->getUserByColumn("WeChatId",$LoginData['openid']);
                        //如果查询到
                        if(isset($UserInfo['UserId']))
                        {
                            //用户数据比对
                            //写缓存
                        }
                        else
                        {
                            //创建用户
                            //写缓存
                        }
                    }
                    else
                    {
                        $UserInfo = $this->getUserInfo($m);
                        if(isset($UserInfo['UserId']))
                        {
                            //用户数据比对
                            //写缓存
                        }
                        else
                        {
                            //创建用户
                            //写缓存
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
}
