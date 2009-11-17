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
 * Class definition file for stock counting records in the pt_gsastock extension
 *
 * $Id: class.tx_ptgsastock_stockcount.php,v 1.4 2009/04/01 14:43:04 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-18
 */ 



/**
 * Inclusion of helper classes
 */
require_once t3lib_extMgm::extPath('pt_tools') . '/res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_objectstorage') . '/res/abstract/class.tx_ptobjectstorage_ptRowObject.php';



/**
 * Class definition for stock count records.
 *
 * Stock count records are temporary records for storing article stock counts
 * that are not yet booked into the article record.
 * 
 * @author Michael Knoll <knoll@punkt.de>
 * @package Typo3
 * @subpackage tx_ptgsastock
 * @since 2009-03-18
 */
class tx_ptgsastock_stockcount extends tx_ptobjectstorage_ptRowObject {

	

	/**
	 * Constructor for tx_ptgsastock_stockcount row object
	 * 
	 * @param    int     $uid    UID of record
	 * @return   void  
     * @author   Michael Knoll <knoll@punkt.de>
     * @since    2009-03-18  
	 */
	public function __construct($stockCountUid = 0) {
		
		tx_pttools_assert::isNumeric($stockCountUid, array('message' => '$stockCountUid must be int but was ' . $stockCountUid));
		$this->tableName = 'tx_ptgsastock_stockcount';
		parent::__construct(array('uid' => $stockCountUid));
		
	}
	
	
	
    /**
     * Validate values for quantity property
     * 
     * @param 	float	$qty	Quantity of stock count
     * @return 	void
     * @author Michael Knoll <knoll@punkt.de>
     * @since 2009-03-18
     */
    public function validate_qty($qty) {
    	
    	tx_pttools_assert::isNumeric($qty, array('message' => 'Qunatity $qty must be numeric but was ' . $qty));
    	
    }

    
    
    /**
     * Increases temporary stock count by given quantity
     * 
     * @param   float   $quantity       Quantity to increase stock count by
     * @return  float                   Current stock count quantity
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-18
     */
    public function increaseStockBy($quantity) {
    	
    	$this['qty'] = $this['qty'] + $quantity;
    	return $this['qty'];
    	
    }
    
    
    
    /**
     * Decreases temporary stock count by given quantity
     * 
     * @param   float   $quantity       Quantity to decrease stock count by
     * @return  float                   Current stock count quantity
     * @author Michael Knoll <knoll@punkt.de>
     * @since 2009-03-18
     */
    public function decreaseStockBy($quantity) {
    	
    	return $this->increaseStockBy(-$quantity);
    	
    }
    
    
    
    /**
     * Template method for setting available fields for row object
     * 
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since 2009-03-18
     */
    protected function setAvailableFields() {
        
    	$this->availableFieldsArray = array(
            'uid',
			'pid',
			'tstamp',
			'crdate',
			'cruser_id',
			'order_state',
			'qty',
			'artikel_nummer'
        );
        
    }
    
    
}
 
 ?>