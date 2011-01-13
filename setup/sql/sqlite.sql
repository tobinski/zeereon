--
--
-- etempus datenbankstruktur für sqlite
-- lizenz: gnu gpl
-- (c) 2009 cyrill von wattenwyl, protagonist gmbh
--
--

CREATE TABLE [ansatz] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[name] varchar(255)  NOT NULL,
		[wert] int(11)  NOT NULL,
		[aktiv] int(11)  NOT NULL
);

CREATE TABLE [beleg] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[projekt_id] int(11)  NOT NULL,
		[user_id] int(11)  NOT NULL,
		[zeit] int(11)  NOT NULL,
		[beschreibung] text  NOT NULL,
		[betrag] int(11)  NOT NULL
);

CREATE TABLE [config] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[name] varchar(255)  UNIQUE NOT NULL,
		[value] text  NOT NULL
);

CREATE TABLE [home_msg] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[user_id] int(11)  NOT NULL,
		[message] text  NOT NULL,
		[zeit] int(11)  NOT NULL
);

CREATE TABLE [kunden] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[name] varchar(255)  NOT NULL,
		[adresse] varchar(255)  NOT NULL,
		[plz] varchar(255)  NOT NULL,
		[ort] varchar(255)  NOT NULL,
		[ansprechpartner] varchar(255)  NOT NULL,
		[telefon] varchar(255)  NOT NULL,
		[email] varchar(255)  NOT NULL,
		[overhead] int(11)  NOT NULL
);

CREATE TABLE [projekte] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[kunden_id] int(11)  NOT NULL,
		[name] varchar(255)  NOT NULL,
		[ansatz] text  NOT NULL,
		[user] text  NOT NULL,
		[kostendach] int(11)  NOT NULL,
		[overhead] int(11)  NOT NULL
);

CREATE TABLE [user] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[login] varchar(255)  UNIQUE NOT NULL,
		[name] varchar(255)  NOT NULL,
		[lang] varchar(255)  NOT NULL,
		[layout] varchar(255)  NOT NULL
);

CREATE TABLE [zeit] (
		[id] INTEGER  NOT NULL PRIMARY KEY,
		[projekt_id] int(11)  NOT NULL,
		[user_id] int(11)  NOT NULL,
		[ansatz_id] int(11)  NOT NULL,
		[zeit_start] int(11)  NOT NULL,
		[zeit_ende] int(11)  NOT NULL,
		[beschreibung] text  NOT NULL
);
