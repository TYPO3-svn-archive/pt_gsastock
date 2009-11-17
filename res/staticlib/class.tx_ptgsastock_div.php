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
 * Class for static functions for stock functionality
 *
 * $Id: class.tx_ptgsastock_div.php,v 1.7 2009/10/16 15:06:19 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-02-13
 */ 



/**
 * Inclusions of Typo3 sources
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stockcount.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stockAccessor.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock.php';

require_once t3lib_extMgm::extPath('pt_tools') . 'res/objects/class.tx_pttools_sessionStorageAdapter.php';



/**
 * Collection of static functions for gsa_stock extension
 * 
 * @author Michael Knoll <knoll@punkt.de>
 * @subpackage pt_gsastock
 * @package Typo3
 * @since 2009-02-11
 *
 */
class tx_ptgsastock_div {
	
	
	
	/**
	 * Deletes stockcount records from database where tstamp < given timestamp
	 * 
	 * Adds "Bestand" to artikel again
	 * 
	 * @param  int     $time       timestamp to delete stockcount records from
	 * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-25
	 */
	public static function deleteStockCounts($time = 0) {
		
		$dbObj = $GLOBALS['TYPO3_DB'];
		
		/* Query stockcount ids from db */
		$select = 'uid';
		$from = 'tx_ptgsastock_stockcount';
		$where = '';
		if ($time != 0) {
			tx_pttools_assert::isNumeric($time, array('message' => '$time should be numeric but was ' . $time));
			$where = 'tstamp < ' . intval($time);
		}
		$res = $dbObj->exec_SELECTquery($select, $from, $where);
		tx_pttools_assert::isMySQLRessource($res, $dbObj, array('message' => '$res should be mysql ressource!'));
		
		/* Add stocks to articles and delete stockcount records */
		while ($uid = $dbObj->sql_fetch_assoc($res)) {
			
			$stockcount = new tx_ptgsastock_stockcount($uid['uid']);
			$stockObj = new tx_ptgsastock_stock($stockcount['artikel_nummer']);
			$oldArticleStock = $stockObj->get_bestand();
			$stockObj->set_bestand($oldArticleStock + $stockcount['qty']);
			tx_ptgsastock_stockAccessor::getInstance()->updateStockData($stockObj);
			$stockcount->delete();
			
		}
		
	}
	
	
	
	/**
	 * Returns temporary stock count from session
	 * 
	 * @return     array   Stock count array of the form (artNr => stockCount)
	 * @author     Michael Knoll <knoll@punkt.de>
	 * @since      2009-10-16
	 */
	public static function getSessionStockcount() {
		$sessionAdapter = tx_pttools_sessionStorageAdapter::getInstance();
		$sessionStockCount = $sessionAdapter->read('tx_ptgsastock_stockCount', true);
		if (is_array($sessionStockCount)) {
		    return $sessionStockCount;
		} else {
			return array();
		}
	}
	
	
	
	/**
	 * Writes temporary stock count to session
	 *
	 * @param      array    $sessionStockCount     Array with stock counts of the form (artNr => stockCount)
	 * @return     void
     * @author     Michael Knoll <knoll@punkt.de>
     * @since      2009-10-16
	 */
	public static function writeSessionStockCount($sessionStockCount) {
		$sessionAdapter = tx_pttools_sessionStorageAdapter::getInstance();
		$sessionAdapter->store('tx_ptgsastock_stockCount', $sessionStockCount, true);
	}

}

?>