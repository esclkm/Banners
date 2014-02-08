CREATE TABLE IF NOT EXISTS `cot_banner_queries` (
  `query_id` INTEGER NOT NULL auto_increment,
  `query_cat` VARCHAR(255) NOT NULL DEFAULT '',
  `query_client` VARCHAR(255) NOT NULL DEFAULT '',
  `query_string` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`query_id`)
)  ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;