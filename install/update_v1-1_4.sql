CREATE TABLE IF NOT EXISTS `users_todos` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `issue_id` bigint(20) default NULL,
  `user_id` bigint(20) default NULL,
  `status` tinyint(2) default '1',
  `weight` bigint(20) default 1,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `projects_issues`
	ADD `weight` bigint(20) default '1';
	ADD `points` decimal(2,1) default '0';
