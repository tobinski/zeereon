--
--
-- etempus datenbankstruktur für mysql
-- lizenz: gnu gpl
-- (c) 2009 cyrill von wattenwyl, protagonist gmbh
--
--

CREATE TABLE ansatz (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  wert int(11) NOT NULL,
  aktiv int(11) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE beleg (
  id int(11) NOT NULL auto_increment,
  projekt_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  zeit int(11) NOT NULL,
  beschreibung text NOT NULL,
  betrag int(11) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE config (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE home_msg (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL,
  message text NOT NULL,
  zeit int(11) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE kunden (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  adresse varchar(255) NOT NULL,
  plz varchar(255) NOT NULL,
  ort varchar(255) NOT NULL,
  ansprechpartner varchar(255) NOT NULL,
  telefon varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  overhead int(11) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE projekte (
  id int(11) NOT NULL auto_increment,
  kunden_id int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  ansatz text NOT NULL,
  `user` text NOT NULL,
  kostendach int(11) NOT NULL,
  overhead int(11) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE `user` (
  id int(11) NOT NULL auto_increment,
  login varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  lang varchar(255) NOT NULL,
  layout varchar(255) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY login (login)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



CREATE TABLE zeit (
  id int(11) NOT NULL auto_increment,
  projekt_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  ansatz_id int(11) NOT NULL,
  zeit_start int(11) NOT NULL,
  zeit_ende int(11) NOT NULL,
  beschreibung text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
