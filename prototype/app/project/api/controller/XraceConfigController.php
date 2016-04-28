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
        $this->oUser = new Xrace_User();
    }

    /**
     *获取所有赛事的列表
     */
    public function getRaceCatalogListAction()
    {
        //是否显示说明注释 默认为1
        $GetComment = isset($this->request->GetComment) ? abs(intval($this->request->GetComment)) : 1;
        //获得赛事列表
        $RaceCatalogList = $this->oRace->getRaceCatalogList();
        //如果没有返回值,默认为空数组
        if (!is_array($RaceCatalogList)) {
            $RaceCatalogList = array();
        }
        //循环赛事列表数组
        foreach ($RaceCatalogList as $RaceCatalogId => $RaceCatalogInfo) {
            //如果有输出赛事图标的绝对路径
            if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon'])) {
                //删除
                unset($RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon']);
            }
            //如果有输出赛事图标的相对路径
            if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon_root'])) {
                //拼接上ADMIN站点的域名
                $RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon'] = $this->config->adminUrl . $RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon_root'];
                //删除原有数据
                unset($RaceCatalogList[$RaceCatalogId]['comment']['RaceCatalogIcon_root']);
            }
            //如果参数不显示说明文字
            if ($GetComment != 1) {
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
        //是否显示说明注释 默认为1
        $GetComment = isset($this->request->GetComment) ? abs(intval($this->request->GetComment)) : 1;
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId) ? abs(intval($this->request->RaceCatalogId)) : 0;
        //赛事ID必须大于0
        if ($RaceCatalogId) {
            //获取赛事信息
            $RaceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId);
            //检测主键存在,否则值为空
            if (isset($RaceCatalogInfo['RaceCatalogId'])) {
                //解包数组
                $RaceCatalogInfo['comment'] = isset($RaceCatalogInfo['comment']) ? json_decode($RaceCatalogInfo['comment'], true) : array();
                //如果有输出赛事图标的绝对路径
                if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon'])) {
                    //删除
                    unset($RaceCatalogInfo['comment']['RaceCatalogIcon']);
                }
                //如果有输出赛事图标的相对路径
                if (isset($RaceCatalogInfo['comment']['RaceCatalogIcon_root'])) {
                    //拼接上ADMIN站点的域名
                    $RaceCatalogInfo['comment']['RaceCatalogIcon'] = $this->config->adminUrl . $RaceCatalogInfo['comment']['RaceCatalogIcon_root'];
                    //删除原有数据
                    unset($RaceCatalogInfo['comment']['RaceCatalogIcon_root']);
                }
                //根据赛事获取组别列表
                $RaceGroupList = isset($RaceCatalogInfo['RaceCatalogId']) ? $this->oRace->getRaceGroupList($RaceCatalogInfo['RaceCatalogId'], "RaceGroupId,RaceGroupName") : array();
                //根据赛事获取分站列表
                $RaceStageList = isset($RaceCatalogInfo['RaceCatalogId']) ? $this->oRace->getRaceStageList($RaceCatalogInfo['RaceCatalogId'], "RaceStageId,RaceStageName") : array();
                //如果参数不显示说明文字
                if ($GetComment != 1) {
                    //则删除该字段
                    unset($RaceCatalogInfo['RaceCatalogComment']);
                }
                //结果数组
                $result = array("return" => 1, "RaceCatalogInfo" => $RaceCatalogInfo, 'RaceGroupList' => $RaceGroupList, 'RaceStageList' => $RaceStageList);
            } else {
                //全部置为空
                $result = array("return" => 0, "RaceCatalog" => array(), 'RaceGroupList' => array(), 'RaceStageList' => array(), "comment" => "请指定一个有效的赛事ID");
            }

        } else {
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
        //格式化赛事ID,默认为0
        $RaceCatalogId = isset($this->request->RaceCatalogId) ? abs(intval($this->request->RaceCatalogId)) : 0;
        $RaceStageStatus = isset($this->request->RaceStageStatus) ? abs(intval($this->request->RaceStageStatus)) : 0;
        //赛事ID必须大于0
        if ($RaceCatalogId) {
            //获得分站列表
            $RaceStageList = $this->oRace->getRaceStageList($RaceCatalogId);
            //如果没有返回值,默认为空数组
            if (!is_array($RaceStageList)) {
                //全部置为空
                $result = array("return" => 0, "RaceStageList" => array(), "comment" => "请指定一个有效的赛事ID");
            } else {
                //初始化一个空的产品列表
                $ProductList = array();
                //循环分站数组
                foreach ($RaceStageList as $RaceStageId => $RaceStageInfo) {
                    //说明文字解码
                    $RaceStageList[$RaceStageId]['RaceStageComment'] = urldecode($RaceStageInfo['RaceStageComment']);
                    //解包数组
                    $RaceStageList[$RaceStageId]['comment'] = json_decode($RaceStageInfo['comment'], true);
                    //解包图片数组
                    $RaceStageList[$RaceStageId]['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'], true);
                    //获取当前比赛的时间状态信息
                    $RaceStageList[$RaceStageId]['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId, 0);
                    if (($RaceStageStatus > 0 && $RaceStageList[$RaceStageId]['RaceStageStatus']['StageStatus'] == $RaceStageStatus) || ($RaceStageStatus == 0)) {
                        //如果有配置分站图片
                        if (isset($RaceStageList[$RaceStageId]['RaceStageIcon'])) {
                            //循环图片列表
                            foreach ($RaceStageList[$RaceStageId]['RaceStageIcon'] as $IconId => $IconInfo) {
                                //拼接上ADMIN站点的域名
                                $RaceStageList[$RaceStageId]['comment']['RaceStageIconList'][$IconId]['RaceStageIcon'] = $this->config->adminUrl . $IconInfo['RaceStageIcon_root'];
                            }
                            //删除原有数据
                            unset($RaceStageList[$RaceStageId]['RaceStageIcon']);
                        }

                        //如果有配置分组信息，暂不输出
                        if (isset($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'])) {
                            //循环图片列表
                            foreach ($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'] as $RaceGroupId) {
                                //获取赛事分组基本信息
                                $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId, "RaceGroupId,RaceGroupName");
                                //如果有获取到分组信息
                                if ($RaceGroupInfo['RaceGroupId']) {
                                    //提取分组名称
                                    $RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'][$RaceGroupId] = $RaceGroupInfo;
                                } else {
                                    //删除
                                    unset($RaceStageList[$RaceStageId]['comment']['SelectedRaceGroup'][$RaceGroupId]);
                                }
                            }
                        }
                        //如果有配置分站的价格，则格式化价格
                        $RaceStageList[$RaceStageId]['comment']['PriceList'] = isset($RaceStageList[$RaceStageId]['comment']['PriceList']) ? $this->oRace->getPriceList($RaceStageList[$RaceStageId]['comment']['PriceList']) : array();
                        //不输出产品相关信息
                        unset($RaceStageList[$RaceStageId]['comment']['SelectedProductList']);
                        /*
                        //如果有配置产品信息,暂不输出
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
                        */
                    } else {
                        unset($RaceStageList[$RaceStageId]);
                    }


                }
                //结果数组
                $result = array("return" => 1, "RaceStageList" => $RaceStageList);
            }
        } else {
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
        //格式化赛事分站ID,默认为0
        $RaceStageId = isset($this->request->RaceStageId) ? abs(intval($this->request->RaceStageId)) : 0;
        //格式化用户ID,默认为空
        $UserId = isset($this->request->UserId) ? trim($this->request->UserId) : "";
        //赛事分站D必须大于0
        if ($RaceStageId) {
            //获得分站信息
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
            //检测主键存在,否则值为空
            $RaceStageInfo = isset($RaceStageInfo['RaceStageId']) ? $RaceStageInfo : array();
            if (isset($RaceStageInfo['RaceStageId'])) {
                //说明文字解码
                $RaceStageInfo['RaceStageComment'] = urldecode($RaceStageInfo['RaceStageComment']);
                //解包数组
                $RaceStageInfo['comment'] = isset($RaceStageInfo['comment']) ? json_decode($RaceStageInfo['comment'], true) : array();
                //解包图片数组
                $RaceStageInfo['RaceStageIcon'] = isset($RaceStageInfo['RaceStageIcon']) ? json_decode($RaceStageInfo['RaceStageIcon'], true) : array();
                //如果有配置分站图片
                if (isset($RaceStageInfo['RaceStageIcon'])) {
                    //循环图片列表
                    foreach ($RaceStageInfo['RaceStageIcon'] as $IconId => $IconInfo) {
                        //拼接上ADMIN站点的域名
                        $RaceStageInfo['comment']['RaceStageIconList'][$IconId]['RaceStageIcon'] = $this->config->adminUrl . $IconInfo['RaceStageIcon_root'];
                    }
                    //删除原有数据
                    unset($RaceStageInfo['RaceStageIcon']);
                }
                //如果有配置分组信息
                if (isset($RaceStageInfo['comment']['SelectedRaceGroup'])) {
                    //循环图片列表
                    foreach ($RaceStageInfo['comment']['SelectedRaceGroup'] as $RaceGroupId) {

                        //获取赛事分组基本信息
                        $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId, "RaceGroupId,RaceGroupName,comment");
                        //如果有获取到分组信息
                        if (isset($RaceGroupInfo['RaceGroupId'])) {
                            //默认当前组别可选
                            $RaceGroupInfo['checkable'] = true;
                            //数据解包
                            $RaceGroupInfo['comment'] = json_decode($RaceGroupInfo['comment'], true);
                            //执照条件的审核
                            $RaceGroupInfo['LicenseList'] = $this->oRace->raceLicenseCheck($RaceGroupInfo['comment']['LicenseList'], $UserId, $RaceStageInfo, $RaceGroupInfo);
                            foreach ($RaceGroupInfo['LicenseList'] as $k => $v) {
                                //如果发现条件为不可选
                                if (isset($v['checked']) && $v['checked'] == false) {
                                    //将当前组别置为不可选
                                    $RaceGroupInfo['checkable'] = false;
                                    break;
                                }
                            }
                            //格式化执照的条件，供显示
                            $LicenseListText = $this->oRace->ParthRaceLicenseListToHtml($RaceGroupInfo['LicenseList'], 0, 0, 1);
                            //循环执照审核条件的文字
                            foreach ($LicenseListText as $key => $LicenseInfo) {
                                //分别置入权限审核列表
                                $RaceGroupInfo['LicenseList'][$key]['LicenseTextArr'] = $LicenseInfo;
                            }
                            //提取分组名称
                            unset($RaceGroupInfo['comment']);
                            $RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId] = $RaceGroupInfo;
                        } else {
                            //删除
                            unset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]);
                        }
                    }
                }
                //如果有配置分组信息
                if (isset($RaceStageInfo['comment']['SelectedProductList'])) {
                    //初始化一个空的产品列表
                    $ProductList = array();
                    //循环产品列表
                    foreach ($RaceStageInfo['comment']['SelectedProductList'] as $ProductId => $Product) {
                        //如果产品列表中没有此产品
                        if (!isset($ProductList[$ProductId])) {
                            //获取产品信息
                            $ProductInfo = $this->oProduct->getProduct($ProductId, "ProductId,ProductName");
                            //如果产品信息获取到
                            if (isset($ProductInfo['ProductId'])) {
                                //放入产品列表中
                                $ProductList[$ProductId] = $ProductInfo;
                            } else {
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
                $RaceStageInfo['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId, 0);
                //处理价格列表
                $RaceStageInfo['comment']['PriceList'] = ($RaceStageInfo['comment']['PriceList']);
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
            if (isset($RaceGroupInfo['comment']['LicenseList'])) {
                //暂时先删除,其后版本再添加
                unset($RaceGroupInfo['comment']['LicenseList']);
            }
            //结果数组
            $result = array("return" => 1, "RaceGroupInfo" => $RaceGroupInfo);
        } else {
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
        //赛事分站和赛事组别ID必须大于0
        if ($RaceStageId && $RaceGroupId) {
            //获得比赛列表
            $RaceList = $this->oRace->getRaceList($RaceStageId, $RaceGroupId, "RaceId,RaceTypeId,RouteInfo,RaceName,PriceList,SingleUser,TeamUser,StartTime,EndTime,ApplyStartTime,ApplyEndTime,comment,RaceComment,MustSelect");
            if (!is_array($RaceList)) {
                $RaceList = array();
            }
            //获取运动类型类表
            $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
            //解包数组
            foreach ($RaceList as $RaceId => $RaceInfo) {
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
                if (isset($RaceTypeInfo['RaceTypeId'])) {
                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                    //如果有输比赛类型图标的相对路径
                    if (isset($RaceTypeInfo['comment']['RaceTypeIcon_root'])) {
                        //拼接上ADMIN站点的域名
                        $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl . ($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                    }
                    //删除原有数据
                    unset($RaceTypeInfo['comment']);
                }
                //存入结果数组
                $RaceList[$RaceId]['RaceTypeInfo'] = $RaceTypeInfo;
                //如果有配置运动分段
                if (isset($RaceInfo['comment']['DetailList'])) {
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
                            if (isset($TimingInfo['TimingId'])) {
                                //数据解包
                                $TimingInfo['comment'] = isset($TimingInfo['comment']) ? json_decode($TimingInfo['comment'], true) : array();
                                //循环计时点信息
                                foreach ($TimingInfo['comment'] as $tid => $tInfo) {
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
                        } else {
                            unset($RaceList[$RaceId]['comment']['DetailList'][$detailId]);
                        }
                    }
                } else {
                    //初始化为空数组
                    $RaceList[$RaceId]['comment']['DetailList'] = array();
                }
            }
            //结果数组 如果列表有数据则为成功，否则为失败
            $result = array("return" => count($RaceList) ? 1 : 0, "RaceList" => $RaceList);
        } else {
            $result = array("return" => 0, "RaceList" => array());
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
        if ($RaceId) {
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId);
            //检测主键存在,否则值为空
            if (isset($RaceInfo['RaceId'])) {
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
                if ($RaceTypeInfo['RaceTypeId']) {
                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                    //如果有输比赛类型图标的相对路径
                    if (isset($RaceTypeInfo['comment']['RaceTypeIcon_root'])) {
                        //拼接上ADMIN站点的域名
                        $RaceTypeInfo['RaceTypeIcon'] = $this->config->adminUrl . ($RaceTypeInfo['comment']['RaceTypeIcon_root']);
                    }
                    //删除原有数据
                    unset($RaceTypeInfo['comment']);
                }
                //存入结果数组
                $RaceInfo['RaceTypeInfo'] = $RaceTypeInfo;
                //如果有配置运动分段
                if (isset($RaceInfo['comment']['DetailList'])) {
                    //获取运动类型类表
                    $SportsTypeList = $this->oSports->getAllSportsTypeList("SportsTypeId,SportsTypeName");
                    //循环运动分段
                    foreach ($RaceInfo['comment']['DetailList'] as $detailId => $detailInfo) {
                        //如果有配置过该运动分段
                        if (isset($SportsTypeList[$detailInfo['SportsTypeId']])) {
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
                } else {
                    //初始化为空数组
                    $RaceInfo['comment']['DetailList'] = array();
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
    public function getRaceTeamListAction()
    {
        //格式化赛事ID
        $RaceCatalogId = abs(intval($this->request->RaceCatalogId));
        //格式化赛事ID
        $RaceGroupId = abs(intval($this->request->RaceGroupId));
        //赛事ID必须大于0
        if ($RaceCatalogId) {
            //获取赛事信息
            $RaceCatalogInfo = $this->oRace->getRaceCatalog($RaceCatalogId);
            //检测主键存在,否则值为空
            if (isset($RaceCatalogInfo['RaceCatalogId'])) {
                //获取赛事组别信息
                $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId);
                //检测主键存在,否则值为空
                if (isset($RaceGroupInfo['RaceGroupId']) && ($RaceGroupInfo['RaceCatalogId'] == $RaceCatalogInfo['RaceCatalogId'])) {
                    $oTeam = new Xrace_Team();
                    //获取分组相关的队伍列表
                    $RaceTeamList = $oTeam->getRaceTeamListByGroup($RaceGroupInfo, 1);
                    //结果数组
                    if (count($RaceTeamList['RaceTeamList'])) {
                        $result = array("return" => 1, "RaceTeamList" => $RaceTeamList['RaceTeamList']);
                    } else {
                        $result = array("return" => 0, "RaceTeamList" => array(), "comment" => "组别下并未有队伍");
                    }
                } else {
                    //全部置为空
                    $result = array("return" => 0, "RaceTeamList" => array(), "comment" => "请指定一个有效的分组ID");
                }

            } else {
                //全部置为空
                $result = array("return" => 0, "RaceTeamList" => array(), "comment" => "请指定一个有效的赛事ID");
            }
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
                $RaceUserList = $this->oUser->getRaceUserListByRace($RaceId, $TeamId, 1);
                if (count($RaceUserList['RaceUserList'])) {
                    //返回车手名单和车队列表
                    $result = array("return" => 1, "RaceUserList" => $RaceUserList['RaceUserList'], "RaceTeamList" => $RaceUserList['RaceTeamList']);
                } else {
                    //全部置为空
                    $result = array("return" => 0, "RaceUserList" => array(), "RaceTeamList" => array(), "comment" => "尚无选手报名");
                }
            } else {
                //全部置为空
                $result = array("return" => 0, "RaceUserList" => array(), "RaceTeamList" => array(), "comment" => "请指定一个有效的比赛ID");
            }
        } else {
            //全部置为空
            $result = array("return" => 0, "RaceUserList" => array(), "RaceTeamList" => array(), "comment" => "请指定一个有效的赛事ID");
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
        $UserId = abs(intval($this->request->UserId));
        //用户BIB
        $BIB = trim($this->request->BIB);
        if (!$UserId) {
            //根据用户的BIB获取比赛报名信息
            $UserApplyInfo = $this->oUser->getRaceApplyUserInfoByBIB($RaceId, $BIB);
            //如果查询到报名记录
            if ($UserApplyInfo['ApplyId']) {
                //保存用户ID
                $UserId = $UserApplyInfo['UserId'];
            }
        }
        //获取用户比赛的详情
        $UserRaceInfo = $this->oRace->getUserRaceInfo($RaceId, $UserId);
        //如果有查出数据
        if (!isset($UserRaceInfo['UserInfo'])) {
            //重新生成该场比赛所有人的配置数据
            $this->oRace->genRaceLogToText($RaceId, $UserId);
            //重新获取比赛详情
            $UserRaceInfo = $this->oRace->getUserRaceInfo($RaceId, $UserId);
        }
        $result = array("return" => isset($UserRaceInfo['ApplyInfo']) ? 1 : 0, "UserRaceInfo" => $UserRaceInfo);
        echo json_encode($result);
    }

    /*
     * 获取指定用户的报名记录
    */
    public function getUserRaceListAction()
    {
        //比赛ID
        $UserId = abs(intval($this->request->UserId));
        if ($UserId) {
            //获取用户信息
            $UserInfo = $this->oUser->getUserInfo($UserId, 'user_id,name');
            //如果有获取到用户信息
            if ($UserInfo['user_id']) {
                //根据用户获取报名记录
                $UserApplyList = $this->oUser->getRaceUserList(array('UserId' => $UserInfo['user_id']));
                //获取赛事列表
                $RaceCatalogList = $this->oRace->getRaceCatalogList("RaceCatalogId,RaceCatalogName");
                $RaceGroupList = array();
                $RaceStageList = array();
                $RaceTypeList = array();
                //循环报名列表
                foreach ($UserApplyList as $key => $ApplyInfo) {
                    if (isset($RaceCatalogList[$ApplyInfo['RaceCatalogId']])) {
                        $UserApplyList[$key]['comment'] = json_decode($ApplyInfo['comment'], true);
                        $UserApplyList[$key]['RaceCatalogName'] = $RaceCatalogList[$ApplyInfo['RaceCatalogId']]['RaceCatalogName'];
                        if (!isset($RaceGroupList[$ApplyInfo['RaceGroupId']])) {
                            $RaceGroupInfo = $this->oRace->getRaceGroup($ApplyInfo['RaceGroupId'], 'RaceGroupId,RaceGroupName');
                            if (isset($RaceGroupInfo['RaceGroupId'])) {
                                $RaceGroupList[$ApplyInfo['RaceGroupId']] = $RaceGroupInfo;
                            } else {
                                unset($UserApplyList[$key]);
                            }
                        }
                        $UserApplyList[$key]['RaceGroupName'] = $RaceGroupList[$ApplyInfo['RaceGroupId']]['RaceGroupName'];

                        if (!isset($RaceStageList[$ApplyInfo['RaceStageId']])) {
                            $RaceStageInfo = $this->oRace->getRaceStage($ApplyInfo['RaceStageId'], 'RaceStageId,RaceStageName');
                            if (isset($RaceStageInfo['RaceStageId'])) {
                                $RaceStageList[$ApplyInfo['RaceStageId']] = $RaceStageInfo;
                            } else {
                                unset($UserApplyList[$key]);
                            }
                        }
                        $UserApplyList[$key]['RaceStageName'] = $RaceStageList[$ApplyInfo['RaceStageId']]['RaceStageName'];

                        $RaceInfo = $this->oRace->getRace($ApplyInfo['RaceId'], "*");
                        if (isset($RaceInfo['RaceId'])) {
                            $UserApplyList[$key]['RaceName'] = $RaceInfo['RaceName'];
                            if (!isset($RaceTypeList[$RaceInfo['RaceTypeId']])) {
                                $RaceTypeInfo = $this->oRace->getRaceType($RaceInfo['RaceTypeId'], '*');
                                if (isset($RaceTypeInfo['RaceTypeId'])) {
                                    $RaceTypeInfo['comment'] = json_decode($RaceTypeInfo['comment'], true);
                                    //拼接上ADMIN站点的域名
                                    $RaceTypeInfo['comment']['RaceTypeIcon'] = $this->config->adminUrl . $RaceTypeInfo['comment']['RaceTypeIcon_root'];
                                    $RaceTypeList[$RaceInfo['RaceTypeId']] = $RaceTypeInfo;
                                } else {
                                    unset($UserApplyList[$key]);
                                }
                            }
                            $UserApplyList[$key]['RaceTypeIcon'] = $RaceTypeList[$RaceInfo['RaceTypeId']]['comment']['RaceTypeIcon'];
                            $UserApplyList[$key]['RaceTypeName'] = $RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName'];
                        } else {
                            unset($UserApplyList[$key]);
                        }
                    } else {
                        unset($UserApplyList[$key]);
                    }
                }
                $result = array("return" => 1, "UserRaceList" => $UserApplyList);
            } else {
                $result = array("return" => 0, "UserRaceList" => array(), "comment" => "无此用户");
            }
        } else {
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
        //BIB号码
        $BIB = trim($this->request->BIB);
        //赛事ID必须大于0
        if ($RaceId) {
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId);
            //检测主键存在,否则值为空
            if (isset($RaceInfo['RaceId'])) {
                //获取选手和车队名单
                $RaceUserList = $this->oUser->getRaceUserListByRace($RaceId, 0, 1);
                if (count($RaceUserList['RaceUserList'])) {
                    $t = array();
                    foreach ($RaceUserList['RaceUserList'] as $ApplyId => $ApplyInfo) {
                        if ((strlen(trim($BIB)) && strstr($ApplyInfo['BIB'], $BIB))) {
                            $t[] = $ApplyInfo;
                        }
                    }
                    $RaceUserList['RaceUserList'] = $t;
                    //重新获取比赛详情
                    $UserRaceTimingInfo = $this->oRace->GetUserRaceTimingInfo($RaceId);
                    //返回车手名单和车队列表
                    $result = array("return" => 1, "RaceUserList" => $RaceUserList['RaceUserList'], "UserRaceTimingInfo" => $UserRaceTimingInfo);
                } else {
                    //全部置为空
                    $result = array("return" => 0, "RaceUserList" => array(), "RaceTeamList" => array(), "comment" => "尚无选手报名");
                }
            } else {
                //全部置为空
                $result = array("return" => 0, "RaceUserList" => array(), "RaceTeamList" => array(), "comment" => "请指定一个有效的比赛ID");
            }
        } else {
            //全部置为空
            $result = array("return" => 0, "RaceUserList" => array(), "RaceTeamList" => array(), "comment" => "请指定一个有效的赛事ID");
        }
        echo json_encode($result);
    }

    /*
    * 测试生成计时点
    */
    public function timingTextAction()
    {
        //格式化比赛ID
        $RaceId = abs(intval($this->request->RaceId));
        $RaceInfo = $this->oRace->getRace($RaceId);
        $this->oRace->genRaceLogToText($RaceId);
        //获取选手和车队名单
        $RaceUserList = $this->oUser->getRaceUserListByRace($RaceId, 0, 0);
        $ChipList = array();
        $UserList = array();
        foreach ($RaceUserList['RaceUserList'] as $ApplyId => $ApplyInfo) {
            if (trim($ApplyInfo['ChipId'])) {
                $ChipList[] = "'" . $ApplyInfo['ChipId'] . "'";
                $UserList[$ApplyInfo['ChipId']]['UserId'] = $ApplyInfo['UserId'];
                $UserList[$ApplyInfo['ChipId']]['Name'] = $ApplyInfo['Name'];
                $UserList[$ApplyInfo['ChipId']]['BIB'] = $ApplyInfo['BIB'];
            }
        }
        $oMylaps = new Xrace_Mylaps();
        $i = 1;
        $pageSize = 1000;
        $Count = $pageSize;
        $currentChip = "";
        while ($Count == $pageSize) {
            $params = array('page' => $i, 'pageSize' => $pageSize, 'ChipList' => count($ChipList) ? implode(",", $ChipList) : "0");
            $TimingList = $oMylaps->getTimingData($params);
            foreach ($TimingList as $Key => $TimingInfo) {
                if ($currentChip != $TimingInfo['Chip']) {
                    $currentChip = $TimingInfo['Chip'];
                    echo $currentChip . "--------------" . $UserList[$TimingInfo['Chip']]['UserId'] . "<br>";
                }
                $TimingInfo['ChipTime'] = strtotime($TimingInfo['ChipTime']) - 8 * 3600;
                echo ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000) . "-" . date("Y-m-d H:i:s", $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000) . "<br>";
                if ($TimingInfo['ChipTime'] >= strtotime($RaceInfo['StartTime'])) {
                    $UserRaceInfo = $this->oRace->getUserRaceInfo($RaceId, $UserList[$TimingInfo['Chip']]['UserId']);

                    if (!isset($UserRaceInfo['CurrentPoint'])) {
                        $c = 1;
                        $i = 1;
                        $FirstPointInfo = $UserRaceInfo['Point'][$i];
                        if ($FirstPointInfo['ChipId'] == $TimingInfo['Location']) {
                            $UserRaceInfo['CurrentPoint'] = $i;
                            $UserRaceInfo['NextPoint'] = $i + 1;
                            $UserRaceInfo['Point'][$i]['inTime'] = $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000;
                            $filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/" . "UserList" . "/";
                            $fileName = $UserList[$TimingInfo['Chip']]['UserId'] . ".php";
                            //生成配置文件
                            Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfo, "Timing");

                            $UserRaceInfoList = $this->oRace->getUserRaceInfoList($RaceId);
                            $UserRaceInfoList['Point'][$i]['inTime'] = $UserRaceInfoList['Point'][$i]['inTime'] == 0 ? ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000) : min(sprintf("%0.4f", $UserRaceInfoList['Point'][$i]['inTime']), $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000);

                            if (isset($UserRaceInfoList['Point'][$i]['UserList']) && count($UserRaceInfoList['Point'][$i]['UserList'])) {
                                $UserRaceInfoList['Point'][$i]['UserList'][count($UserRaceInfoList['Point'][$i]['UserList']) + 1] = array("TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                            } else {
                                $UserRaceInfoList['Point'][$i]['UserList'][1] = array("TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                            }
                            $t = array();
                            foreach ($UserRaceInfoList['Point'][$i]['UserList'] as $k => $v) {
                                $UserRaceInfoList['Point'][$i]['UserList'][$k]['TimeLag'] = abs(sprintf("%0.4f", $UserRaceInfoList['Point'][$i]['inTime']) - $v['inTime']);
                                $t[$k] = $UserRaceInfoList['Point'][$i]['UserList'][$k]['TimeLag'];
                            }
                            array_multisort($t, SORT_ASC, $UserRaceInfoList['Point'][$i]['UserList']);

                            if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total'])) {
                                $found = 0;
                                foreach ($UserRaceInfoList['Total'] as $k => $v) {
                                    if ($v['UserId'] == $UserList[$TimingInfo['Chip']]['UserId']) {
                                        $UserRaceInfoList['Total'][$k] = array("CurrentPosition" => 1, "TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                                        $found = 1;
                                        break;
                                    }
                                }
                                if ($found == 0) {
                                    $UserRaceInfoList['Total'][count($UserRaceInfoList['Total']) + 1] = array("CurrentPosition" => 1, "TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                                }
                            } else {
                                $UserRaceInfoList['Total'][1] = array("CurrentPosition" => 1, "TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                            }
                            $t = array();
                            foreach ($UserRaceInfoList['Total'] as $k => $v) {
                                $t1[$k] = $v['CurrentPosition'];
                                $t2[$k] = $v['TotalTime'];
                            }
                            array_multisort($t1, SORT_DESC, $t2, SORT_ASC, $UserRaceInfoList['Total']);
                            $filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/";
                            $fileName = "Total" . ".php";
                            //生成配置文件
                            Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfoList, "Timing");


                        }
                    } else {
                        $c = $UserRaceInfo['CurrentPoint'];
                        do {
                            if (isset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']])) {
                                $CurrentPointInfo = $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']];
                                $timeLag = sprintf("%20.4f", $CurrentPointInfo['inTime']) - ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000) . "<br>";
                                if (abs($timeLag) <= 30) {
                                    break;
                                }
                            } else {
                                break;
                            }
                        } while
                        (
                            (($CurrentPointInfo['ChipId'] != $TimingInfo['Location']) || (($CurrentPointInfo['ChipId'] == $TimingInfo['Location']) && ($CurrentPointInfo['inTime'] != ""))) && ($UserRaceInfo['CurrentPoint']++)
                        );
                        if ($CurrentPointInfo['ChipId'] && $c != $UserRaceInfo['CurrentPoint']) {
                            $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] = $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000;
                            $filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/" . "UserList" . "/";
                            $fileName = $UserList[$TimingInfo['Chip']]['UserId'] . ".php";
                            //生成配置文件
                            Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfo, "Timing");
                            $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] == 0 ? ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000) : min(sprintf("%0.4f", $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime']), $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000);

                            if (isset($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']) && count($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'])) {
                                $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][count($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']) + 1] = array("TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                            } else {
                                $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][1] = array("TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                            }
                            $t = array();
                            foreach ($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'] as $k => $v) {
                                $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['TimeLag'] = abs(sprintf("%0.4f", $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime']) - $v['inTime']);
                                $t[$k] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['TimeLag'];

                            }
                            array_multisort($t, SORT_ASC, $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']);

                            if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total'])) {
                                $found = 0;
                                foreach ($UserRaceInfoList['Total'] as $k => $v) {
                                    if ($v['UserId'] == $UserList[$TimingInfo['Chip']]['UserId']) {
                                        $UserRaceInfoList['Total'][$k] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                                        $found = 1;
                                        break;
                                    }
                                }
                                if ($found == 0) {
                                    $UserRaceInfoList['Total'][count($UserRaceInfoList['Total']) + 1] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                                }
                            } else {
                                $UserRaceInfoList['Total'][1] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "TotalTime" => ($TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000 - strtotime($RaceInfo['StartTime'])), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId']);
                            }
                            $t = array();
                            foreach ($UserRaceInfoList['Total'] as $k => $v) {
                                $t1[$k] = $v['CurrentPosition'];
                                $t2[$k] = $v['TotalTime'];
                            }
                            array_multisort($t1, SORT_DESC, $t2, SORT_ASC, $UserRaceInfoList['Total']);


                            $filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/";
                            $fileName = "Total" . ".php";
                            //生成配置文件
                            Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfoList, "Timing");

                            $UserRaceInfo['NextPoint'] = $UserRaceInfo['CurrentPoint'];
                        }
                    }
                }
            }
            $Count = count($TimingList);
            $i++;
        }
    }
}
    //insert into xrace.user_race (RaceCatalogId,UserId,BIB,ChipId,RaceGroupId,RaceStageId,RaceId) select 10,u.user_id,BIB,r.chipcode,g.RaceGroupId,g.RaceStageId,g.RaceId from mylaps.zs_user as r,xrace_config.config_race as g,xrace.user_profile as u where r.Race=g.RaceName and u.name=r.Name}