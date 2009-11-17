<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005-2009 Michael Knoll (knoll@punkt.de)
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
 * Accessor for article stock information. 
 * 
 * @TODO Requires additional fields in gsa_minidb 
 *
 * $Id: class.tx_ptgsastock_stockAccessor.php,v 1.5 2009/10/16 15:06:19 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-02-16
 */ 

/**
 * Inclusion of external resources
 */
require_once t3lib_extMgm::extPath('pt_gsasocket').'res/class.tx_ptgsasocket_gsaDbAccessor.php'; // parent class for all GSA database accessor classes
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingleton.php'; // interface for Singleton design pattern
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general helper library class

/**
 *  Database accessor class for stock information taken from Article-Data (based on GSA database structure)
 *
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-02-16
 * @package     TYPO3
 * @subpackage  tx_ptgsashop
 */
class tx_ptgsastock_stockAccessor extends tx_ptgsasocket_gsaDbAccessor implements tx_pttools_iSingleton {
    
	
	
	/**
     * Properties
     */
    protected static $uniqueInstance = NULL; // (tx_ptgsashop_articleAccessor object) Singleton unique instance
    
    
    
    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/ 
    
    
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the protected class constructor.
     *
     * @param   void
     * @return  tx_ptgsashop_articleAccessor      unique instance of the object (Singleton) 
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-10
     */
    public static function getInstance() {
        
        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className;
        }
        
        return self::$uniqueInstance;
        
    }
    
    
    
    /***************************************************************************
     *   GSA DB RELATED METHODS
     **************************************************************************/
    
    
    
	/**
     * Returns an array with the basic stock information for an article (specified by article UID) from the GSA database.
     *
     * @param   integer     UID of the article from the GSA database (GSA database field "ARTIKEL.NUMMER")
     * @return  array       associative array with data of an article from the GSA database
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-02-16
     */
    public function selectStockData($gsaArticleUid) {
        
    	/* Assert correct article UID */
    	tx_pttools_assert::isNumeric($gsaArticleUid, array('message' => '$gsaArticleUid should be an integer but was ' . $gsaArticleUid));
    	tx_pttools_assert::isTrue($gsaArticleUid > 0, array('message' => '$gsArticleUid should be > 0 but was ' . $gsaArticleUid));
    	
        // query preparation
        /**
         * Annotations for the fields selected:
         * art.BESTAND		Actual stock of article
         * art.RESERVIERT	Number of articles that are used in "Auftragsbestätigungen"
         * art.VERFUEGBAR	art.BESTAND - art.RESERVIERT
         * art.MINDEST		Minimum number of articles to be in stock
         * art.MELDE		An message is send by GS-Auftrag if article count is bellow this number
         */
        $select  = 'art.BESTAND AS BESTAND, art.RESERVIERT AS RESERVIERT, art.VERFUEGBAR AS VERFUEGBAR, art.MINDEST AS MINDEST, art.MELDE AS MELDE';
        $from 	 = $this->getTableName('ARTIKEL').' art';
        $where   = 'NUMMER = '.intval($gsaArticleUid);
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed' . $this->gsaDbObj->sql_error(), 1, $this->gsaDbObj->sql_error());
        }
        $a_row = $this->gsaDbObj->sql_fetch_assoc($res);
        $this->gsaDbObj->sql_free_result($res);
        
        // if enabled, do charset conversion of all non-binary string data 
        if ($this->charsetConvEnabled == 1) {
            $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
        }
        
        trace($a_row); 
        
        return $a_row;
        
    }   
    
    
    
    /**
     * Updates stock data for given stock object in GSA database
     * 
     * @param       tx_ptgsastock_stock     $stockObj   Stock object to be updated
     * @return      void
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-04-01
     */
    public function updateStockData($stockObj) {
    	
    	$from = $this->getTableName('ARTIKEL');
    	$where = 'NUMMER = ' . intval($stockObj->get_articleUid());
    	
    	$updateDataArray['BESTAND'] = (strlen((float)$stockObj->get_bestand())) > 0 ? (float)$stockObj->get_bestand() : NULL;
    	$updateDataArray['RESERVIERT'] = (strlen((float)$stockObj->get_reserviert())) > 0 ? (float)$stockObj->get_reserviert() : NULL;
    	$updateDataArray['VERFUEGBAR'] = (strlen((float)$stockObj->get_verfuegbar())) > 0 ? (float)$stockObj->get_verfuegbar() : NULL;
    	$updateDataArray['MINDEST'] = (strlen((float)$stockObj->get_mindest())) > 0 ? (float)$stockObj->get_mindest() : NULL;
    	$updateDataArray['MELDE'] = (strlen((float)$stockObj->get_melde())) > 0 ? (float)$stockObj->get_melde() : NULL;
    	
        $res = $this->gsaDbObj->exec_UPDATEquery($from, $where, $updateDataArray);
        tx_pttools_assert::isMySQLRessource($res);
    	
    }
    
    
}
 
?>