-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 20, 2021 at 02:37 AM
-- Server version: 10.2.37-MariaDB-log-cll-lve
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `extremesports_240418`
--

-- --------------------------------------------------------

--
-- Table structure for table `eav_attribute_option`
--

CREATE TABLE `eav_attribute_option` (
  `option_id` int(10) UNSIGNED NOT NULL COMMENT 'Option Id',
  `attribute_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Attribute Id',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Sort Order'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Eav Attribute Option';

--
-- Dumping data for table `eav_attribute_option`
--

INSERT INTO `eav_attribute_option` (`option_id`, `attribute_id`, `sort_order`) VALUES
(3, 81, 0),
(4, 81, 0),
(5, 81, 0),
(6, 81, 0),
(7, 81, 0),
(8, 81, 0),
(26, 81, 0),
(52, 81, 0),
(105, 81, 0),
(146, 81, 0),
(234, 81, 0),
(237, 81, 0),
(271, 81, 0),
(320, 81, 0),
(323, 81, 0),
(326, 81, 0),
(359, 81, 0),
(563, 81, 0),
(574, 81, 0),
(575, 81, 0),
(577, 81, 0),
(585, 81, 0),
(587, 81, 0),
(588, 81, 0),
(1015, 81, 0),
(1016, 81, 1),
(1086, 81, 0),
(1087, 81, 1),
(1107, 81, 0),
(1108, 81, 1),
(1192, 81, 0),
(1193, 81, 1),
(1217, 81, 1),
(1237, 81, 1),
(1276, 81, 0),
(1277, 81, 1),
(1326, 81, 0),
(1327, 81, 1),
(1360, 81, 0),
(1361, 81, 1),
(1374, 81, 0),
(1375, 81, 1),
(1376, 81, 0),
(1377, 81, 1),
(1410, 81, 0),
(1411, 81, 1),
(1419, 81, 1),
(1517, 81, 1),
(1522, 81, 0),
(1523, 81, 1),
(1544, 81, 0),
(1545, 81, 1),
(1615, 81, 0),
(1616, 81, 1),
(1621, 81, 0),
(1622, 81, 1),
(1643, 81, 0),
(1644, 81, 1),
(1653, 81, 0),
(1654, 81, 1),
(1815, 81, 1),
(1838, 81, 0),
(1839, 81, 1),
(1900, 81, 0),
(1901, 81, 1),
(1984, 81, 0),
(1985, 81, 1),
(1996, 81, 0),
(1997, 81, 1),
(2025, 81, 0),
(2026, 81, 1),
(2029, 81, 0),
(2030, 81, 1),
(2037, 81, 0),
(2038, 81, 1),
(2058, 81, 1),
(2093, 81, 0),
(2094, 81, 1),
(2119, 81, 0),
(2120, 81, 1),
(2682, 81, 0),
(2683, 81, 1),
(2684, 81, 0),
(2685, 81, 1),
(2739, 81, 0),
(2740, 81, 1),
(2993, 81, 0),
(2994, 81, 1),
(5217, 81, 0),
(5218, 81, 1),
(5661, 81, 0),
(6197, 81, 0),
(6198, 81, 0),
(6199, 81, 0),
(6200, 81, 0),
(6201, 81, 0),
(6202, 81, 0),
(6203, 81, 0),
(6204, 81, 0),
(6205, 81, 0),
(6206, 81, 0),
(6207, 81, 0),
(6208, 81, 0),
(6209, 81, 0),
(6210, 81, 0),
(6211, 81, 0),
(6212, 81, 0),
(6213, 81, 0),
(6214, 81, 0),
(6215, 81, 0),
(6216, 81, 0),
(6217, 81, 0),
(6218, 81, 0),
(6219, 81, 0),
(6220, 81, 0),
(6221, 81, 0),
(6222, 81, 0),
(6223, 81, 0),
(6224, 81, 0),
(6225, 81, 0),
(6226, 81, 0),
(6227, 81, 0),
(6228, 81, 0),
(6229, 81, 0),
(6230, 81, 0),
(6231, 81, 0),
(6232, 81, 0),
(6233, 81, 0),
(6234, 81, 0),
(6428, 81, 0),
(6429, 81, 1),
(6443, 81, 0),
(6444, 81, 1),
(6447, 81, 0),
(6448, 81, 1),
(6449, 81, 0),
(6450, 81, 1),
(6453, 81, 0),
(6454, 81, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eav_attribute_option`
--
ALTER TABLE `eav_attribute_option`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `IDX_EAV_ATTRIBUTE_OPTION_ATTRIBUTE_ID` (`attribute_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eav_attribute_option`
--
ALTER TABLE `eav_attribute_option`
  MODIFY `option_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Option Id', AUTO_INCREMENT=6461;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eav_attribute_option`
--
ALTER TABLE `eav_attribute_option`
  ADD CONSTRAINT `FK_EAV_ATTRIBUTE_OPTION_ATTRIBUTE_ID_EAV_ATTRIBUTE_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
