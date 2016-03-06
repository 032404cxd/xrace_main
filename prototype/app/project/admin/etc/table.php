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

return $table;
