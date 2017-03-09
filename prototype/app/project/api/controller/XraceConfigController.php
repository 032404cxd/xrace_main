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
    protected $oUser;

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
        $this->oUser = new Xrace_UserInfo();
    }

    /**
     *获取所有赛事的列表(缓存)
     */
    public function getRaceCatalogListAction()
    {
        //是否调用缓存
        $Cache = isset($this->request->Cache) ? abs(intval($this->request->Cache)) : 1;
        //是否显示说明注释 默认为1
        $GetComment = isset($this->request->GetComment) ? abs(intval($this->request->GetComment)) : 1;
        //获得赛事列表
        $RaceCatalogList = $this->oRace->getRaceCatalogList(1,"*",$Cache);
        //如果没有返回值,默认为空数组
        if (!is_array($RaceCatalogList))
        {
            $RaceCatalogList = array();
        }
        //循环赛事列表数组
        foreach ($RaceCatalogList as $RaceCatalogId => $RaceCatalogInfo)
        {
            //如果有输出赛事图标的绝对路径
            if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon']))
            {
                //删除
                unset($RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon']);
            }
            //如果有输出赛事图标的相对路径
            if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']))
            {
                //拼接上ADMIN站点的域名
                $RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon'] = $this->config->adminUrl . $RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon_root'];
                //删除原有数据
                unset($RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon_root']);
            }
            //如果参数不显示说明文字
            if ($GetComment != 1)
            {
                //则删除该字段
                unset($RaceCatalogList[$RaceCatalogId]['RaceCatalogComment']);
            }
        }
        //结果数组 如果列表中有数据则返回成功，否则返回失败
        $result = array("return" => count($RaceCatalogList) ? 1 : 0, "RaceCatalogList" => $RaceCatalogList);
        echo json_encode($result);
    }

    /*
     * 获取单个赛事信息
     */
    public function getRaceCatalogInfoAction()
    {
        //是否调用缓存
        $Cache = isset($this->request->Cache) ? abs(intval($this->request->Cache)) : 1;
        //是否显示说明注释 默认为1
        $GetComment = isset($this->request->GetComment) ? abs(intval($this->request->GetComment)) : 1;
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId) ? abs(intval($this->request->RaceCatalogId)) : 0;
        //赛事ID必须大于0
        if ($RaceCatalogId) {
            //获取赛事信息
            $RaceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId,"*",$Cache);
            //检测主键存在,否则值为空
            if (isset($RaceCatalogInfo['RaceCatalogId']))
            {
                //解包数组
                $RaceCatalogInfo['comment'] = isset($RaceCatalogInfo['comment']) ? json_decode($RaceCatalogInfo['comment'], true) : array();
                //如果有输出赛事图标的绝对路径
                if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon']))
                {
                    //删除
                    unset($RaceCatalogInfo['comment']['RaceCatalogIcon']);
                }
                //如果有输出赛事图标的相对路径
                if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']))
                {
                    //拼接上ADMIN站点的域名
                    $RaceCatalogInfo['comment']['RaceCatalogIcon'] = $this->config->adminUrl . $RaceCatalogInfo['comment']['RaceCatalogIcon_root'];
                    //删除原有数据
                    unset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']);
                }
                //根据赛事获取组别列表
                $RaceGroupList = isset($RaceCatalogInfo['RaceCatalogId']) ? $this->oRace->getRaceGroupList($RaceCatalogInfo['RaceCatalogId'], "RaceGroupId,RaceGroupName") : array();
                //根据赛事获取分站列表
                $RaceStageList = isset($RaceCatalogInfo['RaceCatalogId']) ? $this->oRace->getRaceStageList($RaceCatalogInfo['RaceCatalogId'], "RaceStageId,RaceStageName",1) : array();
                //如果参数不显示说明文字
                if ($GetComment != 1)
                {
                    //则删除该字段
                    unset($RaceCatalogInfo['RaceCatalogComment']);
                }
                //结果数组
                $result = array("return" => 1, "RaceCatalogInfo" => $RaceCatalogInfo, 'RaceGroupList' => $RaceGroupList, 'RaceStageList' => $RaceStageList);
            }
            else
            {
                //全部置为空
                $result = array("return" => 0, "RaceCatalog" => array(), 'RaceGroupList' => array(), 'RaceStageList' => array(), "comment" => "请指定一个有效的赛事ID");
            }

        }
        else
        {
            //全部置为空
            $result = array("return" => 0, "RaceCatalog" => array(), 'RaceGroupList' => array(), 'RaceStageList' => array(), "comment" => "请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }

    /*
     * 根据赛事获取所有分站列表
     */
    public function getRaceStageListAction()
    {
        //比赛-分组的层级规则
        $RaceStructureList  = $this->oRace->getRaceStructure();
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId) ? abs(intval($this->request->RaceCatalogId)) : 0;
        $RaceStageStatus = isset($this->request->RaceStageStatus) ? abs(intval($this->request->RaceStageStatus)) : 0;
        //赛事ID必须大于0
        if ($RaceCatalogId)
        {
            //获得分站列表
            $RaceStageList = $this->oRace->getRaceStageList($RaceCatalogId,"*",1);
            //如果没有返回值,默认为空数组
            if (!is_array($RaceStageList))
            {
                //全部置为空
                $result = array("return" => 0, "RaceStageList" => array(), "comment" => "请指定一个有效的赛事ID");
            }
            else
            {
                //循环分站数组
                foreach ($RaceStageList as $RaceStageId => $RaceStageInfo)
                {
                    //如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
                    if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
                    {
                        //默认为分组优先
                        $RaceStageList[$RaceStageId]['comment']['RaceStructure'] = "group";
                    }
                    //说明文字解码
                    $RaceStageList[$RaceStageId]['RaceStageComment'] = urldecode($RaceStageInfo['RaceStageComment']);
                    //解包图片数组
                    $RaceStageList[$RaceStageId]['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'], true);
                    //获取当前比赛的时间状态信息
                    $RaceStageList[$RaceStageId]['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId, 0);
                    if (($RaceStageStatus > 0 && $RaceStageList[$RaceStageId]['RaceStageStatus']['StageStatus'] == $RaceStageStatus) || ($RaceStageStatus == 0)) {
                        //如果有配置分站图片
                        if (isset($RaceStageList[$RaceStageId]['RaceStageIcon']))
                        {
                            //循环图片列表
                            foreach ($RaceStageList[$RaceStageId]['RaceStageIcon'] as $IconId => $IconInfo)
                            {
                                //拼接上ADMIN站点的域名
                                $RaceStageList[$RaceStageId]['comment']['RaceStageIconList'][$IconId]['RaceStageIcon'] = $this->config->adminUrl . $IconInfo['RaceStageIcon_root'];
                            }
                            //删除原有数据
                            unset($RaceStageList[$RaceStageId]['RaceStageIcon']);
                        }
                        //如果有配置分组信息，暂不输出
                        if (isset($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup']))
                        {
                            //循环图片列表
                            foreach ($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'] as $RaceGroupId)
                            {
                                //获取赛事分组基本信息
                                $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId, "RaceGroupId,RaceGroupName");
                                //如果有获取到分组信息
                                if ($RaceGroupInfo['RaceGroupId'])
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
                        //不输出产品相关信息
                        unset($RaceStageList[$RaceStageId]['comment']['SelectedProductList']);
                    }
                    else
                    {
                        unset($RaceStageList[$RaceStageId]);
                    }
                }
                //结果数组
                $result = array("return" => 1, "RaceStageList" => $RaceStageList);
            }
        }
        else
        {
            //全部置为空
            $result = array("return" => 0, "RaceStageList" => array(), "comment" => "请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }
    /*
     * 获取单个赛事分站信息
     */
    public function getRaceStageInfoAction()
    {
        //比赛-分组的层级规则
        $RaceStructureList  = $this->oRace->getRaceStructure();
        //格式化赛事分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        //格式化用户ID,默认为空
        $UserId = isset($this->request->UserId) ? trim($this->request->UserId) : "";
        //筛选单人比赛
        $SingleUser = isset($this->request->SingleUser) ? abs(intval($this->request->SingleUser)) : 1;
        //筛选团队比赛
        $TeamUser = isset($this->request->TeamUser) ? abs(intval($this->request->TeamUser)) : 1;
        //筛选通票/单场
        $RacePriceMode = isset($this->request->RacePriceMode) ? trim($this->request->RacePriceMode) : "";
        if($RaceStageId)
        {
            //获得分站信息
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
            //检测主键存在,否则值为空
            $RaceStageInfo = isset($RaceStageInfo['RaceStageId']) ? $RaceStageInfo : array();
            if (isset($RaceStageInfo['RaceStageId']))
            {
                //说明文字解码
                $RaceStageInfo['RaceStageComment'] = urldecode($RaceStageInfo['RaceStageComment']);
                //解包数组
                $RaceStageInfo['comment'] = isset($RaceStageInfo['comment']) ? json_decode($RaceStageInfo['comment'], true) : array();
                //处理价格列表
                $RaceStageInfo["comment"]['PriceList'] = $this->oRace->getPriceList($RaceStageInfo["comment"]['PriceList']);
                //如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
                if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
                {
                    //默认为分组优先
                    $RaceStageInfo['comment']['RaceStructure'] = "group";
                }
                //解包图片数组
                $RaceStageInfo['RaceStageIcon'] = isset($RaceStageInfo['RaceStageIcon']) ? json_decode($RaceStageInfo['RaceStageIcon'], true) : array();
                //如果有配置分站图片
                if (isset($RaceStageInfo['RaceStageIcon']))
                {
                    //循环图片列表
                    foreach ($RaceStageInfo['RaceStageIcon'] as $IconId => $IconInfo)
                    {
                        //拼接上ADMIN站点的域名
                        $RaceStageInfo['comment']['RaceStageIconList'][$IconId]['RaceStageIcon'] = $this->config->adminUrl . $IconInfo['RaceStageIcon_root'];
                    }
                    //删除原有数据
                    unset($RaceStageInfo['RaceStageIcon']);
                }
                //如果有配置分组信息
                if (isset($RaceStageInfo['comment']['SelectedRaceGroup']))
                {
                    //循环图片列表
                    foreach ($RaceStageInfo['comment']['SelectedRaceGroup'] as $RaceGroupId)
                    {
                        //获取赛事分组基本信息
                        $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId, "RaceGroupId,RaceGroupName,comment");
                        //如果有获取到分组信息
                        if (isset($RaceGroupInfo['RaceGroupId']))
                        {
                            //默认当前组别可选
                            $RaceGroupInfo['checkable'] = true;
                            //数据解包
                            $RaceGroupInfo['comment'] = json_decode($RaceGroupInfo['comment'], true);
                            //执照条件的审核
                            $RaceGroupInfo['LicenseList'] = $this->oRace->raceLicenseCheck($RaceGroupInfo['comment']['LicenseList'], $UserId, $RaceStageInfo, $RaceGroupInfo);
                            foreach ($RaceGroupInfo['LicenseList'] as $k => $v)
                            {
                                //如果发现条件为不可选
                                if (isset($v['checked']) && $v['checked'] == false)
                                {
                                    //将当前组别置为不可选
                                    $RaceGroupInfo['checkable'] = false;
                                    break;
                                }
                            }
                            //格式化执照的条件，供显示
                            $LicenseListText = $this->oRace->ParthRaceLicenseListToHtml($RaceGroupInfo['LicenseList'], 0, 0, 1);
                            //循环执照审核条件的文字
                            foreach ($LicenseListText as $key => $LicenseInfo)
                            {
                                //分别置入权限审核列表
                                $RaceGroupInfo['LicenseList'][$key]['LicenseTextArr'] = $LicenseInfo;
                            }
                            //提取分组名称
                            unset($RaceGroupInfo['comment']);
                            $RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId] = $RaceGroupInfo;
                            //获取比赛列表
                            $RaceList  = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageId,"RaceGroupId"=>$RaceGroupId),$fields = 'RaceId,TeamUser,SingleUser,PriceList');
                            //如果有比赛
                            if(count($RaceList))
                            {
                                //循环比赛列表
                                foreach($RaceList as $RaceId => $RaceInfo)
                                {
                                    //如果选定了担任比赛
                                    if($SingleUser == 1 && $RaceInfo['SingleUser'] == 1)
                                    {
                                        break;
                                    }
                                    //如果选定了团队比赛
                                    elseif($TeamUser == 1 && $RaceInfo['TeamUser'] == 1)
                                    {
                                        break;
                                    }
                                    //如果不限定价
                                    elseif($RacePriceMode == "")
                                    {
                                        break;
                                    }
                                    //如果选定了只要比赛独立定价 且 比赛独立定价
                                    elseif($RacePriceMode == "race" && $RaceInfo['PriceList'] != "")
                                    {
                                        break;
                                    }
                                    //如果选定了只要分站通票定价 且 比赛未独立定价
                                    elseif($RacePriceMode == "stage" && $RaceInfo['PriceList'] == "")
                                    {
                                        break;
                                    }
                                    else
                                    {
                                        //删除当前比赛
                                        unset($RaceList[$RaceId]);
                                        //如果比赛列表为空
                                        if(!count($RaceList))
                                        {
                                            //删除当前分组
                                            unset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                unset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]);
                            }
                        }
                        else
                        {
                            //删除
                            unset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]);
                        }
                    }
                }
                //如果有配置分组信息
                if (isset($RaceStageInfo['comment']['SelectedProductList']))
                {
                    //初始化一个空的产品列表
                    $ProductList = array();
                    //循环产品列表
                    foreach ($RaceStageInfo['comment']['SelectedProductList'] as $ProductId => $SkuList)
                    {
                        //如果产品列表中没有此产品
                        if (!isset($ProductList[$ProductId]))
                        {
                            //获取产品信息
                            $ProductInfo = $this->oProduct->getProduct($ProductId, "ProductId,ProductName");
                            //如果产品信息获取到
                            if (isset($ProductInfo['ProductId']))
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
                        $ProductSkuList = $this->oProduct->getAllProductSkuList($ProductId);
                        foreach($SkuList as $ProductSkuId => $ProductSkuInfo)
                        {
                            if(isset($ProductSkuList[$ProductId][$ProductSkuId]) && $ProductSkuInfo['Stock']>0)
                            {
                                $SkuList[$ProductSkuId]['ProductSkuName'] =  $ProductSkuList[$ProductId][$ProductSkuId]['ProductSkuName'];
                            }
                            else
                            {
                                unset($SkuList[$ProductSkuId]);
                            }
                        }
                        if(count($SkuList)>0)
                        {
                            $Product = array("SkuList"=>$SkuList,'ProductName'=>$ProductInfo['ProductName']);
                            //存入产品名称
                            $RaceStageInfo['comment']['SelectedProductList'][$ProductId] = $Product;
                        }
                        else
                        {
                            unset($RaceStageInfo['comment']['SelectedProductList'][$ProductId]);
                        }
                    }
                }
                //获取当前比赛的时间状态信息
                $RaceStageInfo['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId, 0);
                //结果数组
                $result = array("return" => 1, "RaceStageInfo" => $RaceStageInfo);
            } else {
                //全部置为空
                $result = array("return" => 0, "RaceStageInfo" => array(), "comment" => "请指定一个有效的分站ID");
            }
        } else {
            //全部置为空
            $result = array("return" => 0, "RaceStageInfo" => array(), "comment" => "请指定一个有效的分站ID");
        }
        echo json_encode($result);
    }
    /*
     * 获取单个赛事组别的信息
     */
    public function getRaceGroupInfoAction()
    {
        //格式化赛事组别ID,默认为0
        $RaceGroupId = isset($this->request->RaceGroupId) ? intval($this->request->RaceGroupId) : 0;
        //赛事组别必须大于0
        if ($RaceGroupId) {
            //获取赛事组别信息
            $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId);
            //检测主键存在,否则值为空
            $RaceGroupInfo = isset($RaceGroupInfo['RaceGroupId']) ? $RaceGroupInfo : array();
            //解包数组
            $RaceGroupInfo['comment'] = isset($RaceGroupInfo['comment']) ? json_decode($RaceGroupInfo['comment'], true) : array();
            //如果有配置分组的审核规则
            if (isset($RaceGroupInfo['comment']['LicenseList']))
            {
                //暂时先删除,其后版本再添加
                unset($RaceGroupInfo['comment']['LicenseList']);
            }
            //结果数组
            $result = array("return" => 1, "RaceGroupInfo" => $RaceGroupInfo);
        }
        else
        {
            //全部置为空
            $result = array("return" => 0, "RaceGroupInfo" => array(), "comment" => "请指定一个有效的赛事分组ID");
        }
        echo json_encode($result);
    }

    /*
     * 获取赛事分站和赛事组别获取比赛列表
     */
    public function getRaceListAction()
    {
        //格式化赛事分站和赛事组别ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        $RaceGroupId = isset($this->request->RaceGroupId) ? abs(intval($this->request->RaceGroupId)) : 0;
        //筛选通票/单场
        $RacePriceMode = isset($this->request->RacePriceMode) ? trim($this->request->RacePriceMode) : "";
        //获得分站信息
        $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
        //数据解包
        $RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
        //比赛-分组的层级规则
        $RaceStructureList  = $this->oRace->getRaceStructure();
        //如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
        if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
        {
            //默认为分组优先
            $RaceStageInfo['comment']['RaceStructure'] = "group";
        }
        //分组优先
        if($RaceStageInfo['comment']['RaceStructure'] == "group")
        {
            //赛事分站和赛事组别ID必须大于0
            if ($RaceStageId && $RaceGroupId)
            {
                //获得比赛列表
                $RaceList = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageId, "RaceGroupId"=>$RaceGroupId), "RaceId,RaceTypeId,RouteInfo,RaceName,PriceList,SingleUser,TeamUser,StartTime,EndTime,ApplyStartTime,ApplyEndTime,comment,RaceComment,MustSelect");
                if (!is_array($RaceList))
                {
                    $RaceList = array();
                }
                //获取运动类型类表
                $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
                //解包数组
                foreach ($RaceList as $RaceId => $RaceInfo)
                {
                        //如果不限定价
                    if($RacePriceMode == "")
                    {

                    }
                    //如果选定了只要比赛独立定价 且 比赛独立定价
                    elseif($RacePriceMode == "stage" && $RaceInfo['PriceList'] != "")
                    {
                        unset($RaceList[$RaceId]);
                        break;
                    }
                    //如果选定了只要分站通票定价 且 比赛未独立定价
                    elseif($RacePriceMode == "race" && $RaceInfo['PriceList'] == "")
                    {
                        unset($RaceList[$RaceId]);
                        break;
                    }
                    //说明文字解码
                    $RaceList[$RaceId]['RaceComment'] = urldecode($RaceInfo['RaceComment']);
                    //解包地图数据数组
                    $RaceList[$RaceId]['RouteInfo'] = json_decode($RaceInfo['RouteInfo'], true);
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
                    $RaceTypeInfo = $RaceInfo['RaceTypeId'] ? $this->oRace->getRaceType($RaceInfo['RaceTypeId'], '*') : array();
                    //如果获取到比赛类型信息
                    if (isset($RaceTypeInfo['RaceTypeId']))
                    {
                        $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                        //如果有输比赛类型图标的相对路径
                        if (isset($RaceTypeInfo['comment']['RaceTypeIcon_root']))
                        {
                            //拼接上ADMIN站点的域名
                            $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl . ($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                        }
                        //删除原有数据
                        unset($RaceTypeInfo['comment']);
                    }
                    //存入结果数组
                    $RaceList[$RaceId]['RaceTypeInfo'] = $RaceTypeInfo;
                    //如果有配置运动分段
                    if (isset($RaceInfo['comment']['DetailList']))
                    {
                        //循环运动分段
                        foreach ($RaceInfo['comment']['DetailList'] as $detailId => $detailInfo)
                        {
                            //如果有配置过该运动分段
                            if (isset($SportsTypeList[$detailInfo['SportsTypeId']]))
                            {
                                //获取运动类型名称
                                $RaceList[$RaceId]['comment']['DetailList'][$detailId]['SportsTypeName'] = $SportsTypeList[$detailInfo['SportsTypeId']]['SportsTypeName'];
                                //初始化运动分段的长度
                                $RaceList[$RaceId]['comment']['DetailList'][$detailId]['Distence'] = 0;
                                //初始化运动分段的海拔上升
                                $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltAsc'] = 0;
                                //初始化运动分段的海拔下降
                                $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltDec'] = 0;
                                //获取计时点信息
                                $TimingInfo = isset($detailInfo['TimingId']) ? $this->oRace->getTimingDetail($detailInfo['TimingId']) : array();
                                //如果获取到计时点信息
                                if (isset($TimingInfo['TimingId']))
                                {
                                    //数据解包
                                    $TimingInfo['comment'] = isset($TimingInfo['comment']) ? json_decode($TimingInfo['comment'], true) : array();
                                    //循环计时点信息
                                    foreach ($TimingInfo['comment'] as $tid => $tInfo)
                                    {
                                        //累加里程到运动分段
                                        $RaceList[$RaceId]['comment']['DetailList'][$detailId]['Distence'] += $tInfo['ToNext'] * $tInfo['Round'];
                                        //累加里程到比赛
                                        $RaceList[$RaceId]['Distence'] += $tInfo['ToNext'] * $tInfo['Round'];
                                        //累加海拔上升到运动分段
                                        $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltAsc'] += $tInfo['AltAsc'] * $tInfo['Round'];
                                        //累加海拔上升到比赛
                                        $RaceList[$RaceId]['AltAsc'] += $tInfo['AltAsc'] * $tInfo['Round'];
                                        //累加海拔下降到运动分段
                                        $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltDec'] += $tInfo['AltDec'] * $tInfo['Round'];
                                        //累加海拔下降到比赛
                                        $RaceList[$RaceId]['AltDec'] += $tInfo['AltDec'] * $tInfo['Round'];
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
                $result = array("return" => count($RaceList) ? 1 : 0, "RaceList" => $RaceList);
            }
            else
            {
                $result = array("return" => 0, "RaceList" => array());
            }
        }
        else
        {
            //获取当前赛事下的分组列表
            $RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],"RaceGroupName,RaceGroupId");
            //获得比赛列表
            $RaceList = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageId), "RaceId,RaceTypeId,RouteInfo,RaceName,PriceList,SingleUser,TeamUser,StartTime,EndTime,ApplyStartTime,ApplyEndTime,comment,RaceComment,MustSelect");
            if (!is_array($RaceList))
            {
                $RaceList = array();
            }
            //获取运动类型类表
            $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
            //解包数组
            foreach ($RaceList as $RaceId => $RaceInfo)
            {
                //说明文字解码
                $RaceList[$RaceId]['RaceComment'] = urldecode($RaceInfo['RaceComment']);
                //解包地图数据数组
                $RaceList[$RaceId]['RouteInfo'] = json_decode($RaceInfo['RouteInfo'], true);
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
                $RaceTypeInfo = $RaceInfo['RaceTypeId'] ? $this->oRace->getRaceType($RaceInfo['RaceTypeId'], '*') : array();
                //如果获取到比赛类型信息
                if (isset($RaceTypeInfo['RaceTypeId']))
                {
                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                    //如果有输比赛类型图标的相对路径
                    if (isset($RaceTypeInfo['comment']['RaceTypeIcon_root']))
                    {
                        //拼接上ADMIN站点的域名
                        $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl . ($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                    }
                    //删除原有数据
                    unset($RaceTypeInfo['comment']);
                }
                //存入结果数组
                $RaceList[$RaceId]['RaceTypeInfo'] = $RaceTypeInfo;
                //如果有配置运动分段
                if (isset($RaceInfo['comment']['DetailList']))
                {
                    //循环运动分段
                    foreach ($RaceInfo['comment']['DetailList'] as $detailId => $detailInfo) {
                        //如果有配置过该运动分段
                        if (isset($SportsTypeList[$detailInfo['SportsTypeId']])) {
                            //获取运动类型名称
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['SportsTypeName'] = $SportsTypeList[$detailInfo['SportsTypeId']]['SportsTypeName'];
                            //初始化运动分段的长度
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['Distence'] = 0;
                            //初始化运动分段的海拔上升
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltAsc'] = 0;
                            //初始化运动分段的海拔下降
                            $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltDec'] = 0;
                            //获取计时点信息
                            $TimingInfo = isset($detailInfo['TimingId']) ? $this->oRace->getTimingDetail($detailInfo['TimingId']) : array();
                            //如果获取到计时点信息
                            if (isset($TimingInfo['TimingId']))
                            {
                                //数据解包
                                $TimingInfo['comment'] = isset($TimingInfo['comment']) ? json_decode($TimingInfo['comment'], true) : array();
                                //循环计时点信息
                                foreach ($TimingInfo['comment'] as $tid => $tInfo)
                                {
                                    //累加里程到运动分段
                                    $RaceList[$RaceId]['comment']['DetailList'][$detailId]['Distence'] += $tInfo['ToNext'] * $tInfo['Round'];
                                    //累加里程到比赛
                                    $RaceList[$RaceId]['Distence'] += $tInfo['ToNext'] * $tInfo['Round'];
                                    //累加海拔上升到运动分段
                                    $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltAsc'] += $tInfo['AltAsc'] * $tInfo['Round'];
                                    //累加海拔上升到比赛
                                    $RaceList[$RaceId]['AltAsc'] += $tInfo['AltAsc'] * $tInfo['Round'];
                                    //累加海拔下降到运动分段
                                    $RaceList[$RaceId]['comment']['DetailList'][$detailId]['AltDec'] += $tInfo['AltDec'] * $tInfo['Round'];
                                    //累加海拔下降到比赛
                                    $RaceList[$RaceId]['AltDec'] += $tInfo['AltDec'] * $tInfo['Round'];
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
                //如果有配置可选的分组
                if (isset($RaceInfo['comment']['SelectedRaceGroup']))
                {
                    //寻源已经选定的分组列表
                    foreach($RaceInfo['comment']['SelectedRaceGroup'] as $k => $v)
                    {
                        //如果查到就保留
                        if(isset($RaceGroupList[$k]))
                        {
                            $RaceList[$RaceId]['comment']['SelectedRaceGroup'][$k] = $RaceGroupList[$k];
                        }
                        //否则就删除
                        else
                        {
                            unset($RaceList[$RaceId]['comment']['SelectedRaceGroup'][$k]);
                        }
                    }
                }
            }
            //结果数组 如果列表有数据则为成功，否则为失败
            $result = array("return" => count($RaceList) ? 1 : 0, "RaceList" => $RaceList);
        }
        echo json_encode($result);
    }
    /*
     * 获得单个比赛信息
     */
    public function getRaceInfoAction()
    {
        //格式化比赛ID,默认为0
        $RaceId = isset($this->request->RaceId) ? abs(intval($this->request->RaceId)) : 0;
        //比赛ID必须大于0
        if ($RaceId)
        {
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId);
            //检测主键存在,否则值为空
            if (isset($RaceInfo['RaceId']))
            {
                //说明文字解码
                $RaceInfo['RaceComment'] = urldecode($RaceInfo['RaceComment']);
                //解包地图数据数组
                $RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'], true);
                //获取当前比赛的时间状态信息
                $RaceInfo['RaceStatus'] = $this->oRace->getRaceTimeStatus($RaceInfo);
                //解包数组
                $RaceInfo['comment'] = json_decode($RaceInfo['comment'], true);
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
                $RaceTypeInfo = $RaceInfo['RaceTypeId'] ? $this->oRace->getRaceType($RaceInfo['RaceTypeId'], '*') : array();
                //如果获取到比赛类型信息
                if ($RaceTypeInfo['RaceTypeId'])
                {
                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                    //如果有输比赛类型图标的相对路径
                    if (isset($RaceTypeInfo['comment']['RaceTypeIcon_root']))
                    {
                        //拼接上ADMIN站点的域名
                        $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl . ($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                    }
                    //删除原有数据
                    unset($RaceTypeInfo['comment']);
                }
                //存入结果数组
                $RaceInfo['RaceTypeInfo'] = $RaceTypeInfo;
                //如果有配置运动分段
                if (isset($RaceInfo['comment']['DetailList']))
                {
                    //获取运动类型类表
                    $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
                    //循环运动分段
                    foreach ($RaceInfo['comment']['DetailList'] as $detailId => $detailInfo)
                    {
                        //如果有配置过该运动分段
                        if (isset($SportsTypeList[$detailInfo['SportsTypeId']]))
                        {
                            //获取运动类型名称
                            $RaceInfo['comment']['DetailList'][$detailId]['SportsTypeName'] = $SportsTypeList[$detailInfo['SportsTypeId']]['SportsTypeName'];
                            //初始化运动分段的长度
                            $RaceInfo['comment']['DetailList'][$detailId]['Distence'] = 0;
                            //初始化运动分段的海拔上升
                            $RaceInfo['comment']['DetailList'][$detailId]['AltAsc'] = 0;
                            //初始化运动分段的海拔下降
                            $RaceInfo['comment']['DetailList'][$detailId]['AltDec'] = 0;
                            //获取计时点信息
                            $TimingInfo = isset($detailInfo['TimingId']) ? $this->oRace->getTimingDetail($detailInfo['TimingId']) : array();
                            //如果获取到计时点信息
                            if (isset($TimingInfo['TimingId'])) {
                                //数据解包
                                $TimingInfo['comment'] = isset($TimingInfo['comment']) ? json_decode($TimingInfo['comment'], true) : array();
                                //循环计时点信息
                                foreach ($TimingInfo['comment'] as $tid => $tInfo) {
                                    //累加里程到运动分段
                                    $RaceInfo['comment']['DetailList'][$detailId]['Distence'] += $tInfo['ToNext'] * $tInfo['Round'];
                                    //累加里程到比赛
                                    $RaceInfo['Distence'] += $tInfo['ToNext'] * $tInfo['Round'];
                                    //累加海拔上升到运动分段
                                    $RaceInfo['comment']['DetailList'][$detailId]['AltAsc'] += $tInfo['AltAsc'] * $tInfo['Round'];
                                    //累加海拔上升到比赛
                                    $RaceInfo['AltAsc'] += $tInfo['AltAsc'] * $tInfo['Round'];
                                    //累加海拔下降到运动分段
                                    $RaceInfo['comment']['DetailList'][$detailId]['AltDec'] += $tInfo['AltDec'] * $tInfo['Round'];
                                    //累加海拔下降到比赛
                                    $RaceInfo['AltDec'] += $tInfo['AltDec'] * $tInfo['Round'];
                                }
                            }
                        } else {
                            unset($RaceInfo['comment']['DetailList'][$detailId]);
                        }
                    }
                }
                else
                {
                    //初始化为空数组
                    $RaceInfo['comment']['DetailList'] = array();
                }
                //比赛-分组的层级规则
                $RaceStructureList  = $this->oRace->getRaceStructure();
                //获得分站信息
                $RaceStageInfo = $this->oRace->getRaceStage($RaceInfo['RaceStageId'],"RaceStageId,comment,RaceCatalogId");
                //数据解包
                $RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
                //如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
                if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
                {
                    //默认为分组优先
                    $RaceStageInfo['comment']['RaceStructure'] = "group";
                }
                else
                {
                    $RaceStageInfo['comment']['RaceStructure'] = "race";
                }
                //复写赛事结构
                $RaceInfo['RaceStructure'] = $RaceStageInfo['comment']['RaceStructure'];
                //赛事结构为分组优先
                if($RaceStageInfo['comment']['RaceStructure'] == "group")
                {

                }
                else
                {
                    //如果有配置可选的分组
                    if (isset($RaceInfo['comment']['SelectedRaceGroup']))
                    {
                        //获取当前赛事下的分组列表
                        $RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],"RaceGroupName,RaceGroupId,comment");
                        //寻源已经选定的分组列表
                        foreach($RaceInfo['comment']['SelectedRaceGroup'] as $k => $v)
                        {
                            //如果查到就保留
                            if(isset($RaceGroupList[$k]) && $v['Selected'])
                            {
                                $RaceGroupInfo = $RaceGroupList[$k];
                                //默认当前组别可选
                                $RaceGroupInfo['checkable'] = true;
                                //数据解包
                                $RaceGroupInfo['comment'] = json_decode($RaceGroupInfo['comment'], true);
                                //执照条件的审核
                                $RaceGroupInfo['comment']['LicenseList'] = $this->oRace->raceLicenseCheck($RaceGroupInfo['comment']['LicenseList'], 0, $RaceStageInfo, $RaceGroupInfo);
                                foreach ($RaceGroupInfo['comment']['LicenseList'] as $k2 => $v2)
                                {
                                    //如果发现条件为不可选
                                    if (isset($v2['checked']) && $v2['checked'] == false)
                                    {
                                        //将当前组别置为不可选
                                        $RaceGroupInfo['checkable'] = false;
                                        break;
                                    }
                                }
                                //格式化执照的条件，供显示
                                $LicenseListText = $this->oRace->ParthRaceLicenseListToHtml($RaceGroupInfo['comment']['LicenseList'], 0, 0, 1);
                                foreach($LicenseListText as $k3 => $v3)
                                {
                                    if(isset($RaceGroupInfo['comment']['LicenseList'][$k3]))
                                    {
                                        $RaceGroupInfo['comment']['LicenseList'][$k3]['LicenseListText'] = $v3;
                                    }
                                }
                                $RaceInfo['comment']['SelectedRaceGroup'][$k] = $RaceGroupInfo;
                            }
                            //否则就删除
                            else
                            {
                                unset($RaceInfo['comment']['SelectedRaceGroup'][$k]);
                            }
                        }
                    }

                }
                //结果数组
                $result = array("return" => 1, "RaceInfo" => $RaceInfo);
            } else {
                //全部置为空
                $result = array("return" => 0, "RaceInfo" => array(), "comment" => "请指定一个有效的比赛ID");
            }
        } else {
            //全部置为空
            $result = array("return" => 0, "RaceInfo" => array(), "comment" => "请指定一个有效的比赛ID");
        }
        echo json_encode($result);
    }

    /*
     * 获取指定赛事组别下的车队列表
     */
    public function getTeamListAction()
    {
        //格式化赛事ID
        $RaceCatalogId = abs(intval($this->request->RaceCatalogId));
        //格式化赛事ID
        $RaceGroupId = abs(intval($this->request->RaceGroupId));
        //赛事ID必须大于0
        if ($RaceCatalogId)
        {
            //获取赛事信息
           // $RaceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId,"RaceCatalogId,RaceCatalgName",1);
           // print_R($RaceCatalogInfo);
            //检测主键存在,否则值为空
           // if (isset($RaceCatalogInfo['RaceCatalogId']))
            {
                $oTeam = new Xrace_Team();
                $TeamList = $oTeam->getTeamList(array("RaceCatalogId"=>$RaceCatalogId), 1);
                    //结果数组
                    if (count($TeamList['TeamList'])) {
                        $result = array("return" => 1, "TeamList" => $TeamList['TeamList']);
                    } else {
                        $result = array("return" => 0, "TeamList" => array(), "comment" => "组别下并未有队伍");
                    }
                }

            // else {
            //    //全部置为空
            //    $result = array("return" => 0, "TeamList" => array(), "comment" => "请指定一个有效的赛事ID");
           // }
        } else {
            //全部置为空
            $result = array("return" => 0, "RaceCatalog" => array(), 'RaceGroupList' => array(), 'RaceStageList' => array(), "comment" => "请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }

    /*
     * 获取指定比赛报名的车队列表
    */
    public function getRaceUserListByRaceAction()
    {
        //比赛ID
        $RaceId = abs(intval($this->request->RaceId));
        //队伍ID -1表示个人选手 0表示全部
        $TeamId = intval($this->request->TeamId) >= -1 ? intval($this->request->TeamId) : 0;
        //赛事ID必须大于0
        if ($RaceId) {
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId);
            //检测主键存在,否则值为空
            if (isset($RaceInfo['RaceId'])) {
                //获取选手和车队名单
                $RaceUserList = $this->oUser->getRaceUserListByRace($RaceId, 0,$TeamId, 1);
                if (count($RaceUserList['RaceUserList'])) {
                    //返回车手名单和车队列表
                    $result = array("return" => 1, "RaceUserList" => $RaceUserList['RaceUserList'], "TeamList" => $RaceUserList['TeamList']);
                } else {
                    //全部置为空
                    $result = array("return" => 0, "RaceUserList" => array(), "TeamList" => array(), "comment" => "尚无选手报名");
                }
            } else {
                //全部置为空
                $result = array("return" => 0, "RaceUserList" => array(), "TeamList" => array(), "comment" => "请指定一个有效的比赛ID");
            }
        } else {
            //全部置为空
            $result = array("return" => 0, "RaceUserList" => array(), "TeamList" => array(), "comment" => "请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }

    /*
     * 获取指定用户或BIB在比赛中的详情
    */
    public function getUserRaceInfoAction()
    {
        //比赛ID
        $RaceId = abs(intval($this->request->RaceId));
        //比赛ID
        $RaceUserId = abs(intval($this->request->RaceUserId));
        //用户BIB
        $BIB = trim($this->request->BIB);
        if (!$RaceUserId) {
            //根据用户的BIB获取比赛报名信息
            $UserApplyInfo = $this->oUser->getRaceApplyUserInfoByBIB($RaceId, $BIB);
            //如果查询到报名记录
            if ($UserApplyInfo['ApplyId']) {
                //保存用户ID
                $UserId = $UserApplyInfo['RaceUserId'];
            }
        }
        //获取用户比赛的详情
        $UserRaceInfo = $this->oRace->getUserRaceInfo($RaceId, $RaceUserId);
        //如果有查出数据
        if (!isset($UserRaceInfo['RaceUserInfo']))
        {
            //重新生成该场比赛所有人的配置数据
            $this->oRace->genRaceLogToText($RaceId, $RaceUserId);
            //重新获取比赛详情
            $UserRaceInfo = $this->oRace->getUserRaceInfo($RaceId, $RaceUserId);
        }
        $UserRaceInfo['ApplyInfo']['RaceStatus'] = $this->oRace->getUserRaceStatus($UserRaceInfo);
        $result = array("return" => isset($UserRaceInfo['ApplyInfo']) ? 1 : 0, "UserRaceInfo" => $UserRaceInfo);
        echo json_encode($result);
    }

    /*
     * 获取指定用户的报名记录
    */
    public function getUserRaceListAction()
    {
        //用户ID
        $UserId = abs(intval($this->request->UserId));
        if ($UserId)
        {
            //获取用户信息
                $UserInfo = $this->oUser->getUserInfo($UserId, 'UserId,name');
                //如果有获取到用户信息
                if ($UserInfo['UserId'])
                {
                //根据用户获取报名记录
                $UserApplyList = $this->oUser->getRaceUserList(array('UserId' => $UserInfo['UserId']));
                //获取赛事列表
                $RaceCatalogList = $this->oRace->getRaceCatalogList(0,"RaceCatalogId,RaceCatalogName");
                $RaceGroupList = array();
                $RaceStageList = array();
                $RaceTypeList = array();
                //循环报名列表
                foreach ($UserApplyList as $key => $ApplyInfo)
                {
                    if (isset($RaceCatalogList[$ApplyInfo['RaceCatalogId']]))
                    {
                        $UserApplyList[$key]['comment'] = json_decode($ApplyInfo['comment'], true);
                        $UserApplyList[$key]['RaceCatalogName'] = $RaceCatalogList[$ApplyInfo['RaceCatalogId']]['RaceCatalogName'];
                        if (!isset($RaceGroupList[$ApplyInfo['RaceGroupId']]))
                        {
                            $RaceGroupInfo = $this->oRace->getRaceGroup($ApplyInfo['RaceGroupId'], 'RaceGroupId,RaceGroupName');
                            if (isset($RaceGroupInfo['RaceGroupId']))
                            {
                                $RaceGroupList[$ApplyInfo['RaceGroupId']] = $RaceGroupInfo;
                            }
                            else
                            {
                                unset($UserApplyList[$key]);
                            }
                        }
                        $UserApplyList[$key]['RaceGroupName'] = $RaceGroupList[$ApplyInfo['RaceGroupId']]['RaceGroupName'];
                        if (!isset($RaceStageList[$ApplyInfo['RaceStageId']]))
                        {
                            $RaceStageInfo = $this->oRace->getRaceStage($ApplyInfo['RaceStageId'], 'RaceStageId,RaceStageName');
                            if (isset($RaceStageInfo['RaceStageId']))
                            {
                                $RaceStageList[$ApplyInfo['RaceStageId']] = $RaceStageInfo;
                            }
                            else
                                {
                                unset($UserApplyList[$key]);
                            }
                        }
                        $UserApplyList[$key]['RaceStageName'] = $RaceStageList[$ApplyInfo['RaceStageId']]['RaceStageName'];

                        $RaceInfo = $this->oRace->getRace($ApplyInfo['RaceId'], "*");
                        if (isset($RaceInfo['RaceId']))
                        {
                            $UserApplyList[$key]['RaceName'] = $RaceInfo['RaceName'];
                            if (!isset($RaceTypeList[$RaceInfo['RaceTypeId']]))
                            {
                                $RaceTypeInfo = $this->oRace->getRaceType($RaceInfo['RaceTypeId'], '*');
                                if (isset($RaceTypeInfo['RaceTypeId']))
                                {
                                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                                    //拼接上ADMIN站点的域名
                                    $RaceTypeInfo['comment']['RaceTypeIcon'] = $this->config->adminUrl . $RaceTypeInfo['comment']['RaceTypeIcon_root'];
                                    $RaceTypeList[$RaceInfo['RaceTypeId']] = $RaceTypeInfo;
                                }
                                else
                                {
                                    unset($UserApplyList[$key]);
                                }
                            }
                            $UserApplyList[$key]['RaceTypeIcon'] = $RaceTypeList[$RaceInfo['RaceTypeId']]['comment']['RaceTypeIcon'];
                            $UserApplyList[$key]['RaceTypeName'] = $RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName'];
                            $UserApplyList[$key]['RaceStatus'] = $this->oRace->getUserRaceStatus($this->oRace->getUserRaceInfo($ApplyInfo['RaceId'], $UserId));
                        }
                        else
                        {
                            unset($UserApplyList[$key]);
                        }
                    }
                    else
                    {
                        unset($UserApplyList[$key]);
                    }
                }
                $result = array("return" => 1, "UserRaceList" => $UserApplyList);
            }
            else
            {
                $result = array("return" => 0, "UserRaceList" => array(), "comment" => "无此用户");
            }
        }
        else
        {
            $result = array("return" => 0, "UserRaceList" => array(), "comment" => "请指定一个有效的用户ID");
        }
        echo json_encode($result);
    }

    /*
 * 获取指定比赛报名的车队列表
*/
    public function getRaceUserListByBibAction()
    {
        //比赛ID
        $RaceId = abs(intval($this->request->RaceId));
        //比赛ID
        $RaceGroupId = abs(intval($this->request->RaceGroupId));
        //BIB号码
        $BIB = trim($this->request->BIB);
        //赛事ID必须大于0
        if ($RaceId) {
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId);
            //检测主键存在,否则值为空
            if (isset($RaceInfo['RaceId']))
            {
                $RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
                //获取选手和车队名单
                $RaceUserList = $this->oUser->getRaceUserListByRace($RaceId, 0,0, 0);
                if (count($RaceUserList['RaceUserList']))
                {
                    $t = array();
                    foreach ($RaceUserList['RaceUserList'] as $ApplyId => $ApplyInfo)
                    {
                        if ((((strlen($BIB)>0) && strstr($ApplyInfo['BIB'], $BIB)) || (strlen($BIB)==0)) && (($RaceGroupId == 0) || (($RaceGroupId >0) && ($RaceGroupId == $ApplyInfo['RaceGroupId']))))
                        {
                            $t[] = $ApplyInfo;
                        }
                    }
                    $RaceUserList['RaceUserList'] = $t;
                    //重新获取比赛详情
                    $UserRaceTimingInfo = $this->oRace->GetUserRaceTimingInfo($RaceId);
                    foreach($UserRaceTimingInfo['Point'] as $k => $v)
                    {
                        foreach($v['UserList'] as $k2 => $v2)
                        {
                            if(($RaceGroupId == 0) || (($RaceGroupId >0) && ($RaceGroupId == $v2['RaceGroupId'])))
                            {
                                $UserRaceTimingInfo['Point'][$k]['UserList'][$k2]['Rank'] = $k2+1;
                            }
                            if ((strlen(trim($BIB)) && !strstr( $v2['BIB'], $BIB)) || (($RaceGroupId >0) && ($RaceGroupId != $v2['RaceGroupId'])))
                            {
                                unset($UserRaceTimingInfo['Point'][$k]['UserList'][$k2]);
                            }
                        }
                        $UserRaceTimingInfo['Point'][$k]['UserList'] = array_values($UserRaceTimingInfo['Point'][$k]['UserList']);
                    }
                    foreach($UserRaceTimingInfo['Total'] as $k => $v)
                    {
                        if(($RaceGroupId == 0) || (($RaceGroupId >0) && ($RaceGroupId == $v['RaceGroupId'])))
                        {
                            $UserRaceTimingInfo['Total'][$k]['Rank'] = $k+1;
                        }
                        if ((strlen(trim($BIB)) && !strstr( $v['BIB'], $BIB)) || (($RaceGroupId >0) && ($RaceGroupId != $v['RaceGroupId'])))
                        {
                            unset($UserRaceTimingInfo['Total'][$k]);
                        }
                    }
                    $UserRaceTimingInfo['Total'] = array_values($UserRaceTimingInfo['Total']);
                    foreach($UserRaceTimingInfo['Team'] as $k => $v)
                    {
                        if($k != $RaceGroupId)
                        {
                            unset($UserRaceTimingInfo['Team'][$k]);
                        }
                    }
                    //返回车手名单和车队列表
                    $result = array("return" => 1, "RaceUserList" => count($UserRaceTimingInfo['Total'])==0?$RaceUserList['RaceUserList']:array(), "UserRaceTimingInfo" => $UserRaceTimingInfo);
                } else {
                    //全部置为空
                    $result = array("return" => 0, "RaceUserList" => array(), "TeamList" => array(), "comment" => "尚无选手报名");
                }
            }
            else
            {
                //全部置为空
                $result = array("return" => 0, "RaceUserList" => array(), "TeamList" => array(), "comment" => "请指定一个有效的比赛ID");
            }
        }
        else
        {
            //全部置为空
            $result = array("return" => 0, "RaceUserList" => array(), "TeamList" => array(), "comment" => "请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }
    /*
    * 测试生成计时点
    */
    public function timingTextAction()
    {
        set_time_limit(0);
        $oMylaps = new Xrace_Mylaps();
        //格式化比赛ID
        $RaceId = abs(intval($this->request->RaceId));
        $Force = abs(intval($this->request->Force));
        $Type = trim($this->request->type);
        //if($Type=="new")
        //{
            $oMylaps->genMylapsTimingInfo($RaceId,$Force);
            die();
        //}
    }
    /*
    * 获取指定选手指定分站的签到信息
    */
    public function getRaceUserCheckInAction()
    {
        //用户ID
        $UserId = abs(intval($this->request->UserId));
        //分站ID
        $RaceStageId = abs(intval($this->request->RaceStageId));
        //获取用户签到信息
        $UserCheckInInfo = $this->oUser->getUserCheckInInfo($UserId,$RaceStageId);
        //如果找到记录
        if($UserCheckInInfo['RaceStageId'])
        {
            //获得分站信息
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,"RaceStageId,RaceStageName");
            //如果获取到分站信息
            if(!$RaceStageInfo['RaceStageId'])
            {
                $RaceStageInfo = array();
            }
            //获取用户信息
            $UserInfo = $this->oUser->getUserInfo($UserCheckInInfo['UserId'], 'UserId,name');
            //如果有获取到用户信息
            if (!$UserInfo['UserId'])
            {
                $UserInfo = array();
            }
            //根据用户获取报名记录
            $UserRaceList = $this->oUser->getRaceUserList(array('UserId' => $UserInfo['UserId'],'RaceStageId'=>$RaceStageInfo['RaceStageId']));
            //初始化空的比赛列表
            $RaceList = array();
            //初始化空的分组列表
            $RaceGroupList = array();
            //循环报名记录
            foreach($UserRaceList as $key => $ApplyInfo)
            {
                if(!isset($RaceList[$ApplyInfo['RaceId']]))
                {
                    $RaceInfo = $this->oRace->getRace($ApplyInfo['RaceId'], "RaceId,RaceName");
                    if (isset($RaceInfo['RaceId']))
                    {
                        $RaceList[$ApplyInfo['RaceId']] = $RaceInfo;
                    }
                }
                if(!isset($RaceGroupList[$ApplyInfo['RaceGroupId']]))
                {
                    $RaceGroupInfo = $this->oRace->getRaceGroup($ApplyInfo['RaceGroupId'], "RaceGroupId,RaceGroupName");
                    if (isset($RaceGroupInfo['RaceGroupId']))
                    {
                        $RaceGroupList[$ApplyInfo['RaceGroupId']] = $RaceGroupInfo;
                    }
                }
                $UserRaceList[$key]['RaceName'] = $RaceList[$ApplyInfo['RaceId']]['RaceName'];
                $UserRaceList[$key]['RaceGroupName'] = $RaceGroupList[$ApplyInfo['RaceGroupId']]['RaceGroupName'];
            }
            //全部置为空
            $result = array("return" => 1, "UserCheckInInfo"=>$UserCheckInInfo,"UserRaceList"=>$UserRaceList, "UserInfo" => $UserInfo, "RaceStageInfo" => $RaceStageInfo);
        }
        else
        {
            //全部置为空
            $result = array("return" => 0, "UserInfo" => array(), "RaceStageInfo" => array(), "comment" => "签到信息有误");
        }
        echo json_encode($result);
    }
    /*
     * 获取指定选手指定分站的签到信息
     */
    public function getRaceUserCheckInListAction()
    {
        //用户ID
        $UserId = abs(intval($this->request->UserId));
        //获取用户签到信息
        $UserCheckInList = $this->oUser->getRaceUserCheckInList(array('UserId'=>$UserId));
        //初始化空的分站列表
        $RaceStageList = array();
        foreach($UserCheckInList as $key => $CheckInInfo)
        {
            if(!isset($RaceStageList[$CheckInInfo['RaceStageId']]))
            {
                $RaceStageInfo = $this->oRace->getRaceStage($CheckInInfo['RaceStageId'], "RaceStageId,RaceStageName");
                if(isset($RaceStageInfo['RaceStageId']))
                {
                    $RaceStageList[$CheckInInfo['RaceStageId']] = $RaceStageInfo;
                }
            }
            $UserCheckInList[$key]['RaceStageName'] = $RaceStageList[$CheckInInfo['RaceStageId']]['RaceStageName'];
        }
        $result = array("return" => 1, "UserCheckInList" => $UserCheckInList);
        echo json_encode($result);
    }
    public function getCombinationListByStageAction()
    {
        //格式化赛事分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        $CombinationList = $this->oRace->getRaceCombinationList(array("RaceStageId"=>$RaceStageId));
        foreach($CombinationList as $Id => $CombinationInfo)
        {
            print_R($CombinationInfo);
        }
    }
}