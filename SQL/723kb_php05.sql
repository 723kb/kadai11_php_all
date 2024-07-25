-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql635.db.sakura.ne.jp
-- 生成日時: 2024 年 7 月 25 日 14:37
-- サーバのバージョン： 5.7.40-log
-- PHP のバージョン: 8.2.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `723kb_php05`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `kadai11_likes`
--

CREATE TABLE `kadai11_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `kadai11_likes`
--

INSERT INTO `kadai11_likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(219, 52, 70, '2024-07-14 03:40:20'),
(226, 19, 75, '2024-07-16 08:07:52'),
(229, 21, 75, '2024-07-22 14:46:51');

-- --------------------------------------------------------

--
-- テーブルの構造 `kadai11_msgs_table`
--

CREATE TABLE `kadai11_msgs_table` (
  `id` int(12) NOT NULL,
  `name` varchar(64) NOT NULL,
  `message` varchar(140) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `date` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `picture_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `kadai11_msgs_table`
--

INSERT INTO `kadai11_msgs_table` (`id`, `name`, `message`, `latitude`, `longitude`, `date`, `updated_at`, `picture_path`) VALUES
(33, 'てすと', 'ほげ２', NULL, NULL, '2024-07-09 15:32:25', '2024-07-16 06:46:06', 'img/upload/2e7bab9a503c476a1c949684bcd19633.jpg'),
(75, 'てすと', 'すとんず', 35.68123600, 139.76712500, '2024-07-16 17:07:47', NULL, 'img/upload/8e03d1e2353848ec15081b77fd889961.jpg'),
(77, 'てすと', 'ほげ', 35.69050000, 139.69950000, '2024-07-16 18:22:05', NULL, 'img/upload/8163048d64144de555a785d5d4b09709.jpg'),
(80, 'テスト１管理者', 'てすと\r\n写真と位置\r\n更新', 35.77342090, 139.82698360, '2024-07-17 17:53:29', '2024-07-22 14:52:04', 'img/upload/688c40b247f19c1714513e0dbded306f.jpg'),
(90, 'てすと', 'なうなう', 35.77340288, 139.82698121, '2024-07-17 18:52:03', NULL, 'img/upload/9f5e68c9e15474e33581b1fa97814ed1.jpeg');

-- --------------------------------------------------------

--
-- テーブルの構造 `kadai11_users_table`
--

CREATE TABLE `kadai11_users_table` (
  `id` int(12) NOT NULL,
  `lid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `kanri_flg` int(1) NOT NULL,
  `life_flg` int(1) NOT NULL,
  `indate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `kadai11_users_table`
--

INSERT INTO `kadai11_users_table` (`id`, `lid`, `username`, `email`, `password`, `kanri_flg`, `life_flg`, `indate`) VALUES
(19, 'test1', 'てすと', 'test@test.com', '$2y$10$X7yEci6lqjz5xmt0mwc6B.SNqXx0o4EMoXJh6tvdAcwLNQdVLRSVy', 0, 1, '2024-07-09 06:00:15'),
(21, 'admin1', 'テスト１管理者', 'admin@test.com', '$2y$10$Uu9Rj9X4vgIijauYtQw3UuNtpuLqdaNONhEllEMwNM1y7nAE6pX/K', 1, 1, '2024-07-09 06:00:15'),
(52, 'hoge', 'ほげ1号', 'hoge@hoge.com', '$2y$10$6sNeL7iCuxam3OLTBNfdt.ZJIzkNSvEHHR6hTG8TBEGshdS3hYVNa', 0, 1, '2024-07-14 02:52:31');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `kadai11_likes`
--
ALTER TABLE `kadai11_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_post_unique` (`user_id`,`post_id`);

--
-- テーブルのインデックス `kadai11_msgs_table`
--
ALTER TABLE `kadai11_msgs_table`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `kadai11_users_table`
--
ALTER TABLE `kadai11_users_table`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `kadai11_likes`
--
ALTER TABLE `kadai11_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- テーブルの AUTO_INCREMENT `kadai11_msgs_table`
--
ALTER TABLE `kadai11_msgs_table`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- テーブルの AUTO_INCREMENT `kadai11_users_table`
--
ALTER TABLE `kadai11_users_table`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
