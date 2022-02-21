-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 04, 2021 at 02:33 AM
-- Server version: 10.2.40-MariaDB-log-cll-lve
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
-- Database: `m2sportszonecana_newm2v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `widget_instance`
--

CREATE TABLE `widget_instance` (
  `instance_id` int(10) UNSIGNED NOT NULL COMMENT 'Instance ID',
  `instance_type` varchar(255) DEFAULT NULL COMMENT 'Instance Type',
  `theme_id` int(10) UNSIGNED NOT NULL COMMENT 'Theme ID',
  `title` varchar(255) DEFAULT NULL COMMENT 'Widget Title',
  `store_ids` varchar(255) NOT NULL DEFAULT '0' COMMENT 'Store ids',
  `widget_parameters` text DEFAULT NULL COMMENT 'Widget parameters',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Sort order'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Instances of Widget for Package Theme';

--
-- Dumping data for table `widget_instance`
--

INSERT INTO `widget_instance` (`instance_id`, `instance_type`, `theme_id`, `title`, `store_ids`, `widget_parameters`, `sort_order`) VALUES
(1, 'Magento\\Cms\\Block\\Widget\\Block', 4, 'promo block', '0', '{\"block_id\":\"17\"}', 0),
(2, 'Magento\\Cms\\Block\\Widget\\Block', 4, 'mobile menu', '0', '{\"block_id\":\"20\"}', 0),
(3, 'Magento\\Cms\\Block\\Widget\\Block', 4, 'Top custom header', '0', '{\"block_id\":\"3\"}', 0),
(4, 'Magento\\Cms\\Block\\Widget\\Block', 4, 'category filter', '0', '{\"block_id\":\"29\"}', 0),
(5, 'Magento\\Cms\\Block\\Widget\\Block', 4, 'home critical css', '0', '{\"block_id\":\"31\"}', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `widget_instance`
--
ALTER TABLE `widget_instance`
  ADD PRIMARY KEY (`instance_id`),
  ADD KEY `WIDGET_INSTANCE_THEME_ID_THEME_THEME_ID` (`theme_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `widget_instance`
--
ALTER TABLE `widget_instance`
  MODIFY `instance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Instance ID', AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `widget_instance`
--
ALTER TABLE `widget_instance`
  ADD CONSTRAINT `WIDGET_INSTANCE_THEME_ID_THEME_THEME_ID` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
