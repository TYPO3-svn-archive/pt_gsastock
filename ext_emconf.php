<?php

########################################################################
# Extension Manager/Repository config file for ext: "pt_gsastock"
#
# Auto generated 11-02-2009 16:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'GSA Stock Management',
	'description' => 'Adds stock management to GSA shop. Shows availability hints for articles in the shop.',
	'category' => 'General Shop Applications',
	'author' => 'Michael Knoll',
	'author_email' => 'knoll@punkt.de',
	'shy' => '',
	'dependencies' => 'pt_tools,pt_objectstorage,pt_gsashop,pt_gsaadmin',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.3',
	'constraints' => array(
		'depends' => array(
			'pt_tools' => '1.0.0-',
            'pt_objectstorage' => '',
            'pt_gsasocket' => '1.0.0-',
            'pt_gsashop' => '1.0.0-',
            'pt_gsaadmin' => '0.1.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:21:{s:9:"ChangeLog";s:4:"3880";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"53a3";s:21:"ext_localconf.php_weg";s:4:"5cdf";s:14:"ext_tables.php";s:4:"3df1";s:14:"ext_tables.sql";s:4:"56e3";s:35:"icon_tx_ptgsastock_stock_status.gif";s:4:"475a";s:37:"icon_tx_ptgsastock_stock_treshold.gif";s:4:"475a";s:41:"icon_tx_ptgsastock_stock_treshold_set.gif";s:4:"475a";s:33:"icon_tx_ptgsastock_stockcount.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"1c39";s:7:"tca.php";s:4:"93de";s:19:"doc/wizard_form.dat";s:4:"432d";s:20:"doc/wizard_form.html";s:4:"b99a";s:13:"mod1/conf.php";s:4:"dc1c";s:14:"mod1/index.php";s:4:"927d";s:18:"mod1/locallang.xml";s:4:"957f";s:22:"mod1/locallang_mod.xml";s:4:"9d2f";s:19:"mod1/moduleicon.gif";s:4:"8074";s:87:"res/hooks/class.tx_ptgsastock_hooks_ptgsashop_displayArticleInfobox_MarkerArrayHook.php";s:4:"04ec";}',
);

?>