-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 25, 2021 at 12:17 AM
-- Server version: 10.2.38-MariaDB-log-cll-lve
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
-- Database: `m2sportszonecana_newv3`
--

-- --------------------------------------------------------

--
-- Table structure for table `aw_blog_post_store`
--

CREATE TABLE `aw_blog_post_store` (
  `post_id` int(10) UNSIGNED NOT NULL COMMENT 'Post Id',
  `store_id` smallint(5) UNSIGNED NOT NULL COMMENT 'Store Id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blog Post To Store Relation Table';

--
-- Dumping data for table `aw_blog_post_store`
--

INSERT INTO `aw_blog_post_store` (`post_id`, `store_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aw_blog_post_store`
--
ALTER TABLE `aw_blog_post_store`
  ADD PRIMARY KEY (`post_id`,`store_id`),
  ADD KEY `AW_BLOG_POST_STORE_POST_ID` (`post_id`),
  ADD KEY `AW_BLOG_POST_STORE_STORE_ID` (`store_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aw_blog_post_store`
--
ALTER TABLE `aw_blog_post_store`
  ADD CONSTRAINT `AW_BLOG_POST_STORE_POST_ID_AW_BLOG_POST_ID` FOREIGN KEY (`post_id`) REFERENCES `aw_blog_post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `AW_BLOG_POST_STORE_STORE_ID_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
