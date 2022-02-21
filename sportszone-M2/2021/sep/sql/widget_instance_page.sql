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
-- Table structure for table `widget_instance_page`
--

CREATE TABLE `widget_instance_page` (
  `page_id` int(10) UNSIGNED NOT NULL COMMENT 'Page ID',
  `instance_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Instance ID',
  `page_group` varchar(25) DEFAULT NULL COMMENT 'Block Group Type',
  `layout_handle` varchar(255) DEFAULT NULL COMMENT 'Layout Handle',
  `block_reference` varchar(255) DEFAULT NULL COMMENT 'Container',
  `page_for` varchar(25) DEFAULT NULL COMMENT 'For instance entities',
  `entities` text DEFAULT NULL COMMENT 'Catalog entities (comma separated)',
  `page_template` varchar(255) DEFAULT NULL COMMENT 'Path to widget template'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Instance of Widget on Page';

--
-- Dumping data for table `widget_instance_page`
--

INSERT INTO `widget_instance_page` (`page_id`, `instance_id`, `page_group`, `layout_handle`, `block_reference`, `page_for`, `entities`, `page_template`) VALUES
(1, 1, 'all_pages', 'default', 'top.container', 'all', '', 'widget/static_block/default.phtml'),
(2, 2, 'all_pages', 'default', 'after.body.start', 'all', '', 'widget/static_block/default.phtml'),
(3, 3, 'all_pages', 'default', 'after.body.start', 'all', '', 'widget/static_block/default.phtml'),
(4, 4, 'anchor_categories', 'catalog_category_view_type_layered', 'sidebar.main', 'all', '', 'widget/static_block/default.phtml'),
(5, 5, 'pages', 'cms_index_index', 'after.body.start', 'all', '', 'widget/static_block/default.phtml');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `widget_instance_page`
--
ALTER TABLE `widget_instance_page`
  ADD PRIMARY KEY (`page_id`),
  ADD KEY `WIDGET_INSTANCE_PAGE_INSTANCE_ID` (`instance_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `widget_instance_page`
--
ALTER TABLE `widget_instance_page`
  MODIFY `page_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Page ID', AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `widget_instance_page`
--
ALTER TABLE `widget_instance_page`
  ADD CONSTRAINT `WIDGET_INSTANCE_PAGE_INSTANCE_ID_WIDGET_INSTANCE_INSTANCE_ID` FOREIGN KEY (`instance_id`) REFERENCES `widget_instance` (`instance_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
