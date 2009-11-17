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
 * Interface definition for stock calculation
 *
 * $Id: class.tx_ptgsastock_iStockCalculator.php,v 1.1 2009/04/01 13:16:42 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-31
 * 
 */ 




/**
 * Interface for stock calculation
 * 
 * Interface holds a set of hook methods that need to be implemented for stock calculation. 
 * The class to be used for stock calculation can be set in TS via
 * 
 * config.tx_ptgsastock.stockCalculatorClass = EXT:<your_extension>/<path_to_class>/<class_name>
 * 
 * Make sure, your class implements this interface!
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package TYPO3
 * @subpackage pt_gsastock
 * @since 2009-03-31
 */
interface tx_ptgsastock_iStockCalculator {

    
    
    /**
     * Calculates article stock data, if checkout is processed
     *
     * @todo check given parameters
     * 
     * @param   tx_ptgsashop_pi1        $cartObj        Instance of cart that is checked out
     * @param   tx_ptgsashop_article    $articleObj
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function exec_checkout_articleDataHook($cartObj, $articleObj);
    
    
    
    
    /**
     * Calculates article stock data, if order is submitted
     * 
     * @param   array   $params     Array of parameters passed to hook method
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function processPostOrderProcessingHook($params);
    
    
    
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
    public function updateArtDistrQtyChangesConsequencesHook($gsaShopPi3, $articleId, $artTotalQty);
    

}
 


?>