SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
/*!40101 SET NAMES utf8 */;
DROP TABLE IF EXISTS `cm_action`;


CREATE TABLE `cm_action` (
  `actorId` int(10) unsigned DEFAULT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `actionType` tinyint(3) unsigned NOT NULL,
  `entityType` tinyint(3) unsigned NOT NULL,
  `actionLimitType` tinyint(3) unsigned DEFAULT NULL,
  `createStamp` int(10) unsigned NOT NULL,
  KEY `actorId` (`actorId`),
  KEY `ip` (`ip`),
  KEY `action` (`actionType`),
  KEY `createStamp` (`createStamp`),
  KEY `entityType` (`entityType`),
  KEY `type` (`actionLimitType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_actionLimit`;


CREATE TABLE `cm_actionLimit` (
  `entityType` int(10) unsigned NOT NULL,
  `actionType` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `role` tinyint(3) unsigned DEFAULT NULL,
  `limit` int(10) unsigned DEFAULT NULL,
  `period` int(10) unsigned NOT NULL,
  UNIQUE KEY `entityType` (`entityType`,`actionType`,`type`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_captcha`;


CREATE TABLE `cm_captcha` (
  `captcha_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_contentlist`;


CREATE TABLE `cm_contentlist` (
  `type` int(10) unsigned NOT NULL,
  `string` varchar(100) NOT NULL,
  PRIMARY KEY (`type`,`string`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_ipBlocked`;


CREATE TABLE `cm_ipBlocked` (
  `ip` int(10) unsigned NOT NULL,
  `createStamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ip`),
  KEY `createStamp` (`createStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_lang`;


CREATE TABLE `cm_lang` (
  `lang_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `abbrev` varchar(5) NOT NULL DEFAULT '',
  `label` varchar(30) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `abbrev` (`abbrev`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_langKey`;


CREATE TABLE `cm_langKey` (
  `lang_key_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang_section_id` int(10) unsigned NOT NULL,
  `key` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`lang_key_id`),
  KEY `lang_section_id` (`lang_section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_langSection`;


CREATE TABLE `cm_langSection` (
  `lang_section_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_section_id` int(10) unsigned NOT NULL DEFAULT '0',
  `section` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`lang_section_id`),
  UNIQUE KEY `section` (`parent_section_id`,`section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_langValue`;


CREATE TABLE `cm_langValue` (
  `lang_key_id` int(10) unsigned NOT NULL,
  `lang_id` tinyint(3) unsigned NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `lang_key_id` (`lang_key_id`,`lang_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_locationCity`;


CREATE TABLE `cm_locationCity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stateId` int(10) unsigned DEFAULT NULL,
  `countryId` int(10) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `_maxmind` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maxmind` (`_maxmind`),
  KEY `name` (`name`),
  KEY `stateId` (`stateId`),
  KEY `countryId` (`countryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_locationCityIp`;


CREATE TABLE `cm_locationCityIp` (
  `cityId` int(10) unsigned NOT NULL,
  `ipStart` int(10) unsigned NOT NULL,
  `ipEnd` int(10) unsigned NOT NULL,
  KEY `cityId` (`cityId`),
  KEY `ipEnd` (`ipEnd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_locationCountry`;


CREATE TABLE `cm_locationCountry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `abbreviation` char(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_locationCountryIp`;


CREATE TABLE `cm_locationCountryIp` (
  `countryId` int(10) unsigned NOT NULL,
  `ipStart` int(10) unsigned NOT NULL,
  `ipEnd` int(10) unsigned NOT NULL,
  KEY `ipEnd` (`ipEnd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_locationState`;


CREATE TABLE `cm_locationState` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `countryId` int(10) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `_maxmind` char(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maxmind` (`_maxmind`),
  KEY `name` (`name`),
  KEY `countryId` (`countryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_locationZip`;


CREATE TABLE `cm_locationZip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `cityId` int(10) unsigned NOT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cityId` (`cityId`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_log`;


CREATE TABLE `cm_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `msg` varchar(5000) NOT NULL,
  `timeStamp` int(10) unsigned NOT NULL,
  `metaInfo` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`timeStamp`),
  KEY `msg` (`msg`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_mail`;


CREATE TABLE `cm_mail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(256) DEFAULT NULL,
  `text` text,
  `html` mediumtext,
  `senderAddress` varchar(256) NOT NULL,
  `recipientAddress` varchar(256) NOT NULL,
  `senderName` varchar(64) DEFAULT NULL,
  `createStamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `createStamp` (`createStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_role`;


CREATE TABLE `cm_role` (
  `userId` int(10) unsigned NOT NULL,
  `role` tinyint(3) unsigned NOT NULL,
  `startStamp` int(10) unsigned NOT NULL,
  `expirationStamp` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`userId`,`role`),
  KEY `expirationStamp` (`expirationStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_session`;


CREATE TABLE `cm_session` (
  `sessionId` char(32) NOT NULL,
  `data` text NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sessionId`),
  KEY `expires` (`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_smiley`;


CREATE TABLE `cm_smiley` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setId` int(10) NOT NULL,
  `code` varchar(50) NOT NULL,
  `file` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `section` (`setId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_smileySet`;


CREATE TABLE `cm_smileySet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_stream`;


CREATE TABLE `cm_stream` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `channel` varchar(32) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_string`;


CREATE TABLE `cm_string` (
  `type` int(10) unsigned NOT NULL,
  `string` varchar(100) NOT NULL,
  PRIMARY KEY (`type`,`string`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_svm`;


CREATE TABLE `cm_svm` (
  `id` int(11) NOT NULL,
  `trainingChanges` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `trainingChanges` (`trainingChanges`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_svmtraining`;


CREATE TABLE `cm_svmtraining` (
  `svmId` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `values` blob NOT NULL,
  `createStamp` int(10) unsigned NOT NULL,
  KEY `svmId` (`svmId`),
  KEY `createStamp` (`createStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_tmp_location`;


CREATE TABLE `cm_tmp_location` (
  `level` tinyint(4) NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `1Id` int(10) unsigned DEFAULT NULL,
  `2Id` int(10) unsigned DEFAULT NULL,
  `3Id` int(10) unsigned DEFAULT NULL,
  `4Id` int(10) unsigned DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `abbreviation` char(2) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  UNIQUE KEY `levelId` (`level`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_tmp_userfile`;


CREATE TABLE `cm_tmp_userfile` (
  `uniqid` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `size` int(10) unsigned NOT NULL,
  `uploadstamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uniqid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_user`;


CREATE TABLE `cm_user` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activityStamp` int(10) unsigned NOT NULL,
  `createStamp` int(10) unsigned NOT NULL,
  `site` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`userId`),
  KEY `activityStamp` (`activityStamp`),
  KEY `createStamp` (`createStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_user_online`;


CREATE TABLE `cm_user_online` (
  `userId` int(10) unsigned NOT NULL,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`userId`),
  KEY `visible` (`visible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_user_preference`;


CREATE TABLE `cm_user_preference` (
  `userId` int(10) unsigned NOT NULL,
  `preferenceId` int(10) unsigned NOT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`userId`,`preferenceId`),
  KEY `preferenceId` (`preferenceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_user_preferenceDefault`;


CREATE TABLE `cm_user_preferenceDefault` (
  `preferenceId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(128) NOT NULL,
  `key` varchar(128) NOT NULL,
  `defaultValue` tinyint(1) NOT NULL,
  `configurable` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`preferenceId`),
  UNIQUE KEY `section` (`section`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cm_useragent`;


CREATE TABLE `cm_useragent` (
  `userId` int(10) unsigned NOT NULL,
  `createStamp` int(10) unsigned NOT NULL,
  `useragent` varchar(200) NOT NULL,
  PRIMARY KEY (`userId`,`useragent`),
  KEY `createStamp` (`createStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cm_lang` VALUES (1,'eng','English',1);
INSERT INTO `cm_langKey` VALUES (1,1,'ok'),(2,1,'cancel'),(3,1,'confirmation_title'),(4,2,'badword_replacement'),(5,4,'required'),(6,4,'illegal_value');
INSERT INTO `cm_langSection` VALUES (1,0,'interface',NULL),(2,0,'txt',NULL),(3,0,'forms',NULL),(4,3,'_errors',NULL);
INSERT INTO `cm_langValue` VALUES (1,1,'ok'),(2,1,'cancel'),(3,1,'confirmation_title'),(4,1,'badword_replacement'),(5,1,'required'),(6,1,'illegal_value');
INSERT INTO `cm_smiley` VALUES (1,1,':-),:),=)','a1.png'),(2,1,':-(,:(,:[,=(','a2.png'),(3,1,';-),;)','a3.gif'),(4,1,':-D,;D,=D','a4.png'),(5,1,'%-),%),O.o,o.O','a5.png'),(6,1,':-p','a6.png'),(7,1,'*SHY*','a7.png'),(8,1,':O,:-O,:o,:-o','a8.png'),(9,1,':|,:-|,:-/','a9.png'),(10,1,':-@,(:-&','a10.png'),(11,1,'O),o-)','a11.png'),(12,1,'B-),B),8-),8)','a12.png'),(13,1,'3),3-)','a13.png'),(14,1,':\'(,;-(,:\'-(','a14.gif'),(15,1,':-X,:-x','a15.png'),(16,1,'*TIRED*','a16.gif'),(17,1,'*ROFL*,*LOL*','a17.gif'),(18,1,'(:-$,:-(*)','a18.png'),(19,1,'*PHONE*','a19.png'),(20,1,'*IN LOVE*','a20.gif'),(21,1,':-P,:-)~','a21.gif'),(22,1,'*CRAZY*','a22.gif'),(23,1,':-<>,:-*,:*,*KISSING*','a23.gif'),(24,1,'*PRINCESS*','a24.png'),(25,1,'*FLIRTMALE*','a25.gif'),(26,1,'*FLIRTFEMALE*','a26.gif'),(27,1,'*ANGRY*','a27.gif'),(28,1,'*MASK*','a28.png'),(29,1,'*KISSED*','a29.png'),(30,1,':-[','a30.png'),(31,1,'*PARTY*','a31.gif'),(32,1,':-\\\\','a32.gif'),(33,1,'<3,*HEART*','a33.png'),(34,1,'</3,*BROKEN HEART*','a34.png'),(35,1,'*ROSE*','a35.png'),(36,1,'*STAR*','a36.png'),(37,1,'*GIFT*','a37.png'),(38,1,'*RAINBOW*','a38.png'),(39,1,'*BIRTHDAY*','a39.gif'),(40,1,'*FILM*','a40.png'),(41,1,'*PHOTO*','a41.png'),(42,1,'*WRITE*,*MAIL*','a42.png'),(43,1,'*MOBILE*','a43.png'),(44,1,'*DRINK*','a44.png'),(45,1,'*CONDOM*','a45.gif'),(46,1,'*ASS*','a46.gif'),(47,1,'*STRING*','a47.png'),(48,1,'*HANDCUFF*','a48.png'),(49,1,'*SEX*','a49.png'),(50,1,'*COCK*','a50.gif'),(51,1,'*PLAYBOY*','a51.gif'),(52,1,'*PLAYMATE*','a52.gif'),(53,1,'*MONEY*','a53.png'),(54,1,'*PEACE*','a54.png');
