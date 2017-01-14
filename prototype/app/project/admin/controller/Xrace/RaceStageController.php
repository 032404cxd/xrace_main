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
			$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
			//赛事分站列表
			$RaceStageArr = $this->oRace->getRaceStageList($RaceCatalogId,"RaceStageId,RaceStageName,RaceCatalogId,comment,StageStartDate,StageEndDate,RaceStageIcon,Display");
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
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
				//如果相关赛事ID有效
				if(isset($RaceCatalogList[$RaceStageInfo['RaceCatalogId']]))
				{
					//获取赛事ID
					$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'] = isset($RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'])?$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName']:$RaceCatalogList[$RaceStageInfo['RaceCatalogId']]['RaceCatalogName'];
					//解包压缩数组
					$RaceStageInfo['RaceStageIcon'] = json_decode($RaceStageInfo['RaceStageIcon'],true);
					$t = array();
					$TotalRaceCount = 0;
					//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
					if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
					{
						//默认为分组优先
						$RaceStageInfo['comment']['RaceStructure'] = "group";
					}
					//分组优先模式
					if($RaceStageInfo['comment']['RaceStructure'] == "group")
					{
						//如果有已经选择的赛事组别
						if(isset($RaceStageInfo['comment']['SelectedRaceGroup']) && is_array($RaceStageInfo['comment']['SelectedRaceGroup']))
						{
							$TotalRaceCount = 0;
							//循环各个组别
							foreach($RaceStageInfo['comment']['SelectedRaceGroup'] as $k => $v)
							{
								//获取各个组别的比赛场次数量
								$RaceCount = $this->oRace->getRaceCount($RaceStageInfo['RaceStageId'],$v);
								//如果有配置比赛场次
								if($RaceCount>0)
								{
									//添加场次数量
									$Prefix = "(".$RaceCount.")";
								}
								else
								{
									$Prefix = "";
								}
								$TotalRaceCount+=$RaceCount;
								//如果赛事组别配置有效
								if(isset($RaceGroupList[$v]))
								{
									//生成到比赛详情页面的链接
									$t[$k] = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$RaceStageInfo['RaceStageId'],'RaceGroupId'=>$v)) ."'>".$RaceGroupList[$v]['RaceGroupName'].$Prefix."</a>";
								}
							}
						}
						//如果检查后有至少一个有效的赛事组别配置
						if(count($t))
						{
							//生成页面显示的数组
							$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedGroupList'] = implode("/",$t);
							$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['GroupCount'] = count($t);
						}
						else
						{
							//生成默认的入口
							$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedGroupList'] = "尚未配置";
							$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['GroupCount'] = 0;
						}
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceList']  = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$RaceStageInfo['RaceStageId'])) ."'>"."比赛列表(".$TotalRaceCount.")";"</a>";
					}
					else
					{
						//获取比赛列表
						$RaceList = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageInfo['RaceStageId']),"RaceId,RaceName,comment");
						//比赛数量
						$RaceCount = count($RaceList);
						$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceList']  = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$RaceStageInfo['RaceStageId'])) ."'>"."比赛列表(".$RaceCount.")";"</a>";
						$t = array();
						foreach($RaceList as $RaceId => $RaceInfo)
						{
							$t[$RaceId] = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.list',array('RaceStageId'=>$RaceStageInfo['RaceStageId'])) ."'>".$RaceInfo["RaceName"]."(".count($RaceInfo['comment']['SelectedRaceGroup']).")";"</a>";
						}
						//如果检查后有至少一个有效的比赛配置
						if(count($t))
						{
							//生成页面显示的数组
							$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedGroupList'] = implode("/",$t);
						}
						else
						{
							//生成默认的入口
							$RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['SelectedGroupList'] = "尚未配置";
						}
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
								$Stock = 0;
							    //如果缓存中的产品类型有累加数量
								if(isset($t[$ProductInfo['ProductTypeId']]))
								{
									foreach($ProductConfig as $k => $v)
                                    {
                                        $Stock += $v['Stock'];
                                    }
                                    if($Stock)
                                    {
                                        //数量累加
                                        $t[$ProductInfo['ProductTypeId']]['ProductCount']++;
                                    }
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
                    //获取比赛结构名称
                    $RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStructureName'] = $RaceStructureList[$RaceStageInfo['comment']['RaceStructure']];
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
                $RaceStageList[$RaceStageInfo['RaceCatalogId']]['RaceStageList'][$RaceStageId]['RaceStageName'].=($RaceStageInfo['Display'])?"":"(隐藏)";
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
			//加载富文本编辑器
			include('Third/ckeditor/ckeditor.php');
			$editor =  new CKEditor();
			$editor->BasePath = '/js/ckeditor/';
			$editor->config['height'] = 150;
			$editor->config['width'] =750;
			//初始化起止时间
			$StageStartDate = date("Y-m-d",time()+30*86400);
			$StageEndDate = date("Y-m-d",time()+32*86400);
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
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
		$bind=$this->request->from('RaceStageName','RaceCatalogId','StageStartDate','StageEndDate','RaceStageComment','RaceStructure','ApplyStartTime','ApplyEndTime','PriceList','Display');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
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
			//比赛结构
			$bind['comment']['RaceStructure'] = $bind['RaceStructure'];
			//删除原有数据
			unset($bind['RaceStructure']);
            //价格对应列表
            $bind['comment']['PriceList'] = $this->oRace->getPriceList(trim($bind['PriceList']),1);
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
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
			//分站数据
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
			if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
			{
				//默认为分组优先
				$RaceStageInfo['comment']['RaceStructure'] = "group";
			}
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
			//加载富文本编辑器
			include('Third/ckeditor/ckeditor.php');
			$editor =  new CKEditor();
			$editor->BasePath = '/js/ckeditor/';
			$editor->config['height'] = 150;
			$editor->config['width'] =750;
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
		$bind = $this->request->from('RaceStageId','RaceStageName','RaceCatalogId','StageStartDate','StageEndDate','RaceStageComment','RaceStructure','ApplyStartTime','ApplyEndTime','PriceList','Display');
		//获取已经选定的分组列表
		$SelectedRaceGroup = $this->request->from('SelectedRaceGroup');
		//赛事列表
		$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
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
			$bind['comment']['RaceStructure'] = $bind['RaceStructure'];
			//删除原有数据
			unset($bind['RaceStructure']);
            //价格对应列表
            $bind['comment']['PriceList'] = $this->oRace->getPriceList(trim($bind['PriceList']),1);
            //删除原有数据
            unset($bind['PriceList']);
			//数据压缩
			$bind['comment'] = json_encode($bind['comment']);
			//图片数据压缩
			$bind['RaceStageIcon'] = json_encode($bind['RaceStageIcon']);
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
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'*');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
			if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
			{
				//默认为分组优先
				$RaceStageInfo['comment']['RaceStructure'] = "group";
			}
			//如果当前分站未配置了当前分组
			if(!in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
			{
				$RaceGroupId = 0;
			}
			//获取赛事分组信息
			$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
			//如果赛事分组尚未配置
			if(!$RaceGroupInfo['RaceGroupId'])
			{
				$RaceGroupId = 0;
			}
			//获取比赛列表
			$RaceList = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageId,"RaceGroupId"=>$RaceGroupId));
			//获取比赛类型列表
			$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
			//赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			foreach($RaceList as $RaceId => $RaceInfo)
			{
				//获取比赛当前状态
				$RaceStatus = $this->oRace->getRaceTimeStatus($RaceInfo);
				$RaceList[$RaceId]['RaceStatus'] = $RaceStatus['RaceStatusName'];
				if($RaceStageInfo['comment']['RaceStructure'] == "group")
				{
					//获取比赛类型名称
					$RaceList[$RaceId]['RaceGroupName'] = isset($RaceGroupList[$RaceInfo['RaceGroupId']]['RaceGroupName'])?$RaceGroupList[$RaceInfo['RaceGroupId']]['RaceGroupName']:"未配置";
				}
				else
				{
					$t = array();
                    foreach($RaceInfo['comment']['SelectedRaceGroup'] as $k => $v)
					{
						if(isset($RaceGroupList[$k]))
						{
							$t[$k] = $RaceGroupList[$k]['RaceGroupName'];
						}
					}
					$RaceList[$RaceId]['RaceGroupName'] = count($t)?implode("<br>",$t):"未配置";
				}
				//获取比赛类型名称
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
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取赛事分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,"RaceStageId,RaceStageName,RaceCatalogId,comment,ApplyStartTime,ApplyEndTime");
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
			if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
			{
				//默认为分组优先
				$RaceStageInfo['comment']['RaceStructure'] = "group";
			}
			//获取当前赛事下的分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],"RaceGroupName,RaceGroupId");
			//如果当前传入的分组ID没有配置
			if(!in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
			{
				//置为0
				$RaceGroupId = 0;
                //循环已经配置的分组
                foreach($RaceStageInfo['comment']['SelectedRaceGroup'] as $k => $v)
                {
                    //如果查到就保留
                    if(isset($RaceGroupList[$v]))
                    {
                        $RaceStageInfo['comment']['SelectedRaceGroup'][$k] = $RaceGroupList[$v];
                    }
                    //否则就删除
                    else
                    {
                        unset($RaceStageInfo['comment']['SelectedRaceGroup'][$k]);
                    }
                }
			}
			//获取比赛类型列表
			$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
			//获取计时数据方式
			$RaceTimingTypeList = $this->oRace->getTimingType();
			//获取计时成绩计算方式
			$RaceTimingResultTypeList = $this->oRace->getRaceTimingResultType();
			//报名时间调用分站的报名时间
			$ApplyStartTime = $RaceStageInfo['ApplyStartTime'];
			$ApplyEndTime = $RaceStageInfo['ApplyEndTime'];
			//初始化开始和结束时间
			$StartTime = date("Y-m-d H:i:s",time()+86400*15);
			$EndTime = date("Y-m-d H:i:s",time()+86400*16);
			//加载富文本编辑器
			include('Third/ckeditor/ckeditor.php');
			$editor =  new CKEditor();
			$editor->BasePath = '/js/ckeditor/';
			$editor->config['height'] = 150;
			$editor->config['width'] =750;
			$MaxTeamRank = 5;
			for($i=2;$i<=$MaxTeamRank;$i++)
			{$t[$i] = $i;}
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
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//比赛分组
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
            //获取赛事分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceInfo['RaceStageId'],"RaceStageId,RaceStageName,RaceCatalogId,comment");
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//格式化分组ID
			$RaceGroupId = in_array($RaceGroupId,array(0,$RaceInfo['RaceGroupId']))?$RaceGroupId:0;
			//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
			if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
			{
				//默认为分组优先
				$RaceStageInfo['comment']['RaceStructure'] = "group";
			}
			//如果有获取到比赛信息
			if(isset($RaceInfo['RaceId']))
			{
				//获取比赛类型列表
				$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
				//获取计时数据方式
				$RaceTimingTypeList = $this->oRace->getTimingType();
				//获取计时成绩计算方式
				$RaceTimingResultTypeList = $this->oRace->getRaceTimingResultType();
				//数据解包
				$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
                //解包数组
				$RaceInfo['comment']['RaceStartMicro'] = isset($RaceInfo['comment']['RaceStartMicro'])?$RaceInfo['comment']['RaceStartMicro']:0;
				//解包地图数组
				$RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
				if($RaceStageInfo['comment']['RaceStructure'] == "race")
				{
					//获取当前赛事下的分组列表
					$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],"RaceGroupName,RaceGroupId");
                    //置为0
					$RaceGroupId = 0;
					//循环已经配置的分组
					foreach($RaceStageInfo['comment']['SelectedRaceGroup'] as $k => $v)
					{
					    //如果查到就保留
						if(isset($RaceGroupList[$v]))
						{
                            $RaceStageInfo['comment']['SelectedRaceGroup'][$k] = array_merge(isset($RaceInfo['comment']['SelectedRaceGroup'][$v])?$RaceInfo['comment']['SelectedRaceGroup'][$v]:array(),$RaceGroupList[$v]);
						    if(strtotime($RaceStageInfo['comment']['SelectedRaceGroup'][$k]['StartTime'])==0)
                            {
                                $RaceStageInfo['comment']['SelectedRaceGroup'][$k]['StartTime'] = $RaceInfo['StartTime'];
                                $RaceStageInfo['comment']['SelectedRaceGroup'][$k]['RaceStartMicro'] = $RaceInfo['comment']['RaceStartMicro'];
                                $RaceStageInfo['comment']['SelectedRaceGroup'][$k]['EndTime'] = $RaceInfo['EndTime'];
                            }
						}
						//否则就删除
						else
						{
							unset($RaceStageInfo['comment']['SelectedRaceGroup'][$k]);
						}
					}
				}
				//加载富文本编辑器
				include('Third/ckeditor/ckeditor.php');
				$editor =  new CKEditor();
				$editor->BasePath = '/js/ckeditor/';
				$editor->config['height'] = 150;
				$editor->config['width'] =750;
				$MaxTeamRank = 5;
				for($i=2;$i<=$MaxTeamRank;$i++)
				{$t[$i] = $i;}
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
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','ApplyStartTime','ApplyEndTime','StartTime','EndTime','SingleUser','TeamUser','SingleUserLimit','TeamLimit','TeamUserMin','TeamUserMax','SexUser','RaceTypeId','RaceComment','MustSelect','SingleSelect','MylapsPrefix','RaceTimingType','RaceTimingResultType','RaceStartMicro','SelectedRaceGroup','NoStart','TeamResultRank');
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
		//单人报名和团队报名至少要选择一个
		elseif((intval($bind['SingleUser'])+intval($bind['TeamUser'])) == 0)
		{
			$response = array('errno' => 7);
		}
		//结束时间不能早于开始时间
		elseif($EndTime<$StartTime)
		{
			$response = array('errno' => 10);
		}
        //结束报名时间不能早于开始报名时间
        elseif ((count($bind['SelectedRaceGroup'])==0) && ($ApplyEndTime<=$ApplyStartTime))
        {
            $response = array('errno' => 11);
        }
        //结束报名时间不能晚于比赛开始时间
        elseif ((count($bind['SelectedRaceGroup'])==0) && ($ApplyEndTime>=$StartTime))
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
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
			//获取分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($bind['RaceStageId'],"RaceStageId,comment");
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
			if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
			{
				//默认为分组优先
				$RaceStageInfo['comment']['RaceStructure'] = "group";
			}
			if(($RaceStageInfo['comment']['RaceStructure']=="group") && (intval($bind['RaceGroupId'])<=0))
			{
				$response = array('errno' => 3);
			}
			elseif(($RaceStageInfo['comment']['RaceStructure']=="race") && (count($bind['SelectedRaceGroup'])==0))
			{
				$response = array('errno' => 17);
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
                $bind['comment']['SexUser'] = $bind['SexUser'];
                unset($bind['SexUser']);
				$bind['comment']['RaceStartMicro'] = intval(abs($bind['RaceStartMicro']));
				$bind['comment']['RaceStartMicro'] = min(999,$bind['comment']['RaceStartMicro']);
				unset($bind['RaceStartMicro']);
				//保存mylaps计时数据表的前缀
				$bind['RouteInfo']['MylapsPrefix'] = $bind['MylapsPrefix'];
				unset($bind['MylapsPrefix']);
				//保存百度地图信息
				$bind['RouteInfo']['BaiDuMapID'] = $bind['BaiDuMapID'];
				unset($bind['BaiDuMapID']);
				//保存单个计时点的忍耐时间（在该时间范围内的将被忽略）
				$bind['RouteInfo']['MylapsTolaranceTime'] = abs(intval($bind['MylapsTolaranceTime']));
				unset($bind['MylapsTolaranceTime']);
				//成绩计算数据源
				$bind['RouteInfo']['RaceTimingType'] = trim($bind['RaceTimingType']);
				unset($bind['RaceTimingType']);
				//成绩计算方式
				$bind['RouteInfo']['RaceTimingResultType'] = trim($bind['RaceTimingResultType']);
				unset($bind['RaceTimingResultType']);
				//循环选定的分组
                foreach($bind['SelectedRaceGroup'] as $Group => $GroupInfo)
                {
                    //删除未选定的元素
                    if($GroupInfo['Selected']!=1)
                    {
                        unset($bind['SelectedRaceGroup'][$Group]);
                    }
                    else
                    {
                        //获取最早的开始时间作为比赛开始时间
                        $bind['StartTime'] = date("Y-m-d H:i:s",(strtotime($bind['StartTime'])>0?max(strtotime($bind['StartTime']),strtotime($GroupInfo['StartTime'])):strtotime($GroupInfo['StartTime'])));
                        //获取最晚的结束时间作为比赛结束时间
                        $bind['EndTime'] = date("Y-m-d H:i:s",(strtotime($bind['EndTime'])>0?max(strtotime($bind['EndTime']),strtotime($GroupInfo['EndTime'])):strtotime($GroupInfo['EndTime'])));
                        //获取毫秒时间
                        $bind['comment']['RaceStartMicro'] = ($bind['StartTime']==$GroupInfo['StartTime'])?$GroupInfo['RaceStartMicro']:$bind['comment']['RaceStartMicro'];
                        //结束报名时间不能晚于比赛开始时间
                        if ($ApplyEndTime>=(strtotime($GroupInfo['StartTime'])+$GroupInfo['RaceStartMicro']/1000))
                        {
                            $response = array('errno' => 12);break;
                        }
                    }
                }
                $bind['comment']['SelectedRaceGroup'] = $bind['SelectedRaceGroup'];
                unset($bind['SelectedRaceGroup']);
				//是否包含起点
				$bind['comment']['NoStart'] = $bind['NoStart'];
				unset($bind['NoStart']);
				//团队成绩计算名次
				$bind['comment']['TeamResultRank'] = $bind['TeamResultRank'];
				unset($bind['TeamResultRank']);
				//数据打包
				$bind['comment'] = json_encode($bind['comment']);
				//地图数据打包
				$bind['RouteInfo'] = json_encode($bind['RouteInfo']);
                if(!isset($response))
                {
                    //新增比赛
                    $AddRace = $this->oRace->addRace($bind);
                    $response = $AddRace ? array('errno' => 0) : array('errno' => 9);
                }
			}
		}
		echo json_encode($response);
		return true;
	}
	//修改比赛
	public function raceUpdateAction()
	{
		//获取 页面参数
		$bind=$this->request->from('RaceName','RaceStageId','RaceGroupId','PriceList','ApplyStartTime','ApplyEndTime','StartTime','EndTime','SingleUser','TeamUser','SingleUserLimit','TeamLimit','TeamUserMin','TeamUserMax','SexUser','RaceTypeId','RaceComment','MustSelect','SingleSelect','MylapsPrefix','RaceTimingType','RaceTimingResultType','RaceStartMicro','SelectedRaceGroup','NoStart','TeamResultRank');
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
		elseif($EndTime<$StartTime)
		{
			$response = array('errno' => 10);
		}
		//结束报名时间不能早于开始报名时间
		elseif ((count($bind['SelectedRaceGroup'])==0) && ($ApplyEndTime<=$ApplyStartTime))
		{
			$response = array('errno' => 11);
		}
		//结束报名时间不能晚于比赛开始时间
		elseif ((count($bind['SelectedRaceGroup'])==0) && ($ApplyEndTime>=$StartTime))
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
			//比赛-分组的层级规则
			$RaceStructureList  = $this->oRace->getRaceStructure();
			//获取分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($bind['RaceStageId'],"RaceStageId,comment");
			//数据解包
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			//如果没有配置比赛结构 或者 比赛结构配置不在配置列表中
			if(!isset($RaceStageInfo['comment']['RaceStructure']) || !isset($RaceStructureList[$RaceStageInfo['comment']['RaceStructure']]))
			{
				//默认为分组优先
				$RaceStageInfo['comment']['RaceStructure'] = "group";
			}
			if(($RaceStageInfo['comment']['RaceStructure']=="group") && (intval($bind['RaceGroupId'])<=0))
			{
				$response = array('errno' => 3);
			}
			elseif(($RaceStageInfo['comment']['RaceStructure']=="race") && (count($bind['SelectedRaceGroup'])==0))
			{
				$response = array('errno' => 17);
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
                $bind['comment']['SexUser'] = $bind['SexUser'];
                unset($bind['SexUser']);
				$bind['comment']['RaceStartMicro'] = intval(abs($bind['RaceStartMicro']));
				$bind['comment']['RaceStartMicro'] = min(999,$bind['comment']['RaceStartMicro']);
				unset($bind['RaceStartMicro']);
				//保存mylaps计时数据表的前缀
				$bind['RouteInfo']['MylapsPrefix'] = $bind['MylapsPrefix'];
				unset($bind['MylapsPrefix']);
				//保存单个计时点的忍耐时间（在该时间范围内的将被忽略）
				$bind['RouteInfo']['MylapsTolaranceTime'] = abs(intval($bind['MylapsTolaranceTime']));
				unset($bind['MylapsTolaranceTime']);
				//成绩计算数据源
				$bind['RouteInfo']['RaceTimingType'] = trim($bind['RaceTimingType']);
				unset($bind['RaceTimingType']);
				//成绩计算方式
				$bind['RouteInfo']['RaceTimingResultType'] = trim($bind['RaceTimingResultType']);
				unset($bind['RaceTimingResultType']);
                //循环选定的分组
                foreach($bind['SelectedRaceGroup'] as $Group => $GroupInfo)
                {
                    //删除未选定的元素
                    if($GroupInfo['Selected']==0)
                    {
                        unset($bind['SelectedRaceGroup'][$Group]);
                    }
                    else
                    {
                        //获取最早的开始时间作为比赛开始时间
                        $bind['StartTime'] = date("Y-m-d H:i:s",(strtotime($bind['StartTime'])>0?max(strtotime($bind['StartTime']),strtotime($GroupInfo['StartTime'])):strtotime($GroupInfo['StartTime'])));
                        //获取最晚的结束时间作为比赛结束时间
                        $bind['EndTime'] = date("Y-m-d H:i:s",(strtotime($bind['EndTime'])>0?max(strtotime($bind['EndTime']),strtotime($GroupInfo['EndTime'])):strtotime($GroupInfo['EndTime'])));
                        //获取毫秒时间
                        $bind['comment']['RaceStartMicro'] = ($bind['StartTime']==$GroupInfo['StartTime'])?$GroupInfo['RaceStartMicro']:$bind['comment']['RaceStartMicro'];
                        //结束报名时间不能晚于比赛开始时间
                        if ($ApplyEndTime>=(strtotime($GroupInfo['StartTime'])+$GroupInfo['RaceStartMicro']/1000))
                        {
                            $response = array('errno' => 12);break;
                        }
                    }
                }
				$bind['comment']['SelectedRaceGroup'] = $bind['SelectedRaceGroup'];
				unset($bind['SelectedRaceGroup']);
				//是否包含起点
				$bind['comment']['NoStart'] = $bind['NoStart'];
				unset($bind['NoStart']);
				//团队成绩计算名次
				$bind['comment']['TeamResultRank'] = $bind['TeamResultRank'];
				unset($bind['TeamResultRank']);
				//数据打包
				$bind['comment'] = json_encode($bind['comment']);
				//地图数据打包
				$bind['RouteInfo'] = json_encode($bind['RouteInfo']);
                if(!isset($response))
                {
                    //更新比赛
                    $UpdateRace = $this->oRace->updateRace($RaceId,$bind);
                    $response = $UpdateRace ? array('errno' => 0) : array('errno' => 9);
                }
			}
		}
		echo json_encode($response);
		return true;
	}
    //删除比赛
    public function raceDeleteAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("RaceDelete");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceId = intval($this->request->RaceId);
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId,'RaceId');
            //如果有获取到比赛信息
            if(isset($RaceInfo['RaceId']))
            {
                //删除
                $this->oRace->deleteRaceInfo($RaceId);
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
	//比赛详情页面
	public function raceDetailAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//赛事分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId,"RaceId,RaceName,RaceStageId,RaceGroupId,comment");
			//如果有获取到比赛信息
			if(isset($RaceInfo['RaceId']))
			{
				//获取当前分站信息
				$RaceStageInfo = $this->oRace->getRaceStage($RaceInfo['RaceStageId'],'RaceStageId,RaceStageName,comment,RaceCatalogId');
                $oCredit = new Xrace_Credit();
                //获取关联赛事下的积分类目列表
                $CreditArr = $oCredit->getCreditList($RaceStageInfo['RaceCatalogId']);
                //解包压缩数组
				$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
				//数据解包
				$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
				//如果当前分站未配置了当前分组
				if(!in_array($RaceGroupId,$RaceStageInfo['comment']['SelectedRaceGroup']))
				{
					$RaceGroupId = 0;
				}
				//获取赛事分组信息
				$RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,'*');
				//如果赛事分组尚未配置
				if(!$RaceGroupInfo['RaceGroupId'])
				{
					$RaceGroupId = 0;
				}
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
							//累加里程,如果距离为正数
							$RaceInfo['comment']['DetailList'][$Key]['Total']['Distence'] += (($tinfo['ToNext']>0)?($tinfo['ToNext']):0)*	$tinfo['Round'];
							//累加计时点数量
							$RaceInfo['comment']['DetailList'][$Key]['Total']['ChipCount'] += $tinfo['Round'];
							//累加海拔上升
							$RaceInfo['comment']['DetailList'][$Key]['Total']['AltAsc'] += $tinfo['AltAsc']*	$tinfo['Round'];
							//累加海拔下降
							$RaceInfo['comment']['DetailList'][$Key]['Total']['AltDec'] += $tinfo['AltDec']*	$tinfo['Round'];
                            //如果包含积分配置
                            if(count($tinfo['CreditList']))
                            {
                                //循环积分配置列表
                                foreach($tinfo['CreditList'] as $CreditId => $CreditInfo)
                                {
                                    //如果在总表中有找到
                                    if(isset($CreditArr[$CreditId]))
                                    {
                                        //保存积分名称
                                        $RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'][$tid]['CreditList'][$CreditId]['CreditName'] = $CreditArr[$CreditId]['CreditName'];
                                    }
                                    else
                                    {
                                        //删除该积分配置
                                        unset($RaceInfo['comment']['DetailList'][$Key]['TimingDetailList']['comment'][$tid]['CreditList'][$CreditId]);
                                    }
                                }
                            }
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
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
				if(isset($RaceInfo['RaceId']))
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
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']))
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
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
			$bind = $this->request->from('TName','ToNext','AltAsc','AltDec','Round','ChipId','TolaranceTime');
			//添加计时点
			$AddTimingPoint = $this->oRace->addTimingPoint($RaceId,$SportsTypeId,$After,$bind);
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//需要添加的运动类型置于哪个位置之后，默认为开头
			$After = isset($this->request->After)?intval($this->request->After):-1;
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']))
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			$this->oSports = new Xrace_Sports();
			//获取运动类型列表
			$SportsTypeList = $this->oSports->getAllSportsTypeList('SportsTypeId,SportsTypeName');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
			if(isset($RaceInfo['RaceId']))
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//获取 页面参数
			$bind = $this->request->from('TName','ToNext','AltAsc','AltDec','Round','ChipId','TolaranceTime');
			//更新计时点
			$UpdateTimingPoint = $this->oRace->updateTimingPoint($RaceId,$SportsTypeId,$TimingId,$bind);
			$response = $UpdateTimingPoint ? array('errno' => 0) : array('errno' => 9);
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
		$PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//计时点ID
			$TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
			//删除计时点
			$DeleteTimingPoint = $this->oRace->deleteTimingPoint($RaceId,$SportsTypeId,$TimingId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
    //添加计时点积分数据提交页面
    public function timingPointCreditAddAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceId = intval($this->request->RaceId);
            //运动类型ID
            $SportsTypeId = intval($this->request->SportsTypeId);
            //计时点ID
            $TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId,"RaceId,RaceStageId");
            //如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
            if(isset($RaceInfo['RaceId']))
            {
                $RaceStageInfo = $this->oRace->getRaceStage($RaceInfo['RaceStageId'],"RaceCatalogId,RaceStageId");
                $oCredit = new Xrace_Credit();
                //获取关联赛事下的积分类目列表
                $CreditArr = $oCredit->getCreditList($RaceStageInfo['RaceCatalogId']);
                //渲染模板
                include $this->tpl('Xrace_Race_TimingPointCreditAdd');
            }
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //添加计时点积分配置数据
    public function timingPointCreditInsertAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceId = intval($this->request->RaceId);
            //运动类型ID
            $SportsTypeId = intval($this->request->SportsTypeId);
            //计时点ID
            $TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
            //获取 页面参数
            $bind = $this->request->from('CreditRule','CreditId');
            //更新计时点
            $UpdateTimingPoint = $this->oRace->insertTimingPointCredit($RaceId,$SportsTypeId,$TimingId,$bind);
            $response = $UpdateTimingPoint ? array('errno' => 0) : array('errno' => $UpdateTimingPoint);
            echo json_encode($response);
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //更新计时点积分数据提交页面
    public function timingPointCreditModifyAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceId = intval($this->request->RaceId);
            //运动类型ID
            $SportsTypeId = intval($this->request->SportsTypeId);
            //计时点ID
            $TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
            //积分ID
            $CreditId = isset($this->request->CreditId)?intval($this->request->CreditId):0;
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId,"RaceId,RaceStageId,comment");
            //如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
            if(isset($RaceInfo['RaceId']))
            {
                //数据解包
                $RaceInfo['comment'] = isset($RaceInfo['comment'])?json_decode($RaceInfo['comment'],true):array();
                //获取关联的赛事分站信息
                $RaceStageInfo = $this->oRace->getRaceStageInfo($RaceInfo['RaceStageId'],"RaceCatalogId,RaceStageId");
                $this->oCredit = new Xrace_Credit();
                //获取关联赛事下的积分类目列表
                $CreditArr = $this->oCredit->getCreditList($RaceStageInfo['RaceCatalogId']);
                //获取运动类型信息
                $SportsTypeInfo = $RaceInfo['comment']['DetailList'][$SportsTypeId];
                //初始化计时点列表
                $SportsTypeInfo['TimingDetailList'] = isset($SportsTypeInfo['TimingId'])?$this->oRace->getTimingDetail($SportsTypeInfo['TimingId']):array();
                //解包数据
                $SportsTypeInfo['TimingDetailList']['comment'] = isset($SportsTypeInfo['TimingDetailList']['comment'])?json_decode($SportsTypeInfo['TimingDetailList']['comment'],true):array();
                //获取计时点信息
                $TimingInfo = $SportsTypeInfo['TimingDetailList']['comment'][$TimingId];
                //获取积分信息
                $CreditInfo = $TimingInfo['CreditList'][$CreditId];
                //渲染模板
                include $this->tpl('Xrace_Race_TimingPointCreditModify');
            }
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //更新计时点积分数据提交页面
    public function timingPointCreditDeleteAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("TimingPointModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceId = intval($this->request->RaceId);
            //运动类型ID
            $SportsTypeId = intval($this->request->SportsTypeId);
            //计时点ID
            $TimingId = isset($this->request->TimingId)?intval($this->request->TimingId):0;
            //积分ID
            $CreditId = isset($this->request->CreditId)?intval($this->request->CreditId):0;
            //获取比赛信息
            $RaceInfo = $this->oRace->getRace($RaceId,"RaceId");
            //如果有获取到比赛信息 并且 赛事分站ID和赛事分组ID相符
            if(isset($RaceInfo['RaceId']))
            {
                //更新计时点
                $DeleteTimingPoint = $this->oRace->deleteTimingPointCredit($RaceId,$SportsTypeId,$TimingId,$CreditId);
                $this->response->goBack();
            }
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
		$PermissionCheck = $this->manager->checkMenuPermission("ProductModify");
		if($PermissionCheck['return'])
		{
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
						$ProductSkuList = $this->oProduct->getAllProductSkuList($ProductId);
						foreach($ProductSkuList[$ProductId] as $ProductSkuId => $ProductSkuInfo)
						{
							if(isset($SelectedProductList[$ProductId][$ProductSkuId]))
							{
								$ProductSkuList[$ProductId][$ProductSkuId]['Stock'] = 	$SelectedProductList[$ProductId][$ProductSkuId]['Stock'];
								$ProductSkuList[$ProductId][$ProductSkuId]['ProductPrice'] = 	$SelectedProductList[$ProductId][$ProductSkuId]['ProductPrice'];
								$ProductSkuList[$ProductId][$ProductSkuId]['ProductLimit'] = 	$SelectedProductList[$ProductId][$ProductSkuId]['ProductLimit'];
							}
							else
							{
								$ProductSkuList[$ProductId][$ProductSkuId]['Stock'] = 	0;
								$ProductSkuList[$ProductId][$ProductSkuId]['ProductPrice'] = 	0;
								$ProductSkuList[$ProductId][$ProductSkuId]['ProductLimit'] = 	0;
							}
						}
						$ProductTypeList[$ProductTypeId]['ProductList'][$ProductId]['ProductSkuList'] = $ProductSkuList[$ProductId];
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
		$PermissionCheck = $this->manager->checkMenuPermission("ProductModify");
		if($PermissionCheck['return'])
		{
			//赛事ID
			$RaceStageId = intval($this->request->RaceStageId);
			//获取赛站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,"RaceStageId,comment");
			//解包数组
			$bind['comment'] = json_decode($RaceStageInfo['comment'],true);
			if($RaceStageInfo['RaceStageId'])
			{
				//获取已经选定的商品列表
				//$CheckedProduct = $this->request->from('ProductChecked');
				//获取已经选定的商品数据
				$ProductPrice = $this->request->from('ProductPrice');
				//循环已选择的产品列表
				foreach($ProductPrice['ProductPrice'] as $ProductId => $ProductSkuList)
				{

					foreach($ProductSkuList as $ProductSkuId => $ProductSkuInfo)
					{
						if(intval($ProductSkuInfo['Stock'])<=0)
						{
							unset($ProductPrice['ProductPrice'][$ProductId][$ProductSkuId]);
						}
						else
						{
							$ProductPrice['ProductPrice'][$ProductId][$ProductSkuId]['ProductLimit'] = intval($ProductSkuInfo['ProductLimit'])>=3?3:intval($ProductSkuInfo['ProductLimit']);
						}
					}
				}
			}
			//存入数组中
			$bind['comment']['SelectedProductList'] = $ProductPrice['ProductPrice'];
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
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//比赛分组
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
			//格式化分组ID
			$RaceGroupId = (in_array($RaceGroupId,array(0,$RaceInfo['RaceGroupId'])) || in_array($RaceGroupId,$RaceInfo['comment']['SelectedRaceGroup']))?$RaceGroupId:0;
			//生成查询条件
			$params = array('RaceId'=>$RaceInfo['RaceId']);
			$oUser = new Xrace_User();
			//获取选手名单
			$RaceUserList = $oUser->getRaceUserListByRace($RaceInfo['RaceId'],$RaceGroupId,0,0);
            //渲染模板
			include $this->tpl('Xrace_Race_RaceUserList');
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
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//获取BIB号码列表
			$UserList = $this->request->from('UserList');
			$oUser = new Xrace_User();
			//循环号码牌列表
			foreach($UserList['UserList'] as $Id => $UserInfo)
			{
				//根据报名记录ID获取用户报名信息
				$RaceUserInfo = $oUser->getRaceApplyUserInfo($UserInfo['ApplyId'],'ApplyId,UserId,RaceId,comment');
				//复制到待更新数据
				$bind = $RaceUserInfo;
				//数据解包
				$bind['comment'] = json_decode($bind['comment'],true);
				//BIB
				$bind['BIB'] = trim($UserInfo['BIB']);
				//计时芯片ID
				$bind['ChipId'] = trim($UserInfo['ChipId']);
				//XPLOVER追踪链接
				$bind['comment']['XpUrl'] = trim($UserInfo['XpUrl']);
				//北斗魔盒的设备ID
				$bind['comment']['BDDeviceId'] = trim($UserInfo['BDDeviceId']);
				//数据打包
				$bind['comment'] = json_encode($bind['comment']);
				//更新报名记录
				$oUser->updateRaceUserApply($UserInfo['ApplyId'],$bind);
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
	//报名记录上传提交页面
	public function raceUserUploadSubmitAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//比赛分组
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			$RaceGroupId = in_array($RaceGroupId,array(0,$RaceInfo['RaceGroupId']))?$RaceGroupId:0;
			//如果有获取到比赛信息
			if(isset($RaceInfo['RaceId']))
			{
				//渲染模板
				include $this->tpl('Xrace_Race_RaceUserUpload');
			}
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//报名记录上传
	public function raceUserUploadAction()
	{
        $NameErrorUser = array();
	    //检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//比赛分组
			$RaceGroupId = intval($this->request->RaceGroupId);
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			$RaceGroupId = in_array($RaceGroupId,array(0,$RaceInfo['RaceGroupId']))?$RaceGroupId:0;
			//如果有获取到比赛信息
			if(isset($RaceInfo['RaceId']))
			{
				//获取当前时间
				$CurrentTime = date("Y-m-d H:i:s",time());
				//获取赛事信息
				$RaceStageInfo = $this->oRace->getRaceStage($RaceInfo['RaceStageId'],"RaceStageId,RaceCatalogId");
				//文件上传
				$oUpload = new Base_Upload('RaceUserList');
				$upload = $oUpload->upload('RaceUserList');
				$res = $upload->resultArr;
				//打开文件
				$handle = fopen($res['1']['path'], 'r');
				$content = '';
				$ApplyCount = 0;
				$oUser = new Xrace_User();
				$oTeam = new Xrace_Team();
				//循环到文件结束
				while(!feof($handle))
				{
					//获取每行信息
					$content= fgets($handle, 8080);
					//以,为分隔符解开
					$t = explode(",",$content);
                    $mobile = trim($t[5]);
					//如果手机号码默认不填
                    if($mobile=='tbd')
					{
						$new = 1;
					}
					//如果手机号码已经被占用并且用以报名
					elseif(isset($SuccessMobilieList[$mobile]))
                    {
                        $mobile = 'tbd';$new = 1;
                    }
					elseif($mobile!="")
					{
						//根据手机号码获取用户信息
						$UserInfo = $oUser->getUserInfoByMobile($mobile,"user_id,name");
					}
					//如果用户没有获取到 并且手机号码不为空
					if($new == 1 || (!isset($UserInfo['user_id']) && $mobile!=""))
					{
						//生成新用户ID
						$NewUserId = $oUser->genNewUserId();
						//如果生成成功
						if($NewUserId )
						{
							//生成用户信息
							$UserInfo = array('user_id'=>$NewUserId,'name'=>trim(iconv('GB2312', 'UTF-8//IGNORE', $t[2])),'sex'=>intval($t[3]),'phone'=>$mobile,'pwd'=>'tbd','crt_time'=>$CurrentTime);
							//创建 用户
							$InsertUser = $oUser->insertUser($UserInfo);
							//如果创建不成功
							if(!$InsertUser)
							{
								continue;
							}
						}
						else
						{
							continue;
						}
					}
					//如果检测到用户ID
					if(isset($UserInfo['user_id']))
					{
					    //如果姓名为空
						if($UserInfo['name']=="")
						{
							//$bind = array('name'=>trim($t[2]),'sex'=>intval($t[3]));
							//$bind = array('name'=>trim(iconv('GB2312', 'UTF-8//IGNORE', $t[2])));

							$bind = array('name'=>trim(iconv('GB2312', 'UTF-8//IGNORE', $t[2])),'sex'=>intval($t[3]));
							$oUser->updateUserInfo($UserInfo['user_id'], $bind);
						}
						elseif($UserInfo['name']!=trim(iconv('GB2312', 'UTF-8//IGNORE', $t[2])))
                        {
                            //记录名字不匹配的用户
                            $NameErrorUser[] = trim(iconv('GB2312', 'UTF-8//IGNORE', $t[2]));
                            continue;
                        }
						//获取车队名称
						//$RaceTeamName = trim($t[6]);
						$RaceTeamName = trim(iconv('GB2312', 'UTF-8//IGNORE', $t[6]));
						//获取车队信息
						$RaceTeamInfo = $oTeam->getRaceTeamInfoByName($RaceTeamName,'team_id as RaceTeamId,name as RaceTeamName');
						//判断车队是否获取到
						$RaceTeamId = isset($RaceTeamInfo['RaceTeamId'])?$RaceTeamInfo['RaceTeamId']:$oTeam->insertRaceTeam(array("name"=>$RaceTeamName,"crt_time"=>date("Y-m-d H:i:s",time()),"is_open"=>0));
                        if($RaceGroupId==0)
                        {
                            if(!isset($RaceGroupList[trim(iconv('GB2312', 'UTF-8//IGNORE', $t[1]))]))
                            {
                                $RaceGroupInfo = $this->oRace->getRaceGroupByName(trim(iconv('GB2312', 'UTF-8//IGNORE', $t[1])),"RaceGroupId,RaceGroupName");
                                if($RaceGroupInfo['RaceGroupId'])
                                {
                                    $RaceGroupList[$RaceGroupInfo['RaceGroupName']] = $RaceGroupInfo;
                                }
                            }
                            if(isset($RaceGroupList[trim(iconv('GB2312', 'UTF-8//IGNORE', $t[1]))]))
                            {
                                //初始化新报名记录的信息
                                $ApplyInfo = array("ApplyTime"=>$CurrentTime,"UserId"=>$UserInfo['user_id'],"RaceCatalogId"=>$RaceStageInfo['RaceCatalogId'],"RaceGroupId"=>$RaceGroupList[trim(iconv('GB2312', 'UTF-8//IGNORE', $t[1]))]['RaceGroupId'],"RaceStageId"=>$RaceInfo['RaceStageId'],"RaceId"=>$RaceInfo['RaceId'],"BIB"=>trim($t[0]),"ChipId"=>trim($t[4]),"RaceTeamId"=>$RaceTeamId);
                            }
                            else
                            {
                                continue;
                            }
                        }
                        else
                        {
                            //初始化新报名记录的信息
                            $ApplyInfo = array("ApplyTime"=>$CurrentTime,"UserId"=>$UserInfo['user_id'],"RaceCatalogId"=>$RaceStageInfo['RaceCatalogId'],"RaceGroupId"=>$RaceInfo['RaceGroupId'],"RaceStageId"=>$RaceInfo['RaceStageId'],"RaceId"=>$RaceInfo['RaceId'],"BIB"=>trim($t[0]),"ChipId"=>trim($t[4]),"RaceTeamId"=>$RaceTeamId);
                        }
						//如果存在，则更新部分信息
						$ApplyUpdateInfo = array("ApplyTime"=>$CurrentTime,"BIB"=>trim($t[0]),"ChipId"=>trim($t[4]),"RaceTeamId"=>$RaceTeamId);
						//创建/更新报名记录
                        $Apply = $oUser->insertRaceApplyUserInfo($ApplyInfo,$ApplyUpdateInfo);
						//如果创建成功
                        if($Apply)
						{
						    //添加用户的签到记录
                            $CheckInInfo = array("UserId"=>$UserInfo['user_id'],"RaceCatalogId"=>$RaceStageInfo['RaceCatalogId'],"RaceStageId"=>$RaceInfo['RaceStageId'],"Mobile"=>$mobile,"CheckinCode"=>sprintf("%06x",$RaceInfo['RaceStageId'])."|".sprintf("%08x",$UserInfo['user_id']));
                            $oUser->insertUserCheckInInfo($CheckInInfo);
						    //成功数量递增
						    $ApplyCount ++;
                            //成功用于报名的用户记录临时数组
                            $SuccessMobilieList[$mobile] = 1;
						}
					}
				}
			}
			echo json_encode(array('errno' => 0,'ApplyCount'=>$ApplyCount,'NameErrorUserCount'=>count($NameErrorUser),'NameErrorUser'=>count($NameErrorUser)>0?implode(",",$NameErrorUser):"无"));
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//用户退出比赛
	public function userRaceDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			$oUser = new Xrace_User();
			//报名记录ID
			$ApplyId = intval($this->request->ApplyId);
			//更新数据
			$res = $oUser->deleteUserRace($ApplyId);
			//返回之前页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
		//整场比赛用户退出比赛
	public function userRaceDeleteByRaceAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			$oUser = new Xrace_User();
			//比赛ID
			$RaceId = intval($this->request->RaceId);
			//分组ID
			$RaceGroupId = intval($this->request->RaceGroupId);
			//更新数据
			$res = $oUser->deleteUserRaceByRace($RaceId,$RaceGroupId);
			//返回之前页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//单场比赛的成绩单
	public function raceResultListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			$oUser = new Xrace_User();
			//比赛ID
			$RaceId = intval($this->request->RaceId);
            //比赛ID
            $RaceGroupId = intval($this->request->RaceGroupId);
            //用户ID
			$UserId = intval($this->request->UserId);
			//获取用户信息
			$UserInfo = $oUser->getUserInfo($UserId,'user_id,name');
			//获取比赛信息
			$RaceInfo = $this->oRace->getRace($RaceId);
			//数据解包
			$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
			//获取成绩列表
			$RaceResultList = $this->oRace->getRaceResult($RaceId,$RaceGroupId);
            if(count($RaceResultList['UserRaceTimingInfo']['RaceInfo']['comment']['SelectedRaceGroup']))
            {
                foreach($RaceResultList['UserRaceTimingInfo']['RaceInfo']['comment']['SelectedRaceGroup'] as $RaceGroupId)
                {
                    $RaceGroupInfo = $this->oRace->getRaceGroup($RaceGroupId,"RaceGroupId,RaceGroupName");
                    if($RaceGroupInfo['RaceGroupId'])
                    {
                        $RaceGroupList[$RaceGroupId] = 	"<a href='".Base_Common::getUrl('','xrace/race.stage','race.result.list',array('RaceId'=>$RaceId,'RaceGroupId'=>$RaceGroupId)) ."'>".$RaceGroupInfo['RaceGroupName']."</a>";
                    }
                }
            }
            //渲染模板
			include $this->tpl('Xrace_Race_RaceResultList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//套餐添加填写页面
	public function raceCombinationAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//获取比赛信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId);
			//获取比赛列表
			$RaceList = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageInfo['RaceStageId']),'RaceId,RaceName,RaceGroupId,RaceTypeId');
			//获取比赛类型列表
			$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
			//赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//循环比赛列表
			foreach($RaceList as $RaceId => $RaceInfo)
			{
				//获取比赛类型名称
				$RaceList[$RaceId]['RaceGroupName'] = isset($RaceGroupList[$RaceInfo['RaceGroupId']]['RaceGroupName'])?$RaceGroupList[$RaceInfo['RaceGroupId']]['RaceGroupName']:"未配置";
				//获取比赛类型名称
				$RaceList[$RaceId]['RaceTypeName'] = isset($RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName'])?$RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName']:"未配置";
			}
			//解包数组
			$RaceStageInfo['comment'] = isset($RaceStageInfo['comment']) ? json_decode($RaceStageInfo['comment'], true) : array();
			if (isset($RaceStageInfo['comment']['SelectedProductList']))
			{
				//初始化一个空的产品列表
				$ProductList = array();
				//循环产品列表
				foreach ($RaceStageInfo['comment']['SelectedProductList'] as $ProductId => $ProductSkuList)
				{
					//如果产品列表中没有此产品
					if (!isset($ProductList[$ProductId]))
					{
						//获取产品信息
						$ProductInfo = $this->oProduct->getProduct($ProductId, "ProductId,ProductName,comment");
						//如果产品信息获取到
						if(isset($ProductInfo['ProductId']))
						{
							$SkuList = $this->oProduct->getAllProductSkuList($ProductId);
							$t = array();
							foreach($SkuList[$ProductId] as $k => $v)
							{
								if(isset($ProductSkuList[$k]))
								{
									$t[$k] = $v['ProductSkuName'];
								}
							}
							if(count($SkuList[$ProductId])>=1)
							{
								//存入产品名称
								$RaceStageInfo['comment']['SelectedProductList'][$ProductId] = array('ProductName' => $ProductInfo['ProductName'],'ProductSkuList'=>implode("/",$t));
							}
							else
							{
								unset($RaceStageInfo['comment']['SelectedProductList'][$ProductId]);
							}
						}
						else
						{
							unset($RaceStageInfo['comment']['SelectedProductList'][$ProductId]);
						}
					}
					else
					{
						continue;
					}
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceCombinationAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//套餐添加
	public function raceCombinationInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//获取 页面参数
			$bind=$this->request->from('RaceStageId','RaceCombinationName','PriceList');
			//获取已经选定的比赛列表
			$RaceList = $this->request->from('RaceList');
			//获取已经选定的产品列表
			$ProductList = $this->request->from('ProductList');
			//套餐名称不能为空
			if(trim($bind['RaceCombinationName'])=="")
			{
				$response = array('errno' => 1);
			}
			else
			{
				//获取当前分站信息
				$RaceStageInfo = $this->oRace->getRaceStage($bind['RaceStageId'],'*');
				//如果有获取到分站信息
				if(!isset($RaceStageInfo['RaceStageId']))
				{
					$response = array('errno' => 2);
				}
				else
				{
					//套餐内的比赛数量
					$RaceCount = count($RaceList['RaceList']);
					foreach($ProductList['ProductList'] as $ProductId => $ProductInfo)
					{
						if($ProductInfo['ProductCount']<1)
						{
							unset($ProductList['ProductList'][$ProductId]);
						}
					}
					//套餐内产品数量
					$ProductCount = count($ProductList['ProductList']);
					//如果产品与比赛数量小于2
					if(($RaceCount+$ProductCount)<2)
					{
						$response = array('errno' => 3);
					}
					else
					{
						$bind['ProductList'] = json_encode($ProductList['ProductList']);
						$bind['RaceList'] = json_encode($RaceList['RaceList']);
						$bind['RaceCatalogId'] = $RaceStageInfo['RaceCatalogId'];
						//插入数据
						$res = $this->oRace->insertRaceCombination($bind);
						$response = $res ? array('errno' => 0) : array('errno' => 9);
					}
				}
			}
			echo json_encode($response);
			return true;
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//比赛列表页面
	public function raceCombinationListAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//赛事分站ID
			$RaceStageId = intval($this->request->RaceStageId);
			//获取当前分站信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'RaceStageId,RaceStageName,comment');
			//解包压缩数组
			$RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
			$params = array('RaceStageId'=>$RaceStageId);
			//获取报名套餐列表
			$RaceCombinationList = $this->oRace->getRaceCombinationList($params);
			//循环套餐列表
			foreach($RaceCombinationList as $RaceCombinationId => $RaceCombinationInfo)
			{
				//解压缩比赛列表和产品列表
				$RaceCombinationList[$RaceCombinationId]['RaceList'] = json_decode($RaceCombinationInfo['RaceList'],true);
				//如果有配置比赛
				if(count($RaceCombinationList[$RaceCombinationId]['RaceList']))
				{
					//循环比赛列表
					foreach($RaceCombinationList[$RaceCombinationId]['RaceList'] as $RaceId => $Race)
					{
						//获取比赛信息
						$RaceInfo = $this->oRace->getRace($RaceId,"RaceId,RaceName,RaceGroupId");
						//如果有获取到
						if(isset($RaceInfo['RaceId']))
						{
							$RaceGroupInfo = $this->oRace->getRaceGroup($RaceInfo['RaceGroupId'],"RaceGroupId,RaceGroupName");
							if(isset($RaceGroupInfo['RaceGroupId']))
							{
								$RaceInfo['RaceGroupInfo'] = $RaceGroupInfo;
							}
							//保存比赛信息
							$RaceCombinationList[$RaceCombinationId]['RaceList'][$RaceId] = $RaceInfo;
						}
						else
						{
							//否则删除数据
							unset($RaceCombinationList[$RaceCombinationId]['RaceList'][$RaceId]);
						}
					}
				}
				$RaceCombinationList[$RaceCombinationId]['ProductList'] = json_decode($RaceCombinationInfo['ProductList'],true);
				//如果有配置比赛
				if(count($RaceCombinationList[$RaceCombinationId]['ProductList']))
				{
					//循环比赛列表
					foreach($RaceCombinationList[$RaceCombinationId]['ProductList'] as $ProductId => $Product)
					{
						if($Product['ProductCount']>=1)
						{
							//获取比赛信息
							$ProductInfo = $this->oProduct->getProduct($ProductId,"ProductId,ProductName");
							//如果有获取到
							if(isset($ProductInfo['ProductId']))
							{
								//如果有找到配置的SKU列表
								if(isset($RaceStageInfo['comment']['SelectedProductList'][$ProductId]))
								{
									$t = array();
									//获取产品的SKU列表
									$SkuList = $this->oProduct->getAllProductSkuList($ProductId);
									//循环分站已配置的SKU列表
									foreach($RaceStageInfo['comment']['SelectedProductList'][$ProductId] as $SkuId => $SkuInfo)
									{
										//如果SKU存在
										if(isset($SkuList[$ProductId][$SkuId]))
										{
											$t[] = $SkuList[$ProductId][$SkuId]['ProductSkuName'];
											//保存SKU名称
											$RaceStageInfo['comment']['SelectedProductList'][$ProductId][$SkuId]['SkuName'] = $SkuList[$ProductId][$SkuId]['ProductSkuName'];
										}
									}
									//生成显示用的SKU列表
									$ProductInfo['SkuListText'] = "(".implode("/",$t).")";
									//保存产品信息
									$ProductInfo['SkuList'] = $RaceStageInfo['comment']['SelectedProductList'][$ProductId];
								}
								$ProductInfo['ProductCount'] = $Product['ProductCount'];
								//保存比赛信息
								$RaceCombinationList[$RaceCombinationId]['ProductList'][$ProductId] = $ProductInfo;
							}
							else
							{
								//否则删除数据
								unset($RaceCombinationList[$RaceCombinationId]['ProductList'][$ProductId]);
							}
						}
						else
						{
							//否则删除数据
							unset($RaceCombinationList[$RaceCombinationId]['ProductList'][$ProductId]);
						}
					}
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceCombinationList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	public function raceCombinationModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//分站ID
			$RaceCombinationId = intval($this->request->RaceCombinationId);
			//获取套餐信息
			$RaceCombinationInfo = $this->oRace->getRaceCombination($RaceCombinationId);
			//解包比赛列表
			$RaceCombinationInfo['RaceList'] = json_decode($RaceCombinationInfo['RaceList'],true);
			//解包产品列表
			$RaceCombinationInfo['ProductList'] = json_decode($RaceCombinationInfo['ProductList'],true);
			//获取比赛信息
			$RaceStageInfo = $this->oRace->getRaceStage($RaceCombinationInfo['RaceStageId']);
			//获取比赛列表
			$RaceList = $this->oRace->getRaceList($RaceCombinationInfo['RaceStageId'],0,'RaceId,RaceName,RaceGroupId,RaceTypeId');
			//获取比赛类型列表
			$RaceTypeList  = $this->oRace->getRaceTypeList("RaceTypeId,RaceTypeName");
			//赛事分组列表
			$RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
			//循环比赛列表
			foreach($RaceList as $RaceId => $RaceInfo)
			{
				$RaceList[$RaceId]['selected'] = isset($RaceCombinationInfo['RaceList'][$RaceId])?1:0;
				//获取比赛类型名称
				$RaceList[$RaceId]['RaceGroupName'] = isset($RaceGroupList[$RaceInfo['RaceGroupId']]['RaceGroupName'])?$RaceGroupList[$RaceInfo['RaceGroupId']]['RaceGroupName']:"未配置";
				//获取比赛类型名称
				$RaceList[$RaceId]['RaceTypeName'] = isset($RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName'])?$RaceTypeList[$RaceInfo['RaceTypeId']]['RaceTypeName']:"未配置";
			}
			//解包数组
			$RaceStageInfo['comment'] = isset($RaceStageInfo['comment']) ? json_decode($RaceStageInfo['comment'], true) : array();
			if (isset($RaceStageInfo['comment']['SelectedProductList']))
			{
				//初始化一个空的产品列表
				$ProductList = array();
				//循环产品列表
				foreach ($RaceStageInfo['comment']['SelectedProductList'] as $ProductId => $ProductSkuList)
				{
					//如果产品列表中没有此产品
					if (!isset($ProductList[$ProductId]))
					{
						//获取产品信息
						$ProductInfo = $this->oProduct->getProduct($ProductId, "ProductId,ProductName,comment");
						//如果产品信息获取到
						if(isset($ProductInfo['ProductId']))
						{
							$SkuList = $this->oProduct->getAllProductSkuList($ProductId);
							$t = array();
							foreach($SkuList[$ProductId] as $k => $v)
							{
								if(isset($ProductSkuList[$k]))
								{
									$t[$k] = $v['ProductSkuName'];
								}
							}
							if(count($SkuList[$ProductId])>=1)
							{
								//存入产品名称
								$RaceStageInfo['comment']['SelectedProductList'][$ProductId] = array('ProductName' => $ProductInfo['ProductName'],'ProductSkuList'=>implode("/",$t),'ProductCount'=>isset($RaceCombinationInfo['ProductList'][$ProductId])?$RaceCombinationInfo['ProductList'][$ProductId]['ProductCount']:0);
							}
							else
							{
								unset($RaceStageInfo['comment']['SelectedProductList'][$ProductId]);
							}
						}
						else
						{
							unset($RaceStageInfo['comment']['SelectedProductList'][$ProductId]);
						}
					}
					else
					{
						continue;
					}
				}
			}
			//渲染模板
			include $this->tpl('Xrace_Race_RaceCombinationModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//套餐添加
	public function raceCombinationUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
		if($PermissionCheck['return'])
		{
			//获取 页面参数
			$bind=$this->request->from('RaceCombinationId','RaceCombinationName','PriceList');
			//获取已经选定的比赛列表
			$RaceList = $this->request->from('RaceList');
			//获取已经选定的产品列表
			$ProductList = $this->request->from('ProductList');
			//套餐名称不能为空
			if(trim($bind['RaceCombinationName'])=="")
			{
				$response = array('errno' => 1);
			}
			else
			{
				//获取套餐信息
				$RaceCombinationInfo = $this->oRace->getRaceCombination($bind['RaceCombinationId']);
				//获取当前分站信息
				$RaceStageInfo = $this->oRace->getRaceStage($RaceCombinationInfo['RaceStageId'],'*');
				//套餐内的比赛数量
				$RaceCount = count($RaceList['RaceList']);
				foreach($ProductList['ProductList'] as $ProductId => $ProductInfo)
				{
					if($ProductInfo['ProductCount']<1)
					{
						unset($ProductList['ProductList'][$ProductId]);
					}
				}
				//套餐内产品数量
				$ProductCount = count($ProductList['ProductList']);
				//如果产品与比赛数量小于2
				if(($RaceCount+$ProductCount)<2)
				{
					$response = array('errno' => 3);
				}
				else
				{
					$bind['ProductList'] = json_encode($ProductList['ProductList']);
					$bind['RaceList'] = json_encode($RaceList['RaceList']);
					$bind['RaceCatalogId'] = $RaceStageInfo['RaceCatalogId'];
					//插入数据
					$res = $this->oRace->updateRaceCombination($bind['RaceCombinationId'],$bind);
					$response = $res ? array('errno' => 0) : array('errno' => 9);
				}

			}
			echo json_encode($response);
			return true;
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
    //用户签到信息列表
    public function raceUserCheckInStatusAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceStageId = intval($this->request->RaceStageId);
            //分站数据
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'RaceStageId,RaceStageName');
            $oUser = new Xrace_User();
            $params = array('RaceStageId'=>$RaceStageInfo['RaceStageId']);
            //获取选手签到状态列表
            $UserCheckInStatusList = $oUser->getRaceUserCheckInList($params);
            $CheckInStatus = array();
            $oUser = new Xrace_User();
            //获取签到状态列表
            $UserCheckInStatus = $oUser->getUserCheckInStatus();
            foreach($UserCheckInStatus as $Status => $StatusName)
            {
                $CheckInStatus[$Status]['UserCount'] = 0;
                $CheckInStatus[$Status]['CheckInStatusName'] = $StatusName;
                $CheckInStatus[$Status]['StatusUrl'] = "<a href='".Base_Common::getUrl('','xrace/race.stage','user.check.in.status',array('RaceStageId'=>$RaceStageInfo['RaceStageId'],'UserCheckInStatus'=>$Status)) ."'>".$StatusName."</a>";
            }
            //获取签到短信发送状态列表
            $UserCheckInSmsSentStatus = $oUser->getUserCheckInSmsSentStatus();
            foreach($UserCheckInStatusList as $key => $CheckInInfo)
            {
                //获取用户信息
                $UserInfo = $oUser->getUserInfo($CheckInInfo['UserId'],"user_id,name");
                //如果未获取到用户信息
                if(!isset($UserInfo['user_id']))
                {
                    unset($UserCheckInStatusList[$key]);
                }
                else
                {
                    $CheckInStatus[$CheckInInfo['CheckinStatus']]['UserCount'] ++;
                    $CheckInStatus[0]['UserCount'] ++;
                    $UserCheckInStatusList[$key]['UserInfo'] = $UserInfo;
                }
                //签到状态
                $UserCheckInStatusList[$key]['CheckInStatusName'] = $UserCheckInStatus[$CheckInInfo['CheckinStatus']];
                //签到短信状态
                $UserCheckInStatusList[$key]['CheckInSmsSentStatusName'] = $UserCheckInSmsSentStatus[$CheckInInfo['SmsSentStatus']];
            }
            foreach($CheckInStatus as $Status => $StatusInfo)
            {
                $CheckInStatus[$Status]['StatusUrl'] = $StatusInfo['CheckInStatusName'].":"."<a href='".Base_Common::getUrl('','xrace/race.stage','user.check.in.status',array('RaceStageId'=>$RaceStageInfo['RaceStageId'],'UserCheckInStatus'=>$Status)) ."'>".$StatusInfo['UserCount']."人</a>";
            }
            $CheckInUrl = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.user.check.in',array('RaceStageId'=>$RaceStageInfo['RaceStageId'])) ."'>去签到</a>";
            //渲染模板
            include $this->tpl('Xrace_Race_RaceUserCheckInList');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //用户签到t提交页面
    public function raceUserCheckInAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceStageId = intval($this->request->RaceStageId);
            //分站数据
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'RaceStageId,RaceStageName');
            //签到状态列表
            $CheckInStatusUrl = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.user.check.in.status',array('RaceStageId'=>$RaceStageInfo['RaceStageId'])) ."'>返回</a>";
            //渲染模板
            include $this->tpl('Xrace_Race_RaceUserCheckIn');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    public function userCheckInAction()
    {
        //获取 页面参数
        $bind=$this->request->from('CheckInCode','RaceStageId');
        $CheckIn = $this->oRace->CheckIn($bind['RaceStageId'],$bind['CheckInCode']);
        if($CheckIn)
        {
            $response = array('errno' => 0,'UserId'=>$CheckIn);
        }
        else
        {
            $response = array('errno' => 1);
        }
        echo json_encode($response);
        return true;
    }
    //比赛选手列表 批量更新BIB和计时芯片ID
    public function raceUserCheckInBibAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
        if($PermissionCheck['return'])
        {
            //用户ID
            $UserId = intval($this->request->UserId);
            //比赛分站
            $RaceStageId = intval($this->request->RaceStageId);
            //分站数据
            $RaceStageInfo = $this->oRace->getRaceStage($RaceStageId,'RaceStageId,RaceStageName,RaceCatalogId,comment');
            //数据解包
            $RaceStageInfo['comment'] = json_decode($RaceStageInfo['comment'],true);
            $oUser = new Xrace_User();
            //获取用户信息
            $UserInfo = $oUser->getUserInfo($UserId,'user_id,name');
            $params = array('RaceStageId'=>$RaceStageId,'UserId'=>$UserId);
            //获取选手报名记录
            $UserRaceList = $oUser->getRaceUserList($params);
            //获取报名记录来源列表
            $ApplySourceList = $oUser->getRaceApplySourceList();
            //获取分组列表
            $RaceGroupList = $this->oRace->getRaceGroupList($RaceStageInfo['RaceCatalogId'],'RaceGroupId,RaceGroupName');
            //获取比赛列表
            $RaceList = $this->oRace->getRaceList(array("RaceStageId"=>$RaceStageInfo['RaceStageId']),"RaceId,RaceName,comment");
            foreach($UserRaceList as $key => $ApplyInfo)
            {
                $UserRaceList[$key]["ApplySourceName"] = $ApplySourceList[$ApplyInfo['ApplySource']];
                $UserRaceList[$key]["RaceGroupName"] = $RaceGroupList[$ApplyInfo['RaceGroupId']]['RaceGroupName'];
                $UserRaceList[$key]["RaceName"] = $RaceList[$ApplyInfo['RaceId']]['RaceName'];
            }
            //签到状态列表
            $CheckInupUrl = "<a href='".Base_Common::getUrl('','xrace/race.stage','race.user.check.in',array('RaceStageId'=>$RaceStageInfo['RaceStageId'])) ."'>返回</a>";
            //渲染模板
            include $this->tpl('Xrace_Race_RaceUserCheckInBIB');
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //批量更新比赛选手列表
    public function userRaceListUpdateAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
        if($PermissionCheck['return'])
        {
            //比赛ID
            $RaceStageId = intval($this->request->RaceStageId);
            //获取BIB号码列表
            $UserRaceList = $this->request->from('UserRaceList');
            $oUser = new Xrace_User();
            //循环号码牌列表
            foreach($UserRaceList['UserRaceList'] as $Id => $UserRaceInfo)
            {
                //BIB
                $bind['BIB'] = trim($UserRaceInfo['BIB']);
                //计时芯片ID
                $bind['ChipId'] = trim($UserRaceInfo['ChipId']);
                //更新报名记录
                $oUser->updateRaceUserApply($UserRaceInfo['ApplyId'],$bind);
            }
            $response = array('errno' => 0);
            echo json_encode($response);
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }
    //批量下载Mylaps用的用户名单
    public function mylpasListDownloadAction()
    {
        //检查权限
        $PermissionCheck = $this->manager->checkMenuPermission("RaceModify");
        if($PermissionCheck['return'])
        {
            //分站ID
            $RaceStageId = intval($this->request->RaceStageId);
            //生成查询条件
            $params = array('RaceStageId'=>$RaceStageId);
            $oUser = new Xrace_User();
            $oTeam = new Xrace_Team();
            //获取选手名单
            $RaceUserList = $oUser->getRaceUserList($params);
            $filename = 'xxx.txt';
            header("Content-Type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            foreach ($RaceUserList as $key => $ApplyInfo)
            {
                $t = array();
                $t['BIB'] = $ApplyInfo['BIB'];
                if(!isset($UserList[$ApplyInfo['UserId']]))
                {
                    $UserInfo = $oUser->getUserInfo($ApplyInfo['UserId'],"user_id,name,sex");
                }
                else
                {
                    $UserInfo = $UserList[$ApplyInfo['UserId']];
                }
                if(!isset($RaceGroupList[$ApplyInfo['RaceGroupId']]['RaceGroupId']))
                {
                    $RaceGroupInfo = $this->oRace->getRaceGroup($ApplyInfo['RaceGroupId'],'RaceGroupId,RaceGroupName');
                }
                else
                {
                    $RaceGroupInfo = $RaceGroupList[$ApplyInfo['RaceGroupId']];
                }
                if(!isset($RaceList[$ApplyInfo['RaceId']]['RaceId']))
                {
                    $RaceInfo = $this->oRace->getRace($ApplyInfo['RaceId'],'RaceId,RaceName');
                }
                else
                {
                    $RaceInfo = $RaceList[$ApplyInfo['RaceId']];
                }
                if(!isset($TeamList[$ApplyInfo['RaceTeamId']]['team_id']))
                {
                    $TeamInfo = $oTeam->getRaceTeamInfo($ApplyInfo['RaceTeamId'],'team_id,name');
                }
                else
                {
                    $TeamInfo = $TeamList[$ApplyInfo['RaceTeamId']];
                }
                $t['Name'] = $UserInfo['name'];
                $t['sex'] = $UserInfo['sex'];
                $t['RaceGroupName'] = $RaceGroupInfo['RaceGroupName'];
                $t['RaceName'] = $RaceInfo['RaceName'];
                $t['ChipId'] = $ApplyInfo['ChipId'];
                $t['TeamName'] = $TeamInfo['name'];
                echo implode(",",$t)."\r\n";
            }
            $response = array('errno' => 0);
            echo json_encode($response);
        }
        else
        {
            $home = $this->sign;
            include $this->tpl('403');
        }
    }


}
