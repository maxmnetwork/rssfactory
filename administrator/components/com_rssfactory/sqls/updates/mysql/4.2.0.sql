ALTER TABLE `#__rssfactory` ADD `last_error` mediumtext NULL AFTER `rsserror`;
ALTER TABLE `#__rssfactory`ADD `last_refresh_stories` int NULL AFTER `last_error`;
