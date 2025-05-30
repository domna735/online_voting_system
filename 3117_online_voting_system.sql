-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-04-18 10:43:12
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `3117_online_voting_system`
--

-- --------------------------------------------------------

--
-- 資料表結構 `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `comments`
--

INSERT INTO `comments` (`comment_id`, `poll_id`, `user_id`, `comment_text`, `created_at`) VALUES
(15, 15, 8, 'hi', '2025-04-16 09:32:58'),
(16, 15, 6, '&amp;#39; OR &amp;#39;1&amp;#39;=&amp;#39;1', '2025-04-18 07:21:29'),
(17, 15, 6, '&amp;#39; OR &amp;#39;1&amp;#39;=&amp;#39;1', '2025-04-18 07:21:49'),
(18, 15, 9, 'hi', '2025-04-18 07:23:32'),
(19, 15, 9, 'hi', '2025-04-18 07:32:33'),
(20, 15, 9, 'y', '2025-04-18 08:41:48'),
(21, 16, 3, 'hi', '2025-04-18 08:42:10');

-- --------------------------------------------------------

--
-- 資料表結構 `login_attempts`
--

CREATE TABLE `login_attempts` (
  `attempt_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `login_attempts`
--

INSERT INTO `login_attempts` (`attempt_id`, `ip_address`, `attempt_time`) VALUES
(3, '127.0.0.1', '2025-04-18 16:06:27'),
(4, '127.0.0.1', '2025-04-18 16:06:32'),
(5, '127.0.0.1', '2025-04-18 16:06:32'),
(6, '127.0.0.1', '2025-04-18 16:06:32'),
(7, '127.0.0.1', '2025-04-18 16:06:32');

-- --------------------------------------------------------

--
-- 資料表結構 `options`
--

CREATE TABLE `options` (
  `option_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `options`
--

INSERT INTO `options` (`option_id`, `poll_id`, `option_text`) VALUES
(18, 15, 'y'),
(19, 15, 'n'),
(20, 16, '1'),
(21, 16, '2');

-- --------------------------------------------------------

--
-- 資料表結構 `polls`
--

CREATE TABLE `polls` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `polls`
--

INSERT INTO `polls` (`poll_id`, `user_id`, `question`, `created_at`, `content`) VALUES
(15, 8, 'hi', '2025-04-16 08:51:13', 'hi'),
(16, 9, 'hi', '2025-04-18 07:27:03', 'test');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `login_id` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`user_id`, `login_id`, `nickname`, `email`, `profile_pic`, `password`, `created_at`, `login_attempts`, `last_attempt`) VALUES
(1, 'domna735', 'domna735', 'domna735@gmail.com', '', '$2y$10$Bj9vV122lyLClro4Wdw2K.s0o.ua.WGQZMsnQ267G5ciKy2c4nNo6', '2025-02-01 16:33:27', 0, NULL),
(3, 'don', 'donma', 'donma204@yahoo.com.hk', 'uploads/b29c6128bc29487f03ce104a0e9f0ad5.png', '$2y$10$oLzDtIl6CYlgDL7RZ3fAi.h4.mc4KxXH4w0ZSi4tWcHrlPOAYSJWC', '2025-02-02 16:37:30', 0, NULL),
(5, 'ma_don', 'ma_don', 'donma219@gmail.com', 'uploads/49db1e8c9dd450c6ac0670e0f45324a5.png', '$2y$10$xFBX2N4f/khsIF/KnbBtlOG/dUtaq4vHO8Vr7tyHidrXwE77fFHEu', '2025-02-04 17:00:58', 0, NULL),
(6, 'ma_kai_lun', 'ma_kai_lun-0', 'domnama03@gmail.com', '', '$2y$10$C7hXPQhq6AONywLg01qCN.r9eYCJ/dN4hAn1nuCifFrCpY6R2Q9rG', '2025-02-04 17:38:13', 0, NULL),
(7, 'donma204', 'donma204', 'domna204735@gmail.com', '', '$2y$10$4ebtaNjbNJ2mEyq/H8HFGOeEXEZYDVG0Nv6cQtboPBJMEiykc3uou', '2025-02-19 08:35:28', 0, NULL),
(8, 'kai_lun', 'Kai_lun', 'donma2042@gmail.com-1', '', '$2y$10$axnQtBmvL494.m9M5I4OLOe7cymVvyJ3jOBooWMj2R6TWDkw9xw7u', '2025-03-06 07:04:06', 0, NULL),
(9, 'do', 'donma', 'do@gmail.com', '', '$2y$10$TAq8hN16Z7PGje5FUyXjNeCZqp7zXkxgxqCXWYPs7EfuTHSuZiqq.', '2025-03-06 15:33:23', 0, NULL),
(12, 'ZAP', 'ZAP', 'zaproxy@example.com', '', '$2y$10$29o32t3r2kLmooL9dM6QCudHDMeV4BlLAUCeNBpmD0192lQ5aQmEG', '2025-04-18 08:06:27', 0, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `votes`
--

INSERT INTO `votes` (`vote_id`, `poll_id`, `option_id`, `user_id`, `voted_at`) VALUES
(28, 15, 18, 7, '2025-04-16 08:55:21'),
(29, 15, 19, 5, '2025-04-16 08:55:34'),
(31, 15, 19, 8, '2025-04-16 09:32:53'),
(33, 15, 18, 9, '2025-04-18 08:41:58'),
(34, 16, 20, 3, '2025-04-18 08:42:14');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `poll_id` (`poll_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`);

--
-- 資料表索引 `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- 資料表索引 `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`poll_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 資料表索引 `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `votes_ibfk_1` (`poll_id`),
  ADD KEY `votes_ibfk_2` (`option_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `options`
--
ALTER TABLE `options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `polls`
--
ALTER TABLE `polls`
  MODIFY `poll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `options` (`option_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
