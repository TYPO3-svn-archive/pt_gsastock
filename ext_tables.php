<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_ptgsastock_stockcount'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stockcount',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsastock_stockcount.gif',
	),
);

$TCA['tx_ptgsastock_stock_status'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_status',		
		'label'     => 'name',
		'label_alt' => 'hint',	
		'label_alt_force' => 1,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsastock_stock_status.gif',
	),
);

$TCA['tx_ptgsastock_stock_treshold_set'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold_set',		
		'label'     => 'description',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsastock_stock_treshold_set.gif',
	),
);

$TCA['tx_ptgsastock_stock_treshold'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold',		
		'label'     => 'description',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsastock_stock_treshold.gif',
	),
);

$TCA['tx_ptgsastock_stock_articleextension'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_articleextension',     
        'label'     => 'base_article',   
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate',  
        'enablecolumns' => array (      
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsastock_stock_articleextension.gif',
    ),
);

if (TYPO3_MODE == 'BE') {
	
	$extPath = t3lib_extMgm::extPath($_EXTKEY) . 'mod1/';
	
	/* add main module (GSA Admin) after 'File' module */
    if (!isset($TBE_MODULES['txptgsaadminM1']) && is_array($TBE_MODULES)) {
        $tempTbeModules = array();
        foreach ($TBE_MODULES as $key=>$val) {
            $tempTbeModules[$key] = $val;
            if ($key == 'file') {
                $tempTbeModules['txptgsaadminM1'] = $val;
            }
        }
        $TBE_MODULES = $tempTbeModules;
    }
	
	t3lib_extMgm::addModulePath('txptgsastockM1', $extPath);
	t3lib_extMgm::addModule('txptgsaadminM1', 'txptgsastockM1', '', $extPath);
}

/**
 * Add static TS
 */
t3lib_extMgm::addStaticFile($_EXTKEY,"static/","GSA Stock: General configuration");

?>