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
 * Frontend Plugin 'GSA Stock: Stock display' for the 'pt_gsastock' extension.
 *
 * $Id: class.tx_ptgsastock_hooks_ptgsashop_displayArticleInfobox_MarkerArrayHook.php,v 1.7 2009/10/16 15:06:19 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-10-02
 */ 



/**
 * Inclusion of parent class
 */
require_once t3lib_extMgm::extPath('pt_gsashop').'pi2/class.tx_ptgsashop_pi2.php';



/**
 * Inclusion of helper classes
 */
require_once t3lib_extMgm::extPath('pt_gsastock').'res/staticlib/class.tx_ptgsastock_div.php';
require_once t3lib_extMgm::extPath('pt_gsastock'). 'res/models/class.tx_ptgsastock_stock_articleextension.php';



/**
 * Provides stocking information for article display in gsa shop
 *
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-02-10
 * @package     TYPO3
 * @subpackage  tx_ptgsastock
 */
class tx_ptgsastock_hooks_ptgsashop_displayArticleInfobox_MarkerArrayHook extends tx_ptgsashop_pi2 {
	
	
	
	/**
     * tslib_pibase (parent class) instance variables
     */
    public $extKey = 'pt_gsastock';    // The extension key.
    public $prefixId = 'tx_ptgsastock_pi1';    // Same as class name
    public $scriptRelPath = 'pi1/class.tx_ptgsastock_pi1.php';    // Path to this script relative to the extension dir.
    
    
    
    /*************************************************************
     * Hook methods
     *************************************************************/
    
    
    
    /**
     * Hook functionality for setting Stock information in ArticleInfobox Marker Array
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-02-11
     * @param  $articleInfoboxPlugin			 tx_ptgsashop_pi2           Caller class of hook function
     * @param  $markerArray	                 array                      Array of smarty variables for smarty template
     * @param  $articleObj	                 tx_ptgsashop_baseArticle   Holds the article object for which to show stock information
     * @return array						Changed array of smarty variable for smarty template 
     */
    public function displayArticleInfobox_MarkerArrayHook($articleInfoboxPlugin, $markerArray, $articleObj) {
        $articleId = $articleObj->get_id();
    	$extensionArticleUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($articleId);
    	if ($extensionArticleUid > 0) {
	        $stockArticle = new tx_ptgsastock_stock_articleextension($extensionArticleUid);
	        $markerArray['articleStockInformation'] = $stockArticle->renderStockInformation();
    	}
    	return $markerArray;
    }
    
    
    
}
?>