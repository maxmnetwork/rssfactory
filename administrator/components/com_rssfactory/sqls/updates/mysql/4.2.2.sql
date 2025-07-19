ALTER TABLE `#__rssfactory_cache` ADD INDEX `item_date` (`item_date`), ADD FULLTEXT `item_description` (`item_description`);
ALTER TABLE `#__rssfactory` ADD INDEX `nrfeeds` (`nrfeeds`), ADD INDEX `ordering` (`ordering`), ADD INDEX `published` (`published`);
