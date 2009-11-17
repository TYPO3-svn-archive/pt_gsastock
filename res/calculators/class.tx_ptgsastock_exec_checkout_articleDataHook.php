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
 * Class definition file for checkout hook for stock extension
 * 
 * @version     $Id: class.tx_ptgsastock_exec_checkout_articleDataHook.php,v 1.2 2009/10/16 15:06:19 ry21 Exp $
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-16
 */ 



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/abstract/class.tx_ptgsastock_iExec_checkout_articleDataHook.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/staticlib/class.tx_ptgsastock_div.php';

require_once t3lib_extMgm::extPath('pt_gsashop') . 'pi1/class.tx_ptgsashop_pi1.php';



/**
 * Hook for processing stock changes when user checks out from shop
 * 
 * @package     Typo3
 * @subpackage  pt_gsastock
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-16
 */
class tx_ptgsastock_exec_checkout_articleDataHook extends tx_ptgsashop_pi1 implements tx_ptgsastock_iExec_checkout_articleDataHook {
	
	
	
	/**
	 * @see res/abstract/tx_ptgsastock_iExec_checkout_articleDataHook#exec_checkout_articleDataHook()
	 * 
	 * @param  tx_ptgsashop_pi1     $piInstance    Instance of plugin, hook is written for
	 * @param  tx_ptgsashop_article $articleObj    Article object on which checkout is processed
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-10-16
	 */
	public function exec_checkout_articleDataHook($piInstance, $articleObj) {
		
		#$GLOBALS['trace'] = 1;
		$this->removeDeletedArticlesFromStockCount($piInstance);
		
        if (tx_ptgsastock_stock_articleextension::existsArticleextensionForBaseArticle($articleObj->get_id())) {
			$this->processQuantityChange($articleObj);
        }
		
		#$GLOBALS['trace'] = 0;
		
	}
	
	
	
	/***********************************************************************************
	 * Helper methods
	 ***********************************************************************************/
	
	/**
	 * Removes stock counts for deleted articles which are still stored to session.
	 * 
	 * TODO this method is processed for each article in cart, although it would be 
	 * enough to process it only once. Perhaps find a better way to implement this.
	 *
	 * @param  tx_ptgsashop_pi1     $piInstance    Instance of plugin, hook is written for
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-10-16
	 */
	protected function removeDeletedArticlesFromStockCount($piInstance) {
		$sessionStockCountArr = tx_ptgsastock_div::getSessionStockcount();
		$newSessionStockCountArr = array();
		foreach ($sessionStockCountArr as $articleUid => $stockCount) {
			if (!$piInstance->cartObj->getItem($articleUid)) {
				$articleExtensionUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($articleUid);
	            $articleExtensionObj = new tx_ptgsastock_stock_articleextension($articleExtensionUid);
	            $articleExtensionObj->increaseStockBy($stockCount);
	            $articleExtensionObj->decreaseTempStockCountBy($stockCount);
			} else {
				$newSessionStockCountArr[$articleUid] = $stockCount;
			}
		}
		tx_ptgsastock_div::writeSessionStockCount($newSessionStockCountArr);
	}
	
	
	
	/**
	 * Changes article stock count for a given article object
	 * by comparing the quantity given in the object by the quantity
	 * set in the session and changing the stock count by the difference
	 * between those two values.
	 *
	 * @param  tx_ptgsashop_article     $articleObj    Article object to change stock count for
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-10-16
	 */
	protected function processQuantityChange($articleObj) {
		$articleUid = $articleObj->get_id();
        $newArticleQuantity = $articleObj->get_quantity();
		$oldArticleQuantity = $this->getSessionArticleStockCountByArticleId($articleUid);
        
		if ($newArticleQuantity != $oldArticleQuantity) {
			$articleCountDiff = $newArticleQuantity - $oldArticleQuantity;
			$articleExtensionUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($articleUid);
		    $articleExtensionObj = new tx_ptgsastock_stock_articleextension($articleExtensionUid);
		    $articleExtensionObj->decreaseStockBy($articleCountDiff);
            $articleExtensionObj->increaseTempStockCountBy($articleCountDiff);	
            $this->setSessionArticleStockCountByArticleId($articleUid, $newArticleQuantity);
		}

	}
	
	
	
	/**
	 * Returns stock count for a given article uid stored in session.
	 *
	 * @param  int         $articleUid     Article UID to get session stock count for
	 * @return float                       Stock count for article from session
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-10-16
	 */
	protected function getSessionArticleStockCountByArticleId($articleUid) {
		$sessionStockCountArr = tx_ptgsastock_div::getSessionStockcount();
		if (array_key_exists($articleUid, $sessionStockCountArr)) {
			return $sessionStockCountArr[$articleUid];
		} else {
			return 0;
		}
	}
	
	
	
	/**
	 * Writes a stock count for an article to session.
	 *
	 * @param  int         $articleUid     UID of article to write stock count for
	 * @param  float       $stockCount     Stock count of article to write to session
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-16
	 */
	protected function setSessionArticleStockCountByArticleId($articleUid, $stockCount) {
		$sessionStockCountArr = tx_ptgsastock_div::getSessionStockcount();
		$sessionStockCountArr[$articleUid] = $stockCount;
		tx_ptgsastock_div::writeSessionStockCount($sessionStockCountArr);
	}
	
	
	
}

?>