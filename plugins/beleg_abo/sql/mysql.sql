CREATE TABLE IF NOT EXISTS `belge_abo` (
  `id` int(11) NOT NULL auto_increment,
  `projekt_id` int(11) NOT NULL,
  `betrag` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `diff` int(11) NOT NULL,
  `next` int(11) NOT NULL,
  `layout_data` text NOT NULL,
  `last_book` int(11) NOT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
