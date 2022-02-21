-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 25, 2021 at 12:19 AM
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
-- Table structure for table `aw_blog_author`
--

CREATE TABLE `aw_blog_author` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Author Id',
  `firstname` varchar(255) NOT NULL COMMENT 'Author First Name',
  `lastname` varchar(255) NOT NULL COMMENT 'Author Last Name',
  `url_key` varchar(255) NOT NULL COMMENT 'URL-Key',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'Meta Title',
  `meta_keywords` varchar(255) DEFAULT NULL COMMENT 'Meta Keywords',
  `meta_description` varchar(255) DEFAULT NULL COMMENT 'Meta Description',
  `meta_prefix` varchar(255) DEFAULT NULL COMMENT 'Meta Prefix',
  `meta_suffix` varchar(255) DEFAULT NULL COMMENT 'Meta Suffix',
  `job_position` varchar(255) DEFAULT NULL COMMENT 'Job Position',
  `image_file` varchar(255) DEFAULT NULL COMMENT 'Image File',
  `short_bio` text DEFAULT NULL COMMENT 'Short Bio',
  `twitter_id` varchar(255) DEFAULT NULL COMMENT 'Twitter ID',
  `facebook_id` varchar(255) DEFAULT NULL COMMENT 'Facebook ID',
  `linkedin_id` varchar(255) DEFAULT NULL COMMENT 'LinkedIn ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blog Author Table';

--
-- Dumping data for table `aw_blog_author`
--

INSERT INTO `aw_blog_author` (`id`, `firstname`, `lastname`, `url_key`, `meta_title`, `meta_keywords`, `meta_description`, `meta_prefix`, `meta_suffix`, `job_position`, `image_file`, `short_bio`, `twitter_id`, `facebook_id`, `linkedin_id`) VALUES
(1, 'Vince ', 'Turkalj', 'vince--turkalj', '', '', '', '', '', '', '', '', '', '', ''),
(2, 'SportsZone ', 'Canada', 'sportszone-canada-sportszone-canada', '', '', '', '', '', '', '', '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aw_blog_author`
--
ALTER TABLE `aw_blog_author`
  ADD PRIMARY KEY (`id`),
  ADD KEY `AW_BLOG_AUTHOR_TWITTER_ID` (`twitter_id`),
  ADD KEY `AW_BLOG_AUTHOR_FACEBOOK_ID` (`facebook_id`),
  ADD KEY `AW_BLOG_AUTHOR_LINKEDIN_ID` (`linkedin_id`),
  ADD KEY `AW_BLOG_AUTHOR_URL_KEY` (`url_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aw_blog_author`
--
ALTER TABLE `aw_blog_author`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Author Id', AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
