-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- ����: 127.0.0.1
-- ��������: 2016-03-24 17:05:06
-- �������汾: 5.6.11
-- PHP �汾: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- ���ݿ�: `xrace_config`
--
CREATE DATABASE IF NOT EXISTS `xrace_config` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `xrace_config`;

DELIMITER $$
--
-- �洢����
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
-- ��Ľṹ `config_app_os`
--

CREATE TABLE IF NOT EXISTS `config_app_os` (
  `AppOSId` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APPϵͳ����',
  `AppOSName` varchar(32) NOT NULL COMMENT 'APPϵͳ��������',
  `comment` varchar(1024) NOT NULL COMMENT 'ѹ������',
  PRIMARY KEY (`AppOSId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='APPϵͳ����' AUTO_INCREMENT=4 ;

--
-- ת����е����� `config_app_os`
--

INSERT INTO `config_app_os` (`AppOSId`, `AppOSName`, `comment`) VALUES
(1, 'IOS2', ''),
(2, 'abc', '');

-- --------------------------------------------------------

--
-- ��Ľṹ `config_app_type`
--

CREATE TABLE IF NOT EXISTS `config_app_type` (
  `AppTypeId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP����',
  `AppTypeName` varchar(32) NOT NULL COMMENT 'APP��������',
  `comment` varchar(1024) NOT NULL COMMENT 'ѹ������',
  PRIMARY KEY (`AppTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='APP���͹���' AUTO_INCREMENT=6 ;

--
-- ת����е����� `config_app_type`
--

INSERT INTO `config_app_type` (`AppTypeId`, `AppTypeName`, `comment`) VALUES
(1, 'HEROS', ''),
(2, 'MIT', ''),
(3, 'abd', ''),
(4, '��������2', '');

-- --------------------------------------------------------

--
-- ��Ľṹ `config_app_version`
--

CREATE TABLE IF NOT EXISTS `config_app_version` (
  `VersionId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `AppVersion` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'APP�汾',
  `AppType` int(4) unsigned NOT NULL COMMENT 'APPϵͳ����',
  `comment` varchar(1024) COLLATE utf8_bin NOT NULL COMMENT 'ѹ������',
  PRIMARY KEY (`VersionId`),
  KEY `AppType` (`AppType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='APP�汾����' AUTO_INCREMENT=1 ;
