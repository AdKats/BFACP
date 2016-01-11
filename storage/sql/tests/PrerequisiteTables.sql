CREATE TABLE `tbl_games` (
  `GameID` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GameID`),
  UNIQUE KEY `name_unique` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_playerdata` (
  `PlayerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `GameID` tinyint(4) unsigned DEFAULT NULL,
  `ClanTag` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `SoldierName` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GlobalRank` smallint(5) unsigned NOT NULL DEFAULT '0',
  `PBGUID` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `EAGUID` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IP_Address` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CountryCode` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PlayerID`),
  UNIQUE KEY `UNIQUE_playerdata` (`EAGUID`,`GameID`),
  KEY `INDEX_SoldierName` (`SoldierName`),
  KEY `INDEX_IP` (`IP_Address`),
  KEY `INDEX_CountryCode` (`CountryCode`),
  KEY `PBGUID` (`PBGUID`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`),
  KEY `GameID` (`GameID`),
  CONSTRAINT `tbl_playerdata_ibfk_1` FOREIGN KEY (`GameID`) REFERENCES `tbl_games` (`GameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_server` (
  `ServerID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ServerGroup` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `IP_Address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ServerName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GameID` tinyint(4) unsigned NOT NULL,
  `usedSlots` smallint(5) unsigned DEFAULT '0',
  `maxSlots` smallint(5) unsigned DEFAULT '0',
  `mapName` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fullMapName` text COLLATE utf8_unicode_ci,
  `Gamemode` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GameMod` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `PBversion` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ConnectionState` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ServerID`),
  UNIQUE KEY `IP_Address` (`IP_Address`),
  KEY `INDEX_SERVERGROUP` (`ServerGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
