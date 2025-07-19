-- Table: #__rssfactory
DROP TABLE IF EXISTS `#__rssfactory`;
CREATE TABLE `#__rssfactory` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `userid` INT NOT NULL,
  `protocol` VARCHAR(10) NOT NULL DEFAULT 'http',
  `url` TEXT NOT NULL,
  `title` TEXT NOT NULL,
  `ordering` INT NOT NULL DEFAULT 0,
  `published` TINYINT(1) NOT NULL DEFAULT 1,
  `nrfeeds` SMALLINT NOT NULL DEFAULT 1,
  `cat` INT NOT NULL,
  `date` DATETIME DEFAULT NULL,
  `rsserror` TINYINT(1) DEFAULT NULL,
  `last_error` MEDIUMTEXT,
  `last_refresh_stories` INT DEFAULT NULL,
  `encoding` VARCHAR(30) DEFAULT NULL,
  `enablerefreshwordfilter` TINYINT(1) NOT NULL DEFAULT 0,
  `refreshallowedwords` TEXT NOT NULL,
  `refreshbannedwords` TEXT NOT NULL,
  `refreshexactmatchwords` TEXT NOT NULL,
  `i2c_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `i2c_author` INT NOT NULL DEFAULT 0,
  `i2c_publishing_period` INT NOT NULL DEFAULT 180,
  `i2c_sectionid` INT NOT NULL DEFAULT 0,
  `i2c_catid` INT NOT NULL DEFAULT 11,
  `i2c_frontpage` TINYINT(1) NOT NULL DEFAULT 0,
  `i2c_published` TINYINT(1) NOT NULL,
  `i2c_prepend` TEXT NOT NULL,
  `i2c_append` TEXT NOT NULL,
  `i2c_full_article` TINYINT(1) NOT NULL DEFAULT 0,
  `i2c_enable_word_filter` TINYINT(1) NOT NULL DEFAULT 1,
  `i2c_words_white_list` TEXT NOT NULL,
  `i2c_words_black_list` TEXT NOT NULL,
  `i2c_words_exact_list` TEXT NOT NULL,
  `i2c_words_replacements` TEXT NOT NULL,
  `ftp_host` VARCHAR(255) NOT NULL,
  `ftp_username` VARCHAR(255) NOT NULL,
  `ftp_password` VARCHAR(255) NOT NULL,
  `ftp_path` TEXT NOT NULL,
  `params` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  KEY `userid` (`userid`),
  KEY `nrfeeds` (`nrfeeds`),
  KEY `ordering` (`ordering`),
  KEY `published` (`published`),
  KEY `id_published` (`id`, `published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_ad_category_map
DROP TABLE IF EXISTS `#__rssfactory_ad_category_map`;
CREATE TABLE `#__rssfactory_ad_category_map` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `adId` INT NOT NULL,
  `categoryId` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_ads
DROP TABLE IF EXISTS `#__rssfactory_ads`;
CREATE TABLE `#__rssfactory_ads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(250) DEFAULT NULL,
  `adtext` TEXT,
  `categories_assigned` MEDIUMTEXT,
  `published` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_cache
DROP TABLE IF EXISTS `#__rssfactory_cache`;
CREATE TABLE `#__rssfactory_cache` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `rssid` INT DEFAULT NULL,
  `rssurl` TEXT,
  `date` DATETIME NOT NULL,
  `channel_title` TEXT,
  `channel_link` TEXT,
  `channel_description` TEXT,
  `channel_category` TEXT,
  `item_title` TEXT,
  `item_description` TEXT,
  `item_link` TEXT,
  `item_source` TEXT,
  `item_date` DATETIME DEFAULT NULL,
  `item_enclosure` TEXT,
  `item_hash` VARCHAR(40) NOT NULL DEFAULT '',
  `archived` TINYINT(1) NOT NULL DEFAULT 0,
  `hits` INT NOT NULL DEFAULT 0,
  `comments` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rssid` (`rssid`),
  KEY `item_hash` (`item_hash`),
  KEY `archived` (`archived`),
  KEY `item_date` (`item_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_comments
DROP TABLE IF EXISTS `#__rssfactory_comments`;
CREATE TABLE `#__rssfactory_comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type_id` TINYINT(1) NOT NULL,
  `item_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `text` MEDIUMTEXT NOT NULL,
  `published` TINYINT(1) NOT NULL,
  `created_at` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_favorites
DROP TABLE IF EXISTS `#__rssfactory_favorites`;
CREATE TABLE `#__rssfactory_favorites` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `feed_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_feed_id` (`feed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_submitted
DROP TABLE IF EXISTS `#__rssfactory_submitted`;
CREATE TABLE `#__rssfactory_submitted` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `url` TEXT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `userid` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table: #__rssfactory_voting
DROP TABLE IF EXISTS `#__rssfactory_voting`;
CREATE TABLE `#__rssfactory_voting` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cacheId` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL,
  `voteValue` TINYINT NOT NULL DEFAULT 0,
  `voteHash` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cacheId` (`cacheId`),
  KEY `userid` (`userid`),
  KEY `voteHash` (`voteHash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
