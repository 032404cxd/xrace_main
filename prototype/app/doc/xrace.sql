-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-02-17 12:38:00
-- 服务器版本： 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xrace`
--
DROP DATABASE `xrace`;
CREATE DATABASE IF NOT EXISTS `xrace` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `xrace`;

-- --------------------------------------------------------

--
-- 表的结构 `op_user`
--

DROP TABLE IF EXISTS `op_user`;
CREATE TABLE `op_user` (
  `op_uid` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
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

DROP TABLE IF EXISTS `user_auth_log`;
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

DROP TABLE IF EXISTS `user_profile`;
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
