-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-03-17 11:35:34
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
-- 表的结构 `config_product`
--

CREATE TABLE `config_product` (
  `ProductId` int(10) UNSIGNED NOT NULL COMMENT '商品标识',
  `ProductName` varchar(32) NOT NULL COMMENT '商品名称',
  `ProductTypeId` int(10) UNSIGNED NOT NULL COMMENT '对应商品类型ID',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_product`
--

INSERT INTO `config_product` (`ProductId`, `ProductName`, `ProductTypeId`, `comment`) VALUES
(2, '三星级酒店1', 1, ''),
(3, '四星级酒店2', 1, ''),
(4, '五星级酒店', 1, ''),
(5, '豪华酒店', 1, ''),
(6, '公交车', 2, ''),
(7, '青年旅社', 1, ''),
(8, '早饭', 3, ''),
(9, '午饭', 3, ''),
(10, '晚饭', 3, ''),
(11, '面包', 10, ''),
(12, '牛奶', 10, ''),
(13, 'L号', 7, ''),
(14, 'M号', 7, ''),
(15, '出租车', 2, ''),
(16, '帽子', 11, '');

-- --------------------------------------------------------

--
-- 表的结构 `config_product_type`
--

CREATE TABLE `config_product_type` (
  `ProductTypeId` int(10) UNSIGNED NOT NULL COMMENT '商品类型标识',
  `RaceCatalogId` int(10) UNSIGNED NOT NULL COMMENT '对应赛事ID',
  `ProductTypeName` varchar(32) NOT NULL COMMENT '商品类型名称',
  `comment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运动类型配置表';

--
-- 转存表中的数据 `config_product_type`
--

INSERT INTO `config_product_type` (`ProductTypeId`, `RaceCatalogId`, `ProductTypeName`, `comment`) VALUES
(1, 1, '住宿', ''),
(2, 1, '交通', ''),
(3, 1, '餐饮', ''),
(7, 1, '衣服', ''),
(10, 2, '食品', ''),
(11, 2, '装备', '');

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
(1, '安亭站', '{"SelectedRaceGroup":{"1":"1","3":"3","11":"11","17":"17","18":"18"},"RaceStageIconList":{"1":{"RaceStageIcon":"D:\\\\xampp\\\\htdocs\\\\xrace_main\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceStageIcon\\/66f73a87gw1f1ckomjlcpj20ic0f1413.jpg","RaceStageIcon_root":"\\/upload\\/RaceStageIcon\\/66f73a87gw1f1ckomjlcpj20ic0f1413.jpg"},"2":{"RaceStageIcon":"D:\\\\xampp\\\\htdocs\\\\xrace_main\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceStageIcon\\/563175761372339534.jpg","RaceStageIcon_root":"\\/upload\\/RaceStageIcon\\/563175761372339534.jpg"}},"SelectedProductList":{"1":{"2":{"ProductId":"2","ProductLimit":1,"ProductPrice":99}}}}', 1, '2016-02-10', '2016-02-16'),
(2, '30-40年龄组', '{"SelectedRaceGroup":{"10":"10","12":"12"}}', 2, '2016-02-03', '2016-02-18'),
(3, '中铁协系列赛', '{"SelectedRaceGroup":{"10":"10","12":"12","13":"13"}}', 2, '2016-02-01', '2016-02-03'),
(9, '金山站', '{"SelectedRaceGroup":{"1":"1","3":"3","11":"11"},"RaceStageIconList":{"1":{"RaceStageIcon":"D:\\\\xampp\\\\htdocs\\\\xrace_main\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceStageIcon\\/66f73a87gw1f1ckomjlcpj20ic0f1413.jpg","RaceStageIcon_root":"\\/upload\\/RaceStageIcon\\/66f73a87gw1f1ckomjlcpj20ic0f1413.jpg"}}}', 1, '2016-02-03', '2016-02-29'),
(10, '南汇站', '{"SelectedRaceGroup":{"1":"1","3":"3","11":"11"}}', 1, '2016-02-03', '2016-02-17'),
(15, '奉贤站', '{"SelectedRaceGroup":{"1":"1","3":"3","11":"11"}}', 1, '2016-02-01', '2016-03-02'),
(16, '最终站', '{"SelectedRaceGroup":["10","12"],"RaceStageIconList":{"1":{"RaceStageIcon":"D:\\\\xampp\\\\htdocs\\\\xrace_main\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceStageIcon\\/563175761372339534.jpg","RaceStageIcon_root":"\\/upload\\/RaceStageIcon\\/563175761372339534.jpg"}}}', 2, NULL, NULL),
(17, '黄山站', '{"SelectedRaceGroup":["1","3"],"RaceStageIconList":{"1":{"RaceStageIcon":"D:\\\\xampp\\\\htdocs\\\\xrace_main\\\\prototype\\\\app\\\\project\\\\admin\\/html\\/upload\\/RaceStageIcon\\/66f73a87gw1f1ckomjlcpj20ic0f1413.jpg","RaceStageIcon_root":"\\/upload\\/RaceStageIcon\\/66f73a87gw1f1ckomjlcpj20ic0f1413.jpg"}}}', 1, '2016-04-08', '2016-04-10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `config_product`
--
ALTER TABLE `config_product`
  ADD PRIMARY KEY (`ProductId`);

--
-- Indexes for table `config_product_type`
--
ALTER TABLE `config_product_type`
  ADD PRIMARY KEY (`ProductTypeId`),
  ADD UNIQUE KEY `ProductTypeName` (`ProductTypeName`);

--
-- Indexes for table `config_race_stage`
--
ALTER TABLE `config_race_stage`
  ADD PRIMARY KEY (`RaceStageId`),
  ADD UNIQUE KEY `Name` (`RaceStageName`,`RaceCatalogId`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `config_product`
--
ALTER TABLE `config_product`
  MODIFY `ProductId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品标识', AUTO_INCREMENT=17;
--
-- 使用表AUTO_INCREMENT `config_product_type`
--
ALTER TABLE `config_product_type`
  MODIFY `ProductTypeId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品类型标识', AUTO_INCREMENT=12;
--
-- 使用表AUTO_INCREMENT `config_race_stage`
--
ALTER TABLE `config_race_stage`
  MODIFY `RaceStageId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '赛事分站标识', AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
