<?php
/**
 *
 * 
 */
class XraceTeamController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oUser;
    protected $oTeam;
    protected $oRace;

    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oUser = new Xrace_UserInfo();
        $this->oTeam = new Xrace_Team();
        $this->oRace = new Xrace_Race();

    }

    /**
     *获取队伍信息(缓存)
     */
    public function getTeamInfoAction()
    {
        //是否调用缓存
        $Cache = isset($this->request->Cache) ? abs(intval($this->request->Cache)) : 1;
        //是否显示说明注释 默认为1
        $TeamId = isset($this->request->TeamId) ? abs(intval($this->request->TeamId)) : 0;
        //获取用户信息
        $TeamInfo = $this->oTeam->getTeamInfo($TeamId,"*",$Cache);
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $result = array("return" => isset($UserInfo['TeamId']) ? 1 : 0, "TeamInfo" => $TeamInfo);
        echo json_encode($result);
    }
    /**
     *登录用户创建队伍
     */
    public function createTeamAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获得用户信息
            $UserInfo = $this->oUser->getUserInfo($TokenInfo['UserId'],"*",0);
            //获取 页面参数
            $bind=$this->request->from('TeamName','TeamComment','IsTemp','RaceStageId');
            //队伍名称不能为空
            if(trim($bind['TeamName'])=="")
            {
                $result = array("return" => 0, "comment" => "队伍名称不能为空");
            }
            //检测队伍说明
            elseif(trim($bind['TeamComment'])=="")
            {
                $result = array("return" => 0, "comment" => "队伍说明不能为空");
            }
            else
            {
                //检测更新用户的信息
                $CreateUserInfo = $this->oUser->getUser($UserInfo['UserId'],"UserId,UserName");
                //如果用户信息合法
                if(isset($CreateUserInfo['UserId']))
                {
                    //保存创建者信息
                    $bind['CreateUserId'] = $CreateUserInfo['UserId'];
                    //获取待更新的队伍信息
                    $TeamInfo = $this->oTeam->getTeamInfoByName($bind['TeamName'],"TeamId,TeamName");
                    //如果获取到
                    if(!isset($TeamInfo['TeamId']))
                    {
                        //如果是临时队伍
                        if($bind['IsTemp']==1)
                        {
                            //获取分站信息
                            $RaceStageInfo = $this->oRace-> getRaceStage($bind['RaceStageId'],"RaceStageId,RaceCatalogId");
                            //如果获取到
                            if(isset($RaceStageInfo['RaceStageId']))
                            {
                                //保存赛事ID
                                $bind['RaceCatalogId'] = $RaceStageInfo['RaceCatalogId'];
                                //创建队伍信息
                                $InsertTeam = $this->oTeam->insertTeam($bind);
                                //创建成功
                                if($InsertTeam)
                                {
                                    //重建缓存
                                    $TeamInfo = $this->oTeam->getTeamInfo($InsertTeam,"*",0);
                                    $result = array("return" => 1,"TeamInfo"=>$TeamInfo,"comment" => "创建成功");
                                }
                                else
                                {
                                    $result = array("return" => 2, "comment" => "创建失败");
                                }
                            }
                            else
                            {
                                $result = array("return" => 1, "comment" => "请指定临时队伍的有效范围");
                            }
                        }
                        else
                        {
                            //删除不必要的信息
                            unset($bind['RaceStageId']);
                            //创建队伍信息
                            $InsertTeam = $this->oTeam->insertTeam($bind);
                            //创建成功
                            if($InsertTeam)
                            {
                                //重建缓存
                                $TeamInfo = $this->oTeam->getTeamInfo($InsertTeam,"*",0);
                                $result = array("return" => 1,"TeamInfo"=>$TeamInfo,"comment" => "创建成功");
                            }
                            else
                            {
                                $result = array("return" => 2, "comment" => "创建失败");
                            }
                        }
                    }
                    else
                    {
                        $result = array("return" => 0, "comment" => "队伍名称已被使用");
                    }
                }
                else
                {
                    $result = array("return" => 0, "comment" => "创建者非法");
                }
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    /**
     *登录用户更新队伍
     */
    public function updateTeamAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获取 页面参数
            $bind=$this->request->from('TeamId','TeamName','TeamComment');
            //检测队伍Id
            if($bind['TeamId'])
            {
                //队伍名称不能为空
                if(trim($bind['TeamName'])=="")
                {
                    $result = array("return" => 0, "comment" => "队伍名称不能为空");
                }
                //检测队伍说明
                elseif(trim($bind['TeamComment'])=="")
                {
                    $result = array("return" => 0, "comment" => "队伍说明不能为空");
                }
                else
                {
                    //检测更新用户的信息
                    $UpdateUserInfo = $this->oUser->getUser($bind['UpdateUserId'],"UserId,UserName");
                    //如果用户信息合法
                    if(isset($UpdateUserInfo['UserId']))
                    {
                        //保存创建者信息
                        $bind['UpdateUserId'] = $UpdateUserInfo['UserId'];
                        //获取待更新的队伍信息
                        $TeamInfo = $this->oTeam->getTeam($bind['TeamId'],"TeamId,TeamName");
                        //如果获取到
                        if(isset($TeamInfo['TeamId']))
                        {
                            //删除无需更新的字段
                            unset($bind['UpdateUserId']);
                            //更新队伍信息
                            $UpdateTeam = $this->oTeam->updateTeam($bind['TeamId'],$bind);
                            //更新成功
                            if($UpdateTeam)
                            {
                                //重建缓存
                                $TeamInfo = $this->oTeam->getTeamInfo($bind['TeamId'],"*",0);
                                $result = array("return" => 1,"TeamInfo"=>$TeamInfo,"comment" => "更新成功");
                            }
                            else
                            {
                                $result = array("return" => 2, "comment" => "更新失败");
                            }
                        }
                        else
                        {
                            $result = array("return" => 0, "comment" => "无此队伍");
                        }

                    }
                    else
                    {
                        $result = array("return" => 0, "comment" => "更新者非法");
                    }
                }
            }
            else
            {
                $result = array("return" => 0, "comment" => "请指定要更新的队伍");
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    /**
     *获取登录用户创建的队伍列表
     */
    public function getCreatedTeamListByTokenAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //分站ID
        $RaceStageId = abs(intval($this->request->RaceStageId));
        //是否临时
        $IsTemp = isset($this->request->IsTemp)?intval($this->request->IsTemp):-1;
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获得用户信息
            $UserInfo = $this->oUser->getUserInfo($TokenInfo['UserId'],"UserId,UserName",0);
            //如果获取到用户
            if(isset($UserInfo['UserId']))
            {
                //获取队伍列表
                $TeamList = $this->oTeam->getUserCreatedTeamList(array("UserId"=>$TokenInfo['UserId'],"RaceStageId"=>$RaceStageId,"IsTemp"=>$IsTemp));
                //循环队伍列表
                foreach($TeamList as $key => $UserTeamInfo)
                {
                    //获取队伍信息
                    $TeamInfo = $this->oTeam->getTeamInfo($UserTeamInfo['TeamId'],"TeamId,IsTemp,TeamName");
                    //如果获取到
                    if(isset($TeamInfo['TeamId']))
                    {
                        //保存队伍信息
                        $TeamList[$key]['TeamInfo'] = $TeamInfo;
                    }
                    else
                    {
                        //删除队伍信息
                        unset($TeamList[$key]);
                    }
                }
                $result = array("return" => 1, "TeamList" => $TeamList);
            }
            else
            {
                $result = array("return" => 0, "comment" => "无此用户");
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    /**
     *获取登录用户参加的队伍列表
     */
    public function getTeamListByTokenAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //获得用户信息
            $UserInfo = $this->oUser->getUserInfo($TokenInfo['UserId'],"UserId,UserName",0);
            //如果获取到用户
            if(isset($UserInfo['UserId']))
            {
                //获取队伍列表
                $TeamList = $this->oTeam->getUserTeamList(array("UserId"=>$TokenInfo['UserId'],"RaceStageId"=>$RaceStageId,"IsTemp"=>$IsTemp));
                //循环队伍列表
                foreach($TeamList as $key => $UserTeamInfo)
                {
                    //获取队伍信息
                    $TeamInfo = $this->oTeam->getTeamInfo($UserTeamInfo['TeamId'],"TeamId,IsTemp,TeamName");
                    //如果获取到
                    if(isset($TeamInfo['TeamId']))
                    {
                        //保存队伍信息
                        $TeamList[$key]['TeamInfo'] = $TeamInfo;
                    }
                    else
                    {
                        //删除队伍信息
                        unset($TeamList[$key]);
                    }
                }
                $result = array("return" => 1, "TeamList" => $TeamList);
            }
            else
            {
                $result = array("return" => 0, "comment" => "无此用户");
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    /**
     *获取用户参与的队伍列表
     */
    public function getTeamListByUserAction()
    {
        //用户ID
        $UserId = isset($this->request->UserId) ? abs(intval($this->request->UserId)) : 0;
        //获得用户信息
        $UserInfo = $this->oUser->getUserInfo($UserId,"UserId,UserName",0);
        //如果获取到用户
        if(isset($UserInfo['UserId']))
        {
            //获取队伍列表
            $TeamList = $this->oTeam->getTeamUserList(array("UserId"=>$UserId));
            //循环队伍列表
            foreach($TeamList as $key => $UserTeamInfo)
            {
                //获取队伍信息
                $TeamInfo = $this->oTeam->getTeamInfo($UserTeamInfo['TeamId'],"TeamId,IsTemp,TeamName");
                //如果获取到
                if(isset($TeamInfo['TeamId']))
                {
                    //保存队伍信息
                    $TeamList[$key]['TeamInfo'] = $TeamInfo;
                }
                else
                {
                    //删除队伍信息
                    unset($TeamList[$key]);
                }
            }
            $result = array("return" => 1, "TeamList" => $TeamList);
        }
        else
        {
            $result = array("return" => 0, "comment" => "无此用户");
        }
        echo json_encode($result);
    }
    /**
     *获取参与队伍的用户列表
     */
    public function getUserListByTeamAction()
    {
        //队伍ID
        $TeamId = isset($this->request->TeamId) ? abs(intval($this->request->TeamId)) : 0;
        //获取队伍信息
        $TeamInfo = $this->oTeam->getTeamInfo($TeamId,"TeamId,IsTemp,TeamName");
        //如果获取到队伍
        if(isset($TeamInfo['TeamId']))
        {
            //获取用户列表
            $UserList = $this->oTeam->getUserTeamList(array("TeamId"=>$TeamId));
            //循环用户列表
            foreach($UserList as $key => $TeamUserInfo)
            {
                //获得用户信息
                $UserInfo = $this->oUser->getUserInfo($TeamUserInfo['UserId'],"UserId,UserName");
                //如果获取到
                if(isset($UserInfo['UserId']))
                {
                    //保存用户信息
                    $UserList[$key]['UserInfo'] = $UserInfo;
                }
                else
                {
                    //删除队伍信息
                    unset($UserList[$key]);
                }
            }
            $result = array("return" => 1, "UserList" => $UserList);
        }
        else
        {
            $result = array("return" => 0, "comment" => "无此队伍");
        }
        echo json_encode($result);
    }
    /**
     *用户加入队伍
     */
    public function joinTeamAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //用户ID  如果未指定则用Token对应的用户
            $UserId = abs(intval($this->request->UserId))>0 ? abs(intval($this->request->UserId)) : $TokenInfo['UserId'];
            //队伍ID
            $TeamId = isset($this->request->TeamId) ? abs(intval($this->request->TeamId)) : 0;
            //获得用户信息
            $UserInfo = $this->oUser->getUserInfo($UserId,"UserId,UserName",0);
            //如果获取到用户
            if(isset($UserInfo['UserId']))
            {
                //获取队伍信息
                $TeamInfo = $this->oTeam->getTeamInfo($TeamId,"TeamId,IsTemp,TeamName,CreateUserId");
                //如果获取到
                if(isset($TeamInfo['TeamId']))
                {
                    //如果登录用户是队伍的创建者 或 指定加入的用户
                    if(($TeamInfo['CreateUserId']==$TokenInfo['UserId']) || ($TokenInfo['UserId']==$UserInfo['UserId']))
                    {
                        //获取用户入队信息
                        $TeamUserInfo = $this->oTeam->getTeamUser($UserId,$TeamId);
                        if(isset($TeamUserInfo['LogId']))
                        {
                            $result = array("return" => 1, "comment" => "加入队伍成功");
                        }
                        else
                        {
                            //加入队伍
                            $Join = $this->oTeam->insertTeamUser(array("UserId"=>$UserId,"TeamId"=>$TeamId));
                            //加入成功
                            if($Join)
                            {
                                $result = array("return" => 1, "comment" => "加入队伍成功");
                            }
                            else
                            {
                                $result = array("return" => 0, "comment" => "加入队伍失败");
                            }
                        }
                    }
                    else
                    {
                        $result = array("return" => 0, "comment" => "只有用户本人或队伍创建者可以执行此操作");
                    }
                }
                else
                {
                    $result = array("return" => 0, "comment" => "无此队伍");
                }
            }
            else
            {
                $result = array("return" => 0, "comment" => "无此用户");
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
    /**
     *用户离开队伍
     */
    public function leaveTeamAction()
    {
        //Token
        $Token = isset($this->request->Token) ? trim($this->request->Token) : "";
        //获取Tokenx信息
        $TokenInfo = $this->oUser->getToken($Token);
        //如果获取到
        if($TokenInfo['UserId'])
        {
            //用户ID  如果未指定则用Token对应的用户
            $UserId = abs(intval($this->request->UserId))>0 ? abs(intval($this->request->UserId)) : $TokenInfo['UserId'];
            //队伍ID
            $TeamId = isset($this->request->TeamId) ? abs(intval($this->request->TeamId)) : 0;
            //获得用户信息
            $UserInfo = $this->oUser->getUserInfo($UserId,"UserId,UserName",0);
            //如果获取到用户
            if(isset($UserInfo['UserId']))
            {
                //获取队伍信息
                $TeamInfo = $this->oTeam->getTeamInfo($TeamId,"TeamId,IsTemp,TeamName");
                //如果获取到
                if(isset($TeamInfo['TeamId']))
                {
                    //如果登录用户是队伍的创建者 或 指定加入的用户
                    if(($TeamInfo['CreateUserId']==$TokenInfo['UserId']) || ($TokenInfo['UserId']==$UserInfo['UserId']))
                    {
                        //获取用户入队信息
                        $TeamUserInfo = $this->oTeam->getTeamUser($UserId,$TeamId);
                        if(!isset($TeamUserInfo['LogId']))
                        {
                            $result = array("return" => 1, "comment" => "离开队伍成功");
                        }
                        else
                        {
                            //离开队伍
                            $Leave = $this->oTeam->deleteTeamUser($UserId,$TeamId);
                            //离开成功
                            if($Leave)
                            {
                                $result = array("return" => 1, "comment" => "离开队伍成功");
                            }
                            else
                            {
                                $result = array("return" => 0, "comment" => "离开队伍失败");
                            }
                        }
                    }
                    else
                    {
                        $result = array("return" => 0, "comment" => "只有用户本人或队伍创建者可以执行此操作");
                    }
                }
                else
                {
                    $result = array("return" => 0, "comment" => "无此队伍");
                }
            }
            else
            {
                $result = array("return" => 0, "comment" => "无此用户");
            }
        }
        else
        {
            $result = array("return" => 0,"NeedLogin"=>1);
        }
        echo json_encode($result);
    }
}