-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 25, 2021 at 02:41 AM
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
-- Table structure for table `ci_shippingrestrictions`
--

CREATE TABLE `ci_shippingrestrictions` (
  `sr_id` int(10) UNSIGNED NOT NULL COMMENT 'Shipping Restrictions Primary Id',
  `name` varchar(255) NOT NULL COMMENT 'shipping Rule Name',
  `carriers` text NOT NULL COMMENT 'Shipping Restrictions Carriers',
  `status` smallint(6) NOT NULL DEFAULT 0 COMMENT 'Status',
  `conditions_serialized` mediumtext DEFAULT NULL COMMENT 'Conditions Serialized',
  `couponcode` varchar(255) DEFAULT NULL COMMENT 'Coupon Code',
  `error_msg` varchar(255) NOT NULL COMMENT 'Error Message',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Sort Order',
  `customer_group` varchar(255) NOT NULL COMMENT 'Customer Groups',
  `stores` varchar(255) DEFAULT NULL COMMENT 'Store Id',
  `days` varchar(255) DEFAULT NULL COMMENT 'Days'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ci_shippingrestrictions';

--
-- Dumping data for table `ci_shippingrestrictions`
--

INSERT INTO `ci_shippingrestrictions` (`sr_id`, `name`, `carriers`, `status`, `conditions_serialized`, `couponcode`, `error_msg`, `sort_order`, `customer_group`, `stores`, `days`) VALUES
(1, 'Restrict Pickup at Store', '', 0, '{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":\"1\",\"is_value_processed\":null,\"aggregator\":\"any\",\"conditions\":[{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"AO\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"postcode\",\"operator\":\"==\",\"value\":\"3207\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"AU\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"BY\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"BE\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"BG\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"BA\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"HR\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"CZ\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"DK\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"EE\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"FI\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"FR\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"DE\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"DE\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"GR\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"HU\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"IE\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"postcode\",\"operator\":\"==\",\"value\":\"5\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"IT\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"LV\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"LI\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"LT\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"LU\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"MC\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"NL\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"NO\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"PL\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"PT\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"RO\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"RS\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"ES\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"SE\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"CH\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"UA\",\"is_value_processed\":false},{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Address\",\"attribute\":\"country_id\",\"operator\":\"==\",\"value\":\"GB\",\"is_value_processed\":false}]}', NULL, '', 0, '0,1,2,3', '0,1', '0,1,2,3,4,5,6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_shippingrestrictions`
--
ALTER TABLE `ci_shippingrestrictions`
  ADD PRIMARY KEY (`sr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ci_shippingrestrictions`
--
ALTER TABLE `ci_shippingrestrictions`
  MODIFY `sr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Shipping Restrictions Primary Id', AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
