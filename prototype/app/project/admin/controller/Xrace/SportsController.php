<?php
//ALTER TABLE `config_sports_type` ADD `SpeedDisplayType` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '速度显示单位' AFTER `SportsTypeName`;

/**
 * 运动管理
 * @author Chen<cxd032404@hotmail.com>
 */

class Xrace_SportsController extends AbstractController
{
	/**运动类型:SportsType
	 * @var string
	 */
	protected $sign = '?ctl=xrace/sports';
	/**
	 * game对象
	 * @var object
	 */
	protected $oSports;

	/**
	 * 初始化
	 * (non-PHPdoc)
	 * @see AbstractController#init()
	 */
	public function init()
	{
		parent::init();
		$this->oSports = new Xrace_Sports();

	}
	//运动类型配置列表页面
	public function indexAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission(0);
		if($PermissionCheck['return'])
		{
			//获取运动类型列表
			$SportTypeList = $this->oSports->getAllSportsTypeList();
			//渲染模版
			include $this->tpl('Xrace_Sports_SportsTypeList');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	//添加运动类型填写配置页面
	public function sportsTypeAddAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("SportsTypeInsert");
		if($PermissionCheck['return'])
		{
            //获取速度显示单位
            $SpeedDisplayTypeList = $this->oSports->getSpeedDisplayList();
			//渲染模版
			include $this->tpl('Xrace_Sports_SportsTypeAdd');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//添加新运动类型
	public function sportsTypeInsertAction()
	{
		//检查权限
		$bind=$this->request->from('SportsTypeName','SpeedDisplayType');
		//运动类型名称不能为空
		if(trim($bind['SportsTypeName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//添加运动类型
			$res = $this->oSports->insertSportsType($bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//修改运动类型信息页面
	public function sportsTypeModifyAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("SportsTypeModify");
		if($PermissionCheck['return'])
		{
			//运动类型ID
			$SportsTypeId = intval($this->request->SportsTypeId);
			//获取运动类型信息
			$SportsTypeInfo = $this->oSports->getSportsType($SportsTypeId,'*');
			//获取速度显示单位
            $SpeedDisplayTypeList = $this->oSports->getSpeedDisplayList();
            //渲染模版
			include $this->tpl('Xrace_Sports_SportsTypeModify');
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
	
	//更新运动类型信息
	public function sportsTypeUpdateAction()
	{
		//接收页面参数
		$bind=$this->request->from('SportsTypeId','SportsTypeName','SpeedDisplayType');
		//运动类型名称不能为空
		if(trim($bind['SportsTypeName'])=="")
		{
			$response = array('errno' => 1);
		}
		else
		{
			//修改运动类型
			$res = $this->oSports->updateSportsType($bind['SportsTypeId'],$bind);
			$response = $res ? array('errno' => 0) : array('errno' => 9);
		}
		echo json_encode($response);
		return true;
	}
	
	//删除运动类型
	public function sportsTypeDeleteAction()
	{
		//检查权限
		$PermissionCheck = $this->manager->checkMenuPermission("SportsTypeDelete");
		if($PermissionCheck['return'])
		{
			//运动类型ID
			$SportsTypeId = trim($this->request->SportsTypeId);
			//删除运动类型
			$this->oSports->deleteSportsType($SportsTypeId);
			//返回之前的页面
			$this->response->goBack();
		}
		else
		{
			$home = $this->sign;
			include $this->tpl('403');
		}
	}
}
