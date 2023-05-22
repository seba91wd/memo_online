SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `beta_post` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `beta_post`;

CREATE TABLE `post_list` (
  `post_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_title` varchar(1000) NOT NULL,
  `post_mesage` varchar(1000) NOT NULL,
  `post_statut` tinyint(1) NOT NULL DEFAULT 0,
  `post_color` varchar(7) DEFAULT NULL,
  `post_date` datetime NOT NULL,
  `post_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `session_list` (
  `session_id` int(11) NOT NULL,
  `session_name` varchar(50) NOT NULL,
  `session_type` tinyint(1) NOT NULL,
  `session_user_id` int(11) NOT NULL,
  `session_update` datetime NOT NULL,
  `session_pass` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_list` (
  `user_id` int(11) NOT NULL,
  `user_pseudo` varchar(50) NOT NULL,
  `user_mail` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_date` datetime NOT NULL,
  `user_statut` int(11) NOT NULL,
  `user_token` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_option` (
  `option_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `memo_simple` tinyint(1) NOT NULL DEFAULT 1,
  `memo_valide` tinyint(1) NOT NULL DEFAULT 1,
  `memo_archive` tinyint(1) NOT NULL DEFAULT 0,
  `display_cat` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_right` (
  `right_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `session_pass` varchar(255) DEFAULT '0',
  `user_rule` tinyint(1) NOT NULL DEFAULT 0,
  `user_acces` tinyint(1) NOT NULL DEFAULT 0,
  `create_post` tinyint(1) NOT NULL DEFAULT 0,
  `edit_post` tinyint(1) NOT NULL DEFAULT 0,
  `delete_post` tinyint(1) NOT NULL DEFAULT 0,
  `valide_post` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


ALTER TABLE `post_list`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `post_list_user_id` (`user_id`),
  ADD KEY `post_list_session_id` (`session_id`);

ALTER TABLE `session_list`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `session_list_user_id` (`session_user_id`);

ALTER TABLE `user_list`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `user_option`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `user_option_session_id` (`session_id`),
  ADD KEY `user_option_user_id` (`user_id`);

ALTER TABLE `user_right`
  ADD PRIMARY KEY (`right_id`),
  ADD KEY `user_right_user_id` (`user_id`),
  ADD KEY `user_right_session_id` (`session_id`);


ALTER TABLE `post_list`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `session_list`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_list`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_option`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_right`
  MODIFY `right_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `post_list`
  ADD CONSTRAINT `post_list_session_id` FOREIGN KEY (`session_id`) REFERENCES `session_list` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_list_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_list` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `session_list`
  ADD CONSTRAINT `session_list_user_id` FOREIGN KEY (`session_user_id`) REFERENCES `user_list` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `user_option`
  ADD CONSTRAINT `user_option_session_id` FOREIGN KEY (`session_id`) REFERENCES `session_list` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_option_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_right` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `user_right`
  ADD CONSTRAINT `user_right_session_id` FOREIGN KEY (`session_id`) REFERENCES `session_list` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_right_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_list` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
