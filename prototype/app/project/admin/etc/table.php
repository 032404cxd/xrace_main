<?php
/**
 * @author Chen <cxd032404@hotmail.com>
 * $Id: table.php 15195 2014-07-23 07:18:26Z 334746 $
 */

$table = array();

#用户审核状态
$table['user_auth']['db'] = 'xrace';
$table['user_auth']['num'] = 1;

#用户审核记录
$table['user_auth_log']['db'] = 'xrace';
$table['user_auth_log']['num'] = 1;

#用户信息
$table['user_profile']['db'] = 'xrace';
$table['user_profile']['num'] = 1;

#计时点
$table['config_race']['db'] = 'xrace_config';
$table['config_race']['num'] = 1;

#计时点
$table['config_timing_point']['db'] = 'xrace_config';
$table['config_timing_point']['num'] = 1;

#运动类型
$table['config_sports_type']['db'] = 'xrace_config';
$table['config_sports_type']['num'] = 1;

#赛事
$table['config_race_catalog']['db'] = 'xrace_config';
$table['config_race_catalog']['num'] = 1;

#比赛类型
$table['config_race_type']['db'] = 'xrace_config';
$table['config_race_type']['num'] = 1;

#商品类型
$table['config_product_type']['db'] = 'xrace_config';
$table['config_product_type']['num'] = 1;

#商品
$table['config_product']['db'] = 'xrace_config';
$table['config_product']['num'] = 1;

#商品
$table['config_product_sku']['db'] = 'xrace_config';
$table['config_product_sku']['num'] = 1;

#赛事报名套餐配置
$table['config_race_combination']['db'] = 'xrace_config';
$table['config_race_combination']['num'] = 1;

#赛事分站
$table['config_race_stage']['db'] = 'xrace_config';
$table['config_race_stage']['num'] = 1;

#赛事分组
$table['config_race_group']['db'] = 'xrace_config';
$table['config_race_group']['num'] = 1;

#管理员
$table['config_manager']['db'] = 'xrace_config';
$table['config_manager']['num'] = 1;

#管理员组
$table['config_group']['db'] = 'xrace_config';
$table['config_group']['num'] = 1;

#菜单
$table['config_menu']['db'] = 'xrace_config';
$table['config_menu']['num'] = 1;

#菜单权限
$table['config_menu_purview']['db'] = 'xrace_config';
$table['config_menu_purview']['num'] = 1;

#菜单权限
$table['config_menu_permission']['db'] = 'xrace_config';
$table['config_menu_permission']['num'] = 1;

//操作日志
$table['config_logs_manager']['db'] = 'xrace_config';
$table['config_logs_manager']['num'] = 16;

//APP版本
$table['config_app_version']['db'] = 'xrace_config';
$table['config_app_version']['num'] = 1;

//APP系统
$table['config_app_os']['db'] = 'xrace_config';
$table['config_app_os']['num'] = 1;

//APP类型
$table['config_app_type']['db'] = 'xrace_config';
$table['config_app_type']['num'] = 1;

//用户执照
$table['user_license']['db'] = 'xrace';
$table['user_license']['num'] = 1;

//报名记录（选手名单）
$table['user_race']['db'] = 'xrace';
$table['user_race']['num'] = 1;

//队伍列表
$table['race_team']['db'] = 'xrace';
$table['race_team']['num'] = 1;

//用户参加队伍列表
$table['user_team']['db'] = 'xrace';
$table['user_team']['num'] = 1;

//用户支付订单表
$table['hs_order']['db'] = 'xrace_bm';
$table['hs_order']['num'] = 1;

//用户支付订单详情表
$table['hs_order_detail']['db'] = 'xrace_bm';
$table['hs_order_detail']['num'] = 1;


return $table;
