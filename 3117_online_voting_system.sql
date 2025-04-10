-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-02-06 05:24:57
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
-- 資料庫： `online_voting_system`
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
(3, 4, 1, 'hi', '2025-02-02 17:26:50'),
(4, 4, 3, 'test 2', '2025-02-02 17:27:07'),
(5, 4, 3, 'hi', '2025-02-02 18:21:30'),
(6, 2, 3, 'hi\r\n', '2025-02-02 18:21:43'),
(7, 2, 3, 'no this is not.', '2025-02-03 14:34:36');

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
(3, 2, 'Yes\r'),
(4, 2, 'No'),
(5, 4, 'ok'),
(6, 4, 'not');

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
(2, 1, 'Is GPA = all of the academic result', '2025-02-01 18:59:14', 'no'),
(4, 3, 'test', '2025-02-02 17:09:47', 'ok');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`user_id`, `login_id`, `nickname`, `email`, `profile_pic`, `password`, `created_at`) VALUES
(1, 'domna735', 'domna735', 'domna735@gmail.com', '', '$2y$10$Bj9vV122lyLClro4Wdw2K.s0o.ua.WGQZMsnQ267G5ciKy2c4nNo6', '2025-02-01 16:33:27'),
(3, 'don', 'donma', 'donma204@yahoo.com.hk', 'uploads/b29c6128bc29487f03ce104a0e9f0ad5.png', '$2y$10$oLzDtIl6CYlgDL7RZ3fAi.h4.mc4KxXH4w0ZSi4tWcHrlPOAYSJWC', '2025-02-02 16:37:30'),
(5, 'ma_don', 'ma_don', 'donma219@gmail.com', 'uploads/49db1e8c9dd450c6ac0670e0f45324a5.png', '$2y$10$xFBX2N4f/khsIF/KnbBtlOG/dUtaq4vHO8Vr7tyHidrXwE77fFHEu', '2025-02-04 17:00:58'),
(6, 'ma_kai_lun', 'ma_kai_lun', 'domnama03@gmail.com', 'uploads/ef3b704c59361a135362db1e7cfe8c7e.png', '$2y$10$sc5zih8XJiNrIpaMSZWaNuZ8z4jRF.i7dKq93IZ4PPIDtOrYTbrg.', '2025-02-04 17:38:13');

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
(2, 2, 3, 1, '2025-02-05 09:45:43'),
(3, 4, 6, 3, '2025-02-02 17:27:36'),
(4, 4, 6, 1, '2025-02-02 17:26:42'),
(5, 2, 4, 3, '2025-02-03 14:34:10');

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
  ADD KEY `poll_id` (`poll_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `options`
--
ALTER TABLE `options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `polls`
--
ALTER TABLE `polls`
  MODIFY `poll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
