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
 * Class definition file for update article distribution hook for stock extension
 * 
 * @version     $Id: class.tx_ptgsastock_updateArtDistrQtyChangesConsequencesHook.php,v 1.3 2009/11/17 15:40:20 ry21 Exp $
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-16
 */ 



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/abstract/class.tx_ptgsastock_iUpdateArtDistrQtyChangesConsequencesHook.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock_articleextension.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/staticlib/class.tx_ptgsastock_div.php';

require_once t3lib_extMgm::extPath('pt_gsashop') . 'pi3/class.tx_ptgsashop_pi3.php';



/**
 * Class implementing hook for processing changes in article distribution quantity.
 * 
 * @package     Typo3
 * @subpackage  pt_gsastock
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-16
 */
class tx_ptgsastock_updateArtDistrQtyChangesConsequencesHook extends tx_ptgsashop_pi3 implements tx_ptgsastock_iUpdateArtDistrQtyChangesConsequencesHook {
	
	
	
	/**
	 * Process stock changes after delivery has been distributed
	 * 
	 * @param      tx_ptgsashop_pi1            $gsaShopPi3     Plugin object calling this hook
	 * @param      int                         $articleId      UID of current article
	 * @param      float                       $artTotalQty    Quantity of article after distribution
	 * @return     void
	 * @author     Michael Knoll <knoll@punkt.de>
	 * @since      2009-04-01
	 */
	public function updateArtDistrQtyChangesConsequencesHook($gsaShopPi3, $articleId, $artTotalQty) {
		
		$articleCollection = $gsaShopPi3->orderObj->getCompleteArticleCollection(); /* @var $articleCollection tx_ptgsashop_articleCollection */
		$processedArticle = $articleCollection->getItem($articleId); /* @var $processedArticle tx_ptgsashop_article */
		$oldArticleQuantity = $processedArticle->get_quantity();
		$quantityDiff = $artTotalQty - $oldArticleQuantity;
		
	    if (tx_ptgsastock_stock_articleextension::existsArticleextensionForBaseArticle($articleId)) {
            $articleExtensionUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($articleId);
            $articleExtensionObj = new tx_ptgsastock_stock_articleextension($articleExtensionUid);
            $articleExtensionObj->decreaseStockBy($quantityDiff);
            $articleExtensionObj->increaseTempStockCountBy($quantityDiff);
            
            // Update session information for article
            $sessionStockCount = tx_ptgsastock_div::getSessionStockcount();
            $sessionStockCount[$articleId] = $artTotalQty;
            tx_ptgsastock_div::writeSessionStockCount($sessionStockCount);
        }
		
	}
	
	
	
}

?>