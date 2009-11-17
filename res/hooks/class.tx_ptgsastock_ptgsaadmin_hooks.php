<?php 



/***************************************************************
*  Copyright notice
*  
*  (c) 2008 Fabrizio Branca (branca@punkt.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/



/**
 * include external ressources
 */
require_once 'HTML/QuickForm/advmultiselect.php'; // PEAR HTML_Quickform_advmultiselect: Advanced mutliselecet element for HTML_Quickform (see http://www.laurent-laville.org/?module=pear&desc=qfams)
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock_articleextension.php';



/**
 * Extending the article gui in pt_gsaadmin
 * 
 * @see typo3conf/ext/pt_gsacategories for hook-mechanism and further configuration
 * 
 * $Id: class.tx_ptgsastock_ptgsaadmin_hooks.php,v 1.4 2009/04/01 14:43:04 ry21 Exp $
 * 
 * @author	Michael Knoll <knoll@punkt.de>
 * @since	2009-03-26
 * @package TYPO3
 * @subpackage pt_gsastock
 */
class tx_ptgsastock_ptgsaadmin_hooks extends tx_ptgsaadmin_submodules {
    
	
	
	/**
	 * Holds ext key prefix for form element names
	 * 
	 * @var string
	 */
	protected $extPrefix = 'pt_gsastock_';
	
	
	
	/*************************************************************
     * Hook methods
     *************************************************************/
	
	
	
    /**
     * This is called when the article data is loaded (e.g. to add addtional data, that is used in the form later)
     *
     * $params['articleDataArr']    article data array
     * $params['articleObj']        tx_ptgsashop_baseArticle
     * 
     * @param   array   array of parameters   
     * @param   tx_ptgsaadmin_module2   calling module object
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-26
     */
    public function loadArticleDefaults(&$params, &$ref){
        
    	/* Article extension holds stock data */
    	$articleExtensionUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($params['articleObj']->get_id());
    	if ($articleExtensionUid > 0) {
	    	$stockDataObj = new tx_ptgsastock_stock_articleextension($articleExtensionUid);
	    	$params['articleDataArr']['stock_data'] = $stockDataObj->exportPropertiesToArray();
    	}
    	
    	/* Load stock count from GSA article using stock object */
    	$stockObject = new tx_ptgsastock_stock($params['articleObj']->get_id());
    	$params['articleDataArr']['stock_data']['stock_count'] = $stockObject->get_bestand();
    	
    }
    
    
    
    /**
     * $params is an empty array (use processRelatedData if you need the article's uid)
     * 
     * This method is called, when form is saved and article related data should be stored to db.
     *
     * @param   array                   $params
     * @param   tx_ptgsaadmin_module2   $ref
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-30
     */
    public function createArticleFromFormData_processRelatedData(&$params, &$ref){
        
    	tx_pttools_assert::isNotEmpty($params['articleUid'], array('message' => '$params[\'articleUid\'] was empty!'));
    	
    	$articleExtensionExists = tx_ptgsastock_stock_articleextension::existsArticleextensionForBaseArticle($params['articleUid']);
    	if ($articleExtensionExists) {
    		/* Articleextension already exists */
    		$articleExtensionUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($params['articleUid']);
    	    $articleExtensionObject = new tx_ptgsastock_stock_articleextension($articleExtensionUid);
    	} else {
    		/* Articleextension did not exist yet */
    		$articleExtensionObject = new tx_ptgsastock_stock_articleextension(0,$params['articleUid']);
    	}
    	$articleExtensionObject['stock_article']       = t3lib_div::GPvar($this->extPrefix . 'stock_article');
    	$articleExtensionObject['stock_treshold_set']  = t3lib_div::GPvar($this->extPrefix . 'stock_treshold_set');
    	$articleExtensionObject['stock_status']        = t3lib_div::GPvar($this->extPrefix . 'stock_status');
    	$articleExtensionObject['description']         = t3lib_div::GPvar($this->extPrefix . 'description');
    	$articleExtensionObject['show_stock']          = t3lib_div::GPvar($this->extPrefix . 'show_stock');
  	    $articleExtensionObject->save();
  	    
  	    $stockObject = new tx_ptgsastock_stock($params['articleUid']);
  	    $stockObject->set_bestand(t3lib_div::GPvar($this->extPrefix . 'stock_count'));
  	    $stockAccessor = tx_ptgsastock_stockAccessor::getInstance();
  	    $stockAccessor->updateStockData($stockObject);
    	
    }
    
    
    
