ALTER TABLE `#__rssfactory`
  CHANGE `cat` `cat` int NOT NULL AFTER `nrfeeds`,
  CHANGE `nrfeeds` `nrfeeds` smallint NOT NULL DEFAULT '1' AFTER `published`,
  CHANGE `rsserror` `rsserror` tinyint(1) NULL AFTER `date`,
  CHANGE `i2c_published` `i2c_published` tinyint(1) NOT NULL AFTER `i2c_frontpage`;

ALTER TABLE `#__rssfactory_cache`
  CHANGE `date` `date` datetime NOT NULL AFTER `rssurl`;

ALTER TABLE `#__rssfactory_cache`
  DROP INDEX `item_description`;
