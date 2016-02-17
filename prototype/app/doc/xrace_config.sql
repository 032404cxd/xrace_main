-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-02-17 12:38:18
-- 服务器版本： 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xrace_config`
--
DROP DATABASE `xrace_config`;
CREATE DATABASE IF NOT EXISTS `xrace_config` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `xrace_config`;

DELIMITER $$
--
-- 存储过程
--
DROP PROCEDURE IF EXISTS `p_nextval`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_nextval` (IN `p_name` VARCHAR(30), OUT `p_value` INTEGER)  begin

    START TRANSACTION;

    select value into p_value from id where name=p_name; 

    if found_rows() = 0 then

        set p_value = -1;

    else

        set p_value = p_value + 1;

        update id set value = p_value where name = p_name;

    end if;

    COMMIT;

end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `config_group`
--

DROP TABLE IF EXISTS `config_group`;
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

DROP TABLE IF EXISTS `config_logs_manager`;
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

DROP TABLE IF EXISTS `config_manager`;
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
(2, 'scadmin', '3e4c853a030f8adb8ad7beb22655b5c2', 40, '10', 1, 1455702705, 1455702713, '127.0.0.1', '', 2011, 0, ''),
(26, '陈光明', 'e10adc3949ba59abbe56e057f20f883e', 34, '10', 0, 1381738898, 1381738898, '180.172.94.74', '58.247.169.182', 1357368334, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `config_menu`
--

DROP TABLE IF EXISTS `config_menu`;
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

DROP TABLE IF EXISTS `config_menu_permission`;
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

DROP TABLE IF EXISTS `config_menu_purview`;
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
-- 表的结构 `config_race_catalog`
--

DROP TABLE IF EXISTS `config_race_catalog`;
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

DROP TABLE IF EXISTS `config_race_group`;
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

DROP TABLE IF EXISTS `config_race_stage`;
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

DROP TABLE IF EXISTS `config_race_stage_group`;
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
(1, 1, '200', '0', '0', '2016-02-08 15:42:46', '2016-02-16 15:42:46', '{"DetailList":[{"SportsTypeId":3,"TimingId":1},{"SportsTypeId":4,"TimingId":"16"},{"SportsTypeId":2,"TimingId":"15"},{"SportsTypeId":3},{"SportsTypeId":1},{"SportsTypeId":3},{"SportsTypeId":4}]}');

-- --------------------------------------------------------

--
-- 表的结构 `config_race_type`
--

DROP TABLE IF EXISTS `config_race_type`;
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
-- 表的结构 `config_sports_type`
--

DROP TABLE IF EXISTS `config_sports_type`;
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
-- 表的结构 `config_timing_point`
--

DROP TABLE IF EXISTS `config_timing_point`;
CREATE TABLE `config_timing_point` (
  `TimingId` int(10) UNSIGNED NOT NULL COMMENT '计时点ID',
  `TimingChipId` varchar(32) NOT NULL COMMENT '计时芯片ID',
  `comment` varchar(2048) NOT NULL COMMENT '压缩数组'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计时芯片配置表';

--
-- 转存表中的数据 `config_timing_point`
--

INSERT INTO `config_timing_point` (`TimingId`, `TimingChipId`, `comment`) VALUES
(1, '', '[{"TName":"\\u8d77\\u70b9","ToNext":"1000","AltAsc":100,"AltDec":200,"ChipId":"abc","Round":10},{"TName":"\\u8ba1\\u65f6\\u70b92","ToNext":150,"AltAsc":200,"AltDec":0,"Round":10,"ChipId":"100"},{"TName":"\\u8ba1\\u65f6\\u70b93","ToNext":150,"AltAsc":200,"AltDec":0,"Round":10,"ChipId":"100"},{"TName":"\\u8ba1\\u65f6\\u70b91","ToNext":"2000","AltAsc":100,"AltDec":200,"ChipId":"abc","Round":10}]'),
(16, '', '[{"TName":"\\u8ba1\\u65f6\\u70b912","ToNext":1000,"AltAsc":0,"AltDec":0,"Round":0,"ChipId":"111"},{"TName":"\\u8ba1\\u65f6\\u70b913","ToNext":125,"AltAsc":15,"AltDec":0,"Round":15,"ChipId":"100"}]');

-- --------------------------------------------------------

--
-- 表的结构 `id`
--

DROP TABLE IF EXISTS `id`;
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

--
-- Indexes for dumped tables
--

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
-- Indexes for table `config_sports_type`
--
ALTER TABLE `config_sports_type`
  ADD PRIMARY KEY (`SportsTypeId`),
  ADD UNIQUE KEY `SportsTypeName` (`SportsTypeName`);

--
-- Indexes for table `config_timing_point`
--
ALTER TABLE `config_timing_point`
  ADD PRIMARY KEY (`TimingId`);

--
-- Indexes for table `id`
--
ALTER TABLE `id`
  ADD PRIMARY KEY (`name`);

--
-- 在导出的表使用AUTO_INCREMENT
--

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
-- 使用表AUTO_INCREMENT `config_timing_point`
--
ALTER TABLE `config_timing_point`
  MODIFY `TimingId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '计时点ID', AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
