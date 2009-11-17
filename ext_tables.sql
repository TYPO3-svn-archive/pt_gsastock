#
# Table structure for table 'tx_ptgsastock_stockcount'
#
CREATE TABLE tx_ptgsastock_stockcount (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	order_state tinytext,
	qty tinytext,
	artikel_nummer tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_ptgsastock_stock_status'
#
CREATE TABLE tx_ptgsastock_stock_status (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(250) DEFAULT '' NOT NULL,
	hint text,
	image text,
	use_arcticle_stock_info tinyint(3) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);




#
# Table structure for table 'tx_ptgsastock_stock_treshold_set_stock_treshold_mm'
# 
#
CREATE TABLE tx_ptgsastock_stock_treshold_set_stock_treshold_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_ptgsastock_stock_treshold_set'
#
CREATE TABLE tx_ptgsastock_stock_treshold_set (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	description varchar(250) DEFAULT '' NOT NULL,
	stock_treshold int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_ptgsastock_stock_treshold'
#
CREATE TABLE tx_ptgsastock_stock_treshold (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	lower_bound varchar(20) DEFAULT '' NOT NULL,
	upper_bound varchar(20) DEFAULT '' NOT NULL,
	stock_status text,
	description text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_ptgsastock_stock_articleextension'
#
CREATE TABLE tx_ptgsastock_stock_articleextension (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    base_article int(11) DEFAULT '0' NOT NULL,
    stock_article int(11) DEFAULT '0' NOT NULL,
    stock_treshold_set int(11) DEFAULT '0' NOT NULL,
    stock_status int(11) DEFAULT '0' NOT NULL,
    description text,
    stock_category int(11) DEFAULT '0' NOT NULL,
    show_stock  tinyint(3) NOT NULL default '0',
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);