    /**
     * This is called when an article was deleted (e.g. for cleaning up all related data)
     * 
     * $params['articleUid']   uid of the deleted article
     *
     * @param   array                   array of parameters   
     * @param   tx_ptgsaadmin_module2   calling module object
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-30
     */   
    public function deleteArticle(&$params, &$ref){
    	
    	$articleExtensionObj = new tx_ptgsastock_stock_articleextension($params['articleUid']);
    	$articleExtensionObj->delete();
    	
    }
    

    
    /**
     * This is called while building the form (after the first button row)
     * 
     * $params['formObj']           HTML_Quickform object
     * $params['defaultsDataArr']   default data array
     * $params['tceformsObj']       t3lib_TCEforms object
     * $params['table']             Name of the virtual_table
     * $params['row']               virtual row for tceforms
     *
     * @param   array   array of parameters   
     * @param   tx_ptgsaadmin_module2   calling module object
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-30
     */
    public function returnArticleForm_formAfterFirstSection(&$params, &$ref){
    	
    	$form = $params['formObj'];
    	
    	/* Set form default data */
    	if (is_array($params['defaultsDataArr']['stock_data'])) {
    		$defaultsArray = array();
    		foreach($params['defaultsDataArr']['stock_data'] as $key => $value) {
    			$defaultsArray[$this->extPrefix . $key] = $value;
    		}
    		$form->setDefaults($defaultsArray);
    	} else {
    		// TODO remove this!
    		#print_r("No defaults set!");
    	}
    	
    	// TODO Translation!
    	$form->addElement('header', 'artHeader3', 'Bestandsverwaltung');
    	$form->addElement('select', $this->extPrefix . 'stock_article', 'Artikel f&uuml;r Bestandsrechnung', $this->returnArticleSetArray());
    	$form->addElement('text', $this->extPrefix . 'stock_count', 'Bestand', $this->getBestandForArticle($params['articleUid']));
    	$form->addElement('select', $this->extPrefix . 'stock_treshold_set', 'Bestandsschranken-Satz', $this->returnTresholdSetArray());
    	$form->addElement('select', $this->extPrefix . 'stock_status', 'Bestands-Status', $this->returnStatusSetArray());
    	$form->addElement('textarea', $this->extPrefix . 'description', 'Bestandsbeschreibung' , array('cols'=>'80', 'rows' => '4', 'class'=>$this->extPrefix.'_inputTextDefault'));
    	$form->addElement('checkbox', $this->extPrefix . 'show_stock', 'Bestandsinformation anzeigen?');
         
    }
    
    
    
    /*************************************************************
     * Helper methods
     *************************************************************/
    
    
    
    /**
     * Selects treshold set records from database
     * 
     * @return  array   Array of treshold sets
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-30
     */
    protected function returnTresholdSetArray() {
    	
    	$select = 'uid, description';
    	$from = 'tx_ptgsastock_stock_treshold_set';
    	// TODO make PID for stock management configurable via TS or EM
    	$where = '';
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
    	tx_pttools_assert::isMySQLRessource($res);
    	$rows = array();
    	$rows[] = '';
    	while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
    		$rows[$row['uid']] = $row['description'];
    	}
    	return $rows;
    	
    }
    
    
    
    /**
     * Selects status records from database
     * 
     * @return  array   Array of stock status
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-30
     */
    protected function returnStatusSetArray() {
    	
        $select = 'uid, name';
        $from = 'tx_ptgsastock_stock_status';
        $where = 'deleted = "0"';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
        tx_pttools_assert::isMySQLRessource($res);
        $rows = array();
        $rows[] = '';
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $rows[$row['uid']] = $row['name'];
        }
        return $rows;
        
    }
    
    
    
    /**
     * Selects article records from database
     * 
     * @return  array   Array of stock status
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    protected function returnArticleSetArray() {
    	
    	$articleSetArray = array();
    	// TODO translate this!
    	$articleSetArray[] = '--- KEIN ARTIKEL F&Uuml;R BESTANDSRECHNUNG ---';
    	$articleAccessor = tx_ptgsashop_articleAccessor::getInstance();
    	$onlineArticlesQuantity = $articleAccessor->selectOnlineArticlesQuantity();
           
        if ($onlineArticlesQuantity > 0) {   
            
            $onlineArticlesArr = $articleAccessor->selectOnlineArticles('ARTNR');
            if (is_array($onlineArticlesArr)) {
                foreach ($onlineArticlesArr as $articleDataArr) {
                    $articleSetArray[$articleDataArr['NUMMER']] = '(' . $articleDataArr['NUMMER'] . ') ' . $articleDataArr['MATCH'] . ' ' . $articleDataArr['MATCH2'];
                }
            }
            
        }
        
        return $articleSetArray;
    	
    }
    
    
    
    protected function getBestandForArticle($gsaUid) {
    	
    	if ($gsaUid > 0) {
	    	$stockObject = new tx_ptgsastock_stock($gsaUid);
	    	return $stockObject->get_bestand();
    	} else {
    		return '';
    	}
    	
    }
    	
    
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsastock/res/hooks/class.tx_ptgsastock_ptgsaadmin_hooks.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsastock/res/hooks/class.tx_ptgsastock_ptgsaadmin_hooks.php']);
}
?>