-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-02-16 06:43:51
-- 服务器版本： 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prototype_global`
--
CREATE DATABASE IF NOT EXISTS `prototype_global` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `prototype_global`;

-- --------------------------------------------------------

--
-- 表的结构 `config_app`
--

CREATE TABLE `config_app` (
  `AppId` int(10) NOT NULL COMMENT '游戏',
  `app_sign` char(10) NOT NULL COMMENT '标识',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `pic` varchar(255) NOT NULL DEFAULT '',
  `app_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏大厅图片',
  `ClassId` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '游戏分类',
  `AppLevelRule` varchar(128) NOT NULL DEFAULT '0' COMMENT '统计用等级范围规则',
  `app_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏大厅描述',
  `site_url` varchar(255) NOT NULL COMMENT '官网地址',
  `exchange_rate` varchar(50) NOT NULL COMMENT '兑换比率',
  `data_url` varchar(255) NOT NULL COMMENT '获取数据接口',
  `comment` text NOT NULL,
  `is_show` tinyint(3) UNSIGNED NOT NULL COMMENT '是否对外显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品';

--
-- 转存表中的数据 `config_app`
--

INSERT INTO `config_app` (`AppId`, `app_sign`, `name`, `pic`, `app_pic`, `ClassId`, `AppLevelRule`, `app_desc`, `site_url`, `exchange_rate`, `data_url`, `comment`, `is_show`) VALUES
(100, 'homepage', '官网', '', '', 4, '0', '', '', '1', '', '{"coin_name":"\\u72f8\\u732b\\u5e01","create_loginid":"1"}', 0),
(101, 'infinite_h', '无尽英雄', '', '', 5, '0', '', '', '100', '', '{"coin_name":"\\u91d1\\u5238","create_loginid":"0"}', 1);

-- --------------------------------------------------------

--
-- 表的结构 `config_area`
--

CREATE TABLE `config_area` (
  `AreaId` tinyint(3) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '所在地区名称',
  `is_abroad` enum('1','2') NOT NULL,
  `display_color` varchar(10) NOT NULL DEFAULT '''aaaaaa''',
  `currency_rate` float(10,8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT '汇率'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='合作商所在地区列表';

--
-- 转存表中的数据 `config_area`
--

INSERT INTO `config_area` (`AreaId`, `name`, `is_abroad`, `display_color`, `currency_rate`) VALUES
(1, '中国', '1', 'FF0033', 1.00000000),
(2, '韩国', '2', 'FFFF00', 0.00575500),
(3, '美国', '2', '''aaaaaa''', 6.23299980),
(4, '台湾', '1', '''aaaaaa''', 0.21400000);

-- --------------------------------------------------------

--
-- 表的结构 `config_class`
--

CREATE TABLE `config_class` (
  `ClassId` int(10) NOT NULL COMMENT '分类ID',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `icon` varchar(255) NOT NULL COMMENT '图片',
  `url` varchar(255) NOT NULL COMMENT '登陆URL',
  `onlines` int(10) NOT NULL DEFAULT '1' COMMENT '人气指数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类表';

--
-- 转存表中的数据 `config_class`
--

INSERT INTO `config_class` (`ClassId`, `name`, `desc`, `icon`, `url`, `onlines`) VALUES
(4, '公司官网', '', '', '', 1),
(5, '客户端游戏', '', '', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `config_group`
--

CREATE TABLE `config_group` (
  `group_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL COMMENT '用户组',
  `ClassId` smallint(6) NOT NULL COMMENT '1为菜单用户组、2为数据用记组'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理组';

--
-- 转存表中的数据 `config_group`
--

INSERT INTO `config_group` (`group_id`, `name`, `ClassId`) VALUES
(10, '无尽英雄-运营', 2),
(25, '无尽英雄-研发', 2),
(26, '狸猫-平台开发', 2),
(28, '狸猫-财务', 2),
(32, '运维部门', 1),
(33, '平台开发部门', 1),
(34, '运营部门', 1),
(35, '其它部门', 1),
(36, '策划管理', 1),
(37, '策划执行', 1),
(38, '财务部门', 1),
(39, '市场部门', 1),
(40, '管理员', 1),
(41, '客服部门', 1),
(42, '商务部门', 1),
(45, '合作伙伴', 1);

-- --------------------------------------------------------

--
-- 表的结构 `config_logs_manager`
--

