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
