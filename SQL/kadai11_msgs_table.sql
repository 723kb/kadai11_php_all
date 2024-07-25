-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2024 年 7 月 25 日 12:47
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
-- テーブルの構造 `kadai11_msgs_table`
--

CREATE TABLE `kadai11_msgs_table` (
  `id` int(12) NOT NULL,
  `name` varchar(64) NOT NULL,
  `message` varchar(140) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `date` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `picture_path` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `kadai11_msgs_table`
--

INSERT INTO `kadai11_msgs_table` (`id`, `name`, `message`, `latitude`, `longitude`, `date`, `updated_at`, `picture_path`, `user_id`) VALUES
(33, 'てすと1', 'ほげ２', NULL, NULL, '2024-07-09 15:32:25', '2024-07-25 09:59:30', 'img/upload/2e7bab9a503c476a1c949684bcd19633.jpg', 19),
(75, 'てすと1', 'すとんず', 35.68123600, 139.76712500, '2024-07-16 17:07:47', '2024-07-25 09:59:30', 'img/upload/8e03d1e2353848ec15081b77fd889961.jpg', 19),
(77, 'てすと1', 'ほげ', 35.69050000, 139.69950000, '2024-07-16 18:22:05', '2024-07-25 09:59:30', 'img/upload/8163048d64144de555a785d5d4b09709.jpg', 19),
(80, 'てすと管理者1', 'てすと\r\n写真と位置\r\n更新', 35.69582910, 139.75238320, '2024-07-17 17:53:29', '2024-07-25 10:26:11', 'img/upload/688c40b247f19c1714513e0dbded306f.jpg', 21),
(91, 'てすと管理者1', 'テスト', 35.67742130, 139.76297440, '2024-07-25 14:54:45', '2024-07-25 10:01:04', 'img/upload/295b09fbea59dea73c42c7ac277ed411.png', 21),
(93, 'てすと1', 'なう', 35.67740030, 139.76298130, '2024-07-25 15:11:39', '2024-07-25 09:59:30', NULL, 19),
(94, 'てすと1', '灯籠流し雨で中止！！', 35.69582910, 139.75238320, '2024-07-25 15:36:42', '2024-07-25 10:24:23', NULL, 19);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `kadai11_msgs_table`
--
ALTER TABLE `kadai11_msgs_table`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `kadai11_msgs_table`
--
ALTER TABLE `kadai11_msgs_table`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
