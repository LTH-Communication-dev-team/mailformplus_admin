#
# Table structure for table 'tx_mailformplusadmin_fields'
#
CREATE TABLE tx_mailformplusadmin_fields (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	submittedfields varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=InnoDB;



#
# Table structure for table 'tx_mailformplusadmin_standardforms'
#
CREATE TABLE tx_mailformplusadmin_standardforms (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	lang tinytext,
	bodytext text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=InnoDB;



#
# Table structure for table 'tx_formhandler_log'
#
CREATE TABLE tx_formhandler_log (
	tx_mailformplusadmin_ok tinyint(3) DEFAULT '0' NOT NULL
);