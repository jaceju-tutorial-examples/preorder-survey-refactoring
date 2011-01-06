SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE DATABASE `refactoring` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `refactoring`;

DROP TABLE IF EXISTS `decoration_kitchens`;
CREATE TABLE IF NOT EXISTS `decoration_kitchens` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `phone` varchar(100) default NULL,
  `cellphone` varchar(100) default NULL,
  `address` varchar(200) default NULL,
  `kitchenQuestion01` enum('y','n') default 'n',
  `kitchenQuestion02` enum('y','n') default 'n',
  `kitchenQuestion03` enum('y','n') default 'n',
  `kitchenQuestion04` enum('y','n') default 'n',
  `kitchenQuestion05` enum('1','2','3','4','5') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='廚房問卷';