CREATE TABLE `config_logs_manager` (
  `id` int(10) UNSIGNED NOT NULL,
  `manager_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `addtime` int(10) UNSIGNED NOT NULL,
  `url` text NOT NULL,
  `referer` text NOT NULL,
  `agent` text NOT NULL,
  `log` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台日志表';

-- --------------------------------------------------------

--
-- 表的结构 `config_manager`
--

CREATE TABLE `config_manager` (
  `id` smallint(6) UNSIGNED NOT NULL COMMENT '管理员id',
  `name` char(60) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码md5',
  `menu_group_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '菜单用户组',
  `data_groups` text NOT NULL COMMENT '数据用户组',
  `is_partner` smallint(6) NOT NULL DEFAULT '0' COMMENT ' 0为内部、1为外部',
  `last_login` int(10) UNSIGNED NOT NULL COMMENT '上次登录时间 unix时间戳',
  `last_active` int(10) UNSIGNED NOT NULL COMMENT '最后活动时间 unix时间戳',
  `last_ip` char(15) NOT NULL COMMENT '上次登录ip',
  `reg_ip` char(15) NOT NULL COMMENT '注册ip',
  `reg_time` int(10) UNSIGNED NOT NULL COMMENT '注册时间 unix时间戳',
  `reset_password` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要强制修改密码',
  `machine_show_list` text NOT NULL COMMENT '运维配置中的服务器显示的列表'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理人员';

--
-- 转存表中的数据 `config_manager`
--

INSERT INTO `config_manager` (`id`, `name`, `password`, `menu_group_id`, `data_groups`, `is_partner`, `last_login`, `last_active`, `last_ip`, `reg_ip`, `reg_time`, `reset_password`, `machine_show_list`) VALUES
(2, 'scadmin', '3e4c853a030f8adb8ad7beb22655b5c2', 40, '10', 1, 1455537417, 1455540122, '127.0.0.1', '', 2011, 0, ''),
(26, '陈光明', 'e10adc3949ba59abbe56e057f20f883e', 34, '10', 0, 1381738898, 1381738898, '180.172.94.74', '58.247.169.182', 1357368334, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `config_menu`
--

CREATE TABLE `config_menu` (
  `menu_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '菜单名称',
  `link` varchar(255) NOT NULL COMMENT '链接地址',
  `parent` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上级菜单id',
  `sort` smallint(6) UNSIGNED NOT NULL DEFAULT '80' COMMENT '排序 从小到大排列 0：隐藏菜单',
  `sign` varchar(255) NOT NULL COMMENT '菜单标识',
  `permission_list` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '权限列表'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单';

--
-- 转存表中的数据 `config_menu`
--

INSERT INTO `config_menu` (`menu_id`, `name`, `link`, `parent`, `sort`, `sign`, `permission_list`) VALUES
(1, '系统管理', '', 0, 81, '', ''),
(2, '菜单', '?ctl=menu', 1, 80, '', '添加菜单:AddMenu|修改菜单:UpdateMenu|删除菜单:DeleteMenu'),
(4, '管理员', '?ctl=manager', 1, 80, '', '添加管理员:AddManager|修改管理员:UpdateManager|删除管理员:DeleteManager'),
(5, '菜单用户组', '?ctl=menu.group', 1, 150, '', '添加菜单用户组:AddMenuGroup|修改菜单用户组:UpdateMenuGroup|删除菜单用户组:DeleteMenuGroup'),
(8, '数据用户组', '?ctl=data.group', 1, 1, '', '添加数据用户组:AddDataGroup|修改数据用户组:UpdateDataGroup|删除数据用户组:DeleteDataGroup'),
(252, 'Xrace', '', 0, 80, '', ''),
(253, '赛事相关', '', 252, 80, '', ''),
(254, '运动类型列表', '?ctl=xrace/sports', 253, 80, '', '运动类型添加:SportsTypeInsert|运动类型修改:SportsTypeModify|运动类型删除:SportsTypeDelete'),
(255, '赛事管理', '?ctl=xrace/race.catalog', 253, 80, '', '赛事列表:RaceCatalogList|运动类型添加:RaceCatalogInsert|赛事修改:RaceCatalogModify|赛事删除:RaceCatalogDelete'),
(256, '赛事组别管理', '?ctl=xrace/race.group', 253, 80, '', '赛事组别列表:RaceGroupList|赛事组别添加:RaceGroupInsert|赛事组别修改:RaceGroupModify|赛事组别删除:RaceGroupDelete'),
(257, '赛事分站管理', '?ctl=xrace/race.stage', 253, 80, '', '赛事分站列表:RaceStageList|赛事分站添加:RaceStageInsert|赛事分站修改:RaceStageModify|赛事分站删除:RaceStageDelete'),
(258, '比赛类型管理', '?ctl=xrace/race.type', 253, 80, '', '比赛类型列表:RaceTypeList|比赛类型添加:RaceTypeInsert|比赛类型修改:RaceTypeModify|比赛类型删除:RaceTypeDelete'),
(259, '用户管理', '?ctl=xrace/user', 260, 80, '', '下载用户列表:UserListDownload|实名审核:UserAuth'),
(260, '用户相关', '', 252, 80, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `config_menu_permission`
--

CREATE TABLE `config_menu_permission` (
  `group_id` smallint(5) UNSIGNED NOT NULL COMMENT '管理组id',
  `menu_id` smallint(5) UNSIGNED NOT NULL COMMENT '菜单id',
  `permission` varchar(32) NOT NULL COMMENT '权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单权限';

--
-- 转存表中的数据 `config_menu_permission`
--

INSERT INTO `config_menu_permission` (`group_id`, `menu_id`, `permission`) VALUES
(32, 2, 'AddMenu'),
(32, 2, 'DeleteMenu'),
(32, 2, 'UpdateMenu'),
(32, 4, 'AddManager'),
(32, 4, 'DeleteManager'),
(32, 4, 'UpdateManager'),
(32, 5, 'AddMenuGroup'),
(32, 5, 'DeleteMenuGroup'),
(32, 5, 'UpdateMenuGroup'),
(32, 8, 'AddDataGroup'),
(32, 8, 'DeleteDataGroup'),
(32, 8, 'UpdateDataGroup'),
(32, 16, 'DeleteArea'),
(32, 16, 'UpdateArea'),
(32, 17, 'AddApp'),
(32, 17, 'DeleteArea'),
(32, 17, 'UpdateApp'),
(32, 18, 'AddPartner'),
(32, 18, 'UpdatePartner'),
(32, 19, 'AddPartnerApp'),
(32, 19, 'DeletePartnerApp'),
(32, 19, 'UpdatePartnerApp'),
(32, 21, 'DeleteServer'),
(32, 21, 'UpdateServer'),
(32, 23, 'AddPassage'),
(32, 23, 'DeletePassage'),
(32, 23, 'UpdatePassage'),
(32, 24, 'AddClass'),
(32, 24, 'DeleteClass'),
(32, 24, 'UpdateClass'),
(32, 251, 'bb'),
(32, 251, 'cc'),
(32, 251, 'dd'),
(33, 4, 'AddManager'),
(33, 4, 'UpdateManager'),
(33, 16, 'AddArea'),
(33, 21, 'AddServer'),
(33, 251, 'aa'),
(33, 251, 'bb'),
(33, 251, 'cc'),
(33, 251, 'dd'),
(34, 2, 'e'),
(34, 4, 'AddManager'),
(34, 5, 'b1'),
(34, 8, 'a1'),
(34, 20, 'c'),
(34, 21, 'AddServer'),
(34, 251, 'aa'),
(35, 2, 'e'),
(35, 4, 'AddManager'),
(35, 5, 'b1'),
(35, 8, 'a1'),
(35, 21, 'AddServer'),
(35, 251, 'aa'),
(35, 251, 'bb'),
(36, 2, 'e'),
(36, 4, 'AddManager'),
(36, 5, 'b1'),
(36, 8, 'a1'),
(36, 21, 'AddServer'),
(36, 251, 'aa'),
(37, 2, 'e'),
(37, 4, 'AddManager'),
(37, 5, 'b1'),
(37, 8, 'a1'),
(37, 21, 'AddServer'),
(37, 251, 'aa'),
(38, 2, 'e'),
(38, 4, 'AddManager'),
(38, 5, 'b1'),
(38, 8, 'a1'),
(38, 21, 'AddServer'),
(38, 251, 'aa'),
(39, 2, 'e'),
(39, 4, 'AddManager'),
(39, 5, 'b1'),
(39, 8, 'a1'),
(39, 21, 'AddServer'),
(39, 251, 'aa'),
(40, 2, 'AddMenu'),
(40, 2, 'DeleteMenu'),
(40, 2, 'UpdateMenu'),
(40, 4, 'AddManager'),
(40, 4, 'DeleteManager'),
(40, 4, 'UpdateManager'),
(40, 5, 'AddMenuGroup'),
(40, 5, 'DeleteMenuGroup'),
(40, 5, 'UpdateMenuGroup'),
(40, 8, 'AddDataGroup'),
(40, 8, 'DeleteDataGroup'),
(40, 8, 'UpdateDataGroup'),
(40, 17, 'AddApp'),
(40, 17, 'DeleteArea'),
(40, 17, 'UpdateApp'),
(40, 18, 'AddPartner'),
(40, 18, 'DeletePartner'),
(40, 18, 'UpdatePartner'),
(40, 19, 'AddPartnerApp'),
(40, 19, 'DeletePartnerApp'),
(40, 19, 'UpdatePartnerApp'),
(40, 21, 'AddServer'),
(40, 21, 'DeleteServer'),
(40, 21, 'UpdateServer'),
(40, 254, 'SportsTypeDelete'),
(40, 254, 'SportsTypeInsert'),
(40, 254, 'SportsTypeModify'),
(40, 255, 'RaceCatalogDelete'),
(40, 255, 'RaceCatalogInsert'),
(40, 255, 'RaceCatalogList'),
(40, 255, 'RaceCatalogModify'),
(40, 256, 'RaceGroupDelete'),
(40, 256, 'RaceGroupInsert'),
(40, 256, 'RaceGroupList'),
(40, 256, 'RaceGroupModify'),
(40, 257, 'RaceStageDelete'),
(40, 257, 'RaceStageInsert'),
(40, 257, 'RaceStageList'),
(40, 257, 'RaceStageModify'),
(40, 258, 'RaceTypeDelete'),
(40, 258, 'RaceTypeInsert'),
(40, 258, 'RaceTypeList'),
(40, 258, 'RaceTypeModify'),
(40, 259, 'UserAuth'),
(40, 259, 'UserListDownload'),
(41, 2, 'e'),
(41, 4, 'AddManager'),
(41, 5, 'b1'),
(41, 8, 'a1'),
(41, 21, 'AddServer'),
(41, 251, 'aa'),
(42, 2, 'e'),
(42, 4, 'AddManager'),
(42, 5, 'b1'),
(42, 8, 'a1'),
(42, 20, 'a'),
(42, 20, 'b'),
(42, 21, 'AddServer'),
(42, 251, 'aa'),
(45, 4, 'AddManager'),
(45, 4, 'UpdateManager'),
(45, 21, 'AddServer'),
(45, 251, 'aa');

-- --------------------------------------------------------

--
-- 表的结构 `config_menu_purview`
--

CREATE TABLE `config_menu_purview` (
  `group_id` smallint(5) UNSIGNED NOT NULL COMMENT '管理组id',
  `menu_id` smallint(5) UNSIGNED NOT NULL COMMENT '菜单id',
  `purview` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1：查看，3：添加，7：修改，15：删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单权限';

--
-- 转存表中的数据 `config_menu_purview`
--

INSERT INTO `config_menu_purview` (`group_id`, `menu_id`, `purview`) VALUES
(32, 1, 1),
(32, 192, 15),
(32, 194, 3),
(33, 127, 15),
(33, 139, 15),
(33, 140, 15),
(33, 141, 15),
(33, 148, 15),
(33, 149, 15),
(33, 152, 15),
(33, 153, 15),
(33, 194, 15),
(40, 1, 15),
(40, 2, 15),
(40, 4, 15),
(40, 5, 15),
(40, 8, 15),
(40, 15, 15),
(40, 127, 15),
(40, 139, 15),
(40, 140, 15),
(40, 141, 15),
(40, 148, 15),
(40, 149, 15),
(40, 152, 15),
(40, 153, 15),
(40, 192, 15),
(40, 194, 15),
(40, 252, 15),
(40, 253, 15),
(40, 254, 15),
(40, 255, 15),
(40, 256, 15),
(40, 257, 15),
(40, 258, 15),
(40, 259, 15),
(40, 260, 15),
(41, 127, 15);

-- --------------------------------------------------------

--
-- 表的结构 `config_partner`
--

CREATE TABLE `config_partner` (
  `PartnerId` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `notes` text NOT NULL COMMENT '其它内容json格式保存'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='合作商';

--
-- 转存表中的数据 `config_partner`
--

INSERT INTO `config_partner` (`PartnerId`, `name`, `notes`) VALUES
(1, '无尽英雄公测', '{"notes":""}');

-- --------------------------------------------------------

--
-- 表的结构 `config_partner_app`
--

CREATE TABLE `config_partner_app` (
  `PartnerId` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `mixed_sign` char(8) NOT NULL COMMENT '混服标识',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `AppId` int(10) UNSIGNED NOT NULL COMMENT '产品ID',
  `LoginStart` int(10) UNSIGNED NOT NULL COMMENT '开始登录时间',
  `NextStart` int(10) UNSIGNED NOT NULL COMMENT '下次服务器开启时间',
  `NextEnd` int(10) UNSIGNED NOT NULL COMMENT '下一次服务器关闭时间',
  `PayStart` int(10) UNSIGNED NOT NULL COMMENT '开始付费时间',
  `PayEnd` int(10) UNSIGNED NOT NULL COMMENT '结束付费时间0为永久',
  `income_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '分成类型 1：固定比例 2：分段比例 3：分段累加',
  `income_rate` text NOT NULL COMMENT '收入分成比例json格式保存',
  `game_site` varchar(255) NOT NULL COMMENT '官网地址',
  `comment` text NOT NULL COMMENT '其它内容json格式保存',
  `AreaId` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `coin_rate` float(10,4) UNSIGNED NOT NULL COMMENT '当地货币与九维币的比值',
  `IsActive` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='合作商应用表';

--
-- 转存表中的数据 `config_partner_app`
--

INSERT INTO `config_partner_app` (`PartnerId`, `mixed_sign`, `name`, `AppId`, `LoginStart`, `NextStart`, `NextEnd`, `PayStart`, `PayEnd`, `income_type`, `income_rate`, `game_site`, `comment`, `AreaId`, `coin_rate`, `IsActive`) VALUES
(1, '', '无尽英雄公测', 100, 0, 0, 0, 0, 0, 1, '1', 'www.lmgame.com', '{"notes":""}', 1, 1.0000, 0),
(1, '', '无尽英雄公测', 101, 0, 0, 0, 0, 0, 1, '1', 'www.wjyx.com', '{"notes":""}', 1, 1.0000, 0);

-- --------------------------------------------------------

--
-- 表的结构 `config_partner_permission`
--

CREATE TABLE `config_partner_permission` (
  `group_id` smallint(5) UNSIGNED NOT NULL COMMENT '管理组id',
  `PartnerId` int(10) NOT NULL COMMENT '合作商id',
  `permission` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1：查看，2：添加，4：修改，8：删除',
  `AppId` int(10) NOT NULL,
  `partner_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '合作种类1官服2专区3混服',
  `AreaId` tinyint(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单权限';

--
-- 转存表中的数据 `config_partner_permission`
--

INSERT INTO `config_partner_permission` (`group_id`, `PartnerId`, `permission`, `AppId`, `partner_type`, `AreaId`) VALUES
(3, 1, 1, 101, 1, 1),
(5, 1, 1, 101, 1, 1),
(8, 1, 1, 21, 1, 1),
(8, 12, 1, 21, 2, 1),
(10, 1, 1, 101, 1, 1),
(25, 1, 1, 101, 1, 1),
(26, 1, 1, 101, 1, 1),
(28, 1, 1, 101, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `config_passage`
--

CREATE TABLE `config_passage` (
  `passage_id` int(10) NOT NULL,
  `passage` varchar(20) NOT NULL COMMENT '支付通道',
  `name` varchar(50) NOT NULL,
  `finance_rate` varchar(50) NOT NULL COMMENT '支付比率',
  `passage_rate` float(10,4) UNSIGNED NOT NULL DEFAULT '1.0000' COMMENT '结算比例',
  `kind` tinyint(4) NOT NULL DEFAULT '0',
  `sort` tinyint(4) NOT NULL DEFAULT '0',
  `weight` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `StagePartnerId` varchar(32) NOT NULL COMMENT '支付平台的本方ID',
  `StageUrl` varchar(128) NOT NULL COMMENT '支付平台的支付跳转地址',
  `StageSecureCode` varchar(64) NOT NULL COMMENT '支付平台密钥'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付通道';

--
-- 转存表中的数据 `config_passage`
--

INSERT INTO `config_passage` (`passage_id`, `passage`, `name`, `finance_rate`, `passage_rate`, `kind`, `sort`, `weight`, `StagePartnerId`, `StageUrl`, `StageSecureCode`) VALUES
(3, 'additional', '手工充值（官方）', '1', 1.0000, 5, 80, 1, '', '', ''),
(5, 'Alipay', '支付宝', '1', 0.9950, 1, 80, 1, '2088801501808115', 'https://mapi.alipay.com/gateway.do?_input_charset=utf-8', 'qaqj14j20i31oblu9620q8t42o1gwu9p'),
(8, 'cash', '现金', '1', 1.0000, 5, 80, 1, '', '', ''),
(15, 'non_test', '非测试充值', '1', 1.0000, 5, 80, 1, '', '', ''),
(26, 'Tenpay', '财付通', '1', 0.9960, 5, 80, 1, '1214567301', 'https://gw.tenpay.com/gateway/pay.htm', '028036528b7376b9cefb0470b7bc4e67'),
(40, 'AliTel', '支付宝(手机卡)', '1', 0.9950, 1, 80, 1, '2088901068120337', ' 	https://mapi.alipay.com/gateway.do?_input_charset=utf-8', 'x5a89qqfab4f83x7uo05cvg8l2a0lui9'),
(41, 'Ka91', '网上支付', '1', 1.0000, 1, 80, 1, 'limao', 'http://limao.pm.91ka.com/pay/interface_index.php', 'fAR8CJISX8SxiF7ssCOkrzyl3LIfQIpT');

-- --------------------------------------------------------

--
-- 表的结构 `config_race_catalog`
--

CREATE TABLE `config_race_catalog` (
  `RaceCatalogId` int(10) UNSIGNED NOT NULL COMMENT '赛事标识ID',
  `RaceCatalogName` varchar(32) NOT NULL COMMENT '赛事名称',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_race_catalog`
--

INSERT INTO `config_race_catalog` (`RaceCatalogId`, `RaceCatalogName`, `comment`) VALUES
(1, 'Heros上海联赛', '{"GameCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/GameCatalogIcon\\/ironman vineman 230x120 1.png","GameCatalogIcon_root":"\\/upload\\/GameCatalogIcon\\/ironman vineman 230x120 1.png","RaceCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceCatalogIcon\\/79228797gw1em2ejx8xktj20w01kw4fe.jpg","RaceCatalogIcon_root":"\\/upload\\/RaceCatalogIcon\\/79228797gw1em2ejx8xktj20w01kw4fe.jpg"}'),
(2, '中铁协系列赛', '{"GameCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/GameCatalogIcon\\/79228797gw1em2ek0ux5wj21kw0w07hg.jpg","GameCatalogIcon_root":"\\/upload\\/GameCatalogIcon\\/79228797gw1em2ek0ux5wj21kw0w07hg.jpg","RaceCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceCatalogIcon\\/79228797gw1em2ek0ux5wj21kw0w07hg.jpg","RaceCatalogIcon_root":"\\/upload\\/RaceCatalogIcon\\/79228797gw1em2ek0ux5wj21kw0w07hg.jpg"}'),
(3, '上海自行车联赛', '{"GameCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/GameCatalogIcon\\/79228797gw1em2ek4dr5qj21kw16odws.jpg","GameCatalogIcon_root":"\\/upload\\/GameCatalogIcon\\/79228797gw1em2ek4dr5qj21kw16odws.jpg","RaceCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceCatalogIcon\\/ironman vineman 230x120 1.png","RaceCatalogIcon_root":"\\/upload\\/RaceCatalogIcon\\/ironman vineman 230x120 1.png"}'),
(4, '测试赛事', '{"RaceCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceCatalogIcon\\/79228797gw1em2ejx8xktj20w01kw4fe.jpg","RaceCatalogIcon_root":"\\/upload\\/RaceCatalogIcon\\/79228797gw1em2ejx8xktj20w01kw4fe.jpg"}');

-- --------------------------------------------------------

--
-- 表的结构 `config_race_group`
--

CREATE TABLE `config_race_group` (
  `RaceGroupId` int(10) UNSIGNED NOT NULL COMMENT '赛事组别标识',
  `RaceGroupName` varchar(32) NOT NULL COMMENT '赛事组别名称',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
  `RaceCatalogId` int(10) UNSIGNED DEFAULT NULL COMMENT '赛事标识'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_race_group`
--

INSERT INTO `config_race_group` (`RaceGroupId`, `RaceGroupName`, `comment`, `RaceCatalogId`) VALUES
(1, '精英组', '', 1),
(2, '大众组', '{"GameCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/GameCatalogIcon\\/ironman vineman 230x120 1.png","GameCatalogIcon_root":"\\/upload\\/GameCatalogIcon\\/ironman vineman 230x120 1.png"}', 3),
(3, '大众组', '{"GameCatalogIcon":"D:\\\\xampp\\\\htdocs\\\\tipask\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/GameCatalogIcon\\/79228797gw1em2ek0ux5wj21kw0w07hg.jpg","GameCatalogIcon_root":"\\/upload\\/GameCatalogIcon\\/79228797gw1em2ek0ux5wj21kw0w07hg.jpg"}', 1),
(4, '群众组', '', 3),
(9, '精英组', '', 3),
(10, '40-50组', '', 2),
(11, '群众组', '', 1),
(12, '30-40组', '', 2),
(13, '20-30组', '', 2),
(14, '50-60组', '', 2),
(16, '60+组', '', 2);

-- --------------------------------------------------------

--
-- 表的结构 `config_race_stage`
--

CREATE TABLE `config_race_stage` (
  `RaceStageId` int(10) UNSIGNED NOT NULL COMMENT '赛事分站标识',
  `RaceStageName` varchar(32) NOT NULL COMMENT '赛事分站名称',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
  `RaceCatalogId` int(10) UNSIGNED DEFAULT NULL COMMENT '赛事标识',
  `StageStartDate` date DEFAULT NULL COMMENT '开始日期',
  `StageEndDate` date DEFAULT NULL COMMENT '结束日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_race_stage`
--

INSERT INTO `config_race_stage` (`RaceStageId`, `RaceStageName`, `comment`, `RaceCatalogId`, `StageStartDate`, `StageEndDate`) VALUES
(1, '测试组别1111', '{"SelectedRaceGroup":{"1":"1","11":"11"}}', 1, '2016-02-10', '2016-02-16'),
(2, '30-40年龄组', '{"SelectedRaceGroup":{"10":"10","12":"12"}}', 2, '2016-02-03', '2016-02-18'),
(3, '中铁协系列赛', '{"SelectedRaceGroup":{"10":"10","12":"12","13":"13"}}', 2, '2016-02-01', '2016-02-03'),
(8, '11111111111111', '{"SelectedRaceGroup":{"10":"10","12":"12","13":"13","14":"14"}}', 2, '2016-02-01', '2016-02-23'),
(9, '上海站', '{"SelectedRaceGroup":{"1":"1"}}', 1, '2016-02-03', '2016-02-29'),
(10, '测试分组', '{"SelectedRaceGroup":{"1":"1","3":"3"}}', 1, '2016-02-03', '2016-02-17'),
(13, '测试分站', '{"SelectedRaceGroup":{"10":"10","12":"12","13":"13"}}', 2, '2016-02-01', '2016-02-29'),
(15, '奉贤分站', '{"SelectedRaceGroup":{"1":"1","3":"3"}}', 1, '2016-02-01', '2016-03-02');

-- --------------------------------------------------------

--
-- 表的结构 `config_race_stage_group`
--

CREATE TABLE `config_race_stage_group` (
  `RaceStageId` int(10) UNSIGNED NOT NULL COMMENT '赛事分站ID',
  `RaceGroupId` int(10) UNSIGNED NOT NULL COMMENT '赛事分组ID',
  `PriceList` varchar(100) NOT NULL COMMENT '价格规范|人数:价格|人数:价格',
  `SingleUser` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否接受单人报名',
  `TeamUser` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否接受团队报名',
  `StartTime` datetime NOT NULL COMMENT '比赛开始时间',
  `EndTime` datetime NOT NULL COMMENT '比赛结束时间',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_race_stage_group`
--

INSERT INTO `config_race_stage_group` (`RaceStageId`, `RaceGroupId`, `PriceList`, `SingleUser`, `TeamUser`, `StartTime`, `EndTime`, `comment`) VALUES
(1, 1, '100', '1', '1', '2016-02-07 15:42:46', '2016-02-08 15:42:46', '{"DetailList":[{"SportsTypeId":2},{"SportsTypeId":4},{"SportsTypeId":3},{"SportsTypeId":4},{"SportsTypeId":2},{"SportsTypeId":3},{"SportsTypeId":1},{"SportsTypeId":4},{"SportsTypeId":6}]}'),
(1, 11, '200', '1', '1', '2016-02-07 15:42:46', '2016-02-08 15:42:46', ''),
(10, 1, '0', '1', '1', '2016-02-09 00:11:13', '2016-02-10 00:11:13', '{"DetailList":[{"SportsTypeId":2},{"SportsTypeId":2},{"SportsTypeId":3},{"SportsTypeId":1}]}');

-- --------------------------------------------------------

--
-- 表的结构 `config_race_type`
--

CREATE TABLE `config_race_type` (
  `RaceTypeId` int(10) UNSIGNED NOT NULL COMMENT '运动类型标识',
  `RaceTypeName` varchar(32) NOT NULL COMMENT '比赛类型名称',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_race_type`
--

INSERT INTO `config_race_type` (`RaceTypeId`, `RaceTypeName`, `comment`) VALUES
(1, '铁人三项', '{"params":{"1":{"paramName":"\\u5708\\u6570","param":"Round"},"2":{"paramName":"\\u7b2c\\u51e0\\u4eba\\u6210\\u7ee9","param":"People"},"3":{"paramName":"e","param":"f"},"4":{"paramName":"g","param":"h"},"5":{"paramName":"i","param":"j"}}}'),
(2, '山地自行车', '{"params":{"1":{"paramName":"1","param":"2"},"2":{"paramName":"2","param":"3"},"3":{"paramName":"3","param":"4"},"4":{"paramName":"5","param":"6"},"5":{"paramName":"7","param":"8"}}}'),
(3, '公路自行车', ''),
(4, '跑步', ''),
(5, '游泳', '');

-- --------------------------------------------------------

--
-- 表的结构 `config_server`
--

CREATE TABLE `config_server` (
  `ServerId` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `server_url` varchar(50) NOT NULL COMMENT 'url',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `AppId` smallint(5) UNSIGNED NOT NULL COMMENT '游戏ID',
  `PartnerId` smallint(5) UNSIGNED NOT NULL COMMENT '运营商ID',
  `LoginStart` int(10) UNSIGNED NOT NULL COMMENT '开服时间',
  `NextStart` int(10) UNSIGNED NOT NULL COMMENT '停服开始时间',
  `NextEnd` int(10) UNSIGNED NOT NULL COMMENT '停服结束时间',
  `PayStart` int(10) NOT NULL COMMENT '开始付费时间',
  `PayEnd` int(10) NOT NULL COMMENT '结束付费时间',
  `ServerIp` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '服务器IP',
  `SocketPort` int(10) UNSIGNED NOT NULL COMMENT 'Socket连接端口',
  `ServerSocketPort` int(10) DEFAULT NULL,
  `GMIp` bigint(20) UNSIGNED NOT NULL,
  `GMSocketPort` int(10) UNSIGNED NOT NULL,
  `Comment` text NOT NULL,
  `is_show` tinyint(3) UNSIGNED NOT NULL COMMENT '是否对外显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏区服';

--
-- 转存表中的数据 `config_server`
--

INSERT INTO `config_server` (`ServerId`, `server_url`, `name`, `AppId`, `PartnerId`, `LoginStart`, `NextStart`, `NextEnd`, `PayStart`, `PayEnd`, `ServerIp`, `SocketPort`, `ServerSocketPort`, `GMIp`, `GMSocketPort`, `Comment`, `is_show`) VALUES
(100001001, 'passport.limaogame.com', '公司官网', 100, 1, 1349075908, 1353568754, 1349075956, 1353568759, 1353568757, 183136, 0, 0, 0, 0, '{"IpListBlack":{"3524149735":1}}', 0),
(101001001, 'test.limaogame.net', '克兰卡纳', 101, 1, 1357023173, 1381365000, 1381363200, 1351495899, 1351495897, 3079177801, 9800, 9901, 3079177827, 9950, '{"IpListWhite":{"989309366":1}}', 1),
(101001002, '', '版署', 101, 1, 1365579544, 1364801952, 1364801947, 1364801958, 1364801955, 3079177806, 9899, 9901, 0, 0, '', 0),
(101001003, '', '测试服', 101, 1, 1370948794, 1370084780, 1370084779, 1370948784, 1370084782, 3079177817, 9898, 9901, 1699183581, 9950, '{"IpListWhite":{"2130706433":1}}', 0),
(101001004, '', '阿斯特兰', 101, 1, 1373607000, 1381365000, 1381363200, 0, 0, 3079177804, 9801, 9901, 3079177803, 9950, '{"IpListWhite":{"989309366":1}}', 1),
(101001005, '', '浩瀚殿堂', 101, 1, 1374225350, 1374225357, 1374225355, 1374225361, 1374225359, 3079178461, 9802, 9901, 3079178727, 9950, 'null', 0);

-- --------------------------------------------------------

--
-- 表的结构 `config_sports_type`
--

CREATE TABLE `config_sports_type` (
  `SportsTypeId` int(10) UNSIGNED NOT NULL COMMENT '运动类型标识',
  `SportsTypeName` varchar(32) NOT NULL COMMENT '运动类型名称',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_sports_type`
--

INSERT INTO `config_sports_type` (`SportsTypeId`, `SportsTypeName`, `comment`) VALUES
(1, '公开水域游泳', '{"params":{"1":{"paramName":"\\u5708\\u6570","param":"Round"},"2":{"paramName":"\\u7b2c\\u51e0\\u4eba\\u6210\\u7ee9","param":"People"},"3":{"paramName":"e","param":"f"},"4":{"paramName":"g","param":"h"},"5":{"paramName":"i","param":"j"}}}'),
(2, '山地自行车', '{"params":{"1":{"paramName":"1","param":"2"},"2":{"paramName":"2","param":"3"},"3":{"paramName":"3","param":"4"},"4":{"paramName":"5","param":"6"},"5":{"paramName":"7","param":"8"}}}'),
(3, '公路自行车', ''),
(4, '跑步', ''),
(6, '泳池游泳', '{"params":{"1":{"paramName":"","param":""},"2":{"paramName":"","param":""},"3":{"paramName":"","param":""},"4":{"paramName":"","param":""},"5":{"paramName":"","param":""}}}');

-- --------------------------------------------------------

--
-- 表的结构 `config_test_user`
--

CREATE TABLE `config_test_user` (
  `PartnerId` int(10) NOT NULL,
  `AppId` int(10) NOT NULL,
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `config_test_user`
--

INSERT INTO `config_test_user` (`PartnerId`, `AppId`, `username`) VALUES
(1, 100, 'wqf07130'),
(1, 102, 'zouxinghb'),
(512, 102, 'jiwangli369'),
(512, 102, 'xlwlsg0000000899'),
(512, 102, 'xlwlsg0000392310'),
(552, 102, '1244288903'),
(1, 103, 'zouxinghb'),
(7, 103, 'cxd'),
(8, 103, 'jiwangli369'),
(20, 103, '1001601652'),
(20, 103, 'saik:1000002809'),
(20, 103, 'saik:1001601652'),
(488, 103, 'zxl521'),
(508, 103, 'test2'),
(512, 103, 'xlwlsg0000000899'),
(512, 103, 'xlwlyx0000000003'),
(512, 103, 'xlwlyx0002486567'),
(520, 103, 'tkote'),
(542, 103, 'oletkkiioou'),
(572, 103, 'kevintestman1'),
(577, 103, 'candybaby01'),
(1, 104, 'cxd'),
(1, 105, 'zouxinghb'),
(488, 105, 'zxl521'),
(494, 105, '700000606'),
(508, 105, '21'),
(510, 105, '10049873'),
(510, 105, 'hcflyy'),
(1, 106, 'zouxinghb'),
(639, 106, '2'),
(1, 107, 'zouxinghb'),
(9, 107, 'clover000'),
(14, 107, 'ad18562595'),
(14, 107, 'caros23'),
(14, 107, 'deepkill'),
(14, 107, 'dreamwolff'),
(14, 107, 'fangmicheal'),
(14, 107, 'fox008'),
(14, 107, 'sjtc001'),
(14, 107, 'tanglaoshi'),
(14, 107, 'xiaotttt'),
(14, 107, 'xiuaoyewansuei'),
(14, 107, 'zhangzhibin'),
(28, 107, '8'),
(488, 107, 'zxl521'),
(492, 107, 'suifengps2'),
(511, 107, 'chenchao7799'),
(530, 107, '22680808'),
(542, 107, 'oletkkiioou'),
(564, 107, 'lilyshuai123'),
(564, 107, 'ttwftonyk'),
(572, 107, 'walkertiti'),
(587, 107, 'tq3ayglT8c'),
(590, 107, 'xl20100919'),
(591, 107, 'love1989'),
(628, 107, '妖孽小鱼'),
(653, 107, '49486309'),
(653, 107, '86313919'),
(654, 107, 'qvod:kw_1497'),
(654, 107, 'qvod:kw_268'),
(1, 111, 'zouxinghb'),
(1, 201, 'zouxinghb'),
(1, 203, 'zouxinghb'),
(1, 205, 'zouxinghb'),
(1, 206, 'zouxinghb'),
(1, 207, 'zouxinghb'),
(1, 208, 'cha369'),
(1, 208, 'zouxinghb'),
(1, 210, 'zouxinghb'),
(1, 211, 'zouxinghb'),
(1, 212, 'zouxinghb'),
(1, 213, 'zouxinghb'),
(1, 214, 'zouxinghb'),
(14, 214, 'doudouJFS1'),
(14, 214, 'dreamwolff'),
(14, 214, 'fspace9721'),
(31, 214, '4935509');

-- --------------------------------------------------------

--
-- 表的结构 `faq`
--

CREATE TABLE `faq` (
  `FaqId` tinyint(3) UNSIGNED NOT NULL COMMENT '问题ID',
  `name` varchar(32) NOT NULL COMMENT '职业名称',
  `FaqTypeId` int(10) UNSIGNED NOT NULL COMMENT '问题分类ID',
  `Answer` text NOT NULL COMMENT 'FAQ回答'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='faq配置表';

--
-- 转存表中的数据 `faq`
--

INSERT INTO `faq` (`FaqId`, `name`, `FaqTypeId`, `Answer`) VALUES
(32, '《无尽英雄》金券怎么充值；充值方式有哪些？', 11, '<p>\n	打开《无尽英雄》<a href="http://passport.limaogame.com/?c=pc_index" target="_blank">充值中心</a>，选择充值方式。目前支持的充值方式有：网上银行充值、支付宝充值、财付通充值、易宝充值、手机卡充值等</p>\n'),
(33, '充值成功后如何查看金券余额？', 11, '请您登陆《无尽英雄》<a href="http://passport.limaogame.com/?c=pc_index" target="_blank">充值中心</a>，登陆后，点击“我的账户”按钮，进行查询。'),
(34, '提示充值成功却没有到账。', 11, '<p>\n	这时可能是由于网络延迟而造成的，这时您稍等几分钟即可；如果较长时间仍未到帐，可能是系统出现了问题，请在线联系客服，或者发送邮箱：<a href="cs@wjyx.com" target="_blank">cs@wjyx.com</a>，我们的充值客服将进行核实查询，在两个工作日之内为您解决。</p>\n'),
(35, '无尽英雄充值到错误账号怎么办？', 11, '<p>\n	请在线联系客服，或者发送邮箱：<a href="mailto:cs@wjyx.com&amp;subject=问题反馈">cs@wjyx.com</a>，我们的充值客服将进行核实查询，在两个工作日之内为您解决。</p>\n'),
(36, '网银如何充值', 11, '<p>\n	1) 进入<a href="http://passport.limaogame.com/?c=pc_index" target="_blank">充值中心</a>。<br />\n	2) 用户选择需要充值的方式，选择 【网银充值】，填写好充值信息 ：【账号】、【确认账号】、【选择金额】、【支付方式】，点击 【立即充值】。</p>\n<p>\n	<img alt="" src="http://dcadmin.limaogame.com/upload/img/1367806771.jpg" style="width: 736px; height: 687px;" /></p>\n<br />\n<div>\n	<img src="file:///C:\\Users\\user\\AppData\\Roaming\\Tencent\\Users\\759752311\\QQ\\WinTemp\\RichOle\\B@(A[@3GA_9IP%C~Y]UNWBI.jpg" /></div>\n<p>\n	3) 主页上显示充值确认信息窗口，用户需确认充值的 【订单编号】、【充值账号】、【充值金额】。确认无误点击 【确认提交】；如果有误，点击【返回修改】，修改充值信息。<br />\n	<img src="http://passport.limaogame.com/faqimg/5-2.png" /><br />\n	<br />\n	4) 点击【确认提交】后，刷新【充值确认信息】窗口，显示如下提示窗。用户支付完成后，点击【支付已完成】关闭窗口；若付款遇到问题，点击【付款遇到问题】，将链接至客服中心页面。<br />\n	<img src="http://passport.limaogame.com/faqimg/5-3.png" /><br />\n	<br />\n	5) 点击【确认提交】后，系统跳转到银行网上支付页面，用户确认交易的订单号，记录下来，以便查询，输入相关的 【银行账号】，如填写错误可点击 【重填】，填写无误后，点击 【提交】 付款。<img src="http://passport.limaogame.com/faqimg/5-4.jpg" /><br />\n	<br />\n	6) 网上银行要求确认支付信息，用户需确认银行的支付情况，查看订单号，确认【账号】、【金额】，点击 【确认】。<img src="http://passport.limaogame.com/faqimg/5-5.jpg" /><br />\n	<br />\n	7) 用户支付成功后，自动跳转回 【充值中心】，如果不能自动跳转，点击 【支付页面】链接。返回【充值中心】后，打开新页面，提示充值成功或者失败。<img src="http://passport.limaogame.com/faqimg/5-6.png" /><br />\n	<br />\n	8) 切换回 &ldquo;充值中心&rdquo;页面，用户点击 【我的账户】查询充值记录。<img src="http://passport.limaogame.com/faqimg/5-7.png" /></p>\n'),
(38, '如何注册游戏账号', 4, '您可以到这里注册账号:<a href="http://passport.limaogame.com/?c=login" target="_blank">用户中心</a>，建议您注册完账号后到填补相关资料,以便密码丢失时方便找回。'),
(39, '我身份证号码记不清，可以继续注册吗？', 4, '<p>\n	亲爱的玩家，不可以。为了您的帐户安全，请填写真实有效的证件号码。</p>\n'),
(40, '我没有E-MAIL，在注册的时候安全邮箱一栏可不可以不填？', 4, 'E-MAIL为找回您帐户密码的重要途径，为必填选项，为了您的帐户安全请如实填写。'),
(41, '快速注册用户，如何补填注册的信息：密保问题、邮箱绑定、防沉迷验证', 4, '您可以登陆用户中心首页：<a href="http://passport.limaogame.com/?c=login" target="_blank">用户中心</a>。登陆状态下，首页会提示您填补注册资料。'),
(42, '密码丢失,如何找回？', 4, '进入<a href="http://passport.limaogame.com/?c=uc_index" target="_blank">用户中心首页</a>，点击找回密码。'),
(43, '快速注册,如何找回密码？', 4, '进入<a href="http://passport.limaogame.com/?c=uc_index" target="_blank">用户中心首页</a>，点击找回密码，输入您的账号后选择联系客服。'),
(44, '如何找回密码保护问题、答案、注册邮箱？', 4, '进入<a href="http://passport.limaogame.com/?c=uc_index" target="_blank">用户中心首页</a>。登陆状态下，您可以联系客服。我们的客服将进行核实查询，在两个工作日之内为您解决。'),
(45, '账号被盗怎么办？', 4, '<p>\n	进入<a href="http://passport.limaogame.com/?c=uc_index" target="_blank">用户中心首页</a>，点击找回密码。</p>\n'),
(46, '我已更新了防沉迷身份信息，为何还是沉迷用户？', 4, '身份信息的添加将在一个小时后生效，请在生效后关闭游戏页面重新进入游戏。'),
(47, '游戏角色名可以修改吗?', 4, '非常遗憾，在我们游戏中，任何一个角色名都是唯一的，也是无法进行修改的，请您谅解！'),
(48, '进入游戏后，昵称是否可以更改？', 4, '目前是无法修改帐号昵称的，所以请慎重考虑后再注册。 同时系统会删除昵称里包含色情、政治、人身攻击等因素的帐号。因此申请帐号请注意以上内容，慎重考虑后再注册。'),
(49, '我账号登陆时提示被封了，怎么办？', 4, '如果发现这种问题请您尽快与客服联系。请详细提交问题的说明，核实正确后客服中心会尽快处理。'),
(50, '如何让英雄行走？', 5, '点击右键进行走路。'),
(51, '如何让英雄（或单位）攻击？', 5, '点击鼠标右键，选中目标之后即可攻击。'),
(52, '如何让英雄（或单位）释放技能？', 5, '在副本中，鼠标选中目标（不需点击），按下技能键即可释放技能。'),
(53, '如何开启副本？', 5, '在主城界面上方选择“克兰卡纳冒险”，之后选择加入游戏或者创建副本。'),
(54, '如何组队打副本？', 5, '在主城界面上方选择“克兰卡纳冒险”，选择创建副本，创建之后邀请战友加入相应房间号。或者点击战友召唤者在其头像上选择“邀请房间”。'),
(55, '召唤者穿戴的装备能带入竞技场吗？', 5, '召唤者穿戴的衣服与当前召唤出的英雄无关，并且只在PVE环境中发挥作用。'),
(56, '什么是精通？', 5, '通过大量材料（副本掉落）进行必成强化（游戏中的“必成精通”），也可以通过少量材料有概率成功的模式进行强化（游戏中的“馈赠精通”，失败有部分补偿返还）。'),
(57, '什么是祝福？', 5, '强化提升装备属性。用一颗N级祝福石能把一个已经是N-1级祝福等级的装备部位提升到N级祝福等级；而一颗N级祝福石可以由5颗N-1级祝福石合成（100%成功）或者少于5颗祝福石按一定概率合成'),
(58, '什么是分解？', 5, '将装备分解成为材料，此材料可以用于兑换饰品。'),
(59, '什么是拆分？', 5, '将堆叠的物品拆分成为若干份。例如装备中有20件精通符文，使用拆分功能将其拆分为相等的两份，每份10件。'),
(60, '在游戏中发现BUG如何处理？', 6, '<p>\n	请在线咨询，或者发送邮箱：<a href="mailto:cs@wjyx.com&amp;subject=问题反馈">cs@wjyx.com</a>&nbsp;反映您的问题，另外您也可以到论坛BUG反馈板块反映您的BUG,我们会尽快为你提供解决方案。</p>\n'),
(61, '什么是MOBA？', 7, 'Multiplayer Online Battle Arena Games 的简写，意为多人联机在线竞技游戏。一般都会以MOBA GAMES一起书写。 MOBA游戏的始祖即为红遍全球的游戏模式DOTA。'),
(62, '《无尽英雄》的配置是什么？', 7, '系统：Windows® XP/Vista/7/8 (包含Directx9.0C)<br>CPU：Intel Pentium 4 1.3 GHz 或 AMD Athlon XP 1500+<br>内存：1 GB RAM (XP), 1.5 GB (Vista/7/8)<br>显卡：NVIDIA GeForce FX 或 ATI Radeon 9500 或更高<br>硬盘：3.0GB以上可用空间'),
(63, '频繁掉线怎么办？', 6, '请各位玩家不要主动变更游戏线路\n如果你需要和好友组队进行游戏, 请打开好友列表邀请好友进入房间,\n即使不在同一线路也可以进组队,并不需要在同一线路.\n这样应该会减少掉线的情况,如果这样操作还是会掉线.'),
(64, '运行游戏死机怎么办？', 6, '<p>\n	请重启您的电脑后，再次进入游戏。</p>\n'),
(65, '手机卡如何充值', 11, '<p>\n	&nbsp;</p>\n<p>\n	1、登陆官方充值中心，选择要充值的游戏产品</p>\n<p>\n	2、选择<strong>移动（神州行卡）、联通、电信卡</strong>充值目录，选择与您持有手机支付卡相等对应面值的支付金额（否则将导致充值失败，卡内余额丢失）</p>\n<p>\n	3、输入充值帐号、再次输入确认充值帐号，提交充值</p>\n'),
(66, '什么是天下通？', 11, '<p>\n	<span style="font-family: Arial, Helvetica, sans-serif; line-height: 25px;">天下通是由广州新泛联数码科技有限公司发行的多种网络产品的支付手段。</span></p>\n'),
(67, '在哪里可以购买到狸猫天下通？', 4, '<p>\n	<span style="font-family: Arial, Helvetica, sans-serif; line-height: 25px;">您可以到天下通官方网站的经销商查询页面查看一下<a href="http://www.txtong.com.cn/jingxiaoshang04.php">http://www.txtong.com.cn/jingxiaoshang04.php</a></span><span style="font-family: Arial, Helvetica, sans-serif; line-height: 25px;">选择您所在的省份地区找到最方便您的购买点卡的地点。</span></p>\n'),
(68, '想确认自己所购买的天下通是否被使用过？', 4, '<p>\n	<span style="font-family: Arial, Helvetica, sans-serif; line-height: 25px;">购买天下通的客户可以打开页面<a href="http://www.txtong.com.cn/txt_icard.php">http://www.txtong.com.cn/txt_icard.php</a></span><span style="font-family: Arial, Helvetica, sans-serif; line-height: 25px;">，然后输入卡号和密码及验证码后方可查询输入的点卡状态。</span></p>\n');

-- --------------------------------------------------------

--
-- 表的结构 `faq_type`
--

CREATE TABLE `faq_type` (
  `FaqTypeId` tinyint(3) UNSIGNED NOT NULL COMMENT '问题分类ID',
  `name` varchar(32) NOT NULL COMMENT '分类名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='faq分类列表';

--
-- 转存表中的数据 `faq_type`
--

INSERT INTO `faq_type` (`FaqTypeId`, `name`) VALUES
(11, '充值问题'),
(7, '其他问题'),
(5, '游戏问题'),
(6, '系统问题'),
(4, '账号问题');

-- --------------------------------------------------------

--
-- 表的结构 `id`
--

CREATE TABLE `id` (
  `name` varchar(30) NOT NULL,
  `value` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `id`
--

INSERT INTO `id` (`name`, `value`) VALUES
('auth_id', '100'),
('op_uid', '100'),
('user_id', '100');

-- --------------------------------------------------------

--
-- 表的结构 `op_user`
--

CREATE TABLE `op_user` (
  `op_uid` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_auth`
--

CREATE TABLE `user_auth` (
  `user_id` int(11) NOT NULL,
  `submit_img1` varchar(100) DEFAULT NULL,
  `submit_img2` varchar(100) DEFAULT NULL,
  `submit_time` datetime DEFAULT NULL,
  `auth_result` tinyint(3) UNSIGNED NOT NULL COMMENT '实名认证审核结果',
  `auth_resp` varchar(200) DEFAULT NULL,
  `op_time` datetime DEFAULT NULL,
  `op_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_auth`
--

INSERT INTO `user_auth` (`user_id`, `submit_img1`, `submit_img2`, `submit_time`, `auth_result`, `auth_resp`, `op_time`, `op_uid`) VALUES
(4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 0, '1111', '2016-02-13 23:21:55', 2);

-- --------------------------------------------------------

--
-- 表的结构 `user_auth_log`
--

CREATE TABLE `user_auth_log` (
  `auth_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `submit_img1` varchar(100) DEFAULT NULL,
  `submit_img2` varchar(100) DEFAULT NULL,
  `submit_time` datetime DEFAULT NULL,
  `auth_result` tinyint(3) UNSIGNED NOT NULL COMMENT '实名认证审核结果',
  `auth_resp` varchar(200) DEFAULT NULL,
  `op_time` datetime DEFAULT NULL,
  `op_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_auth_log`
--

INSERT INTO `user_auth_log` (`auth_id`, `user_id`, `submit_img1`, `submit_img2`, `submit_time`, `auth_result`, `auth_resp`, `op_time`, `op_uid`) VALUES
(124, 4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 0, '1111', '2016-02-13 20:47:31', 2),
(761, 4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 2, '', '2016-02-12 23:17:31', 2),
(934, 4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 0, '1111', '2016-02-13 23:21:55', 2);

-- --------------------------------------------------------

--
-- 表的结构 `user_profile`
--

CREATE TABLE `user_profile` (
  `user_id` int(11) NOT NULL,
  `wx_open_id` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL,
  `nick_name` varchar(30) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `sex` tinyint(3) UNSIGNED NOT NULL COMMENT '用户性别',
  `birth_day` date DEFAULT NULL,
  `id_type` varchar(10) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `expire_day` date DEFAULT NULL,
  `thumb` varchar(100) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `auth_state` tinyint(3) UNSIGNED NOT NULL COMMENT '实名认证状态',
  `ec_name` varchar(30) DEFAULT NULL,
  `ec_relation` varchar(30) DEFAULT NULL,
  `ec_phone1` varchar(30) DEFAULT NULL,
  `ec_phone2` varchar(30) DEFAULT NULL,
  `crt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `wx_open_id`, `phone`, `pwd`, `nick_name`, `name`, `sex`, `birth_day`, `id_type`, `id_number`, `expire_day`, `thumb`, `province`, `city`, `address`, `auth_state`, `ec_name`, `ec_relation`, `ec_phone1`, `ec_phone2`, `crt_time`) VALUES
(1, 'abc', '18621758237', 'e10adc3949ba59abbe56e057f20f883e', '你好', '陈晓东', 1, '1982-01-05', NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(2, '222', '122133', 'b0baee9d279d34fa1dfd71aadb908c3f', 'abc', NULL, 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(3, 'abcd', 'sdfsdf1231', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(4, 'sddff', '111111111', '7fa8282ad93047a4d6fe6111c93b308a', 'abcdde', 'abc', 1, '2016-02-25', 'IDCARD', '111111111', '2016-02-27', 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(5, 'abcd', 'sdfsdf1232', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA2', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(6, 'abcd', 'sdfsdf1236', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA6', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(7, 'abcd', 'sdfsdf1237', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA7', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(8, 'abc8', 'sdfsdf1238', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA8', 'a8a', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `config_app`
--
ALTER TABLE `config_app`
  ADD PRIMARY KEY (`AppId`),
  ADD UNIQUE KEY `game_code` (`app_sign`),
  ADD KEY `is_show` (`is_show`);

--
-- Indexes for table `config_area`
--
ALTER TABLE `config_area`
  ADD PRIMARY KEY (`AreaId`);

--
-- Indexes for table `config_class`
--
ALTER TABLE `config_class`
  ADD PRIMARY KEY (`ClassId`);

--
-- Indexes for table `config_group`
--
ALTER TABLE `config_group`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `config_logs_manager`
--
ALTER TABLE `config_logs_manager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_manager`
--
ALTER TABLE `config_manager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `config_menu`
--
ALTER TABLE `config_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `config_menu_permission`
--
ALTER TABLE `config_menu_permission`
  ADD PRIMARY KEY (`group_id`,`menu_id`,`permission`);

--
-- Indexes for table `config_menu_purview`
--
ALTER TABLE `config_menu_purview`
  ADD PRIMARY KEY (`group_id`,`menu_id`);

--
-- Indexes for table `config_partner`
--
ALTER TABLE `config_partner`
  ADD PRIMARY KEY (`PartnerId`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `config_partner_app`
--
ALTER TABLE `config_partner_app`
  ADD PRIMARY KEY (`PartnerId`,`AppId`);

--
-- Indexes for table `config_partner_permission`
--
ALTER TABLE `config_partner_permission`
  ADD PRIMARY KEY (`group_id`,`PartnerId`,`AppId`,`partner_type`,`AreaId`);

--
-- Indexes for table `config_passage`
--
ALTER TABLE `config_passage`
  ADD PRIMARY KEY (`passage_id`),
  ADD UNIQUE KEY `passage` (`passage`);

--
-- Indexes for table `config_race_catalog`
--
ALTER TABLE `config_race_catalog`
  ADD PRIMARY KEY (`RaceCatalogId`),
  ADD UNIQUE KEY `SportsTypeName` (`RaceCatalogName`);

--
-- Indexes for table `config_race_group`
--
ALTER TABLE `config_race_group`
  ADD PRIMARY KEY (`RaceGroupId`),
  ADD UNIQUE KEY `Name` (`RaceGroupName`,`RaceCatalogId`);

--
-- Indexes for table `config_race_stage`
--
ALTER TABLE `config_race_stage`
  ADD PRIMARY KEY (`RaceStageId`),
  ADD UNIQUE KEY `Name` (`RaceStageName`,`RaceCatalogId`) USING BTREE;

--
-- Indexes for table `config_race_stage_group`
--
ALTER TABLE `config_race_stage_group`
  ADD PRIMARY KEY (`RaceStageId`,`RaceGroupId`);

--
-- Indexes for table `config_race_type`
--
ALTER TABLE `config_race_type`
  ADD PRIMARY KEY (`RaceTypeId`),
  ADD UNIQUE KEY `SportsTypeName` (`RaceTypeName`);

--
-- Indexes for table `config_server`
--
ALTER TABLE `config_server`
  ADD PRIMARY KEY (`ServerId`),
  ADD UNIQUE KEY `ServerIp` (`ServerIp`),
  ADD KEY `product_id` (`AppId`,`PartnerId`),
  ADD KEY `is_show` (`is_show`);

--
-- Indexes for table `config_sports_type`
--
ALTER TABLE `config_sports_type`
  ADD PRIMARY KEY (`SportsTypeId`),
  ADD UNIQUE KEY `SportsTypeName` (`SportsTypeName`);

--
-- Indexes for table `config_test_user`
--
ALTER TABLE `config_test_user`
  ADD PRIMARY KEY (`AppId`,`PartnerId`,`username`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`FaqId`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `faq_type`
--
ALTER TABLE `faq_type`
  ADD PRIMARY KEY (`FaqTypeId`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `id`
--
ALTER TABLE `id`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `op_user`
--
ALTER TABLE `op_user`
  ADD PRIMARY KEY (`op_uid`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_auth_log`
--
ALTER TABLE `user_auth_log`
  ADD PRIMARY KEY (`auth_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `config_area`
--
ALTER TABLE `config_area`
  MODIFY `AreaId` tinyint(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- 使用表AUTO_INCREMENT `config_class`
--
ALTER TABLE `config_class`
  MODIFY `ClassId` int(10) NOT NULL AUTO_INCREMENT COMMENT '分类ID', AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `config_group`
--
ALTER TABLE `config_group`
  MODIFY `group_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- 使用表AUTO_INCREMENT `config_logs_manager`
--
ALTER TABLE `config_logs_manager`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `config_manager`
--
ALTER TABLE `config_manager`
  MODIFY `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员id', AUTO_INCREMENT=27;
--
-- 使用表AUTO_INCREMENT `config_menu`
--
ALTER TABLE `config_menu`
  MODIFY `menu_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=261;
--
-- 使用表AUTO_INCREMENT `config_partner`
--
ALTER TABLE `config_partner`
  MODIFY `PartnerId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `config_passage`
--
ALTER TABLE `config_passage`
  MODIFY `passage_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- 使用表AUTO_INCREMENT `config_race_catalog`
--
ALTER TABLE `config_race_catalog`
  MODIFY `RaceCatalogId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '赛事标识ID', AUTO_INCREMENT=5;
--
-- 使用表AUTO_INCREMENT `config_race_group`
--
ALTER TABLE `config_race_group`
  MODIFY `RaceGroupId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '赛事组别标识', AUTO_INCREMENT=17;
--
-- 使用表AUTO_INCREMENT `config_race_stage`
--
ALTER TABLE `config_race_stage`
  MODIFY `RaceStageId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '赛事分站标识', AUTO_INCREMENT=16;
--
-- 使用表AUTO_INCREMENT `config_race_type`
--
ALTER TABLE `config_race_type`
  MODIFY `RaceTypeId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '运动类型标识', AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `config_sports_type`
--
ALTER TABLE `config_sports_type`
  MODIFY `SportsTypeId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '运动类型标识', AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `faq`
--
ALTER TABLE `faq`
  MODIFY `FaqId` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '问题ID', AUTO_INCREMENT=69;
--
-- 使用表AUTO_INCREMENT `faq_type`
--
ALTER TABLE `faq_type`
  MODIFY `FaqTypeId` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '问题分类ID', AUTO_INCREMENT=12;--
-- Database: `xrace`
--
CREATE DATABASE IF NOT EXISTS `xrace` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `xrace`;

-- --------------------------------------------------------

--
-- 表的结构 `op_user`
--

CREATE TABLE `op_user` (
  `op_uid` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_auth`
--

CREATE TABLE `user_auth` (
  `user_id` int(11) NOT NULL,
  `submit_img1` varchar(100) DEFAULT NULL,
  `submit_img2` varchar(100) DEFAULT NULL,
  `submit_time` datetime DEFAULT NULL,
  `auth_result` tinyint(3) UNSIGNED NOT NULL COMMENT '实名认证审核结果',
  `auth_resp` varchar(200) DEFAULT NULL,
  `op_time` datetime DEFAULT NULL,
  `op_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_auth`
--

INSERT INTO `user_auth` (`user_id`, `submit_img1`, `submit_img2`, `submit_time`, `auth_result`, `auth_resp`, `op_time`, `op_uid`) VALUES
(4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 0, '1111', '2016-02-13 23:21:55', 2);

-- --------------------------------------------------------

--
-- 表的结构 `user_auth_log`
--

CREATE TABLE `user_auth_log` (
  `auth_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `submit_img1` varchar(100) DEFAULT NULL,
  `submit_img2` varchar(100) DEFAULT NULL,
  `submit_time` datetime DEFAULT NULL,
  `auth_result` tinyint(3) UNSIGNED NOT NULL COMMENT '实名认证审核结果',
  `auth_resp` varchar(200) DEFAULT NULL,
  `op_time` datetime DEFAULT NULL,
  `op_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_auth_log`
--

INSERT INTO `user_auth_log` (`auth_id`, `user_id`, `submit_img1`, `submit_img2`, `submit_time`, `auth_result`, `auth_resp`, `op_time`, `op_uid`) VALUES
(124, 4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 0, '1111', '2016-02-13 20:47:31', 2),
(761, 4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 2, '', '2016-02-12 23:17:31', 2),
(934, 4, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', '2016-02-12 13:28:25', 0, '1111', '2016-02-13 23:21:55', 2);

-- --------------------------------------------------------

--
-- 表的结构 `user_profile`
--

CREATE TABLE `user_profile` (
  `user_id` int(11) NOT NULL,
  `wx_open_id` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL,
  `nick_name` varchar(30) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `sex` tinyint(3) UNSIGNED NOT NULL COMMENT '用户性别',
  `birth_day` date DEFAULT NULL,
  `id_type` varchar(10) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `expire_day` date DEFAULT NULL,
  `thumb` varchar(100) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `auth_state` tinyint(3) UNSIGNED NOT NULL COMMENT '实名认证状态',
  `ec_name` varchar(30) DEFAULT NULL,
  `ec_relation` varchar(30) DEFAULT NULL,
  `ec_phone1` varchar(30) DEFAULT NULL,
  `ec_phone2` varchar(30) DEFAULT NULL,
  `crt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `wx_open_id`, `phone`, `pwd`, `nick_name`, `name`, `sex`, `birth_day`, `id_type`, `id_number`, `expire_day`, `thumb`, `province`, `city`, `address`, `auth_state`, `ec_name`, `ec_relation`, `ec_phone1`, `ec_phone2`, `crt_time`) VALUES
(1, 'abc', '18621758237', 'e10adc3949ba59abbe56e057f20f883e', '你好', '陈晓东', 1, '1982-01-05', NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(2, '222', '122133', 'b0baee9d279d34fa1dfd71aadb908c3f', 'abc', NULL, 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(3, 'abcd', 'sdfsdf1231', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(4, 'sddff', '111111111', '7fa8282ad93047a4d6fe6111c93b308a', 'abcdde', 'abc', 1, '2016-02-25', 'IDCARD', '111111111', '2016-02-27', 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(5, 'abcd', 'sdfsdf1232', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA2', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(6, 'abcd', 'sdfsdf1236', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA6', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(7, 'abcd', 'sdfsdf1237', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA7', 'aaa', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00'),
(8, 'abc8', 'sdfsdf1238', 'a8f5f167f44f4964e6c998dee827110c', 'AAAAA8', 'a8a', 2, NULL, NULL, NULL, NULL, 'http%3A%2F%2Fadmin.xrace.com%2Fupload%2FRaceCatalogIcon%2F79228797gw1em2ejx8xktj20w01kw4fe.jpg', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `op_user`
--
ALTER TABLE `op_user`
  ADD PRIMARY KEY (`op_uid`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_auth_log`
--
ALTER TABLE `user_auth_log`
  ADD PRIMARY KEY (`auth_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
