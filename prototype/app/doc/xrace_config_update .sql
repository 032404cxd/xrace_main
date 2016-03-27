-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-03-26 02:35:29
-- 服务器版本： 10.1.10-MariaDB
-- PHP Version: 7.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xrace_config`
--

DELIMITER $$
--
-- 存储过程
--
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
-- 表的结构 `config_license`
--

CREATE TABLE `config_license` (
  `LicenseId` int(10) UNSIGNED NOT NULL COMMENT '执照ID',
  `ManagerId` int(10) NOT NULL COMMENT '管理员ID',
  `UserId` int(10) NOT NULL COMMENT '用户ID',
  `CatalogId` int(10) NOT NULL COMMENT '赛事ID',
  `GroupId` int(10) NOT NULL COMMENT '赛组ID',
  `LicenseStatus` int(1) NOT NULL COMMENT '执照状态（0 :已生效,1:已过期,2:已删除）',
  `LicenseAddDate` datetime NOT NULL COMMENT '执照添加时间',
  `LastUpdateDate` datetime NOT NULL COMMENT '执照更新时间',
  `LicenseStartDate` datetime NOT NULL COMMENT '执照开始时间',
  `LicenseEndDate` datetime NOT NULL COMMENT '执照结束时间',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL COMMENT '执照操作理由'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `config_license`
--
ALTER TABLE `config_license`
  ADD PRIMARY KEY (`LicenseId`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `config_license`
--
ALTER TABLE `config_license`
  MODIFY `LicenseId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '执照ID', AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
