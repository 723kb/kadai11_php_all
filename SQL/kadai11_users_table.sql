-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2024 年 7 月 25 日 12:48
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `gs_board`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `kadai11_users_table`
--

CREATE TABLE `kadai11_users_table` (
  `id` int(12) NOT NULL,
  `lid` varchar(50) NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `kanri_flg` int(1) NOT NULL,
  `life_flg` int(1) NOT NULL,
  `indate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `kadai11_users_table`
--

INSERT INTO `kadai11_users_table` (`id`, `lid`, `username`, `email`, `password`, `kanri_flg`, `life_flg`, `indate`, `updated_at`) VALUES
(19, 'test1', 'てすと1', 'test@test.com', '$2y$10$RORaZXMXPTNT6JuE7LKSKOzr8mIoat6OrcIMcIPQts.HLsCg8tKjS', 0, 1, '2024-07-09 06:00:15', '2024-07-25 09:59:30'),
(21, 'admin1', 'てすと管理者1', 'admin@test.com', '$2y$10$yWZvjWvImsjvR1ub6iLv/u6OnUwY1XKC5NcNIxPeBR/UdeJZMGCTK', 1, 1, '2024-07-09 06:00:15', '2024-07-25 10:01:04'),
(52, 'hoge', 'ほげ1号', 'hoge@hoge.com', '$2y$10$6sNeL7iCuxam3OLTBNfdt.ZJIzkNSvEHHR6hTG8TBEGshdS3hYVNa', 0, 1, '2024-07-14 02:52:31', NULL);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `kadai11_users_table`
--
ALTER TABLE `kadai11_users_table`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `kadai11_users_table`
--
ALTER TABLE `kadai11_users_table`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
