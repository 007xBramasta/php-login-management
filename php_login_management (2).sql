-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2023 at 11:41 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_login_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`) VALUES
('650e7b8dabbba', ' bram'),
('650f091e33387', ' bram'),
('65130f8f0e29f', 'haryono');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`) VALUES
(' bram', 'Bramasta Albatio ', '$2y$10$1dAiT41ajejHONVtM1G8dO9fyrnPZ/DmYTVbcXRWiIH9Lazbr/N8q'),
('albatio', 'albatio ganteng', '$2y$10$lgO/5gTimJoVFeiz78vvLe2XPZgh67b9ybAVu7/Ht7fTMhodcGHlW'),
('domes', 'Domes Shelby', '$2y$10$mMBiuvaOn9A1WqlzbJztaO8ZagYg7VPu.IbjlgwAvxYMxA0Sov.sG'),
('ewe', 'ngentot', '$2y$10$.8HLF9ZkLUaTBqh0/2GIKeQlRhOyhE5JsvxTV/DkCFH7xCYkVgJCu'),
('haryono', 'haryono', '$2y$10$YtZAt4EB/1BHDtUrdivp8.QHXeNCv7uPzYh7MIQ8gA7U3mEX4fBLK'),
('nova', 'nova', '$2y$10$keX2Gt7iKpiYR6qL8HGSG.Cv4e/TZVYeyjlwYF8Bb..4plspa2Oay');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sessions_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
