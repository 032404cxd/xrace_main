<?php
/**
 * 赛事分暂管理
 * @author Chen<cxd032404@hotmail.com>
 */

class Xrace_RaceStageController extends AbstractController
{
	/**赛事分站:
	 * @var string
	 */
	protected $sign = '?ctl=xrace/race.stage';
	/**
	 * race对象
	 * @var object
	 */
	protected $oRace;
        /**
	 * product对象
	 * @var object
	 */
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
		$this->oProduct = new Xrace_Product();
	}
	//赛事分站列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//获取站点根域名
			$RootUrl = "http://".$_SERVER['HTTP_HOST'];
			//赛事ID
			$RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//赛事分站列表
			$RaceStageArr = $this->oRace->getRaceStageList($RaceCatalogId);
			//赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceCatalogId,'RaceGroupId,RaceGroupName');
			//产品类型列表
			$ProductTypeList = $this->oProduct->getProductTypeList($RaceCatalogId,'ProductTypeId,ProductTypeName');
			//初始化一个空的赛事分站列表
			$RaceStageList = array();
			//循环赛事分站列表
			foreach($RaceStageArr as $RaceStageId => $RaceStageInfo)
			{
				$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId] = $RaceStageInfo;
				//计算分站数量，用于页面跨行显示
				$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageCount'] = isset($RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageCount'])?$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageCount']+1:1;
				$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RowCount'] = $RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageCount']+1;
				//如果相关赛事ID有效
				if(isset($RaceCatalogList[$RaceStageInfo['RaceCatalogId']]))
				{
					//获取赛事ID
					$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'] = isset($RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'])?$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName']:$RaceCatalogList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'];
					//解包压缩数组
					$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
					//解包压缩数组
					$RaceStageInfo['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'],true);
					$t = array();
					//如果有已经选择的赛事组别
					if(isset($RaceStageInfo['comment']['SelectedRaceGroup']) && is_array($RaceStageInfo['comment']['SelectedRaceGroup']))
					{
						//循环各个组别
						foreach($RaceStageInfo['comment']['SelectedRaceGroup'] as $k => $v)
						{
							//获取各个组别的比赛场次数量
							$RaceCount = $this->oRace->getRaceCount($RaceStageInfo['RaceStageId'],$v);
							//如果有配置比赛场次
							if($RaceCount>0)
							{
								//添加场次数量
								$Suffix = "(".$RaceCount.")";
							}
							else
							{
								$Suffix = "";
							}
							//如果赛事组别配置有效
							if(isset($RaceGroupList[$v]))
							{
								//生成到比赛详情页面的链接
								$t[$k] = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$RaceStageInfo['RaceStageId'],'RaceGroupId'=>$v)) ."'>".$RaceGroupList[$v]['RaceGroupName'].$Suffix."</a>";
							}
						}
					}
					//如果检查后有至少一个有效的赛事组别配置
					if(count($t))
					{
						//生成页面显示的数组
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedGroupList'] = implode("/",$t);
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['GroupCount'] = count($t);
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RowCount'] = $RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['GroupCount']+1;
					}
					else
					{
						//生成默认的入口
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedGroupList'] = "尚未配置";
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['GroupCount'] = 0;
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RowCount'] = 1;
					}
					// 初始化一个临时数组
					$t = array();
					$t2 = array();
					//如果有已经选择的产品
					if(isset($RaceStageInfo['comment']['SelectedProductList']) && is_array($RaceStageInfo['comment']['SelectedProductList']))
					{
						//循环已选择的产品列表
						foreach($RaceStageInfo['comment']['SelectedProductList'] as $ProductId => $ProductConfig)
						{
							//如果缓存中没有产品数据
							if(!isset($ProductList[$ProductId]))
							{
								//获取产品数据
								$ProductInfo = $this->oProduct->getProduct($ProductId,"ProductId,ProductTypeId");
								 //如果产品获取有效
								if(isset($ProductInfo['ProductId']))
								{
									//置入缓存
									$ProductList[$ProductId] = $ProductInfo;
								}
							}
							else
							{
								$ProductInfo = $ProductList[$ProductId];
							}
							//如果获取到的产品的分类有效
							if(isset($ProductTypeList[$ProductInfo['ProductTypeId']]))
							{
								//如果缓存中的产品类型有累加数量
								if(isset($t[$ProductInfo['ProductTypeId']]))
								{
									//数量累加
									$t[$ProductInfo['ProductTypeId']]['ProductCount']++;
								}
								else
								{
									//初始化数量
									$t[$ProductInfo['ProductTypeId']] = array("ProductCount"=>1,"ProductTypeName"=>$ProductTypeList[$ProductInfo['ProductTypeId']]['ProductTypeName']);
								}
								$t2[$ProductInfo['ProductTypeId']] = $t[$ProductInfo['ProductTypeId']]['ProductTypeName']."(".$t[$ProductInfo['ProductTypeId']]['ProductCount'].")";
							}
						}
					}
					//拼接页面显示的数量
					$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedProductList'] = count($t2)>0?implode("/", $t2):"尚未配置";
				}
				else
				{
					$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'] = 	"未定义";
				}
				if(isset($RaceStageInfo['RaceStageIcon']) && is_array($RaceStageInfo['RaceStageIcon']) && count($RaceStageInfo['RaceStageIcon']))
				{
					foreach ($RaceStageInfo['RaceStageIcon'] as $k => $v)
					{
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStageIconList'] .= "<a href='".$RootUrl.$v['RaceStageIcon_root']."' target='_blank'>图标".$k."</a>/";
					}
					$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStageIconList'] = rtrim($RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStageIconList'], "/");
				}
				else
				{
					$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStageIconList'] = '未上传';
				}
				$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStageStatus'] = $this->oRace->getRaceStageTimeStatus($RaceStageId,0);
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceStageList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加赛事分站填写配置页面
	public function raceStageAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageInsert");
		if($PermissionCheck['return'])
		{
			$StageStartDate = date("Y-m-d",time()+30*86400);
			$StageEndDate = date("Y-m-d",time()+32*86400);
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//渲染模板
			include $this->tpl('Xrace_Race_RaceStageAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加新赛事分站
	public function raceStageInsertAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceStageName','RaceCatalogId','StageStartDate','StageEndDate','RaceStageComment','PriceList');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogList  = $this->oRace->getRaceCatalogList();
		//分站名称不能为空
		if(trim($bind['RaceStageName'])=="")
		{
			$response = array('errno' => 1);
		}
		//必须选定一个有效的赛事ID
		elseif(!isset($RaceCatalogList[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		//至少选定一个分组
		elseif(count($SelectedRaceGroup['SelectedRaceGroup'])==0)
		{
			$response = array('errno' => 4);
		}
		else
		{
			//记录分组信息
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			//文件上传
			$oUpload = new Base_Upload('RaceStageIcon');
			$upload = $oUpload->upload('RaceStageIcon');
			$res = $upload->resultArr;
			foreach($upload->resultArr as $iconkey=>$iconvalue)
			{
				$path = $iconvalue;
				//如果正确上传，就保存文件路径
				if(strlen($path['path'])>2)
				{
					$bind['RaceStageIcon'][$iconkey]['RaceStageIcon'] = $path['path'];
					$bind['RaceStageIcon'][$iconkey]['RaceStageIcon_root'] = $path['path_root'];
				}
			}
			//对说明文字进行过滤和编码
			$bind['RaceStageComment'] = urlencode(htmlspecialchars(trim($bind['RaceStageComment'])));
			//价格对应列表
			$bind['comment']['PriceList'] =  $this->oRace->getPriceList(trim($bind['PriceList']),1);
			//删除原有数据
			unset($bind['PriceList']);
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//图片数据压缩
			$bind['RaceStageIcon'] = json_encode($bind['RaceStageIcon']);
			//插入数据
			$res = $this->oRace->insertRaceStage($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//修改赛事分站填写配置页面
	public function raceStageModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//分站数据
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//说明文字解码
			$RaceStageInfo['RaceStageComment'] = urldecode($RaceStageInfo['RaceStageComment']);
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//图片数据解包
			$RaceStageInfo['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'],true);
			//循环赛事分组列表
			foreach($RaceGroupList as $RaceGroupId => $RaceGroupInfo)
			{
				//如果出现在选定的分组列表当中
				if(in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
				{
					$RaceGroupList[$RaceGroupId]['selected'] = 1;
				}
				else
				{
					$RaceGroupList[$RaceGroupId]['selected'] = 0;
				}
			}
			//获得赛事分组的图标
			$RaceStageIconList = array();
			if(isset($RaceStageInfo['RaceStageIcon']) && is_array($RaceStageInfo['RaceStageIcon']))
			{
				$RaceStageIconList = $RaceStageInfo['RaceStageIcon'];
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceStageModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新赛事分站
	public function raceStageUpdateAction()
	{
		//获取 页面参数
		$bind = $this->request->from('RaceStageId','RaceStageName','RaceCatalogId','StageStartDate','StageEndDate','RaceStageComment','PriceList');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogList  = $this->oRace->getRaceCatalogList();
		//分站名称不能为空
		if(trim($bind['RaceStageName'])=="")
		{
			$response = array('errno' => 1);
		}
		//赛事分站ID必须大于0
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//必须选定一个有效的赛事ID
		elseif(!isset($RaceCatalogList[$bind['RaceCatalogId']]))
		{
			$response = array('errno' => 3);
		}
		//必须选定一个有效的赛事ID
		elseif(count($SelectedRaceGroup['SelectedRaceGroup'])==0)
		{
			$response = array('errno' => 4);
		}
		else
		{
			//获取原有数据
			$oRaceStage = $this->oRace->getRaceStage($bind['RaceStageId']);
			//数据解包
			$bind['comment'] = json_decode($oRaceStage['comment'],true);
			//图片数据解包
			$bind['RaceStageIcon'] = json_decode($oRaceStage['RaceStageIcon'],true);
			$bind['comment']['SelectedRaceGroup'] = $SelectedRaceGroup['SelectedRaceGroup'];
			//文件上传
			$oUpload = new Base_Upload('RaceStageIcon');
			$upload = $oUpload->upload('RaceStageIcon');
			$res = $upload->resultArr;
			foreach($upload->resultArr as $iconkey=>$iconvalue)
			{
				$path = $iconvalue;
				//如果正确上传，就保存文件路径
				if(strlen($path['path'])>2)
				{
					$bind['RaceStageIcon'][$iconkey]['RaceStageIcon'] = $path['path'];
					$bind['RaceStageIcon'][$iconkey]['RaceStageIcon_root'] = $path['path_root'];
				}
			}
			//对说明文字进行过滤和编码
			$bind['RaceStageComment'] = urlencode(htmlspecialchars(trim($bind['RaceStageComment'])));
			//价格对应列表
			$bind['comment']['PriceList'] = $this->oRace->getPriceList(trim($bind['PriceList']),1);
			//删除原有数据
			unset($bind['PriceList']);
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//图片数据压缩
			$bind['RaceStageIcon'] = json_encode($bind['RaceStageIcon']);
			//更新数据
			$res = $this->oRace->updateRaceStage($bind['RaceStageId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//删除赛事分站
	public function raceStageDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageDelete");
		if($PermissionCheck['return'])
		{
			//赛事分赞ID
			$RaceStageId = intval($this->request->RaceStageId);
			//获取赛事分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//如果有获取到赛事分站信息
			if(isset($RaceStageInfo['RaceStageId']))
			{
				//删除
				$this->oRace->deleteRaceStage($RaceStageId);
			}
			//返回之前页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//删除赛事分站图标
	public function raceStageIconDeleteAction()
	{
		//赛事分站ID
		$RaceStageId = intval($this->request->RaceStageId);
		//图标ID
		$LogoId = intval($this->request->LogoId);
		//获取原有数据
		$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,"RaceStageIcon");
		//图片数据解包
		$RaceStageInfo['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'],true);
		foreach($RaceStageInfo['RaceStageIcon'] as $k => $v)
		{
			if($k == $LogoId)
			{
				unset($RaceStageInfo['RaceStageIcon'][$k]);
			}
		}
		//图片数据压缩
		$RaceStageInfo['RaceStageIcon'] = json_encode($RaceStageInfo['RaceStageIcon']);
		//更新数据
		$res = $this->oRace->updateRaceStage($RaceStageId,$RaceStageInfo);
		//返回之前页面
		$this->response->goBack();
	}
	//获取赛事分站已经选择的分组列表
	public function getSelectedGroupAction()
	{
		//赛事ID
		$RaceCatalogId = intval($this->request->RaceCatalogId);
		//赛事分站ID
		$RaceStageId = intval($this->request->RaceStageId);
		//所有赛事分组列表
		$RaceGroupList = $this->oRace->getRaceGroupList($RaceCatalogId);
		//如果有传赛事分站ID
		if($RaceStageId)
		{
			//获取赛事分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
		}
		else
		{
			//置为空数组
			$RaceStageInfo['comment']['SelectedRaceGroup'] = array();
		}
		//循环赛事分组列表
		foreach($RaceGroupList as $RaceGroupId => $RaceGroupInfo)
		{
			//如果有选择该赛事分组
			if(in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
			{
				//拼接单选框，并选中
				$t[$RaceGroupId] = '<input type="checkbox"  name="SelectedRaceGroup[]" value='.$RaceGroupId.' checked>'.$RaceGroupInfo['RaceGroupName'];
			}
			else
			{
				//拼接单选框，不选中
				$t[$RaceGroupId] = '<input type="checkbox"  name="SelectedRaceGroup[]" value='.$RaceGroupId.'>'.$RaceGroupInfo['RaceGroupName'];
			}
		}
		//字符串组合
		$text = implode("  ",$t);
		//如果当前没有已经选择的赛事分组列表
		$text = (trim($text!=""))?$text:"暂无分类";
		echo $text;
		die();
	}
	//比赛列表页面
	public function raceListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取比赛列表
			$RaceList = $this->oRace->getRaceList($RaceStageId,$RaceGroupId);
			//获取比赛类型列表
			$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
			foreach($RaceList as $RaceId => $RaceInfo)
			{
				//获取比赛当前状态
				$RaceStatus = $this->oRace->getRaceTimeStatus($RaceInfo);
				$RaceList[$RaceId]['RaceStatus'] = $RaceStatus['RaceStatusName'];
				//获取比赛分类名称
				$RaceList[$RaceId]['RaceTypeName'] = isset($RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName'])?$RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName']:"未配置";
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加比赛配置信息填写页面
	public function raceAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取比赛类型列表
			$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
			//初始化开始和结束时间
			$ApplyStartTime = date("Y-m-d H:i:s",time()+86400);
			$ApplyEndTime = date("Y-m-d H:i:s",time()+86400*8);
			$StartTime = date("Y-m-d H:i:s",time()+86400*15);
			$EndTime = date("Y-m-d H:i:s",time()+86400*16);
			//渲染模板
			include $this->tpl('Xrace_Race_RaceAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//修改比赛配置信息填写页面
	public function raceModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息
			if(isset($RaceInfo['RaceId']))
			{
				//说明文字解码
				$RaceInfo['RaceComment'] = urldecode($RaceInfo['RaceComment']);
				//获取比赛类型列表
				$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
				//解包数组
				$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
				//解包地图数组
				$RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
				//渲染模板
				include $this->tpl('Xrace_Race_RaceModify');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加比赛
	public function raceInsertAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','ApplyStartTime','ApplyEndTime','StartTime','EndTime','SingleUser','TeamUser','SingleUserLimit','TeamLimit','TeamUserMin','TeamUserMax','BaiDuMapID','BaiDuMapStartTime','BaiDuMapEndTime','RaceTypeId','RaceComment','MustSelect');
		//转化时间为时间戳
		$ApplyStartTime = strtotime(trim($bind['ApplyStartTime']));
		$ApplyEndTime = strtotime(trim($bind['ApplyEndTime']));
		$StartTime = strtotime(trim($bind['StartTime']));
		$EndTime = strtotime(trim($bind['EndTime']));
		//比赛名称不能为空
		if(trim($bind['RaceName'])=="")
		{
			$response = array('errno' => 1);
		}
		//分站ID必须大于0
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//分组ID必须大于0
		elseif(intval($bind['RaceGroupId'])<=0)
		{
			$response = array('errno' => 3);
		}
		//价格参数必须填写
		elseif(trim($bind['PriceList'])=="")
		{
			$response = array('errno' => 4);
		}
		/*
		//开始时间不能早于当前时间
		elseif($StartTime<=time())
		{
			$response = array('errno' => 5);
		}
		//结束时间不能早于当前时间
		elseif($EndTime<=time())
		{
			$response = array('errno' => 6);
		}
		*/
		//单人报名和团队报名至少要选择一个
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 7);
		}
		//结束时间不能早于开始时间
		elseif($EndTime<=$StartTime)
		{
			$response = array('errno' => 10);
		}
		//结束报名时间不能早于开始报名时间
		elseif ($ApplyEndTime<=$ApplyStartTime)
		{
			$response = array('errno' => 11);
		}
		//结束报名时间不能晚于比赛开始时间
		elseif ($ApplyEndTime>=$StartTime)
		{
			$response = array('errno' => 12);
		}
		//开放个人报名时,最大人数必须大于0
		elseif($bind['SingleUser'] == 1 && $bind['SingleUserLimit']<=0)
		{
			$response = array('errno' => 13);
		}
		//开放团队报名时,最大队伍数量必须大于0
		elseif($bind['TeamUser'] == 1 && $bind['TeamLimit']<=0)
		{
			$response = array('errno' => 14);
		}
		//开放团队报名时,队伍人数限制(最小人数必须大于0,最大人数必须大于最小人数)
		elseif($bind['TeamUser'] == 1 && ($bind['TeamUserMin']<=0 || $bind['TeamUserMin'] > $bind['TeamUserMax']))
		{
			$response = array('errno' => 15);
		}
		//未选择比赛类型
		elseif($bind['RaceTypeId'] <=0)
		{
			$response = array('errno' => 16);
		}
		else
		{
			//价格对应列表
			$bind['PriceList'] = $this->oRace->getPriceList(trim($bind['PriceList']),1);
			//将人数限制分别置入压缩数组,并删除原数据
			$bind['comment']['SingleUserLimit'] = $bind['SingleUserLimit'];
			unset($bind['SingleUserLimit']);
			$bind['comment']['TeamLimit'] = $bind['TeamLimit'];
			unset($bind['TeamLimit']);
			$bind['comment']['TeamUserMin'] = $bind['TeamUserMin'];
			unset($bind['TeamUserMin']);
			$bind['comment']['TeamUserMax'] = $bind['TeamUserMax'];
			unset($bind['TeamUserMax']);
			//保存百度地图信息
			$bind['RouteInfo']['BaiDuMapID'] = $bind['BaiDuMapID'];
			unset($bind['BaiDuMapID']);
			//如果有填写百度地图ID,就保存相关的起止时间
			if(strlen($bind['RouteInfo']['BaiDuMapID']))
			{
				$bind['RouteInfo']['BaiDuMapStartTime'] = $bind['BaiDuMapStartTime'];
				$bind['RouteInfo']['BaiDuMapEndTime'] = $bind['BaiDuMapEndTime'];
			}
			else
			{
				$bind['RouteInfo']['BaiDuMapStartTime'] = date("Y-m-d H:i:s",0);
				$bind['RouteInfo']['BaiDuMapEndTime'] = date("Y-m-d H:i:s",0);
			}
			unset($bind['BaiDuMapStartTime']);
			unset($bind['BaiDuMapEndTime']);
			//对说明文字进行过滤和编码
			$bind['RaceComment'] = urlencode(htmlspecialchars(trim($bind['RaceComment'])));
			//数据打包
			$bind['comment'] = json_encode($bind['comment']);
			//地图数据打包
			$bind['RouteInfo'] = json_encode($bind['RouteInfo']);
			//新增比赛
			$AddRace = $this->oRace->addRace($bind);
			$response = $AddRace ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//修改比赛
	public function raceUpdateAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','ApplyStartTime','ApplyEndTime','StartTime','EndTime','SingleUser','TeamUser','SingleUserLimit','TeamLimit','TeamUserMin','TeamUserMax','BaiDuMapID','BaiDuMapStartTime','BaiDuMapEndTime','RaceTypeId','RaceComment','MustSelect');
		//转化时间为时间戳
		$ApplyStartTime = strtotime(trim($bind['ApplyStartTime']));
		$ApplyEndTime = strtotime(trim($bind['ApplyEndTime']));
		$StartTime = strtotime(trim($bind['StartTime']));
		$EndTime = strtotime(trim($bind['EndTime']));
		//比赛ID
		$RaceId = intval($this->request->RaceId);
		//比赛名称不能为空
		if(trim($bind['RaceName'])=="")
		{
			$response = array('errno' => 1);
		}
		//分站ID必须大于0
		elseif(intval($bind['RaceStageId'])<=0)
		{
			$response = array('errno' => 2);
		}
		//分组ID必须大于0
		elseif(intval($bind['RaceGroupId'])<=0)
		{
			$response = array('errno' => 3);
		}
		//价格参数必须填写
		elseif(trim($bind['PriceList'])=="")
		{
			$response = array('errno' => 4);
		}
		/*
		//开始时间不能早于当前时间
		elseif($StartTime<=time())
		{
			$response = array('errno' => 5);
		}
		//结束时间不能早于当前时间
		elseif($EndTime<=time())
		{
			$response = array('errno' => 6);
		}
		*/
		//单人报名和团队报名至少要选择一个
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 7);
		}
		//比赛ID必须大于0
		elseif($RaceId<=0)
		{
			$response = array('errno' => 8);
		}
		//结束时间不能早于开始时间
		elseif($EndTime<=$StartTime)
		{
			$response = array('errno' => 10);
		}
		//结束报名时间不能早于开始报名时间
		elseif ($ApplyEndTime<=$ApplyStartTime)
		{
			$response = array('errno' => 11);
		}
		//结束报名时间不能晚于比赛开始时间
		elseif ($ApplyEndTime>=$StartTime)
		{
			$response = array('errno' => 12);
		}
		//开放个人报名时,最大人数必须大于0
		elseif($bind['SingleUser'] == 1 && $bind['SingleUserLimit']<=0)
		{
			$response = array('errno' => 13);
		}
		//开放团队报名时,最大队伍数量必须大于0
		elseif($bind['TeamUser'] == 1 && $bind['TeamLimit']<=0)
		{
			$response = array('errno' => 14);
		}
		//开放团队报名时,队伍人数限制(最小人数必须大于0,最大人数必须大于最小人数)
		elseif($bind['TeamUser'] == 1 && ($bind['TeamUserMin']<=0 || $bind['TeamUserMin'] > $bind['TeamUserMax']))
		{
			$response = array('errno' => 15);
		}
		//未选择比赛类型
		elseif($bind['RaceTypeId'] <=0)
		{
			$response = array('errno' => 16);
		}
		else
		{
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//解包数组
			$bind['comment'] = json_decode($RaceInfo['comment'],true);
			//解包地图数组
			$bind['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
			//价格对应列表
			$bind['PriceList'] = $this->oRace->getPriceList(trim($bind['PriceList']),1);
			//将人数限制分别置入压缩数组,并删除原数据
			$bind['comment']['SingleUserLimit'] = $bind['SingleUserLimit'];
			unset($bind['SingleUserLimit']);
			$bind['comment']['TeamLimit'] = $bind['TeamLimit'];
			unset($bind['TeamLimit']);
			$bind['comment']['TeamUserMin'] = $bind['TeamUserMin'];
			unset($bind['TeamUserMin']);
			$bind['comment']['TeamUserMax'] = $bind['TeamUserMax'];
			unset($bind['TeamUserMax']);
			//保存百度地图信息
			$bind['RouteInfo']['BaiDuMapID'] = $bind['BaiDuMapID'];
			unset($bind['BaiDuMapID']);
			//如果有填写百度地图ID,就保存相关的起止时间
			if(strlen($bind['RouteInfo']['BaiDuMapID']))
			{
				$bind['RouteInfo']['BaiDuMapStartTime'] = $bind['BaiDuMapStartTime'];
				$bind['RouteInfo']['BaiDuMapEndTime'] = $bind['BaiDuMapEndTime'];
			}
			else
			{
				$bind['RouteInfo']['BaiDuMapStartTime'] = date("Y-m-d H:i:s",0);
				$bind['RouteInfo']['BaiDuMapEndTime'] = date("Y-m-d H:i:s",0);
			}
			unset($bind['BaiDuMapStartTime']);
			unset($bind['BaiDuMapEndTime']);
			//对说明文字进行过滤和编码
			$bind['RaceComment'] = urlencode(htmlspecialchars(trim($bind['RaceComment'])));
			//数据打包
			$bind['comment'] = json_encode($bind['comment']);
			//地图数据打包
			$bind['RouteInfo'] = json_encode($bind['RouteInfo']);
			//更新比赛
			$UpdateRace = $this->oRace->updateRace($RaceId,$bind);
			$response = $UpdateRace ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	//比赛详情页面
	public function raceDetailAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息
			if(isset($RaceInfo['RaceId']))
			{
				//获取当前分站信息
				$RaceStageInfo = $this->oRace->getRaceStage($RaceInfo['RaceStageId'],'*');
				//解包压缩数组
				$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
				//获取赛事分组信息
				$RaceGroupInfo = $this->oRace->getRaceGroup($RaceInfo['RaceGroupId'],'*');
				//数据解包
				$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
				//初始运动类型信息列表
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				$this->oSports = new Xrace_Sports();
				//获取运动类型列表
				$SportTypeList = $this->oSports->getAllSportsTypeList();
				//循环运动类型列表
				foreach($RaceInfo['comment']['DetailList'] as $Key => $RaceSportsInfo)
				{
					//如果运动类型已经配置
					if(isset($SportTypeList[$RaceSportsInfo['SportsTypeId']]))
					{
						//初始化统计信息
						$RaceInfo['comment']['DetailList'][$Key]['Total'] = array('Distence'=>0,'ChipCount'=>0,'AltAsc'=>0,'AltDec'=>0);
						//获取运动类型名称
						$RaceInfo['comment']['DetailList'][$Key]['SportsTypeName'] = $SportTypeList[$RaceSportsInfo['SportsTypeId']]['SportsTypeName'];
						//如果有配置计时点ID 则获取计时点信息
						$RaceInfo['comment']['DetailList'][$Key]['TimingDetailList'] = isset($RaceInfo['comment']['DetailList'][$Key]['TimingId'])?$this->oRace->getTimingDetail($RaceInfo['comment']['DetailList'][$Key]['TimingId']):array();
						//数据解包
						$RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'] = isset($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'])?json_decode($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'],true):array();
						//计时点排序
						ksort($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment']);
						//循环计时点列表
						foreach($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'] as $tid => $tinfo)
						{
							//累加里程
							$RaceInfo['comment']['DetailList'][$Key]['Total']['Distence'] += $tinfo['ToNext']*	$tinfo['Round'];
							//累加计时点数量
							$RaceInfo['comment']['DetailList'][$Key]['Total']['ChipCount'] += $tinfo['Round'];
							//累加海拔上升
							$RaceInfo['comment']['DetailList'][$Key]['Total']['AltAsc'] += $tinfo['AltAsc']*	$tinfo['Round'];
							//累加海拔下降
							$RaceInfo['comment']['DetailList'][$Key]['Total']['AltDec'] += $tinfo['AltDec']*	$tinfo['Round'];
						}
					}
					else
					{
						//从列表中删除
						unset($RaceInfo['comment']['DetailList'][$Key]);
					}
				}
				//渲染模板
				include $this->tpl('Xrace_Race_RaceDetail');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加运动类型分段
	public function raceSportsTypeInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				$response = array('errno' => 1);
			}
			else
			{
				//获取赛事分组信息
				$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
				//如果赛事分组尚未配置
				if(!$RaceGroupInfo['RaceGroupId'])
				{
					$response = array('errno' => 2);
				}
				else
				{
					$this->oSports = new Xrace_Sports();
					//获取运动类型信息
					$SportsTypeInfo = $this->oSports->getSportsType($SportsTypeId,'*');
					//如果未获取到有效的运动类型
					if(!isset($SportsTypeInfo['SportsTypeId']))
					{
						$response = array('errno' => 3);
					}
					else
					{
						//获取比赛信息
						$RaceInfo = $this->oRace->getRace($RaceId);
						//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
						if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
						{
							//数据解包
							$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
							//初始运动类型信息列表
							$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
							//运动类型列表排序
							ksort($RaceInfo['comment']['DetailList']);
							//如果添加在某个元素之后 且 元素下标不越界
							if($After>=0 && $After <= count($RaceInfo['comment']['DetailList']))
							{
								//添加元素
								$RaceInfo['comment']['DetailList'] = Base_Common::array_insert($RaceInfo['comment']['DetailList'],array('SportsTypeId' => $SportsTypeId),$After+1);
							}
							//如果在头部添加
							elseif($After == -1)
							{
								//添加元素
								$RaceInfo['comment']['DetailList'] = Base_Common::array_insert($RaceInfo['comment']['DetailList'],array('SportsTypeId' => $SportsTypeId),$After+1);
							}
							else
							{
								//默认为在表尾部添加元素
								$RaceInfo['comment']['DetailList'][count($RaceInfo['comment']['DetailList'])] = array('SportsTypeId' => $SportsTypeId);
							}
							//数据打包
							$RaceInfo['comment'] = json_encode($RaceInfo['comment']);
							//更新比赛
							$res = $this->oRace->updateRace($RaceId,$RaceInfo);
							$response = $res ? array('errno' => 0) : array('errno' => 9);
						}
					}
				}
			}
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加比赛提交页面
	public function raceSportsTypeAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//初始运动类型信息列表
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				//运动类型列表排序
				ksort($RaceInfo['comment']['DetailList']);
				//循环运动类型列表
				foreach($RaceInfo['comment']['DetailList'] as $Key => $SportsTypeInfo)
				{
					//获取运动类型名称
					$RaceInfo['comment']['DetailList'][$Key]['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				}
				//如果位置为负数
				if($After<0)
				{
					$After = -1;
				}
				//如果添加在某个元素之后 且 元素下标不越界
				elseif( $After >= count($RaceInfo['comment']['DetailList']))
				{
					$After = count($RaceInfo['comment']['DetailList'])-1;
				}
				//渲染模板
				include $this->tpl('Xrace_Race_RaceSportsTypeAdd');
			}

		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加分站-分组的运动类型分段提交页面
	public function raceSportsTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo ['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//初始运动类型信息列表
				$RaceInfo['comment']['DetailList'] = isset($RaceInfo['comment']['DetailList'])?$RaceInfo['comment']['DetailList']:array();
				//运动类型列表排序
				ksort($RaceInfo['comment']['DetailList']);
				//已删除标签为0
				$deleted = 0;
				//循环运动类型列表
				foreach($RaceInfo['comment']['DetailList'] as $Key => $SportsTypeInfo)
				{
					//如果匹配到需要删除的数据
					if($Key == $SportsTypeId)
					{
						//删除数据
						unset($RaceInfo['comment']['DetailList'][$Key]);
						//已删除标签为1
						$deleted = 1;
					}
					//如果已删除，且有后续数据
					if($deleted == 1 && isset($RaceInfo['comment']['DetailList'][$Key+1]))
					{
						//后续数据复制到前一位
						$RaceInfo['comment']['DetailList'][($Key)] = $RaceInfo['comment']['DetailList'][$Key+1];
						//删除后续数据
						unset($RaceInfo['comment']['DetailList'][$Key+1]);
					}
				}
				//数据打包
				$RaceInfo['comment'] = json_encode($RaceInfo['comment']);
				//更新比赛
				$res = $this->oRace->updateRace($RaceId,$RaceInfo);
			}
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新任务信息
	public function timingPointInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取 页面参数
			$bind = $this->request->from('TName','ToNext','AltAsc','AltDec','Round','ChipId');
			//添加计时点
			$AddTimingPoint = $this->oRace->addTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$After,$bind);
			$response = $AddTimingPoint ? array('errno' => 0) : array('errno' => $AddTimingPoint);
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加比赛计时点提交页面
	public function timingPointAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//获取运动类型信息
				$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
				//获取运动类型名称
				$SportsTypeInfo['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				//初始化计时点列表
				$SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
				//解包数据
				$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
				//计时点信息排序
				ksort($SportsTypeInfo['TimingDetailList']['comment']);
				//如果计时点位置为负数
				if($After<0)
				{
					$After = -1;
				}
				//如果添加在某个元素之后 且 元素下标不越界
				elseif( $After >= count($SportsTypeInfo['TimingDetailList']['comment']))
				{
					$After = count($SportsTypeInfo['TimingDetailList']['comment'])-1;
				}
				//渲染模板
				include $this->tpl('Xrace_Race_TimingPointAdd');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加计时点数据提交页面
	public function timingPointModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果当前分站未配置了当前分组
			if(!isset($RaceStageInfo['comment']['SelectedRaceGroup'][$RaceGroupId]))
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				//跳转到分站列表页面
				$this->response->redirect($this->sign);
			}
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']) && ($RaceStageId == $RaceInfo['RaceStageId']) && ($RaceGroupId == $RaceInfo['RaceGroupId']))
			{
				//数据解包
				$RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
				//获取运动类型信息
				$SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
				//获取运动类型名称
				$SportsTypeInfo['SportsTypeName'] = $SportsTypeList[$SportsTypeInfo['SportsTypeId']]['SportsTypeName'];
				//初始化计时点列表
				$SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
				//解包数据
				$SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
				//获取计时点信息
				$TimingInfo = $SportsTypeInfo['TimingDetailList']['comment'][$TimingId];
				//渲染模板
				include $this->tpl('Xrace_Race_TimingPointModify');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//修改计时点数据
	public function timingPointUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//获取 页面参数
			$bind = $this->request->from('TName','ToNext','AltAsc','AltDec','Round','ChipId');

			//更新计时点
			$UpdateTimingPoint = $this->oRace->updateTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$TimingId,$bind);
			$response = $UpdateTimingPoint ? array('errno' => 0) : array('errno' => $UpdateTimingPoint);
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//删除计时点数据
	public function timingPointDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//删除计时点
			$DeleteTimingPoint = $this->oRace->deleteTimingPoint($RaceStageId,$RaceGroupId,$RaceId,$SportsTypeId,$TimingId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新分站相关的产品列表信息填写页面
	public function productModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事ID
			//$RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			//赛事分站ID
			$RaceStageId  = isset($this->request->RaceStageId)?intval($this->request->RaceStageId):0;
			//获取赛站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
			//解包赛站数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//初始化已选定的产品列表
			$SelectedProductList = array();
			//如果有已经选定的产品列表
			if(isset($RaceStageInfo['comment']['SelectedProductList']) && is_array($RaceStageInfo['comment']['SelectedProductList']))
			{
				$SelectedProductList = $RaceStageInfo['comment']['SelectedProductList'];
			}
			//商品类型列表
			$ProductTypeList = $this->oProduct->getProductTypeList($RaceStageInfo['RaceCatalogId'], 'ProductTypeId,ProductTypeName');
			//初始化空的商品列表
			$ProductList = array();
			//获取所有产品的列表
			$ProductList = $this->oProduct->getAllProductList(0, 'ProductTypeId,ProductId,ProductName');
			//根据产品分类循环列表
			foreach($ProductList as $ProductTypeId => $TypeProductList)
			{
				//如果商品分类已存在
				if(isset($ProductTypeList[$ProductTypeId]))
				{
					//产品列表存入
					$ProductTypeList[$ProductTypeId]['ProductList'] = $TypeProductList;
					//循环其下的产品列表
					foreach($ProductTypeList[$ProductTypeId]['ProductList'] as $ProductId => $ProductInfo)
					{
						//如果该产品已选中
						if(isset($SelectedProductList[$ProductId]))
						{
							//置入选中标签
							$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['selected'] = 1;
							//获取已经设定的产品价格和限购数量
							$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['ProductPrice'] = $SelectedProductList[$ProductId]['ProductPrice'];
							$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['ProductLimit'] = $SelectedProductList[$ProductId]['ProductLimit'];
						}
						else
						{
							$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['selected'] = 0;
							//初始化产品价格和限购数量
							$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['ProductPrice'] = 0;
							$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['ProductLimit'] = 0;
						}
					}
				}
				else
				{
					//删除该数据出错的分类
					unset($ProductList[$ProductTypeId]);
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Race_ProductModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
    //更新分站相关的产品列表信息
	public function productUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//赛事ID
			$RaceStageId = intval($this->request->RaceStageId);
			//获取赛站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
			//解包数组
			$bing['comment'] = json_decode($RaceStageInfo['comment'],true);
			if($RaceStageInfo['RaceStageId'])
			{
				//获取已经选定的商品列表
				$CheckedProduct = $this->request->from('ProductChecked');
				//获取已经选定的商品数据
				$ProductPrice = $this->request->from('ProductPrice');
				//循环已选择的产品列表
				foreach($CheckedProduct['ProductChecked'] as $ProductId)
				{
					//如果有填写对应的价格和限购数量
					if(isset($ProductPrice['ProductPrice'][$ProductId]))
					{
						//如果价格填写复数或限购数量小于1
						if($ProductPrice['ProductPrice'][$ProductId]['ProductPrice'] < 0 || $ProductPrice['ProductPrice'][$ProductId]['ProductLimit'] < 1)
						{
							//删除选择
							unset($CheckedProduct[$ProductId]);
						}
						else
						{
							//格式化价格和限购数量
							$ProductPrice['ProductPrice'][$ProductId]['ProductPrice'] = intval($ProductPrice['ProductPrice'][$ProductId]['ProductPrice'])>=9999?9999:intval($ProductPrice['ProductPrice'][$ProductId]['ProductPrice']);
							$ProductPrice['ProductPrice'][$ProductId]['ProductLimit'] = intval($ProductPrice['ProductPrice'][$ProductId]['ProductLimit'])>=3?3:intval($ProductPrice['ProductPrice'][$ProductId]['ProductLimit']);
							$CheckedProduct['ProductChecked'][$ProductId] = $ProductPrice['ProductPrice'][$ProductId];
						}
					}
					else
					{
						unset($CheckedProduct['ProductChecked'][$ProductId]);
					}
				}
			}
			//存入数组中
			$bind['comment']['SelectedProductList'] = $CheckedProduct['ProductChecked'];
			//数据打包
			$bind['comment'] = json_encode($bind['comment']);
			//更新赛事分站信息
			$UpdateRaceStage = $this->oRace->updateRaceStage($RaceStageId, $bind);
			$response = $UpdateRaceStage ? array('errno' => 0) : array('errno' => 9);
			echo json_encode($response);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//比赛选手列表 批量更新BIB和计时芯片ID
	public function raceUserListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取赛站信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//生成查询条件
			$params = array('RaceId'=>$RaceInfo['RaceId']);
			$oUser = new Xrace_User();
			//获取选手名单
			$RaceUserList = $oUser->getRaceUserList($params);
			//如果获取到选手名单
			if(count($RaceUserList))
			{
				$oTeam = new Xrace_Team();
				$RaceTeamList = array();

				foreach($RaceUserList as $ApplyId => $ApplyInfo)
				{
					//获取用户信息
					$UserInfo = $oUser->getUserInfo( $ApplyInfo["UserId"],'user_id,name');
					//如果获取到用户
					if($UserInfo['user_id'])
					{
						$RaceUserList[$ApplyId]['UserId'] = $UserInfo['user_id'];
						$RaceUserList[$ApplyId]['Name'] = $UserInfo['name'];
						if(!isset($RaceTeamList[$ApplyInfo['RaceTeamId']]))
						{
							//队伍信息
							$RaceTeamInfo = $oTeam->getRaceTeamInfo($ApplyInfo['RaceTeamId'],'*');
							if(isset($RaceTeamInfo['RaceTeamId']))
							{
								$RaceTeamList[$ApplyInfo['RaceTeamId']] = $RaceTeamInfo;
							}
						}
						$RaceUserList[$ApplyId]['RaceTeamName'] = isset($RaceTeamList[$ApplyInfo['RaceTeamId']])?$RaceTeamList[$ApplyInfo['RaceTeamId']]['RaceTeamName']:"个人报名";
					}
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceUserList');
			//print_R($RaceUserList);
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//批量更新比赛选手列表
	public function raceUserListUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceStageModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取BIB号码列表
			$UserList = $this->request->from('UserList');
			$oUser = new Xrace_User();
			//循环号码牌列表
			foreach($UserList['UserList'] as $UserId => $UserInfo)
			{
				$bind['BIB'] = trim($UserInfo['BIB']);
				$bind['ChipId'] = trim($UserInfo['ChipId']);
				//更新报名记录
				$oUser->updateRaceUser($RaceId,$UserId,$bind);
			}
			//返回之前页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
