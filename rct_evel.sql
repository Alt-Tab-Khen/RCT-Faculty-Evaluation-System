-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 06:42 AM
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
-- Database: `rct_evel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`) VALUES
(3, 'admin123@gmail.com', '$2y$10$SREG0k0kz8SJZ/5U9Vh...vMhN5FW/xnfAlTMskFmr7PvVNFTlQ1C');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_reads`
--

CREATE TABLE `announcement_reads` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `prof_id` varchar(10) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_instructor_list`
--

CREATE TABLE `course_instructor_list` (
  `id` int(11) NOT NULL,
  `course` enum('bscs','bsa','bsba','bsed','beed','act','bscrim') NOT NULL,
  `prof_id` varchar(10) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_instructor_list`
--

INSERT INTO `course_instructor_list` (`id`, `course`, `prof_id`, `added_at`) VALUES
(52, 'bscs', 'prof_75', '2024-11-14 09:06:34'),
(53, 'bscs', 'prof_76', '2024-11-14 09:06:34'),
(54, 'bscs', 'prof_77', '2024-11-14 09:06:34'),
(55, 'bscs', 'prof_78', '2024-11-14 09:06:34'),
(56, 'bscs', 'prof_79', '2024-11-14 09:06:34'),
(57, 'bscs', 'prof_80', '2024-11-14 09:06:34'),
(58, 'bsa', 'prof_67', '2024-11-14 09:09:52'),
(59, 'bsa', 'prof_68', '2024-11-14 09:09:52'),
(60, 'bsa', 'prof_69', '2024-11-14 09:09:52'),
(61, 'bsa', 'prof_70', '2024-11-14 09:09:52'),
(62, 'bsa', 'prof_71', '2024-11-14 09:09:52'),
(63, 'bsa', 'prof_72', '2024-11-14 09:09:52'),
(64, 'bsa', 'prof_73', '2024-11-14 09:09:52'),
(65, 'bsa', 'prof_74', '2024-11-14 09:09:52'),
(66, 'bsba', 'prof_67', '2024-11-14 09:10:26'),
(67, 'bsba', 'prof_68', '2024-11-14 09:10:26'),
(68, 'bsba', 'prof_69', '2024-11-14 09:10:26'),
(69, 'bsba', 'prof_70', '2024-11-14 09:10:26'),
(70, 'bsba', 'prof_71', '2024-11-14 09:10:26'),
(71, 'bsba', 'prof_72', '2024-11-14 09:10:26'),
(72, 'bsba', 'prof_73', '2024-11-14 09:10:26'),
(73, 'bsba', 'prof_74', '2024-11-14 09:10:26'),
(74, 'bsed', 'prof_55', '2024-11-14 09:10:42'),
(75, 'bsed', 'prof_56', '2024-11-14 09:10:42'),
(76, 'bsed', 'prof_57', '2024-11-14 09:10:42'),
(77, 'bsed', 'prof_58', '2024-11-14 09:10:42'),
(78, 'bsed', 'prof_59', '2024-11-14 09:10:42'),
(79, 'bsed', 'prof_60', '2024-11-14 09:10:42'),
(80, 'bsed', 'prof_61', '2024-11-14 09:10:42'),
(81, 'bsed', 'prof_62', '2024-11-14 09:10:42'),
(82, 'bsed', 'prof_63', '2024-11-14 09:10:42'),
(83, 'bsed', 'prof_64', '2024-11-14 09:10:42'),
(84, 'bsed', 'prof_65', '2024-11-14 09:10:42'),
(85, 'bsed', 'prof_66', '2024-11-14 09:10:42'),
(86, 'beed', 'prof_55', '2024-11-14 09:11:02'),
(87, 'beed', 'prof_56', '2024-11-14 09:11:02'),
(88, 'beed', 'prof_57', '2024-11-14 09:11:02'),
(89, 'beed', 'prof_58', '2024-11-14 09:11:02'),
(90, 'beed', 'prof_59', '2024-11-14 09:11:02'),
(91, 'beed', 'prof_60', '2024-11-14 09:11:02'),
(92, 'beed', 'prof_61', '2024-11-14 09:11:02'),
(93, 'beed', 'prof_62', '2024-11-14 09:11:02'),
(94, 'beed', 'prof_63', '2024-11-14 09:11:02'),
(95, 'beed', 'prof_64', '2024-11-14 09:11:02'),
(96, 'beed', 'prof_65', '2024-11-14 09:11:02'),
(97, 'beed', 'prof_66', '2024-11-14 09:11:02'),
(98, 'act', 'prof_75', '2024-11-14 09:11:11'),
(99, 'act', 'prof_76', '2024-11-14 09:11:11'),
(100, 'act', 'prof_77', '2024-11-14 09:11:11'),
(101, 'act', 'prof_78', '2024-11-14 09:11:11'),
(102, 'act', 'prof_79', '2024-11-14 09:11:11'),
(103, 'act', 'prof_80', '2024-11-14 09:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `prof_id` varchar(10) NOT NULL,
  `question_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_archive_linked`
--

CREATE TABLE `evaluation_archive_linked` (
  `archive_id` int(11) NOT NULL,
  `prof_id` varchar(10) NOT NULL,
  `evaluation_period` varchar(255) NOT NULL,
  `avg_rating` float DEFAULT NULL,
  `num_responses` int(11) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course` varchar(255) NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_archive_linked`
--

INSERT INTO `evaluation_archive_linked` (`archive_id`, `prof_id`, `evaluation_period`, `avg_rating`, `num_responses`, `archived_at`, `course`, `comments`) VALUES
(91, 'prof_78', '2024-11-15 to 2024-11-16', 5, 1, '2024-11-14 23:37:28', 'act', 'Xcdacascsd; bfbvsfvsdfbvfdsb; dasdfvcrverfca'),
(92, 'prof_78', '2024-11-15 to 2024-11-16', 4, 2, '2024-11-14 23:37:28', 'bscs', 'Xcdacascsd; bfbvsfvsdfbvfdsb; dasdfvcrverfca'),
(93, 'prof_77', '2024-11-15 to 2024-11-16', 2, 1, '2024-11-14 23:37:28', 'act', 'gfasvsdvsdv'),
(95, 'prof_78', '2024-11-17 to 2024-11-18', 5, 1, '2024-11-17 12:34:31', 'act', 'dsafwsdcsdxcsadc'),
(96, 'prof_55', '2024-11-27 to 2024-11-28', 3.98, 1, '2024-12-05 11:29:12', 'beed', 'asfsaddscds ass asfdacxscewdf; sdsacsdcdwcwedsc'),
(97, 'prof_55', '2024-11-27 to 2024-11-28', 3.34, 1, '2024-12-05 11:29:12', 'bsed', 'asfsaddscds ass asfdacxscewdf; sdsacsdcdwcwedsc'),
(98, 'prof_56', '2024-11-27 to 2024-11-28', 3.68, 1, '2024-12-05 11:29:12', 'beed', 'sdgsdgfdfcasdfcadfdf; dfawedgfgfgfgfd'),
(99, 'prof_56', '2024-11-27 to 2024-11-28', 5, 1, '2024-12-05 11:29:12', 'bsed', 'sdgsdgfdfcasdfcadfdf; dfawedgfgfgfgfd'),
(100, 'prof_57', '2024-11-27 to 2024-11-28', 3.9, 1, '2024-12-05 11:29:12', 'beed', 'safdsacdsafcsadsaxsa'),
(101, 'prof_58', '2024-11-27 to 2024-11-28', 2.2, 1, '2024-12-05 11:29:12', 'beed', 'safdsdafsdcadscewcawefcd'),
(102, 'prof_59', '2024-11-27 to 2024-11-28', 3.18, 1, '2024-12-05 11:29:12', 'beed', 'adadsdsacewdcwsdsxw'),
(103, 'prof_60', '2024-11-27 to 2024-11-28', 3.34, 1, '2024-12-05 11:29:12', 'beed', 'dsfdscdsafsdfadsfcsd'),
(104, 'prof_61', '2024-11-27 to 2024-11-28', 3.76, 1, '2024-12-05 11:29:12', 'beed', 'dasdscxadwscwedfcwsdc'),
(105, 'prof_62', '2024-11-27 to 2024-11-28', 3.6, 1, '2024-12-05 11:29:12', 'beed', 'safdsacasdcasdcsc'),
(106, 'prof_67', '2024-11-27 to 2024-11-28', 3.89, 2, '2024-12-05 11:29:12', 'bsba', 'fggggggggeddddgsgs; xsafdwsafasdfsdf'),
(107, 'prof_68', '2024-11-27 to 2024-11-28', 3.98, 2, '2024-12-05 11:29:12', 'bsba', 'dfsgvfdvsadfvefverfdv; fgedededededededededededededed'),
(108, 'prof_74', '2024-11-27 to 2024-11-28', 3.78, 1, '2024-12-05 11:29:12', 'bsba', 'fdgvvvvvvvvvvvvvsgggggggg'),
(109, 'prof_55', '2024-11-27 to 2024-11-28', 3.98, 1, '2024-12-05 11:29:36', 'beed', 'asfsaddscds ass asfdacxscewdf; sdsacsdcdwcwedsc'),
(110, 'prof_55', '2024-11-27 to 2024-11-28', 3.34, 1, '2024-12-05 11:29:36', 'bsed', 'asfsaddscds ass asfdacxscewdf; sdsacsdcdwcwedsc'),
(111, 'prof_56', '2024-11-27 to 2024-11-28', 3.68, 1, '2024-12-05 11:29:36', 'beed', 'sdgsdgfdfcasdfcadfdf; dfawedgfgfgfgfd'),
(112, 'prof_56', '2024-11-27 to 2024-11-28', 5, 1, '2024-12-05 11:29:36', 'bsed', 'sdgsdgfdfcasdfcadfdf; dfawedgfgfgfgfd'),
(113, 'prof_57', '2024-11-27 to 2024-11-28', 3.9, 1, '2024-12-05 11:29:36', 'beed', 'safdsacdsafcsadsaxsa'),
(114, 'prof_58', '2024-11-27 to 2024-11-28', 2.2, 1, '2024-12-05 11:29:36', 'beed', 'safdsdafsdcadscewcawefcd'),
(115, 'prof_59', '2024-11-27 to 2024-11-28', 3.18, 1, '2024-12-05 11:29:36', 'beed', 'adadsdsacewdcwsdsxw'),
(116, 'prof_60', '2024-11-27 to 2024-11-28', 3.34, 1, '2024-12-05 11:29:36', 'beed', 'dsfdscdsafsdfadsfcsd'),
(117, 'prof_61', '2024-11-27 to 2024-11-28', 3.76, 1, '2024-12-05 11:29:36', 'beed', 'dasdscxadwscwedfcwsdc'),
(118, 'prof_62', '2024-11-27 to 2024-11-28', 3.6, 1, '2024-12-05 11:29:36', 'beed', 'safdsacasdcasdcsc'),
(119, 'prof_67', '2024-11-27 to 2024-11-28', 3.89, 2, '2024-12-05 11:29:36', 'bsba', 'fggggggggeddddgsgs; xsafdwsafasdfsdf'),
(120, 'prof_68', '2024-11-27 to 2024-11-28', 3.98, 2, '2024-12-05 11:29:36', 'bsba', 'dfsgvfdvsadfvefverfdv; fgedededededededededededededed'),
(121, 'prof_74', '2024-11-27 to 2024-11-28', 3.78, 1, '2024-12-05 11:29:36', 'bsba', 'fdgvvvvvvvvvvvvvsgggggggg');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_periods`
--

CREATE TABLE `evaluation_periods` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_date` date NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_respondents` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_periods`
--

INSERT INTO `evaluation_periods` (`id`, `start_date`, `start_time`, `end_date`, `end_time`, `status`, `created_at`, `total_respondents`) VALUES
(74, '2024-11-15', '2024-11-15 00:30:54', '2024-11-16', '2024-11-16 00:30:54', 'completed', '2024-11-14 23:30:54', 3),
(76, '2024-11-17', '2024-11-17 13:33:18', '2024-11-18', '2024-11-18 13:33:18', 'completed', '2024-11-17 12:33:18', 1),
(79, '2024-11-27', '2024-11-27 12:34:40', '2024-11-28', '2024-11-28 12:34:40', 'completed', '2024-11-27 11:34:40', 4);

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE `instructor` (
  `id` int(11) NOT NULL,
  `prof_id` varchar(10) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `faculty` enum('cpus','cba','educ','crim') NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `rejected_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`id`, `prof_id`, `username`, `first_name`, `last_name`, `email`, `password`, `profile_picture`, `created_at`, `faculty`, `status`, `rejected_at`) VALUES
