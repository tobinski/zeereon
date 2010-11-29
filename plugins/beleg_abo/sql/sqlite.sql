CREATE TABLE [belge_abo] (
   [id] INTEGER  NOT NULL PRIMARY KEY,
   [projekt_id] INTEGER NULL ,
   [betrag] INTEGER NULL ,
   [start] INTEGER NULL ,
   [diff] INTEGER NULL ,
   [next] INTEGER NULL ,
   [layout_data] TEXT NULL ,
   [last_book] INTEGER NULL,
   [desc] TEXT NULL
);
