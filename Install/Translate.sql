DROP TABLE IF EXISTS `cms_translate_language`;
CREATE TABLE `cms_translate_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(255) NOT NULL,
  `lang_name` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='语言';

DROP TABLE IF EXISTS `cms_translate_project`;
CREATE TABLE `cms_translate_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目';

DROP TABLE IF EXISTS `cms_translate_constant`;
CREATE TABLE `cms_translate_constant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `key` varchar(512) NOT NULL DEFAULT '',
  `key_name` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `key` (`key`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='常量表';

DROP TABLE IF EXISTS `cms_translate_constant_category`;
CREATE TABLE `cms_translate_constant_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='常量分类';


DROP TABLE IF EXISTS `cms_translate_dictionary`;
CREATE TABLE `cms_translate_dictionary` (
  `dictionary_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(512) NOT NULL DEFAULT '',
  `value` varchar(512) NOT NULL DEFAULT '',
  `lang` varchar(255) NOT NULL,
  PRIMARY KEY (`dictionary_id`),
  KEY `key` (`key`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='常量分类';

CREATE TABLE `cms_translate_demo_car` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(255) DEFAULT NULL COMMENT '车型',
  `year` varchar(16) DEFAULT NULL COMMENT '年份',
  `transmission` tinyint(1) DEFAULT NULL COMMENT '变速箱类型',
  `vin` varchar(255) DEFAULT NULL COMMENT 'VIN 编码',
  `description` text,
  `input_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;

INSERT INTO `cms_translate_language` (`id`, `lang`, `lang_name`, `is_default`) VALUES (1, '中文', '中文', 1);
INSERT INTO `cms_translate_language` (`id`, `lang`, `lang_name`, `is_default`) VALUES (2, 'English', 'English', 0);
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES (1, 'demo.model', 'Model', 'English');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES (2, 'demo.model', '车型', '中文');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES(3, 'demo.year', 'Year', 'English');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES(4, 'demo.year', '年份', '中文');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES(5, 'demo.transmission', 'Transmission', 'English');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES(6, 'demo.transmission', '变速箱', '中文');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES(7, 'demo.transmission.not_limited', 'Not limited', 'English');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES (8, 'demo.transmission.not_limited', '不限制', '中文');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES(9, 'demo.transmission.automatic', 'Automatic', 'English');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES (10, 'demo.transmission.automatic', '自动挡', '中文');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES (11, 'demo.transmission.manual', 'Manual', 'English');
INSERT INTO `cms_translate_dictionary` (`dictionary_id`, `key`, `value`, `lang`) VALUES (12, 'demo.transmission.manual', '手动挡', '中文');