(55, 'prof_55', 'Neli', 'Nelia', 'Quijano', 'Quijano@gmail.com', '$2y$10$fFZA/elFPEAzbt2E/dZb5epDy8U194D4pvHz5wxcHFNQRaZyvl5d6', 'default_profile.png', '2024-11-14 08:43:36', 'educ', 'validated', NULL),
(56, 'prof_56', 'KRISSY', 'Kristel', 'Alcayde', 'alcayde@gmail.com', '$2y$10$vAbj1JHJCONcZMt0KsG/Ieun3UQQpu5eu0OlMwVnCLhIdrqIdq7cO', 'default_profile.png', '2024-11-14 08:44:32', 'educ', 'validated', NULL),
(57, 'prof_57', 'Eca', 'Jessica Claire', 'Malabanan', 'malabanan@gmail.com', '$2y$10$YtGzyNfg4ls7xixlS6BmhOFBpO.Op3OiK3lXLf/UPtqUSHWZS9Plu', 'default_profile.png', '2024-11-14 08:45:13', 'educ', 'validated', NULL),
(58, 'prof_58', 'Kheyzel', 'Kheyzel Cara', 'Bathan', 'bathan@gmail.com', '$2y$10$QS3VmYoHg4u4wfJBdJQjOeRaikrGKHy0aiHA9uMfoDkJ9zjIRdEZ2', 'default_profile.png', '2024-11-14 08:46:01', 'educ', 'validated', NULL),
(59, 'prof_59', 'Ryan', 'Ryan Christopher', 'Bucad', 'Bucad@gmail.com', '$2y$10$Xxl9mncW1vAzdYJE/f9dn.MTk3a/rzuoz7b6qOZ3h2WW3zHnZIXJW', 'default_profile.png', '2024-11-14 08:46:53', 'educ', 'validated', NULL),
(60, 'prof_60', 'Bryan', 'Nel Bryan', 'Ocampo', 'Ocampo@gmail.com', '$2y$10$Ri6U7YU10Dk4sgkwBW2MX.RRZ60vEwM8xlCmbbxJ3bWtaMOFmI.zS', 'default_profile.png', '2024-11-14 08:47:31', 'educ', 'validated', NULL),
(61, 'prof_61', 'Belle', 'Clabelle', 'Como', 'como@gmail.com', '$2y$10$Osu9eST6WFM4sjCRXiecWe3bIqXbLb5NQPwRbxime2EAAT.IHCe0m', 'default_profile.png', '2024-11-14 08:48:47', 'educ', 'validated', NULL),
(62, 'prof_62', 'Beth', 'Maribeth', 'Coro', 'coro@gmail.com', '$2y$10$lbs4Q9qZY3JGCvGXIpcJYes2yGRppinAWgcLBS76DrCxyl5L1NFyC', 'default_profile.png', '2024-11-14 08:49:29', 'educ', 'validated', NULL),
(63, 'prof_63', 'Diane', 'Lady Diane', 'Villavicencio', 'villavicencio@gmail.com', '$2y$10$KYseshMKannh5sR9jMYp9OhXX27GEqb87k81luyI4VNX40egB5w4S', 'default_profile.png', '2024-11-14 08:50:27', 'educ', 'validated', NULL),
(64, 'prof_64', 'Ardie', 'Ardie Clarence', 'Benamer', 'benamer@gmail.com', '$2y$10$dCOL7CKSIGeZEPSx9r8xjOWec4H10Ek/YAI1zzLL47xfPqowU1owi', 'default_profile.png', '2024-11-14 08:51:16', 'educ', 'validated', NULL),
(65, 'prof_65', 'Kaela', 'Kaela', 'Landicho', 'landicho@gmail.com', '$2y$10$x4ggMhlgWOeaSQRIJQ9dd.gQmUnocVtqZ2uoFnT5reYTT0Mme6Z1.', 'default_profile.png', '2024-11-14 08:51:46', 'educ', 'validated', NULL),
(66, 'prof_66', 'Jeniel', 'Jeniel', 'Villanueva', 'villanueva@gmail.com', '$2y$10$fZ38Bmo5fG/E.NBAKHw7KeO9SJ0EUeQ6L1ujnlEFjupJNQ5IB76qi', 'default_profile.png', '2024-11-14 08:52:23', 'educ', 'validated', NULL),
(67, 'prof_67', 'Marivin', 'Marivin Denice', 'Marasigan', 'marasigan@gmail.com', '$2y$10$m51XiRxvHli705/9Vb2meuMKEkYWISB25htwsGpN0s5O1FC2rBtQK', 'default_profile.png', '2024-11-14 08:56:54', 'cba', 'validated', NULL),
(68, 'prof_68', 'August', 'Augusto', 'Capul', 'capul@gmail.com', '$2y$10$vA/EUTONxuB5QvN1gYrGzOto/EUTI7Q.mP0ThAF5n0NdZSfgTsfBC', 'default_profile.png', '2024-11-14 08:57:32', 'cba', 'validated', NULL),
(69, 'prof_69', 'Joe', 'Michael Joe', 'Buceta', 'buseta@gmail.com', '$2y$10$nWHLkBOWG6oc0rDbJSs8j.17EZmLS5UFMIjqjJvg2HP.mgLkcSkJy', 'default_profile.png', '2024-11-14 08:58:23', 'cba', 'validated', NULL),
(70, 'prof_70', 'James', 'James Edmund', 'Divino', 'divino@gmail.com', '$2y$10$0uFA7G4ZnbP38cJ05W9Lu.ibC0YJhPK0Bh7lE.RNf6bdyFoURFS56', 'default_profile.png', '2024-11-14 08:59:01', 'cba', 'validated', NULL),
(71, 'prof_71', 'Susan', 'Aurora Susan', 'Reyes', 'reyes@gmail.com', '$2y$10$MuuzwNtswaDHWUon1LvM8.jzVGia8tUSkALNy40unBKSIyl58XnqC', 'default_profile.png', '2024-11-14 08:59:41', 'cba', 'validated', NULL),
(72, 'prof_72', 'Teres', 'Teresita', 'Ilagan', 'ilagan@gmail.com', '$2y$10$nWto5c3SokMY.svDaY.eNuMMOYyell.eGIhhzP5YEW0ZT2pTeqHDK', 'default_profile.png', '2024-11-14 09:00:22', 'cba', 'validated', NULL),
(73, 'prof_73', 'Carl', 'Carl', 'Malaluan', 'malaluan@gmail.com', '$2y$10$XfpH4G6ISgXgMYz1oj84juH3koNbBj9TqSfy27H3DJbFpuncbR3MK', 'default_profile.png', '2024-11-14 09:00:54', 'cba', 'validated', NULL),
(74, 'prof_74', 'Kath', 'Rich Katheleen', 'Gubi', 'gubi@gmail.com', '$2y$10$y2ZI2yEEiUPSb78qceWwuuYt/v22tyhiRtf5fjfEKvGT3Gb.IFJXS', 'default_profile.png', '2024-11-14 09:01:32', 'cba', 'validated', NULL),
(75, 'prof_75', 'Gerald', 'Gerald', 'Manalo', 'manalo@gmail.com', '$2y$10$84xekd3Wka1/sqeBp1qvh.6x4xSETiygNBuapVpGMDB2MffqzpLFy', 'default_profile.png', '2024-11-14 09:02:30', 'cpus', 'validated', NULL),
(76, 'prof_76', 'Anot', 'Arnold', 'Casabuena', 'casabuena@gmail.com', '$2y$10$KUvY63PqoRMQ5vPNlmW6SuVTcYfX5IAassRAk5IJbb.KI/hFv/8D2', 'default_profile.png', '2024-11-14 09:03:01', 'cpus', 'validated', NULL),
(77, 'prof_77', 'Chris', 'Christian', 'Mendoza', 'mendoza@gmail.com', '$2y$10$2bvxI9muVjLN1QyqcnfV7O6qikp6ONirOlSM4n2i5R2meggM5NW9S', 'default_profile.png', '2024-11-14 09:04:10', 'cpus', 'validated', NULL),
(78, 'prof_78', 'Zai', 'Zairen Ann', 'Atienza', 'atienza@gmail.com', '$2y$10$j.nqXX2.zD9qMcMQ16eAN.P2HSWF83B8dCbF0JSam9dziaOuXW6Ru', 'default.jpg', '2024-11-14 09:04:49', 'cpus', 'validated', NULL),
(79, 'prof_79', 'Noriel', 'Noriel', 'Panaligan', 'panaligan@gmail.com', '$2y$10$yETki8m71zI4l2nUVae/e.RZHr4fKUMcx3KAQzoNdGqS/jejxK2ba', 'default_profile.png', '2024-11-14 09:05:21', 'cpus', 'validated', NULL),
(80, 'prof_80', 'Robi', 'Robi', 'Suarez', 'suarez@gmail.com', '$2y$10$ouPJ.YeL.UCzavTJ55Zq..5C0.pv0.6b2Ckt9w7ViId1KcCbX6Qiu', 'default_profile.png', '2024-11-14 09:06:03', 'cpus', 'validated', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `questionnaires`
--

CREATE TABLE `questionnaires` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questionnaires`
--

INSERT INTO `questionnaires` (`id`, `question`, `category`, `order`, `status`, `created_at`, `updated_at`) VALUES
(165, 'The instructor explains concepts clearly and effectively.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:41:47', '2024-11-26 12:41:47'),
(166, 'The instructor uses examples that are easy to relate to.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:41:56', '2024-11-26 12:41:56'),
(167, 'The instructor ensures students understand topics before moving forward.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:04', '2024-11-26 12:42:04'),
(168, 'The instructor repeats or clarifies when students express confusion.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:16', '2024-11-26 12:42:16'),
(169, 'The instructor uses simple and direct language during lectures.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:24', '2024-11-26 12:42:24'),
(170, 'The instructor provides clear instructions for activities and tasks.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:31', '2024-11-26 12:42:31'),
(171, 'The instructor organizes lessons logically and understandably.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:37', '2024-11-26 12:42:37'),
(172, 'The instructor summarizes key points at the end of each lesson.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:47', '2024-11-26 12:42:47'),
(173, 'The instructor explains difficult topics patiently.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:42:54', '2024-11-26 12:42:54'),
(174, 'The instructor ensures all students are following the discussion.', 'Teaching Clarity', 0, 'active', '2024-11-26 12:43:04', '2024-11-26 12:43:04'),
(175, 'The instructor encourages active participation during class.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:43:21', '2024-11-26 12:43:21'),
(176, 'The instructor creates an engaging and interactive classroom environment.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:43:28', '2024-11-26 12:43:28'),
(177, 'The instructor involves students in discussions and group activities.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:43:39', '2024-11-26 12:43:39'),
(178, 'The instructor listens attentively to students\' questions and concerns.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:43:46', '2024-11-26 12:43:46'),
(179, 'The instructor makes an effort to know students by name.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:43:52', '2024-11-26 12:43:52'),
(180, 'The instructor maintains enthusiasm and energy while teaching.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:43:57', '2024-11-26 12:43:57'),
(181, 'The instructor makes students feel comfortable sharing their thoughts.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:44:28', '2024-11-26 12:44:28'),
(182, 'The instructor encourages critical thinking and problem-solving.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:44:35', '2024-11-26 12:44:35'),
(183, 'The instructor respects and values student input.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:44:41', '2024-11-26 12:44:41'),
(184, 'The instructor motivates students to stay involved and interested.', 'Engagement and Interaction', 0, 'active', '2024-11-26 12:44:47', '2024-11-26 12:44:47'),
(185, 'The instructor arrives on time and is well-prepared for class.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:02', '2024-11-26 12:45:02'),
(186, 'The instructor ensures that the class starts and ends on schedule.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:11', '2024-11-26 12:45:11'),
(187, 'The instructor maintains order and discipline in the classroom.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:17', '2024-11-26 12:45:17'),
(188, 'The instructor creates a respectful and inclusive learning environment.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:23', '2024-11-26 12:45:23'),
(189, 'The instructor ensures all students have an equal chance to participate.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:28', '2024-11-26 12:45:28'),
(190, 'The instructor handles disruptions in the classroom effectively.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:34', '2024-11-26 12:45:34'),
(191, 'The instructor promotes mutual respect among students.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:39', '2024-11-26 12:45:39'),
(192, 'The instructor adapts lessons when needed to match the class\'s pace.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:45', '2024-11-26 12:45:45'),
(193, 'The instructor effectively balances lecture time and class activities.', 'Classroom Management', 0, 'active', '2024-11-26 12:45:53', '2024-11-26 12:45:53'),
(194, 'The instructor uses classroom time productively and efficiently.', 'Classroom Management', 0, 'active', '2024-11-26 12:46:00', '2024-11-26 12:46:00'),
(195, 'The instructor provides constructive feedback on student performance.', 'Feedback and Support', 0, 'active', '2024-11-26 12:46:17', '2024-11-26 12:46:17'),
(196, 'The instructor helps students improve by offering guidance and tips.', 'Feedback and Support', 0, 'active', '2024-11-26 12:46:23', '2024-11-26 12:46:23'),
(197, 'The instructor is approachable and willing to assist students when needed.', 'Feedback and Support', 0, 'active', '2024-11-26 12:46:37', '2024-11-26 12:46:37'),
(198, 'The instructor makes an effort to understand students\' difficulties.', 'Feedback and Support', 0, 'active', '2024-11-26 12:46:42', '2024-11-26 12:46:42'),
(199, 'The instructor provides extra help or resources when students struggle.', 'Feedback and Support', 0, 'active', '2024-11-26 12:46:49', '2024-11-26 12:46:49'),
(200, 'The instructor gives encouragement and motivation to students.', 'Feedback and Support', 0, 'active', '2024-11-26 12:46:56', '2024-11-26 12:46:56'),
(201, 'The instructor explains how students can improve their work.\r\n', 'Feedback and Support', 0, 'active', '2024-11-26 12:47:04', '2024-11-26 12:47:04'),
(202, 'The instructor is available for questions and consultations outside of class.', 'Feedback and Support', 0, 'active', '2024-11-26 12:47:14', '2024-11-26 12:47:14'),
(203, 'The instructor follows up on student concerns or issues.', 'Feedback and Support', 0, 'active', '2024-11-26 12:47:20', '2024-11-26 12:47:20'),
(204, 'The instructor creates a supportive environment for learning.', 'Feedback and Support', 0, 'active', '2024-11-26 12:47:26', '2024-11-26 12:47:26'),
(205, 'The instructor treats all students with respect and fairness.', 'Professionalism', 0, 'active', '2024-11-26 12:47:41', '2024-11-26 12:47:41'),
(206, 'The instructor demonstrates mastery of the subject matter.', 'Professionalism', 0, 'active', '2024-11-26 12:47:50', '2024-11-26 12:47:50'),
(207, 'The instructor maintains professionalism in their behavior and communication.', 'Professionalism', 0, 'active', '2024-11-26 12:47:55', '2024-11-26 12:47:55'),
(208, 'The instructor is well-organized and plans lessons effectively.', 'Professionalism', 0, 'active', '2024-11-26 12:48:19', '2024-11-26 12:48:19'),
(209, 'The instructor is confident and articulate when delivering lessons.', 'Professionalism', 0, 'active', '2024-11-26 12:48:30', '2024-11-26 12:48:30'),
(210, 'The instructor maintains a positive and encouraging attitude.', 'Professionalism', 0, 'active', '2024-11-26 12:48:36', '2024-11-26 12:48:36'),
(211, 'The instructor respects students\' diverse backgrounds and perspectives.', 'Professionalism', 0, 'active', '2024-11-26 12:48:41', '2024-11-26 12:48:41'),
(212, 'The instructor is receptive to feedback and willing to improve.', 'Professionalism', 0, 'active', '2024-11-26 12:48:47', '2024-11-26 12:48:47'),
(213, 'The instructor adheres to institutional rules and policies.', 'Professionalism', 0, 'active', '2024-11-26 12:48:53', '2024-11-26 12:48:53'),
(214, 'The instructor inspires students through their teaching and professionalism.', 'Professionalism', 0, 'active', '2024-11-26 12:49:00', '2024-11-26 12:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire_edits`
--

CREATE TABLE `questionnaire_edits` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `prof_id` varchar(10) DEFAULT NULL,
  `updated_question` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `section` varchar(10) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `course` enum('bscs','bsba','bscrim','beed','bsed','bsa','act') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `rejected_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `first_name`, `last_name`, `username`, `email`, `password`, `section`, `year`, `course`, `image`, `created_at`, `status`, `rejected_at`) VALUES
(1222, 'Camile', 'Tres', 'cammy', '3@gmail.com', '$2y$10$3xHB4MKhL5ikELZBwVzAa.1G08hAAO72nwh3p20Cqbenk681I5xDW', '1', 2, 'bsba', 'default.jpg', '2024-11-27 11:05:56', 'validated', NULL),
(1344, 'John', 'Dela Luna', 'JD', 'luna@gmail.com', '$2y$10$0chCc995G384N/FGaofVVOIROg178uo8Ne3VZInBge32jV16N.Gdy', 'chrome', 2, 'bscs', 'default.jpg', '2024-11-27 10:58:23', 'validated', NULL),
(2203, 'Denzel', 'Ramos', 'Denny', 'ramos@gmail.com', '$2y$10$Ecvlb7K3C3p8wtaWAWb50.f4GdvNSmLMpFplpOtlqwsAqsigGp0OW', 'Android', 4, 'bscs', 'default.jpg', '2024-11-27 11:00:41', 'validated', NULL),
(2205, 'Geralt', 'Ankins', 'GG', 'ankins@gmail.com', '$2y$10$TrnNumoOORCS/NqMZ1B2veYcsLs5xa0yZoW9xJpH2WAScLAx3DmAu', 'Intel', 3, 'bscs', 'default.jpg', '2024-11-27 10:59:25', 'validated', NULL),
(3434, 'Clark', 'Kent', 'clarky', 'clark@gmail.com', '$2y$10$UfWFYxT7w751Elyx17Hz4u.KIxh5ZgxBOwGqb1ag1n4H8k9rdtD4u', 'acer', 1, 'bscs', 'default.jpg', '2024-11-27 10:57:37', 'validated', NULL),
(3545, 'Gremm', 'Conor', 'Grim', 'conor@gmail.com', '$2y$10$KVocfYWZTjY/vqdZ4FogM.jMir3OVgsqSCgRYDk1CX/RQslB2nZ4m', 'Ryzen', 3, 'bscs', 'default.jpg', '2024-11-27 11:01:41', 'validated', NULL),
(10001, 'Alice', 'Smith', 'alicesmith', 'alice.smith@gmail.com', '$2y$10$CAUxmyJyZGZQVEChXj0mZeBI7sXZu9tVCayYmP0dAbr4sxcWKzj3G', '1', 2, 'beed', 'default.jpg', '2024-11-27 11:25:14', 'validated', NULL),
(10002, 'Bob', 'Johnson', 'bobjohnson', 'bob.johnson@gmail.com', '$2y$10$EurjfS05FCJ7kIDSiP.dhe/RGn2yB.mfTtxCYTjyTvrR0W78tx5lS', '3', 1, 'bsed', 'default.jpg', '2024-11-27 11:25:57', 'validated', NULL),
(10003, 'Charlie', 'Brown', 'charliebrown', 'charlie.brown@gmail.com', '$2y$10$zMgmmt2RevI2Uq.42PvDPuAELgYoVo9MqO8OR5j63acEK/G5kyqRK', '1', 2, 'bsba', 'default.jpg', '2024-11-27 11:26:36', 'validated', NULL),
(10004, 'Diana', 'Evans', 'dianaevans', 'diana.evans@gmail.com', '$2y$10$mfg5bkQBtNjxpGnIvEUrsu9AEcqCNxa0cX3..Jp9aq0MBceXfyM1K', '1', 2, 'act', 'default.jpg', '2024-11-27 11:27:35', 'validated', NULL),
(10005, 'Ethan', 'Harris', 'ethanharris', 'ethan.harris@gmail.com', '$2y$10$dOrt/UdTDcXtp1bAxHeYeuQ5oOHzTtuD0Lom5P1bDthXLfhSgV8ZK', '2', 3, 'bsa', 'default.jpg', '2024-11-27 11:28:15', 'validated', NULL),
(10006, 'Fiona', 'Morris', 'fionamorris', 'fiona.morris@gmail.com', '$2y$10$Bl5JgRs38aOFiG3wyN9EfOiGPdEP6heyBHPq0.KkG5EP6vjZXdOnu', '1', 2, 'bsed', 'default.jpg', '2024-11-27 11:28:51', 'validated', NULL),
(10007, 'George', 'Taylor', 'georgetaylor', 'george.taylor@gmail.com', '$2y$10$Tc61LGZf90/h8H0dH28Uvu2RIJvFufKAwN7Km7T/a4n1rOeAWYvOq', '2', 1, 'bsa', 'default.jpg', '2024-11-27 11:30:00', 'validated', NULL),
(10008, 'Hannah', 'Anderson', 'hannahanderson', 'hannah.anderson@gmail.com', '$2y$10$a5qdMd9nKNbRKRc833ixbeVVkXZzYOZiz1ZeXaecTeatS.aE8pFc2', '3', 2, 'bsba', 'default.jpg', '2024-11-27 11:30:43', 'validated', NULL),
(10009, 'Isaac', 'Thompson', 'isaacthompson', 'isaac.thompson@gmail.com', '$2y$10$iQzVrcVIfNfO1U0pa3XCS.B8yeVTanow5L4kWHGdJkMHRZgRcACaS', '3', 2, 'beed', 'default.jpg', '2024-11-27 11:31:17', 'validated', NULL),
(10010, 'Jasmine', 'Wright', 'jasminewright', 'jasmine.wright@gmail.com', '$2y$10$I7lKbwMaMmW/H9RNZhWsr.ikBXGIn7osWQ.jm5I4MtZl207TyxAQ6', '2', 1, 'beed', 'default.jpg', '2024-11-27 11:32:17', 'validated', NULL),
(10011, 'Kevin', 'White', 'kevinwhite', 'kevin.white@gmail.com', '$2y$10$SI9XHIxUPA4EaTXJs4x0KupAg5xtUKdPxvfkQs9bEstTgpariRPa2', '1', 2, 'bsed', 'default.jpg', '2024-11-27 11:32:58', 'validated', NULL),
(10012, 'Laura', 'Green', 'lauragreen', 'laura.green@gmail.com', '$2y$10$0Q6pH2x9Ebiw5Y4fE0zcwewdS/FnlMqNeBxrGZojH5kYzd6/0SeiG', '3', 2, 'act', 'default.jpg', '2024-11-27 11:33:29', 'validated', NULL),
(11111, 'Estephan', 'Trinidad', 'Steph', 'trinity@gmail.com', '$2y$10$0evdXUm.gF9eb3uys5tc3.QnHf/.6auiLlZW2bqLSz2g2YLeSsiy6', 'appdev', 1, 'act', 'default.jpg', '2024-11-27 11:03:45', 'validated', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`announcement_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `prof_id` (`prof_id`);

--
-- Indexes for table `course_instructor_list`
--
ALTER TABLE `course_instructor_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prof_id` (`prof_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `prof_id` (`prof_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `evaluation_archive_linked`
--
ALTER TABLE `evaluation_archive_linked`
  ADD PRIMARY KEY (`archive_id`),
  ADD KEY `fk_prof_id` (`prof_id`);

--
-- Indexes for table `evaluation_periods`
--
ALTER TABLE `evaluation_periods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_prof_id` (`prof_id`);

--
-- Indexes for table `questionnaires`
--
ALTER TABLE `questionnaires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questionnaire_edits`
--
ALTER TABLE `questionnaire_edits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question` (`question_id`),
  ADD KEY `fk_professor` (`prof_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `course_instructor_list`
--
ALTER TABLE `course_instructor_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1101;

--
-- AUTO_INCREMENT for table `evaluation_archive_linked`
--
ALTER TABLE `evaluation_archive_linked`
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `evaluation_periods`
--
ALTER TABLE `evaluation_periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `instructor`
--
ALTER TABLE `instructor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `questionnaires`
--
ALTER TABLE `questionnaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `questionnaire_edits`
--
ALTER TABLE `questionnaire_edits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21321322;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD CONSTRAINT `announcement_reads_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `announcement_reads_ibfk_2` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`prof_id`) REFERENCES `instructor` (`prof_id`);

--
-- Constraints for table `course_instructor_list`
--
ALTER TABLE `course_instructor_list`
  ADD CONSTRAINT `course_instructor_list_ibfk_1` FOREIGN KEY (`prof_id`) REFERENCES `instructor` (`prof_id`);

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`prof_id`) REFERENCES `instructor` (`prof_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questionnaires` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation_archive_linked`
--
ALTER TABLE `evaluation_archive_linked`
  ADD CONSTRAINT `fk_prof_id` FOREIGN KEY (`prof_id`) REFERENCES `instructor` (`prof_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questionnaire_edits`
--
ALTER TABLE `questionnaire_edits`
  ADD CONSTRAINT `fk_professor` FOREIGN KEY (`prof_id`) REFERENCES `instructor` (`prof_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_question` FOREIGN KEY (`question_id`) REFERENCES `questionnaires` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
