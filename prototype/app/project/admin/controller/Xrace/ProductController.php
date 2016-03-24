<?php
/**
 * 任务管理
 * @author Chen<cxd032404@hotmail.com>
 * $Id: LotoController.php 15195 2014-07-23 07:18:26Z 334746 $
 */

class Xrace_ProductController extends AbstractController
{
	/**商品类型列表:ProductTypeList
	 * 权限限制  ?ctl=xrace/product&ac=product.type
	 * @var string
	 */
	protected $sign = '?ctl=xrace/product';
	/**
	 * game对象
	 * @var object
	 */
	protected $oProduct;
	protected $oRace;
	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oProduct = new Xrace_Product();
		$this->oRace = new Xrace_Race();
	}
	//任务配置列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//对应赛事ID
			$RaceCatalogId = isset($this->request->RaceCatalogId)?intval($this->request->RaceCatalogId):0;
			//获取赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			$ProductTypeArr = $this->oProduct->getProductTypeList($RaceCatalogId);
			$ProductTypeList = array();
			foreach($ProductTypeArr as $ProductTypeId => $ProductTypeInfo)
			{
				$ProductTypeList[$ProductTypeInfo['RaceCatalogId']]['ProductTypeList'][$ProductTypeId] = $ProductTypeInfo;
				$ProductTypeList[$ProductTypeInfo['RaceCatalogId']]['ProductTypeCount'] = isset($ProductTypeList[$ProductTypeInfo['RaceCatalogId']]['ProductTypeCount'])?$ProductTypeList[$ProductTypeInfo['RaceCatalogId']]['ProductTypeCount']+1:1;
				if(isset($RaceCatalogList[$ProductTypeInfo['RaceCatalogId']]))
				{
					$ProductTypeList[$ProductTypeInfo['RaceCatalogId']]['RaceCatalogName'] = $RaceCatalogList[$ProductTypeInfo['RaceCatalogId']]['RaceCatalogName'];
				}
				else
				{
					$ProductTypeList[$ProductTypeInfo['RaceCatalogId']]['RaceCatalogName'] = 	"未定义";
				}
			}
			include $this->tpl('Xrace_Product_ProductTypeList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加任务填写配置页面
	public function productTypeAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeInsert");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//渲染模板
			include $this->tpl('Xrace_Product_ProductTypeAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新任务
	public function productTypeInsertAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeInsert");
		if($PermissionCheck['return'])
		{
			//获取页面参数
			$bind=$this->request->from('ProductTypeName','RaceCatalogId');
			//商品类型名称不能为空
			if(trim($bind['ProductTypeName'])=="")
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
				$res = $this->oProduct->insertProductType($bind);
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
	
	//修改任务信息页面
	public function productTypeModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeModify");
		if($PermissionCheck['return'])
		{
			//赛事列表
			$RaceCatalogList  = $this->oRace->getRaceCatalogList();
			//商品类型ID
			$ProductTypeId = intval($this->request->ProductTypeId);
			//获取商品类型信息
			$ProductTypeInfo = $this->oProduct->getProductType($ProductTypeId,'*');
			//渲染模板
			include $this->tpl('Xrace_Product_ProductTypeModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新任务信息
	public function productTypeUpdateAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeModify");
		if($PermissionCheck['return'])
		{

			//获取页面参数
			$bind=$this->request->from('ProductTypeId','ProductTypeName','RaceCatalogId');
			//商品类型名称不能为空
			if(trim($bind['ProductTypeName'])=="")
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
				$res = $this->oProduct->updateProductType($bind['ProductTypeId'],$bind);
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
	
	//删除任务
	public function productTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeDelete");
		if($PermissionCheck['return'])
		{
			$ProductTypeId = trim($this->request->ProductTypeId);
			$this->oProduct->deleteProductType($ProductTypeId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
        
        //商品列表
        public function productListAction() 
        {
			//检查权限
			$PermissionCheck = $this->manager->checkMenuPermission(0);
			if($PermissionCheck['return'])
			{
				$ProductTypeId = trim($this->request->ProductTypeId);
				$ProductList = $this->oProduct->getAllProductList($ProductTypeId);
				//渲染模板
				include $this->tpl('Xrace_Product_ProductList');
			}
			else
			{
				$home = $this->sign;
				include $this->tpl('403');
			}
        }
        
        //添加商品界面
        public function productAddAction() {
                $ProductTypeId = trim($this->request->ProductTypeId);
                $productSign  = '?ProductTypeId='.$ProductTypeId.'&ctl=xrace/product&ac=product.list';
                //检查权限
                $PermissionCheck = $this->manager->checkMenuPermission("ProductInsert");
                if($PermissionCheck['return'])
                {
                   //渲染模板
                    include $this->tpl('Xrace_Product_ProductAdd'); 
                }
                else
                {
                    $home = $this->sign;
                    include $this->tpl('403');                
                }
            
        }
        
        //添加商品
        public function productInsertAction() {
                //检查权限
                $PermissionCheck = $this->manager->checkMenuPermission("ProductInsert");
                if($PermissionCheck['return'])
                {
                    //获取页面参数
                    $bind=$this->request->from('ProductName','ProductTypeId');
                    //商品名称不能为空
                    if(trim($bind['ProductName'])=="")
                    {
                        $response = array('errno' => 1);
                    }
                    else
                    {
                        $res = $this->oProduct->insertProduct($bind);
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
        
        public function productModifyAction() {
                $ProductTypeId = trim($this->request->ProductTypeId);
                $productSign  = '?ProductTypeId='.$ProductTypeId.'&ctl=xrace/product&ac=product.list';
                //检查权限
                $PermissionCheck = $this->manager->checkMenuPermission("ProductModify");
                if($PermissionCheck['return'])
                {
                    $ProductId = trim($this->request->ProductId);
                    $ProductInfo = $this->oProduct->getProduct($ProductId);
                    //渲染模板
                    include $this->tpl('Xrace_Product_ProductModify');   
                }
                else 
                {
                    $home = $this->sign;
                    include $this->tpl('403');
                }
            
        }
        
        public function productUpdateAction() {
                //检查权限
                $PermissionCheck = $this->manager->checkMenuPermission("ProductModify");
                if($PermissionCheck['return'])
                {
 			//获取页面参数
			$bind=$this->request->from('ProductId','ProductName');
			//商品类型名称不能为空
			if(trim($bind['ProductName'])=="")
			{
				$response = array('errno' => 1);
			}
			else
			{
				$res = $this->oProduct->updateProduct($bind['ProductId'], $bind);
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
        
        public function productDeleteAction() {
                //检查权限
                $PermissionCheck = $this->manager->checkMenuPermission("ProductDelete");
                if($PermissionCheck['return'])
                {
                    $ProductId = trim($this->request->ProductId);
                    $this->oProduct->deleteProduct($ProductId);
                    $this->response->goBack();
                }
                else 
                {
                    $home = $this->sign;
                    include $this->tpl('403');
                }            
        }
}
