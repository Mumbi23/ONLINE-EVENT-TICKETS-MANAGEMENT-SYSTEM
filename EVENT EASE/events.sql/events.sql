-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 05:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oetms`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled','Completed') DEFAULT 'Pending',
  `location` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `organizer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `image`, `status`, `location`, `price`, `organizer_id`, `created_at`, `latitude`, `longitude`) VALUES
(1, 'Nairobi Street Festival', 'It\'s Nairobi\'s premier cultural event, featuring a vibrant mix of diverse street food.', '2025-11-15', 'uploads/1759599863_foodie.jpeg', 'Pending', 'Uhuru Park, Nairobi', 1500.00, 5, '2025-09-13 17:46:03', NULL, NULL),
(2, 'Ngemi Homecoming  Festival', 'A vibrant cultural event celebrating Kikuyu heritage through a blend of traditional and modern elements, including music, food, fashion, and storytelling', '2025-12-06', 'uploads/events/1759597612_ngemi.png', 'Pending', 'Naivasha,Kenya', 2000.00, 5, '2025-09-13 18:29:35', NULL, NULL),
(3, 'Watoi Fun Fest', 'Play, learn and create memories', '2025-11-08', 'uploads/1759598793_watoi.png', 'Pending', 'Two Rivers Mall, Kenya, Nairobi', 700.00, 5, '2025-09-27 11:26:41', NULL, NULL),
(4, 'Faith\'s birthday', 'Come all we celebrate her....', '2025-10-29', 'uploads/1759598810_birthday.png', 'Pending', 'Village Market', 1000.00, 5, '2025-09-29 05:46:04', NULL, NULL),
(6, 'King\'s event', 'Hurray.... It\'s the King\'s events..', '2025-10-29', 'uploads/1760377383_king.png', 'Pending', 'kasarani', 500.00, 2, '2025-10-13 06:43:13', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
