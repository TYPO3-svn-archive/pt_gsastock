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
 * Implementation of a stock calculator
 *
 * $Id: class.tx_ptgsastock_defaultStockCalculator.php,v 1.1 2009/04/01 13:16:42 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-31
 * 
 */ 



/**
 * Inclusion of required classes
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/abstract/class.tx_ptgsastock_iStockCalculator.php';



/**
 * Implementation of a stock calculator
 * 
 * Holds a bunch of hook methods called from pt_gsashop if checkout is processed
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package TYPO3
 * @subpackage pt_gsastock
 * @since 2009-03-31
 */
class tx_ptgsastock_defaultStockCalculator implements tx_ptgsastock_iStockCalculator {


	
	/**
	 * Holds a reference to a exec_checkout_articleDataCalculator object
	 * 
	 * @var tx_ptgsastock_iExec_checkout_articleDataHook
	 */
	protected $exec_checkout_articleDataCalculator = null;
	
	
	
	/**
	 * Holds a reference to a processPostOrderProcessingCalculator object
	 * 
	 * @var tx_ptgsastock_iProcessPostOrderProcessingHook
	 */
	protected $processPostOrderProcessingCalculator = null;
	
	
	
	/**
	 * Holds a reference to a updateArtDistrQtyChangesConsequences object
	 * 
	 * @var tx_ptgsastock_iUpdateArtDistrQtyChangesConsequencesHook
	 */
	protected $updateArtDistrQtyChangesConsequences = null;
	
	
	
	/**
	 * Initializes calculator objects for stock calculation
	 * 
	 * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
	 */
	protected function initCalculators() {
		
        if (is_null($this->exec_checkout_articleDataCalculator)) {
            $this->loadCalculatorFromTs(
                'config.tx_ptgsastock.exec_checkout_articleDataHookClass',
                $this->exec_checkout_articleDataCalculator,
                'tx_ptgsastock_iExec_checkout_articleDataHook'
            );
        }
        
        if (is_null($this->processPostOrderProcessingCalculator)) {
        	$this->loadCalculatorFromTs(
        	   'config.tx_ptgsastock.processPostOrderProcessingHookClass',
        	   $this->processPostOrderProcessingCalculator,
        	   'tx_ptgsastock_iProcessPostOrderProcessingHook'
        	);
        }
        
        if (is_null($this->updateArtDistrQtyChangesConsequences)) {
        	$this->loadCalculatorFromTs(
        	   'config.tx_ptgsastock.updateArtDistrQtyChangesConsequencesHookClass',
        	   $this->updateArtDistrQtyChangesConsequences,
        	   'tx_ptgsastock_iUpdateArtDistrQtyChangesConsequencesHook'
        	);
        }
		
	}
	
	
	
	/**
	 * Tries to load an object for a given TS configuration
	 * 
	 * @param   string     $calculatorTsConf       TS variable that holds user func for object to be loaded
	 * @param   mixed      $localVar               Object property to set object to
	 * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
	 */
	protected function loadCalculatorFromTs($calculatorTsConf, &$localVar, $requiredInterface) {
		
		$tsUserFunc = tx_pttools_div::getTS($calculatorTsConf);
        tx_pttools_assert::isNotEmptyString(
            $tsUserFunc, 
            array('message' => 'Set ' . $calculatorTsConf . ' in your TS (perhaps forgot to load static template?)!')
        );
        $localVar = t3lib_div::getUserObj($tsUserFunc);
        tx_pttools_assert::isInstanceOf(
            $localVar,
            $requiredInterface,
            array('message' => 'Returned object must implement ' . $requiredInterface)
        );
		
	}
	
	
	
    
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
    public function exec_checkout_articleDataHook($cartObj, $articleObj) {
    	
    	t3lib_div::devLog('In ' . __METHOD__ , 'tx_ptgsastock', 1, array('params' => array('$cartObj' => $cartObj, '$articleObj' => $articleObj)));
    	$this->initCalculators();
    	
    	/* Delegate calculation to strategy object */
    	$this->exec_checkout_articleDataCalculator->exec_checkout_articleDataHook($cartObj, $articleObj);
    	
    }
    
    
    
    
    /**
     * Calculates article stock data, if order is submitted
     * 
     * @param   array   $params     Array of parameters passed to hook method
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function processPostOrderProcessingHook($params) {
    	
    	t3lib_div::devLog('In ' . __METHOD__ , 'tx_ptgsastock', 1, array('$params' => array('$params' => $params)));
    	$this->initCalculators();
    	
    	/* Delegate calculation to strategy object */
    	$this->processPostOrderProcessingCalculator->processPostOrderProcessingHook($params);
    	
    }
    
    
    
    /**
     * Process stock changes after delivery has been distributed
     * 
     * @param      tx_ptgsashop_pi3            $gsaShopPi3     Plugin object calling this hook
     * @param      int                         $articleId      UID of current article
     * @param      float                       $artTotalQty    Quantity of article after distribution
     * @return     void
     * @author     Michael Knoll <knoll@punkt.de>
     * @since      2009-04-01
     */
    public function updateArtDistrQtyChangesConsequencesHook($gsaShopPi3, $articleId, $artTotalQty) {
    	
    	t3lib_div::devLog('In ' . __METHOD__ , 'tx_ptgsastock', 1, array('params' => array('$orderObj' => $orderObj, '$articleId' => $articleId, '$artTotalQty' => $artTotalQty)));
    	$this->initCalculators();
    	
    	/* Delegate calculation to strategy object */
    	$this->updateArtDistrQtyChangesConsequences->updateArtDistrQtyChangesConsequencesHook($gsaShopPi3, $articleId, $artTotalQty);
    	
    }
    
    

}
 


?>