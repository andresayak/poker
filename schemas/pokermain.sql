-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 12, 2016 at 07:36 AM
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
-- Table structure for table `shop`
--

CREATE TABLE IF NOT EXISTS `shop` (
  `id` int(11) NOT NULL,
  `translate_code` varchar(64) NOT NULL,
  `title` varchar(64) DEFAULT NULL,
  `price` decimal(13,2) NOT NULL,
  `price_old` decimal(13,2) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `icon_filename` varchar(64) NOT NULL,
  `type` enum('main','progress','military','gem','promo','promo_gem','event') NOT NULL DEFAULT 'main',
  `appstore_id` varchar(64) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `discount` varchar(32) DEFAULT NULL,
  `promotype` enum('simple','event','daily') DEFAULT NULL,
  `auth_type` varchar(32) DEFAULT NULL,
  `theme` varchar(32) DEFAULT NULL,
  `info_count` int(11) DEFAULT NULL,
  `info_bonus` int(11) DEFAULT NULL,
  `promo_period` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shop_object`
--

CREATE TABLE IF NOT EXISTS `shop_object` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `object_code` varchar(64) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ban_status` enum('on','off') NOT NULL DEFAULT 'off',
  `ban_chat_status` enum('on','off') NOT NULL DEFAULT 'off',
  `ban_chat_timeend` int(11) DEFAULT NULL,
  `time_add` int(11) NOT NULL,
  `time_last_connect` int(11) DEFAULT NULL COMMENT 'время последнего входа в игру',
  `exp` bigint(11) NOT NULL DEFAULT '0',
  `role` varchar(32) NOT NULL DEFAULT 'member',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `advice_time_update` int(11) DEFAULT NULL,
  `activity_time_update` int(11) NOT NULL DEFAULT '0',
  `activity_rate` int(11) NOT NULL DEFAULT '0',
  `social_type` varchar(2) NOT NULL,
  `social_name` varchar(256) DEFAULT NULL,
  `social_id` varchar(128) NOT NULL,
  `social_link` text,
  `referrer_id` int(11) DEFAULT NULL,
  `countnotify` int(3) DEFAULT '0',
  `lastnotify` int(11) DEFAULT NULL,
  `newsletter_time_send` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=38776 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `ban_status`, `ban_chat_status`, `ban_chat_timeend`, `time_add`, `time_last_connect`, `exp`, `role`, `level`, `advice_time_update`, `activity_time_update`, `activity_rate`, `social_type`, `social_name`, `social_id`, `social_link`, `referrer_id`, `countnotify`, `lastnotify`, `newsletter_time_send`) VALUES
(38775, '', 'off', 'off', NULL, 1460366876, 1460366876, 0, 'user', 0, NULL, 0, 0, 'fb', NULL, '1145158845514703', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_uid`
--

CREATE TABLE IF NOT EXISTS `user_uid` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `token` varchar(32) DEFAULT NULL,
  `time_connect` int(11) DEFAULT NULL,
  `signkey` varchar(32) DEFAULT NULL,
  `lang` varchar(2) DEFAULT 'en',
  `auth_type` varchar(32) NOT NULL DEFAULT 'default',
  `facebook_access_token` text,
  `mailru_access_token` text
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_uid`
--

INSERT INTO `user_uid` (`id`, `user_id`, `uid`, `token`, `time_connect`, `signkey`, `lang`, `auth_type`, `facebook_access_token`, `mailru_access_token`) VALUES
(2, 38775, '1145158845514703', 'nqi0vvm649j70sd3uqqq8p3q42', 1460391241, NULL, 'en', 'fb', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_object`
--
ALTER TABLE `shop_object`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`,`object_code`),
  ADD KEY `object_code` (`object_code`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `user_uid`
--
ALTER TABLE `user_uid`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user_id`,`uid`,`auth_type`),
  ADD UNIQUE KEY `signkey` (`signkey`,`auth_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shop_object`
--
ALTER TABLE `shop_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38776;
--
-- AUTO_INCREMENT for table `user_uid`
--
ALTER TABLE `user_uid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_uid`
--
ALTER TABLE `user_uid`
  ADD CONSTRAINT `user_uid_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
