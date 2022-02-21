CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `resettable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `roles_mask` int(10) unsigned NOT NULL DEFAULT '0',
  `registered` int(10) unsigned NOT NULL,
  `last_login` int(10) unsigned DEFAULT NULL,
  `force_logout` mediumint(7) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_confirmations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
  `selector` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  KEY `email_expires` (`email`,`expires`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_remembered` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `selector` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `selector` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  KEY `user_expires` (`user`,`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_throttling` (
  `bucket` varchar(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `tokens` float unsigned NOT NULL,
  `replenished_at` int(10) unsigned NOT NULL,
  `expires_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`bucket`),
  KEY `expires_at` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `courses_section`(
 `id`          int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`     int(10) NULL ,
 `course_name` varchar(100) NULL ,

PRIMARY KEY (`id`),
KEY `FK_55` (`user_id`),
CONSTRAINT `FK_53` FOREIGN KEY `FK_55` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `custom_sections_section`(
 `id`          int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`     int(10)  NOT NULL ,
 `title`       varchar(20) NULL ,
 `description` varchar(1000) NULL ,

PRIMARY KEY (`id`),
KEY `FK_136` (`user_id`),
CONSTRAINT `FK_134` FOREIGN KEY `FK_136` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `education_section`(
 `id`          int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`     int(10)  NOT NULL ,
 `degree`      varchar(25) NULL ,
 `field`       varchar(25) NULL ,
 `school_name` varchar(40) NULL ,
 `start_date`  date NULL ,
 `end_date`    date NULL ,

PRIMARY KEY (`id`),
KEY `FK_109` (`user_id`),
CONSTRAINT `FK_107` FOREIGN KEY `FK_109` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `experience_section`(
 `id`               int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`          int(10)  NOT NULL ,
 `position`         varchar(20) NULL ,
 `company_name`     varchar(15) NULL ,
 `company_location` varchar(15) NULL ,
 `start_date`       date NULL ,
 `end_date`         date NULL ,
 `description`      varchar(2000) NULL ,

PRIMARY KEY (`id`),
KEY `FK_62` (`user_id`),
CONSTRAINT `FK_60` FOREIGN KEY `FK_62` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hobbies_section`(
 `id`         int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`    int(10)  NOT NULL ,
 `hobby_name` varchar(15) NULL ,

PRIMARY KEY (`id`),
KEY `FK_102` (`user_id`),
CONSTRAINT `FK_100` FOREIGN KEY `FK_102` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `languages_section`(
 `id`             int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`        int(10)  NOT NULL ,
 `language_name`  varchar(15) NULL ,
 `language_level` int(10) NULL ,

PRIMARY KEY (`id`),
KEY `FK_74` (`user_id`),
CONSTRAINT `FK_72` FOREIGN KEY `FK_74` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_details_section`(
 `id`         int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`    int(10)  NOT NULL ,
 `first_name` varchar(20) NULL ,
 `last_name`  varchar(20) NULL ,
 `phone`      varchar(20) NULL ,
 `address`    varchar(50) NULL ,
 `birthdate`  date NULL ,
 `job_title`  varchar(20) NULL ,
 `email`      varchar(20) NULL ,
 `photo`      varchar(50) NULL ,

PRIMARY KEY (`id`),
KEY `FK_144` (`user_id`),
CONSTRAINT `FK_142` FOREIGN KEY `FK_144` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `profile_section`(
 `id`          int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`     int(10)  NOT NULL ,
 `description` varchar(1000) NULL ,

PRIMARY KEY (`id`),
KEY `FK_47` (`user_id`),
CONSTRAINT `FK_45` FOREIGN KEY `FK_47` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `side_projects_section`(
 `id`          int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`     int(10)  NOT NULL ,
 `title`       varchar(25) NULL ,
 `description` varchar(1000) NULL ,

PRIMARY KEY (`id`),
KEY `FK_120` (`user_id`),
CONSTRAINT `FK_118` FOREIGN KEY `FK_120` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skills_section`(
 `id`          int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`     int(10)  NOT NULL ,
 `skill_name`  varchar(20) NULL ,
 `skill_level` int(10) NULL ,

PRIMARY KEY (`id`),
KEY `FK_94` (`user_id`),
CONSTRAINT `FK_92` FOREIGN KEY `FK_94` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `social_links_section`(
 `id`           int(10) NOT NULL AUTO_INCREMENT ,
 `user_id`      int(10)  NOT NULL ,
 `website_name` varchar(15) NULL ,
 `link`         varchar(50) NULL ,

PRIMARY KEY (`id`),
KEY `FK_128` (`user_id`),
CONSTRAINT `FK_126` FOREIGN KEY `FK_128` (`user_id`) REFERENCES `users` (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;