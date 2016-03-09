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

	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oProduct = new Xrace_Product();

	}
	//任务配置列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			$SportTypeArr = $this->oProduct->getAllProductTypeList();
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
			$maxParams = $this->oProduct->getMaxParmas();
			for($i = 1;$i<=$maxParams;$i++)
			{
				$oProductType['comment']['params'][$i] = array('param'=>'','paramName'=>'');
			}
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
		$bind=$this->request->from('ProductTypeName','ParamsInfo');
		if(trim($bind['ProductTypeName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			$maxParams = $this->oProduct->getMaxParmas();
			for($i = 1;$i<=$maxParams;$i++)
			{
				if(!isset($bind['ParamsInfo'][$i]))
				{
					$bind['ParamsInfo'][$i] = array('param'=>'','paramName'=>'');
				}
			}
			$bind['comment']['params'] = $bind['ParamsInfo'];
			unset($bind['ParamsInfo']);
			$bind['comment'] = json_encode($bind['comment']);
			$res = $this->oProduct->insertProductType($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//修改任务信息页面
	public function productTypeModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeModify");
		if($PermissionCheck['return'])
		{
			$productTypeId = intval($this->request->productTypeId);
			$oProductType = $this->oProduct->getProductType($productTypeId,'*');
			$oProductType['comment'] = json_decode($oProductType['comment'],true);
			$maxParams = $this->oProduct->getMaxParmas();
			if(isset($oProductType['comment']['params']) && is_array($oProductType['comment']['params']))
			{
				for($i = 1;$i<=$maxParams;$i++)
				{
					if(!isset($oProductType['comment']['params'][$i]))
					{
						$oProductType['comment']['params'][$i] = array('param'=>'','paramName'=>'');
					}
				}
			}
			else
			{
				for($i = 1;$i<=$maxParams;$i++)
				{
					$oProductType['comment']['params'][$i] = array('param'=>'','paramName'=>'');
				}
			}
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
		$bind=$this->request->from('ProductTypeId','ProductTypeName','ParamsInfo');
		if(trim($bind['ProductTypeName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			$oProductType = $this->oProduct->getProductType($bind['ProductTypeId'],'*');
			$bind['comment'] = json_decode($oProductType['comment'],true);
			$maxParams = $this->oProduct->getMaxParmas();
			for($i = 1;$i<=$maxParams;$i++)
			{
				if(!isset($bind['ParamsInfo'][$i]))
				{
					$bind['ParamsInfo'][$i] = array('param'=>'','paramName'=>'');
				}
				else
				{
					//$bind['ParamsInfo'][$i] =
				}
			}
			$bind['comment']['params'] = $bind['ParamsInfo'];
			unset($bind['ParamsInfo']);
			$bind['comment'] = json_encode($bind['comment']);
			$res = $this->oProduct->updateProductType($bind['ProductTypeId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//删除任务
	public function productTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("ProductTypeDelete");
		if($PermissionCheck['return'])
		{
			$productTypeId = trim($this->request->productTypeId);
			$this->oProduct->deleteProductType($productTypeId);
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
