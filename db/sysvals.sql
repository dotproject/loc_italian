# phpMyAdmin MySQL-Dump
# version 2.2.2
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# Host: localhost
# Generato il: 17 Apr, 2003 at 11:04 AM
# Versione MySQL: 3.23.54
# Versione PHP: 4.1.2
# Database : `intera`
# --------------------------------------------------------

#
# Struttura della tabella `sysvals`
#

DROP TABLE IF EXISTS sysvals;
CREATE TABLE sysvals (
  sysval_id int(10) unsigned NOT NULL auto_increment,
  sysval_key_id int(10) unsigned NOT NULL default '0',
  sysval_title varchar(48) NOT NULL default '',
  sysval_value text NOT NULL,
  PRIMARY KEY  (sysval_id)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `sysvals`
#

INSERT INTO sysvals VALUES (1, 1, 'ProjectStatus', '0|Non definito\r\n1|Contatto\r\n2|Proposto\r\n3|Attesa risposta\r\n4|In Pianificazione\r\n5|In Corso\r\n6|Sospeso\r\n7|Completato\r\n8|Rifiutato');
INSERT INTO sysvals VALUES (2, 1, 'CompanyType', '0|Non definito\r\n1|Privato\r\n2|Azienda\r\n3|Società\r\n4|Consorzio\r\n5|Ente\r\n6|Interno');

