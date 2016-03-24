<?php
/**
 *
 * 
 */
class XraceConfigController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oRace;
    protected $oSports;
    protected $oProduct;
    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oRace = new Xrace_Race();
        $this->oSports = new Xrace_Sports();
        $this->oProduct = new Xrace_Product();
    }
    /**
     *获取所有赛事的列表
     */
    public function getRaceCatalogListAction()
    {
        //获得赛事列表
        $RaceCatalogList = $this->oRace->getRaceCatalogList();
        //如果没有返回值,默认为空数组
        if(!is_array($RaceCatalogList)) 
        {
           $RaceCatalogList = array();
        }
        //循环赛事列表数组
        foreach($RaceCatalogList as $RaceCatalogId => $RaceCatalogInfo)
        {
            //如果有输出赛事图标的绝对路径
            if(isset($RaceCatalogInfo['comment']['RaceCatalogIcon']))
            {
                //删除
                unset($RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon']);
            }
            //如果有输出赛事图标的相对路径
            if(isset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']))
            {
                //拼接上ADMIN站点的域名
                $RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon'] = $this->config->adminUrl.$RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon_root'];
                //删除原有数据
                unset($RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon_root']);
            }
        }
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $result = array("return"=>count($RaceCatalogList)?1:0,"RaceCatalogList"=>$RaceCatalogList);
        echo json_encode($result);
    }
    /*
     * 获取单个赛事信息
     */
    public function getRaceCatalogInfoAction()
    {
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId)?abs(intval($this->request->RaceCatalogId)):0;
        //赛事ID必须大于0
        if($RaceCatalogId)
        {
            //获取赛事信息
            $RaceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId);
            //检测主键存在,否则值为空
            $RaceCatalogInfo = isset($RaceCatalogInfo['RaceCatalogId'])?$RaceCatalogInfo:array();
            //解包数组
            $RaceCatalogInfo['comment'] = isset($RaceCatalogInfo['comment'])?json_decode($RaceCatalogInfo['comment'],true):array();
            //如果有输出赛事图标的绝对路径
            if(isset($RaceCatalogInfo['comment']['RaceCatalogIcon']))
            {
                //删除
                unset($RaceCatalogInfo['comment']['RaceCatalogIcon']);
            }
            //如果有输出赛事图标的相对路径
            if(isset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']))
            {
                //拼接上ADMIN站点的域名
                $RaceCatalogInfo['comment']['RaceCatalogIcon'] = $this->config->adminUrl.$RaceCatalogInfo['comment']['RaceCatalogIcon_root'];
                //删除原有数据
                unset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']);
            }
            //根据赛事获取组别列表
            $RaceGroupList = isset($RaceCatalogInfo['RaceCatalogId'])?$this->oRace->getRaceGroupList($RaceCatalogInfo['RaceCatalogId'],"RaceGroupId,RaceGroupName"):array();
            //根据赛事获取分站列表
            $RaceStageList = isset($RaceCatalogInfo['RaceCatalogId'])?$this->oRace->getRaceStageList($RaceCatalogInfo['RaceCatalogId'],"RaceStageId,RaceStageName"):array();
            //结果数组
            $result = array("return"=>1,"RaceCatalogInfo"=>$RaceCatalogInfo,'RaceGroupList'=>$RaceGroupList,'RaceStageList'=>$RaceStageList);
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"RaceCatalog"=>array(),'RaceGroupList'=>array(),'RaceStageList'=>array(),"comment"=>"请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }
    /*
     * 根据赛事获取所有分站列表
     */
    public function getRaceStageListAction() {
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId)?abs(intval($this->request->RaceCatalogId)):0;
        $RaceStageStatus = isset($this->request->RaceStageStatus)?abs(intval($this->request->RaceStageStatus)):0;
        //赛事ID必须大于0
        if($RaceCatalogId)
        {
            //获得分站列表
            $RaceStageList = $this->oRace->getRaceStageList($RaceCatalogId);
            //如果没有返回值,默认为空数组
            if(!is_array($RaceStageList))
            {
                $RaceStageList = array();
            }
            //初始化一个空的产品列表
            $ProductList = array();
            //循环分站数组
            foreach ($RaceStageList as $RaceStageId => $RaceStageInfo)
            {
                //解包数组
                $RaceStageList[$RaceStageId]['comment'] = json_decode($RaceStageInfo['comment'],true);
                //解包图片数组
                $RaceStageList[$RaceStageId]['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'],true);
                //获取当前比赛的时间状态信息
                $RaceStageList[$RaceStageId]['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId,0);
                if(($RaceStageStatus>0 && $RaceStageList[$RaceStageId]['RaceStageStatus']['StageStatus']==$RaceStageStatus) || ($RaceStageStatus==0))
                {
                    //如果有配置分站图片
                    if(isset($RaceStageList[$RaceStageId]['RaceStageIcon']))
                    {
                        //循环图片列表
                        foreach($RaceStageList[$RaceStageId]['RaceStageIcon'] as $IconId => $IconInfo)
                        {
                            //拼接上ADMIN站点的域名
                            $RaceStageList[$RaceStageId]['comment']['RaceStageIconList'][$IconId]['RaceStageIcon'] = $this->config->adminUrl.$IconInfo['RaceStageIcon_root'];
                        }
                        //删除原有数据
                        unset($RaceStageList[$RaceStageId]['RaceStageIcon']);
                    }
                    //如果有配置分组信息
                    if(isset($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup']))
                    {
                        //循环图片列表
                        foreach($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'] as $RaceGroupId)
                        {
                            //获取赛事分组基本信息
                            $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,"RaceGroupId,RaceGroupName");
                            //如果有获取到分组信息
                            if($RaceGroupInfo['RaceGroupId'])
                            {
                                //提取分组名称
                                $RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'][$RaceGroupId] = $RaceGroupInfo;
                            }
                            else
                            {
                                //删除
                                unset($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'][$RaceGroupId]);
                            }
                        }
                    }
                    //如果有配置分组信息
                    if(isset($RaceStageList[$RaceStageId]['comment']['SelectedProductList']))
                    {
                        //循环产品列表
                        foreach($RaceStageList[$RaceStageId]['comment']['SelectedProductList'] as $ProductId => $Product)
                        {
                            //如果产品列表中没有此产品
                            if(!isset($ProductList[$ProductId]))
                            {
                                //获取产品信息
                                $ProductInfo = $this->oProduct->getProduct($ProductId);
                                //如果产品信息获取到
                                if(isset($ProductInfo['ProductId']))
                                {
                                    //放入产品列表中
                                    $ProductList[$ProductId] = $ProductInfo;
                                }
                                else
                                {
                                    continue;
                                }
                            }
                            //从产品列表中取出产品
                            $ProductInfo = $ProductList[$ProductId];
                            //存入产品名称
                            $RaceStageList[$RaceStageId]['comment']['SelectedProductList'][$ProductInfo['ProductId']]['ProductName'] = $ProductInfo['ProductName'];
                        }
                    }
                }
                else
                {
                    unset($RaceStageList[$RaceStageId]);
                }


            }
            //结果数组
            $result = array("return"=>1,"RaceStageList"=>$RaceStageList);            
        }
        else 
        {
            //全部置为空
            $result = array("return"=>0,"RaceStageList"=>array(),"comment"=>"请指定一个有效的赛事ID"); 
        }
        echo json_encode($result);
    }
    /*
     * 获取单个赛事分站信息
     */
    public function getRaceStageInfoAction()
    {
        //格式化赛事分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId)?abs(intval($this->request->RaceStageId)):0;
        //赛事分站D必须大于0
        if($RaceStageId)
        {
            //获得分站信息
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
            //检测主键存在,否则值为空
            $RaceStageInfo = isset($RaceStageInfo['RaceStageId'])?$RaceStageInfo:array();
            //解包数组
            $RaceStageInfo['comment'] = isset($RaceStageInfo['comment'])?json_decode($RaceStageInfo['comment'],true):array();
            //解包图片数组
            $RaceStageInfo['RaceStageIcon'] = isset($RaceStageInfo['RaceStageIcon'])?json_decode($RaceStageInfo['RaceStageIcon'],true):array();
            //如果有配置分站图片
            if(isset($RaceStageInfo['RaceStageIcon']))
            {
                //循环图片列表
                foreach($RaceStageInfo['RaceStageIcon'] as $IconId => $IconInfo)
                {
                    //拼接上ADMIN站点的域名
                    $RaceStageInfo['comment']['RaceStageIconList'][$IconId]['RaceStageIcon'] = $this->config->adminUrl.$IconInfo['RaceStageIcon_root'];
                }
                //删除原有数据
                unset($RaceStageInfo['RaceStageIcon']);
            }
            //如果有配置分组信息
            if(isset($RaceStageInfo['comment']['SelectedRaceGroup']))
            {
                //循环图片列表
                foreach($RaceStageInfo['comment']['SelectedRaceGroup'] as $RaceGroupId)
                {
                    //获取赛事分组基本信息
                    $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,"RaceGroupId,RaceGroupName");
                    //如果有获取到分组信息
                    if(isset($RaceGroupInfo['RaceGroupId']))
                    {
                        //提取分组名称
                        $RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId] = $RaceGroupInfo;
                    }
                    else
                    {
                        //删除
                        unset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]);
                    }
                }
            }
            //如果有配置分组信息
            if(isset($RaceStageInfo['comment']['SelectedProductList']))
            {
                //初始化一个空的产品列表
                $ProductList = array();
                //循环产品列表
                foreach($RaceStageInfo['comment']['SelectedProductList'] as $ProductId => $Product)
                {
                    //如果产品列表中没有此产品
                    if(!isset($ProductList[$ProductId]))
                    {
                        //获取产品信息
                        $ProductInfo = $this->oProduct->getProduct($ProductId,"ProductId,ProductName");
                        //如果产品信息获取到
                        if(isset($ProductInfo['ProductId']))
                        {
                            //放入产品列表中
                            $ProductList[$ProductId] = $ProductInfo;
                        }
                        else
                        {
                            continue;
                        }
                    }
                    //从产品列表中取出产品
                    $ProductInfo = $ProductList[$ProductId];
                    //存入产品名称
                    $RaceStageInfo['comment']['SelectedProductList'][$ProductId]['ProductName'] = $ProductInfo['ProductName'];
                }
            }
            //获取当前比赛的时间状态信息
            $RaceStageInfo['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId,0);
            //结果数组
            $result = array("return"=>1,"RaceStageInfo"=>$RaceStageInfo);
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"RaceStageInfo"=>array(),"comment"=>"请指定一个有效的分站ID");
        }
        echo json_encode($result);
    }
    /*
     * 获取单个赛事组别的信息
     */
    public function getRaceGroupInfoAction()
    {
        //格式化赛事组别ID,默认为0
        $RaceGroupId = isset($this->request->RaceGroupId)?intval($this->request->RaceGroupId):0;
        //赛事组别必须大于0
        if($RaceGroupId)
        {   
            //获取赛事组别信息
            $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId);
            //检测主键存在,否则值为空
            $RaceGroupInfo = isset($RaceGroupInfo['RaceGroupId'])?$RaceGroupInfo:array();
            //解包数组
            $RaceGroupInfo['comment'] = isset($RaceGroupInfo['comment'])?json_decode($RaceGroupInfo['comment'],true):array();
            //如果有配置分组的审核规则
            if(isset($RaceGroupInfo['comment']['LicenseList']))
            {
                //暂时先删除,其后版本再添加
                unset($RaceGroupInfo['comment']['LicenseList']);
            }
            //结果数组
            $result = array("return"=>1,"RaceGroupInfo"=>$RaceGroupInfo);            
        }   
        else
        {
            //全部置为空
            $result = array("return"=>0,"RaceGroupInfo"=>array(),"comment"=>"请指定一个有效的赛事分组ID");
        }
        echo json_encode($result);
    }
    /*
     * 获取赛事分站和赛事组别获取比赛列表
     */
    public function getRaceListAction() {
        //格式化赛事分站和赛事组别ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId)?abs(intval($this->request->RaceStageId)):0;
        $RaceGroupId = isset($this->request->RaceGroupId)?abs(intval($this->request->RaceGroupId)):0;
        //赛事分站和赛事组别ID必须大于0
        if($RaceStageId && $RaceGroupId)
        {
            //获得比赛列表
            $RaceList = $this->oRace->getRaceList($RaceStageId, $RaceGroupId,"RaceId,RaceTypeId,RouteInfo,RaceName,PriceList,SingleUser,TeamUser,StartTime,EndTime,ApplyStartTime,ApplyEndTime,comment");
            if(!is_array($RaceList))
            {
                $RaceList =array();
            }
            //获取运动类型类表
            $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
            //解包数组
            foreach ($RaceList as $RaceId => $RaceInfo)
            {
                //解包地图数据数组
                $RaceList[$RaceId]['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
                //处理价格列表
                $RaceList[$RaceId]['PriceList'] = $this->oRace->getPriceList($RaceInfo['PriceList']);
                //获取当前比赛的时间状态信息
                $RaceList[$RaceId]['RaceStatus'] = $this->oRace->getRaceTimeStatus($RaceInfo);
                //初始化比赛里程
                $RaceList[$RaceId]['Distence'] = 0;
                //初始化比赛海拔提升
                $RaceList[$RaceId]['AltAsc'] = 0;
                //初始化比赛海拔下降
                $RaceList[$RaceId]['AltDec'] = 0;
                //获取比赛分类信息
                $RaceTypeInfo  =  $RaceInfo['RaceTypeId']?$this->oRace->getRaceType($RaceInfo['RaceTypeId'],'*'):array();
                //如果获取到比赛类型信息
                if($RaceTypeInfo['RaceTypeId'])
                {
                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'],true);
                    //如果有输比赛类型图标的相对路径
                    if(isset($RaceTypeInfo['comment']['RaceTypeIcon_root']))
                    {
                        //拼接上ADMIN站点的域名
                        $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl.($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                    }
                    //删除原有数据
                    unset($RaceTypeInfo['comment']);
                }
                //存入结果数组
                $RaceList[$RaceId]['RaceTypeInfo'] = $RaceTypeInfo;
                //如果有配置运动分段
                if(isset($RaceInfo['comment']['DetailList']))
                {
                    //循环运动分段
                    foreach($RaceInfo['comment']['DetailList'] as $detailId => $detailInfo)
                    {
                        //如果有配置过该运动分段
                        if(isset($SportsTypeList[$detailInfo['SportsTypeId']]))
                        {
                            //获取运动类型名称
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['SportsTypeName'] =   $SportsTypeList[$detailInfo['SportsTypeId']]['SportsTypeName'];
                            //初始化运动分段的长度
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['Distence'] = 0;
                            //初始化运动分段的海拔上升
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltAsc'] = 0;
                            //初始化运动分段的海拔下降
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltDec'] = 0;
                            //获取计时点信息
                            $TimingInfo = isset($detailInfo['TimingId'])?$this->oRace->getTimingDetail($detailInfo['TimingId']):array();
                            //如果获取到计时点信息
                            if(isset($TimingInfo['TimingId']))
                            {
                                //数据解包
                                $TimingInfo['comment'] = isset($TimingInfo['comment'])?json_decode($TimingInfo['comment'],true):array();
                                //循环计时点信息
                                foreach($TimingInfo['comment'] as $tid => $tInfo)
                                {
                                    //累加里程到运动分段
                                    $RaceList[$RaceId]['comment']['DetailList'][$detailId]['Distence'] += $tInfo['ToNext']*$tInfo['Round'];
                                    //累加里程到比赛
                                    $RaceList[$RaceId]['Distence'] += $tInfo['ToNext']*$tInfo['Round'];
                                    //累加海拔上升到运动分段
                                    $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltAsc'] += $tInfo['AltAsc']*$tInfo['Round'];
                                    //累加海拔上升到比赛
                                    $RaceList[$RaceId]['AltAsc'] += $tInfo['AltAsc']*$tInfo['Round'];
                                    //累加海拔下降到运动分段
                                    $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltDec'] += $tInfo['AltDec']*$tInfo['Round'];
                                    //累加海拔下降到比赛
                                    $RaceList[$RaceId]['AltDec'] += $tInfo['AltDec']*$tInfo['Round'];
                                }
                            }
                        }
                        else
                        {
                            unset($RaceList[$RaceId]['comment']['DetailList'][$detailId]);
                        }
                    }
                }
               else
               {
                   //初始化为空数组
                   $RaceList[$RaceId]['comment']['DetailList'] = array();
               }
            }
            //结果数组 如果列表有数据则为成功，否则为失败
            $result = array("return"=>count($RaceList)?1:0,"RaceList"=>$RaceList);
        }
        else
        {
            $result = array("return"=>0,"RaceList"=>array());
        }
        echo json_encode($result);
    }
    /*
     * 获得单个比赛信息
     */
    public function getRaceInfoAction() {
        //格式化比赛ID,默认为0
        $RaceId = isset($this->request->RaceId)?abs(intval($this->request->RaceId)):0;
        //比赛ID必须大于0
        if($RaceId)
        {
            //获取比赛信息
            $RaceInfo = $this->oRace->getRaceInfo($RaceId);
            //检测主键存在,否则值为空
            if(isset($RaceInfo['RaceId']))
            {
                //解包地图数据数组
                $RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
                //获取当前比赛的时间状态信息
                $RaceInfo['RaceStatus'] = $this->oRace->getRaceTimeStatus($RaceInfo);
                //解包数组
                $RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
                //处理价格列表
                $RaceInfo['PriceList'] = $this->oRace->getPriceList($RaceInfo['PriceList']);
                //获取当前比赛的时间状态信息
                $RaceInfo['RaceStatus'] = $this->oRace->getRaceTimeStatus($RaceInfo);
                //初始化比赛里程
                $RaceInfo['Distence'] = 0;
                //初始化比赛海拔提升
                $RaceInfo['AltAsc'] = 0;
                //初始化比赛海拔下降
                $RaceInfo['AltDec'] = 0;
                //获取比赛分类信息
                $RaceTypeInfo  =  $RaceInfo['RaceTypeId']?$this->oRace->getRaceType($RaceInfo['RaceTypeId'],'*'):array();
                //如果获取到比赛类型信息
                if($RaceTypeInfo['RaceTypeId'])
                {
                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'],true);
                    //如果有输比赛类型图标的相对路径
                    if(isset($RaceTypeInfo['comment']['RaceTypeIcon_root']))
                    {
                        //拼接上ADMIN站点的域名
                        $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl.($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                    }
                    //删除原有数据
                    unset($RaceTypeInfo['comment']);
                }
                //存入结果数组
                $RaceInfo['RaceTypeInfo'] = $RaceTypeInfo;
                //如果有配置运动分段
                if(isset($RaceInfo['comment']['DetailList']))
                {
                    //获取运动类型类表
                    $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
                    //循环运动分段
                    foreach($RaceInfo['comment']['DetailList'] as $detailId => $detailInfo)
                    {
                        //如果有配置过该运动分段
                        if(isset($SportsTypeList[$detailInfo['SportsTypeId']]))
                        {
                            //获取运动类型名称
                            $RaceInfo['comment']['DetailList'][$detailId]['SportsTypeName'] =   $SportsTypeList[$detailInfo['SportsTypeId']]['SportsTypeName'];
                            //初始化运动分段的长度
                            $RaceInfo['comment']['DetailList'][$detailId]['Distence'] = 0;
                            //初始化运动分段的海拔上升
                            $RaceInfo['comment']['DetailList'][$detailId]['AltAsc'] = 0;
                            //初始化运动分段的海拔下降
                            $RaceInfo['comment']['DetailList'][$detailId]['AltDec'] = 0;
                            //获取计时点信息
                            $TimingInfo = isset($detailInfo['TimingId'])?$this->oRace->getTimingDetail($detailInfo['TimingId']):array();
                            //如果获取到计时点信息
                            if(isset($TimingInfo['TimingId']))
                            {
                                //数据解包
                                $TimingInfo['comment'] = isset($TimingInfo['comment'])?json_decode($TimingInfo['comment'],true):array();
                                //循环计时点信息
                                foreach($TimingInfo['comment'] as $tid => $tInfo)
                                {
                                    //累加里程到运动分段
                                    $RaceInfo['comment']['DetailList'][$detailId]['Distence'] += $tInfo['ToNext']*$tInfo['Round'];
                                    //累加里程到比赛
                                    $RaceInfo['Distence'] += $tInfo['ToNext']*$tInfo['Round'];
                                    //累加海拔上升到运动分段
                                    $RaceInfo['comment']['DetailList'][$detailId]['AltAsc'] += $tInfo['AltAsc']*$tInfo['Round'];
                                    //累加海拔上升到比赛
                                    $RaceInfo['AltAsc'] += $tInfo['AltAsc']*$tInfo['Round'];
                                    //累加海拔下降到运动分段
                                    $RaceInfo['comment']['DetailList'][$detailId]['AltDec'] += $tInfo['AltDec']*$tInfo['Round'];
                                    //累加海拔下降到比赛
                                    $RaceInfo['AltDec'] += $tInfo['AltDec']*$tInfo['Round'];
                                }
                            }
                        }
                        else
                        {
                            unset($RaceInfo['comment']['DetailList'][$detailId]);
                        }
                    }
                }
                else
                {
                    //初始化为空数组
                    $RaceInfo['comment']['DetailList'] = array();
                }

                //结果数组
                $result = array("return"=>1,"RaceInfo"=>$RaceInfo);
            }
            else
            {
                //全部置为空
                $result = array("return"=>0,"RaceInfo"=>array(),"comment"=>"请指定一个有效的比赛ID");
            }
        }
        else
        {
            //全部置为空
            $result = array("return"=>0,"RaceInfo"=>array(),"comment"=>"请指定一个有效的比赛ID");
        }
        echo json_encode($result);
    }

}