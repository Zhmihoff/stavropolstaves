-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql100.infinityfree.com
-- Generation Time: May 04, 2026 at 03:13 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41514135_db_staves`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`, `last_login`, `status`) VALUES
(1, 'admin', 'admin123', 'admin@staves.ru', '2026-05-03 12:51:35', '2026-05-03 17:08:38', 'active'),
(3, 'admin1', 'admin1', 'admin1@staves.ru', '2026-05-03 17:04:27', '2026-05-04 00:06:12', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `meter_readings`
--

CREATE TABLE `meter_readings` (
  `id` int(11) NOT NULL,
  `fio` varchar(255) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `meter_number` varchar(50) NOT NULL,
  `reason` enum('monthly','replacement','correction','initial','other') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('new','processed','rejected') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meter_readings`
--

INSERT INTO `meter_readings` (`id`, `fio`, `account_number`, `meter_number`, `reason`, `phone`, `description`, `created_at`, `status`) VALUES
(1, 'ыва ыфваыва', '123412', 'рол123412321', 'correction', '1241231231232', 'ываываыва', '2026-05-03 17:16:22', 'new'),
(2, 'ыва ыфваыва', '213121', 'фыв123123123', 'correction', '89624946692', 'фывфы', '2026-05-03 20:12:59', 'new'),
(3, 'ыва ыфваыва', '213121', 'фыв123123123', 'correction', '89624946692', 'фывфы', '2026-05-03 20:12:59', 'new'),
(4, 'ыва ыфваыва', '213121', 'фыв123123123', 'correction', '89624946692', 'фывфы', '2026-05-03 20:14:45', 'new'),
(5, 'ыва ыфваыва', '213121', 'фыв123123123', 'correction', '89624946692', 'фывфы', '2026-05-03 20:14:48', 'processed'),
(6, 'ыва ыфваыва', '213121', 'фыв123123123', 'correction', '89624946692', 'фывфы', '2026-05-03 20:16:02', 'new'),
(7, 'ыва ыфваыва', '213121', 'фыв123123123', 'correction', '89624946692', 'фывфы', '2026-05-03 20:16:06', 'new'),
(8, 'ыва ыфваыва', '213121', 'фыв123123123', 'other', '89624946692', 'фывфы', '2026-05-03 20:16:11', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `news_name` varchar(255) NOT NULL,
  `news_date` date NOT NULL,
  `news_description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `news_name`, `news_date`, `news_description`, `created_at`) VALUES
(2, 'ВРЕМЕННОЕ ОГРАНИЧЕНИЕ ЭЛЕКТРОЭНЕРГИИ 26 МАРТА', '2026-03-26', 'В связи с проведением электросетевыми компаниями края плановых ремонтных работ <strong>26 МАРТА 2026 г.</strong> будет временно ограничена подача электроэнергии по следующим адресам:<br><br><h3>СТАВРОПОЛЬ</h3><p><strong>9.00-17.00</strong> - ул. Ленина, 1-25; ул. Мира, 10-50</p><h3>ПЯТИГОРСК</h3><p><strong>10.00-16.00</strong> - пр. Калинина, 15-45</p>', '2026-03-30 16:22:22'),
(3, 'ВРЕМЕННОЕ ОГРАНИЧЕНИЕ ЭЛЕКТРОЭНЕРГИИ 25 МАРТА', '2026-03-25', 'В связи с проведением электросетевыми компаниями края плановых ремонтных работ <strong>25 МАРТА 2026 г.</strong> будет временно ограничена подача электроэнергии по следующим адресам:<br><br><h3>СВЕТЛОГРАД</h3><p><strong>8.00-17.00</strong> - ул. Пушкина, 1-100</p>', '2026-03-30 16:22:22'),
(4, 'ВРЕМЕННОЕ ОГРАНИЧЕНИЕ ЭЛЕКТРОЭНЕРГИИ 24 МАРТА', '2026-03-24', 'В связи с проведением электросетевыми компаниями края плановых ремонтных работ <strong>24 МАРТА 2026 г.</strong> будет временно ограничена подача электроэнергии по следующим адресам:<br><br><h3>НЕВИННОМЫССК</h3><p><strong>9.00-17.00</strong> - ул. Гагарина, 1-50</p>', '2026-03-30 16:22:22'),
(5, 'ВРЕМЕННОЕ ОГРАНИЧЕНИЕ ЭЛЕКТРОЭНЕРГИИ 23 МАРТА', '2026-03-23', 'В связи с проведением электросетевыми компаниями края плановых ремонтных работ <strong>23 МАРТА 2026 г.</strong> будет временно ограничена подача электроэнергии по следующим адресам:<br><br><h3>БУДЕННОВСК</h3><p><strong>8.00-17.00</strong> - ул. Ленина, 1-75</p>', '2026-03-30 16:22:22'),
(14, 'Нету света 30-31 марта ночью', '2026-03-30', 'Сиди без света, чилль', '2026-03-31 08:06:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `meter_readings`
--
ALTER TABLE `meter_readings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meter_readings`
--
ALTER TABLE `meter_readings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
