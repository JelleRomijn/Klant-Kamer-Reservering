-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 11:17 AM
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
-- Database: `bureau_kamer`
--

-- --------------------------------------------------------

--
-- Table structure for table `reserveringen`
--

CREATE TABLE `reserveringen` (
  `reservering_id` int(11) NOT NULL,
  `lokaal` varchar(10) NOT NULL,
  `datum` date NOT NULL,
  `start_tijd` time NOT NULL,
  `eind_tijd` time NOT NULL,
  `klant` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `student_nummer` int(6) NOT NULL,
  `tijd_aangemaakt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reserveringen`
--

INSERT INTO `reserveringen` (`reservering_id`, `lokaal`, `datum`, `start_tijd`, `eind_tijd`, `klant`, `type`, `student_nummer`, `tijd_aangemaakt`) VALUES
(20, 'W003a', '2025-03-19', '13:02:00', '14:11:00', 'KAN DIT', 'Klant gesprek', 230838, '2025-03-18 12:03:06'),
(21, 'W003b', '2025-03-19', '13:03:00', '13:04:00', 'LENGTEMETER\na aa a a a a a a a a  a ', 'Klant gesprek', 230838, '2025-03-18 12:03:41'),
(22, 'W002a', '2025-04-05', '14:03:00', '15:05:00', 'LENGTEMETER', 'Klant gesprek', 230998, '2025-03-18 12:04:05'),
(23, 'W003b', '2025-03-18', '14:17:00', '14:45:00', 'test', 'Klant gesprek', 230838, '2025-03-18 12:45:21'),
(24, 'W003a', '2025-06-18', '13:46:00', '16:46:00', 'watermelon', 'Team vergadering', 230838, '2025-03-18 12:47:08'),
(25, 'W003a', '2025-03-20', '13:46:00', '16:46:00', 'watermelon', 'Klant gesprek', 230838, '2025-03-18 12:55:20'),
(26, 'W003a', '2025-03-24', '13:46:00', '16:46:00', 'watermelon', 'Klant gesprek', 230838, '2025-03-18 12:55:34'),
(27, 'W003a', '2025-03-25', '13:46:00', '16:46:00', 'watermelon', 'Klant gesprek', 230838, '2025-03-18 12:56:13'),
(28, 'W002a', '2025-03-19', '08:51:00', '09:24:00', 'Graffr', 'Team vergadering', 230838, '2025-03-19 15:48:16'),
(29, 'W002b', '2025-03-19', '09:18:00', '10:10:00', 'Graffr', 'Klant gesprek', 230838, '2025-03-19 16:20:38');

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `student_nummer` int(6) NOT NULL,
  `voornaam` varchar(255) NOT NULL,
  `achternaam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `gemaakt_op` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `nummer` int(6) NOT NULL,
  `voornaam` varchar(255) NOT NULL,
  `achternaam` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `nummer`, `voornaam`, `achternaam`) VALUES
(1, 210348, 'Jaiden', 'Bruijn'),
(2, 230043, 'Etien', 'den Ouden'),
(3, 230057, 'Jesse', 'Imming'),
(4, 230061, 'Joshua', 'van Eden'),
(5, 230070, 'Lucas', 'de Gooijer'),
(6, 230074, 'Marijn', 'Willems'),
(7, 230076, 'Mark', 'Petrenko'),
(8, 230080, 'Melvin', 'Lieuw'),
(9, 230083, 'Pepijn', 'Bullens'),
(10, 230101, 'Geno', 'Kooijman'),
(11, 230102, 'Jesse', 'Hardorff'),
(12, 230609, 'Thomas', 'Pol'),
(13, 230653, 'Tim', 'Pahlplatz'),
(14, 230654, 'Twan', 'Asselbergs'),
(15, 230752, 'Soera', 'Sarkaut'),
(16, 230783, 'Tjardo', 'Antonie'),
(17, 230838, 'Tristan', 'van Buuren'),
(18, 231195, 'Noah', 'Kamphuisen'),
(19, 530868, 'David', 'Schoots');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reserveringen`
--
ALTER TABLE `reserveringen`
  ADD PRIMARY KEY (`reservering_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_nummer` (`student_nummer`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reserveringen`
--
ALTER TABLE `reserveringen`
  MODIFY `reservering_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
