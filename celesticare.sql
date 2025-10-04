-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2025 at 04:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `celesticare`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `zodiac_sign` varchar(20) DEFAULT NULL,
  `zodiac` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `zodiac_sign`, `zodiac`, `created_at`) VALUES
(8, 'tsukunekun12', 'screwball362@gmail.com', '$2y$10$DnwXQ85LE24UpMKNfdChcOfiVFZD6uhH.TcDNylaRgwVieXICuWIu', NULL, NULL, '2025-10-03 15:38:10'),
(10, '233', 'jhenmendoza18@gmail.com', '$2y$10$u8O84a.y3sAL0/LzSuZh6e2ze9WbxRVDRihiIa/BpvG5YiXCfaj2q', '2323', NULL, '2025-10-03 15:47:49'),
(11, 'tsukunekun12666', 'deguzmanjhen748@gmail.com', '$2y$10$3HE.KEDlMbMIvYuVMEzeY.SfnvYAYUADaT0t9vW52Deol6nIP2SI.', 'cancaer', NULL, '2025-10-03 15:49:53'),
(13, 'e', 'elleskyebabao362@gmail.com', '$2y$10$xA.wQwshvcdn01J1IkJWmucm9EEmaplPu3p6TqvuqgeAcv6gGNsA6', NULL, NULL, '2025-10-04 01:58:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
