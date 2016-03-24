
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

ALTER TABLE `config_app_version` ADD `AppOSId` INT( 4 ) UNSIGNED NOT NULL COMMENT 'APPϵͳ����' AFTER `AppType` ;
ALTER TABLE `config_app_version` CHANGE `AppType` `AppTypeId` INT( 4 ) UNSIGNED NOT NULL COMMENT 'APPϵͳ����';
ALTER TABLE `config_app_version` CHANGE `AppVersion` `AppVersion` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'APP�汾';
ALTER TABLE `config_app_version` CHANGE `comment` `comment` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ѹ������';
ALTER TABLE `config_app_version` CHANGE `VersionId` `AppVersionId` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '����ID';
ALTER TABLE `config_app_version` ADD `AppDownloadUrl` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'APP����·��' AFTER `AppOSId` ;
