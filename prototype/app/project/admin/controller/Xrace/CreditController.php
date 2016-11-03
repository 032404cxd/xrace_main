<?php
/**
 * 产品管理
 * @author Chen<cxd032404@hotmail.com>
 */

class Xrace_CreditController extends AbstractController
{
	/**商品:Credit
	 * @var string
	 */
	protected $sign = '?ctl=xrace/credit';
	/**
	 * game对象
	 * @var object
	 */
	protected $oCredit;
	protected $oRace;
	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oCredit = new Xrace_Credit();
		$this->oRace = new Xrace_Race();
	}
	//商品类型列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//对应赛事ID
			$RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			//获取赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
			//获取商品类型列表
			$CreditArr = $this->oCredit->getCreditList($RaceCatalogId);
			//初始空的产品类型列表
			$CreditList = array();
			//循环产品类型列表
			foreach($CreditArr as $CreditId => $CreditInfo)
			{
				//获取产品类型信息
				$CreditList[$CreditInfo['RaceCatalogId']]['CreditList'][$CreditId] = $CreditInfo;
				//计算商品类型数量
				$CreditList[$CreditInfo['RaceCatalogId']]['CreditCount'] = isset($CreditList[$CreditInfo['RaceCatalogId']]['CreditCount'])?$CreditList[$CreditInfo['RaceCatalogId']]['CreditCount']+1:1;
				//如果对应赛事有配置
				if(isset($RaceCatalogList[$CreditInfo['RaceCatalogId']]))
				{
					//获取赛事名称
					$CreditList[$CreditInfo['RaceCatalogId']]['RaceCatalogName'] = $RaceCatalogList[$CreditInfo['RaceCatalogId']]['RaceCatalogName'];
				}
				else
				{
					$CreditList[$CreditInfo['RaceCatalogId']]['RaceCatalogName'] = 	"未定义";
				}
			}
			//模版渲染
			include $this->tpl('Xrace_Credit_CreditList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加产品配置填写配置页面
	public function creditAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("CreditInsert");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
			//渲染模板
			include $this->tpl('Xrace_Credit_CreditAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加新产品类型
	public function creditInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("CreditInsert");
		if($PermissionCheck['return'])
		{
			//获取页面参数
			$bind=$this->request->from('CreditName','RaceCatalogId');
			//商品类型名称不能为空
			if(trim($bind['CreditName'])=="")
			{
				$response = array('errno' => 1);
			}
			//必须选择一个赛事
			elseif(intval($bind['RaceCatalogId'])==0)
			{
				$response = array('errno' => 2);
			}
			else
			{
				$res = $this->oCredit->insertCredit($bind);
				$response = $res ? array('errno' => 0) : array('errno' => 9);
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
	//修改商品类型页面
	public function creditModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("CreditModify");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList(0,"*",0);
			//商品类型ID
			$CreditId = intval($this->request->CreditId);
			//获取商品类型信息
			$CreditInfo = $this->oCredit->getCredit($CreditId,'*');
			//渲染模板
			include $this->tpl('Xrace_Credit_CreditModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//更新商品类型
	public function creditUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("CreditModify");
		if($PermissionCheck['return'])
		{

			//获取页面参数
			$bind=$this->request->from('CreditId','CreditName','RaceCatalogId');
			//商品类型名称不能为空
			if(trim($bind['CreditName'])=="")
			{
				$response = array('errno' => 1);
			}
			//必须选择一个赛事
			elseif(intval($bind['RaceCatalogId'])==0)
			{
				$response = array('errno' => 2);
			}
			else
			{
				$res = $this->oCredit->updateCredit($bind['CreditId'],$bind);
				$response = $res ? array('errno' => 0) : array('errno' => 9);
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
	//删除产品类型信息
	public function creditDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("CreditDelete");
		if($PermissionCheck['return'])
		{
			$CreditId = trim($this->request->CreditId);
			$this->oCredit->deleteCredit($CreditId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
