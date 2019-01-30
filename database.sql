CREATE TABLE `sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(13) NOT NULL,
  `message` varchar(200) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime DEFAULT NULL,
  `delivery_receipt` tinyint(3) unsigned DEFAULT NULL,
  `delivery_receipt_time` datetime DEFAULT NULL,
  `sending_attempts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `scheduled_message` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Is this a scheduled message?',
  `send_time` datetime DEFAULT NULL COMMENT 'The time this message should go out, if it is a scheduled message',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `content` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

INSERT INTO `configurations` (`title`, `content`) VALUES ('sms_username', 'your SMS user name');
INSERT INTO `configurations` (`title`, `content`) VALUES ('sms_key', 'YourSMSAPIKey');
INSERT INTO `configurations` (`title`, `content`) VALUES ('sms_sender_id', 'YourSMSSenderID');
INSERT INTO `configurations` (`title`, `content`) VALUES ('sms_sender_uri', 'https://api.mysmsservice.com?')
