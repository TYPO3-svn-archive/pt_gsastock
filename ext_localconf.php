<?php



if (!defined ('TYPO3_MODE')) { die ('Access denied.'); }



t3lib_extMgm::addUserTSConfig('
options.saveDocNew.tx_ptgsastock_stock_status=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_ptgsastock_stock_treshold_set=1
');



/* Make sure, T3 is in Frontend Mode */
if (TYPO3_MODE == 'FE') { // WARNING: do not remove this condition since this may stop the backend from working!

	/*********************************************
	 * Registering hooks for shop FE functions
	 *********************************************/
	
	/* Show Stock Information in Article Info Box */
    require(t3lib_extMgm::extPath('pt_gsastock').'res/hooks/class.tx_ptgsastock_hooks_ptgsashop_displayArticleInfobox_MarkerArrayHook.php');
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['pi2_hooks']['displayArticleInfobox_MarkerArrayHook'][] = 'tx_ptgsastock_hooks_ptgsashop_displayArticleInfobox_MarkerArrayHook';  // hook array (loop processing)
    
    /* Stock calculation in exec_checkout process */
    require(t3lib_extMgm::extPath('pt_gsastock').'res/class.tx_ptgsastock_defaultStockCalculator.php');
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['pi1_hooks']['exec_checkout_articleDataHook'] = 'EXT:pt_gsastock/res/class.tx_ptgsastock_defaultStockCalculator.php:tx_ptgsastock_defaultStockCalculator';
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['pi3_hooks']['updateArtDistrQtyChangesConsequencesHook'][] = 'EXT:pt_gsastock/res/class.tx_ptgsastock_defaultStockCalculator.php:tx_ptgsastock_defaultStockCalculator';
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['orderProcessor_hooks']['postOrderProcessingHook'][] = 'EXT:pt_gsastock/res/class.tx_ptgsastock_defaultStockCalculator.php:tx_ptgsastock_defaultStockCalculator->processPostOrderProcessingHook';
    
}  



/*********************************************
 * Registering hooks for BE Module extension *
 *********************************************/
     
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['returnArticleForm_formAfterFirstSection'][] = 'EXT:pt_gsastock/res/hooks/class.tx_ptgsastock_ptgsaadmin_hooks.php:tx_ptgsastock_ptgsaadmin_hooks->returnArticleForm_formAfterFirstSection';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['createArticleFromFormData_processRelatedData'][] =  'EXT:pt_gsastock/res/hooks/class.tx_ptgsastock_ptgsaadmin_hooks.php:tx_ptgsastock_ptgsaadmin_hooks->createArticleFromFormData_processRelatedData';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['loadArticleDefaults'][] = 'EXT:pt_gsastock/res/hooks/class.tx_ptgsastock_ptgsaadmin_hooks.php:tx_ptgsastock_ptgsaadmin_hooks->loadArticleDefaults';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['deleteArticle'][] = 'EXT:pt_gsastock/res/hooks/class.tx_ptgsastock_ptgsaadmin_hooks.php:tx_ptgsastock_ptgsaadmin_hooks->deleteArticle';



/*********************************************
 * Setting up CLI scripts                    *
 *********************************************/

$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array('EXT:'.$_EXTKEY.'/cli/class.tx_ptgsastock_cli.php','_cli_user');
    


/** **********************************************************************************
 *  Setting up object storage configuration 
 * ***********************************************************************************/

/* Stockcount rows */
$pt_objectstorage_stockcount_confArray = array(
   	'accessor' => 'tx_ptobjectstorage_t3rowAccessor',
  	'conf' => array(
		'table' => 'tx_ptgsastock_stockcount'
	) 
);
$TYPO3_CONF_VARS['EXTCONF']['pt_objectstorage']['classes']['tx_ptgsastock_stockcount'] = $pt_objectstorage_stockcount_confArray;

/* Treshold rows */
$pt_objectstorage_treshold_confArray = array(
   	'accessor' => 'tx_ptobjectstorage_t3rowAccessor',
 	'conf'	=> array(
		'table' => 'tx_ptgsastock_treshold'
	)
);
$TYPO3_CONF_VARS['EXTCONF']['pt_objectstorage']['classes']['tx_ptgsastock_treshold'] = $pt_objectstorage_treshold_confArray;

/* Stock status rows */
$pt_objectstorage_stock_status_confArray = array(
	'accessor' => 'tx_ptobjectstorage_t3rowAccessor',
 	'conf'	=> array(
		'table' => 'tx_ptgsastock_stock_status'
	)
);
$TYPO3_CONF_VARS['EXTCONF']['pt_objectstorage']['classes']['tx_ptgsastock_stock_status'] = $pt_objectstorage_stock_status_confArray;

/* Stock treshold set rows */
$pt_objectstorage_stock_treshold_set_confArray = array (
	'accessor' => 'tx_ptobjectstorage_t3rowAccessor',
 	'conf'	=> array(
		'table' => 'tx_ptgsastock_stock_treshold_set'
	)
);
$TYPO3_CONF_VARS['EXTCONF']['pt_objectstorage']['classes']['tx_ptgsastock_stock_treshold_set'] = $pt_objectstorage_stock_treshold_set_confArray;



?>