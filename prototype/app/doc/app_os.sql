-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2016-03-24 17:05:06
-- 服务器版本: 5.6.11
-- PHP 版本: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 数据库: `xrace_config`
--
CREATE DATABASE IF NOT EXISTS `xrace_config` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `xrace_config`;

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_nextval`(
    in p_name          varchar(30),
    out p_value        integer)
begin
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
-- 表的结构 `config_app_os`
--

CREATE TABLE IF NOT EXISTS `config_app_os` (
  `AppOSId` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP系统类型',
  `AppOSName` varchar(32) NOT NULL COMMENT 'APP系统类型名称',
  `comment` varchar(1024) NOT NULL COMMENT '压缩数组',
  PRIMARY KEY (`AppOSId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='APP系统管理' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `config_app_os`
--

INSERT INTO `config_app_os` (`AppOSId`, `AppOSName`, `comment`) VALUES
(1, 'IOS2', ''),
(2, 'abc', '');

-- --------------------------------------------------------

--
-- 表的结构 `config_app_type`
--

CREATE TABLE IF NOT EXISTS `config_app_type` (
  `AppTypeId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP类型',
  `AppTypeName` varchar(32) NOT NULL COMMENT 'APP类型名称',
  `comment` varchar(1024) NOT NULL COMMENT '压缩数组',
  PRIMARY KEY (`AppTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='APP类型管理' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `config_app_type`
--

INSERT INTO `config_app_type` (`AppTypeId`, `AppTypeName`, `comment`) VALUES
(1, 'HEROS', ''),
(2, 'MIT', ''),
(3, 'abd', ''),
(4, '测试类型2', '');

-- --------------------------------------------------------

--
-- 表的结构 `config_app_version`
--

CREATE TABLE IF NOT EXISTS `config_app_version` (
  `VersionId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `AppVersion` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'APP版本',
  `AppType` int(4) unsigned NOT NULL COMMENT 'APP系统类型',
  `comment` varchar(1024) COLLATE utf8_bin NOT NULL COMMENT '压缩数组',
  PRIMARY KEY (`VersionId`),
  KEY `AppType` (`AppType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='APP版本管理' AUTO_INCREMENT=1 ;
