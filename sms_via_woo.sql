-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 22, 2019 at 09:18 AM
-- Server version: 5.7.24-0ubuntu0.18.04.1
-- PHP Version: 7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sms_via_woo`
--

-- --------------------------------------------------------

--
-- Table structure for table `_ec_cron_sms_archive`
--

CREATE TABLE `_ec_cron_sms_archive` (
  `_id` int(11) NOT NULL,
  `_sms_inbox_id` int(11) NOT NULL,
  `_sms_type` varchar(20) DEFAULT NULL COMMENT 'Module Short Name / Others',
  `_number` varchar(20) DEFAULT NULL,
  `_sms_body` longtext,
  `_priority` varchar(3) DEFAULT NULL COMMENT 'High=H, Medium=M, Low=L',
  `_status` varchar(2) DEFAULT '1' COMMENT 'Success=1, Pending=0',
  `_entry_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `_sms_user_name` varchar(100) DEFAULT NULL,
  `_sms_user_pass` varchar(50) DEFAULT NULL,
  `_sms_user_url` varchar(255) DEFAULT NULL,
  `_brand` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `_ec_cron_sms_settings`
--

CREATE TABLE `_ec_cron_sms_settings` (
  `_id` int(11) NOT NULL,
  `_type` varchar(100) NOT NULL,
  `_body` varchar(200) NOT NULL,
  `_status` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `_ec_cron_sms_archive`
--
ALTER TABLE `_ec_cron_sms_archive`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `_ec_cron_sms_settings`
--
ALTER TABLE `_ec_cron_sms_settings`
  ADD PRIMARY KEY (`_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `_ec_cron_sms_archive`
--
ALTER TABLE `_ec_cron_sms_archive`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `_ec_cron_sms_settings`
--
ALTER TABLE `_ec_cron_sms_settings`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
