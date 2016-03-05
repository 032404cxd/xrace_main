-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-03-05 02:56:56
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

-- --------------------------------------------------------

--
-- 表的结构 `config_race`
--

CREATE TABLE `config_race` (
  `RaceId` int(10) UNSIGNED NOT NULL COMMENT '比赛ID',
  `RaceStageId` int(10) UNSIGNED NOT NULL COMMENT '赛事分站ID',
  `RaceGroupId` int(10) UNSIGNED NOT NULL COMMENT '赛事分组ID',
  `RaceName` varchar(32) NOT NULL COMMENT '比赛名称',
  `PriceList` varchar(100) NOT NULL COMMENT '价格规范|人数:价格|人数:价格',
  `SingleUser` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否接受单人报名',
  `TeamUser` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否接受团队报名',
  `ApplyStartTime` datetime NOT NULL COMMENT '开始报名时间',
  `ApplyEndTime` datetime NOT NULL COMMENT '结束报名时间',
  `StartTime` datetime NOT NULL COMMENT '比赛开始时间',
  `EndTime` datetime NOT NULL COMMENT '比赛结束时间',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='比赛详情';

--
-- 转存表中的数据 `config_race`
--

INSERT INTO `config_race` (`RaceId`, `RaceStageId`, `RaceGroupId`, `RaceName`, `PriceList`, `SingleUser`, `TeamUser`, `ApplyStartTime`, `ApplyEndTime`, `StartTime`, `EndTime`, `comment`) VALUES
(6, 1, 1, 'aa11', '111111', '0', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-02-26 17:42:21', '2016-02-29 17:42:21', '{"DetailList":[{"SportsTypeId":1,"TimingId":"26"},{"SportsTypeId":4,"TimingId":"28"},{"SportsTypeId":3,"TimingId":"27"}]}'),
(7, 1, 1, 'bb', '1111', '1', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-02-19 17:42:21', '2016-02-20 17:42:21', ''),
(8, 1, 1, '111', '222', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-02-19 18:05:15', '2016-02-20 18:05:15', ''),
(9, 1, 1, '111', '22', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-02-19 18:05:47', '2016-02-20 18:05:47', ''),
(10, 1, 1, 'aa', '11111', '0', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-02-19 17:42:21', '2016-02-20 17:42:21', ''),
(11, 1, 3, '个人计时赛', '100', '1', '1', '2016-03-06 09:51:02', '2016-03-13 10:00:00', '2016-03-20 11:16:26', '2016-03-21 11:16:26', ''),
(12, 1, 3, '团队计时赛', '11', '1', '1', '2016-03-05 09:55:38', '2016-03-12 10:00:00', '2016-03-20 11:18:36', '2016-03-21 11:18:36', ''),
(13, 1, 3, '大组赛', '11', '1', '1', '2016-03-05 09:54:45', '2016-03-12 10:00:00', '2016-03-20 11:20:41', '2016-03-21 11:20:41', ''),
(14, 1, 3, 'ceshi', '100', '1', '1', '2016-03-07 00:00:00', '2016-03-14 01:00:00', '2016-03-21 08:08:41', '2016-03-22 08:08:41', ''),
(15, 1, 3, '1221', '12', '1', '1', '2016-03-06 08:16:52', '2016-03-13 08:16:52', '2016-03-20 08:16:52', '2016-03-21 08:16:52', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `config_race`
--
ALTER TABLE `config_race`
  ADD PRIMARY KEY (`RaceId`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `config_race`
--
ALTER TABLE `config_race`
  MODIFY `RaceId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '比赛ID', AUTO_INCREMENT=16;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
