-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 15, 2016 at 02:37 PM
-- Server version: 5.6.25
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pokermain`
--

-- --------------------------------------------------------

--
-- Table structure for table `system_paylog`
--

CREATE TABLE IF NOT EXISTS `system_paylog` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `merchant` varchar(32) NOT NULL,
  `transaction` varchar(32) DEFAULT NULL,
  `price` decimal(13,2) DEFAULT NULL,
  `time_add` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `response` text,
  `paystatus` int(1) DEFAULT NULL,
  `errormessages` text,
  `ip_address` bigint(20) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `receipt` text
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `system_paylog`
--
ALTER TABLE `system_paylog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `merchant` (`merchant`,`transaction`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `system_paylog`
--
ALTER TABLE `system_paylog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
