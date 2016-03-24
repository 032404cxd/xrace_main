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
(1, 'IOS', ''),
(2, 'ANDROID', '');

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
  `AppVersionId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `AppVersion` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT 'APP�汾',
  `AppTypeId` int(4) unsigned NOT NULL COMMENT 'APPϵͳ����',
  `AppOSId` int(4) unsigned NOT NULL COMMENT 'APPϵͳ����',
  `AppDownloadUrl` varchar(256) CHARACTER SET utf8 NOT NULL COMMENT 'APP����·��',
  `comment` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT 'ѹ������',
  PRIMARY KEY (`AppVersionId`),
  UNIQUE KEY `UniqueVersion` (`AppVersion`,`AppTypeId`,`AppOSId`),
  KEY `AppType` (`AppTypeId`),
  KEY `AppVersion` (`AppVersion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='APP�汾����' AUTO_INCREMENT=5 ;

--
-- ת����е����� `config_app_version`
--

INSERT INTO `config_app_version` (`AppVersionId`, `AppVersion`, `AppTypeId`, `AppOSId`, `AppDownloadUrl`, `comment`) VALUES
(3, '1.2.1', 1, 2, 'asdfasgsadgasdg', '{"VersionComment":"1.\\u7248\\u672c\\u8bf4\\u660e\\n2.\\u7248\\u672c\\u8bf4\\u660e2\\n3.\\u7248\\u672c\\u8bf4\\u660e3"}'),
(4, '1.2.2', 2, 2, 'adfasfdasdgsaga111', '{"VersionComment":"asgaggsgags111"}');