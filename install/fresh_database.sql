SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `access_logs` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `ip_address` varchar(200) NOT NULL,  `datetime` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `activation_links` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `email` text NOT NULL,  `hash` varchar(255) NOT NULL,  `done` tinyint(1) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `answers` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `question_id` bigint(20) unsigned NOT NULL,  `status` enum('0','1') NOT NULL DEFAULT '0',  `message` text NOT NULL,  `rated` longtext NOT NULL,  `thumbs_up` bigint(20) unsigned NOT NULL,  `thumbs_down` bigint(20) unsigned NOT NULL,  `created` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `categories` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `name` varchar(150) NOT NULL,  `status` enum('0','1') NOT NULL DEFAULT '0',  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `core_settings` (  `name` varchar(100) NOT NULL,  `data` varchar(250) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `credit_bank_logs` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `created` datetime NOT NULL,  `amount` bigint(20) unsigned NOT NULL,  `type` enum('0','1') NOT NULL DEFAULT '0',  `status` enum('0','1') NOT NULL DEFAULT '0',  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `password_links` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `email` text NOT NULL,  `hash` varchar(255) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `personal_messages` (  `id` bigint(20) unsigned NOT NULL,  `recipient` varchar(50) NOT NULL,  `sender` varchar(50) NOT NULL,  `subject` varchar(150) NOT NULL,  `status` enum('s','r') NOT NULL DEFAULT 's',  `datetime_sent` datetime NOT NULL,  `sender_deleted` enum('0','1') NOT NULL DEFAULT '0',  `recipient_deleted` enum('0','1') NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pm_data` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `pm_id` bigint(20) unsigned NOT NULL,  `sender` bigint(20) unsigned NOT NULL,  `message` text NOT NULL,  `datetime` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `profile` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `profile_status` set('0','1') NOT NULL DEFAULT '0',  `profile_msg` varchar(255) NOT NULL,  `about_me` text NOT NULL,  `profile_picture` varchar(255) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `profile` VALUES(1, 688969, '0', 'Lorem ipsum dolor sit amet.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam libero leo, egestas eget venenatis non, convallis pharetra mauris. Donec tempor libero eget tellus consequat scelerisque. Sed ac eros eu nisl iaculis ornare. Donec at neque libero, quis dapibus justo. Vivamus nec erat risus, fermentum fermentum neque. Donec volutpat mi a mauris dignissim ullamcorper. Integer non elementum ipsum.nnPraesent vestibulum eleifend ante, vitae sollicitudin ligula congue non. Nam gravida rhoncus ligula congue molestie. Integer felis ipsum, consequat vitae elementum vel, facilisis feugiat quam. Ut nec est nibh, in euismod dolor. Nullam et dui risus, venenatis eleifend dui. Sed cursus massa et elit volutpat eget commodo nunc condimentum. Etiam vel turpis sit amet nulla accumsan commodo nec at diam.', 'male.jpg');


CREATE TABLE `profile_messages` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `profile_id` bigint(20) unsigned NOT NULL,  `status` enum('0','1') NOT NULL DEFAULT '0',  `message` text NOT NULL,  `sent` datetime NOT NULL,  `type` enum('0','1') NOT NULL DEFAULT '0',  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `question` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `title` varchar(150) NOT NULL,  `description` text NOT NULL,  `created` datetime NOT NULL,  `last_updated` datetime NOT NULL,  `view_count` bigint(20) unsigned NOT NULL,  `viewed` longtext NOT NULL,  `category_id` bigint(20) NOT NULL,  `rated` longtext NOT NULL,  `thumbs_up` bigint(20) unsigned NOT NULL,  `thumbs_down` bigint(20) unsigned NOT NULL,  `answer_count` bigint(20) unsigned NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `reported` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `reason` varchar(250) NOT NULL,  `datetime` datetime NOT NULL,  `type` enum('0','1') NOT NULL,  `reported_id` bigint(20) unsigned NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `staff_notes` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `user_id` varchar(255) NOT NULL,  `username` varchar(255) NOT NULL,  `message` text NOT NULL,  `date` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `users` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `user_id` bigint(11) NOT NULL,  `first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,  `last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,  `gender` enum('Male','Female') COLLATE utf8_unicode_ci NOT NULL,  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `activated` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',  `suspended` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',  `date_created` datetime NOT NULL,  `last_login` datetime NOT NULL,  `signup_ip` varchar(255)COLLATE utf8_unicode_ci NOT NULL,  `last_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,  `staff` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0', `questions_posted` bigint(20) unsigned NOT NULL,  `questions_answered` bigint(20) unsigned NOT NULL,  `oauth_provider` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,  `oauth_id` text COLLATE utf8_unicode_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE `user_activity` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `datetime` datetime NOT NULL,  `task` varchar(255) NOT NULL,  `type` enum('0','1') NOT NULL DEFAULT '0',  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `user_notifications` (  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `note` text NOT NULL,  `datetime` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

