
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

ALTER TABLE `config_app_version` ADD `AppOSId` INT( 4 ) UNSIGNED NOT NULL COMMENT 'APP系统类型' AFTER `AppType` ;
ALTER TABLE `config_app_version` CHANGE `AppType` `AppTypeId` INT( 4 ) UNSIGNED NOT NULL COMMENT 'APP系统类型';
ALTER TABLE `config_app_version` CHANGE `AppVersion` `AppVersion` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'APP版本';
ALTER TABLE `config_app_version` CHANGE `comment` `comment` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '压缩数组';
ALTER TABLE `config_app_version` CHANGE `VersionId` `AppVersionId` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID';
ALTER TABLE `config_app_version` ADD `AppDownloadUrl` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'APP下载路径' AFTER `AppOSId` ;
