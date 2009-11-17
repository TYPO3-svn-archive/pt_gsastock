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
 * Row class definition file for tx_ptgsastock_stock_treshold_set
 *
 * $ID:$
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-18
 */ 



/**
 * Include required ressources
 */
require_once t3lib_extMgm::extPath('pt_objectstorage') . '/res/abstract/class.tx_ptobjectstorage_ptRowObject.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . '/res/models/class.tx_ptgsastock_stock_treshold.php';


 
/**
 * Row class for gsa_stock_treshold_set
 * 
 * A treshold set holds a set of tresholds identified by quantities. 
 * Each treshold has a lower and a upper bound and a status.
 * 
 * @author Michael Knoll <knoll@punkt.de>
 * @since 2009-03-19
 * @package TYPO3
 * @subpackage pt_gsastock
 *
 */
class tx_ptgsastock_stock_treshold_set extends tx_ptobjectstorage_ptRowObject {

	
	
	/**
	 * Holds an array of depending treshold objects
	 * @var array
	 */
	protected $tresholdsArray;
	
	
	
	/**
	 * Constructor for treshold object
	 * @param 	$tresholdUid
	 * @return 	void
 	 * @author  Michael Knoll <knoll@punkt.de>
 	 * @since   2009-03-18
	 */
	public function __construct($tresholdSetUid = 0) {

		tx_pttools_assert::isValidUid($tresholdSetUid, array('Message' => '$tresholdSetUid should be integer but was ' . $tresholdSetUid));
		$this->tableName = 'tx_ptgsastock_stock_treshold_set';
		parent::__construct(array('uid' => $tresholdSetUid));
		
		if ($tresholdSetUid > 0) {
			$this->initTresholds();
		}
		
	}
	
	
	
	/**
	 * Initializes depending tresholds from database
	 * 
	 * @return void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-18
	 */
	protected function initTresholds() {
		
		tx_pttools_assert::isValidUid(intval($this['uid']));
		
	    /* Select treshold rows from database */
        $rows = array();
        $select = 'uid_foreign';
        $from = 'tx_ptgsastock_stock_treshold_set_stock_treshold_mm';
        $where = 'uid_local = ' . intval($this['uid']);
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
        tx_pttools_assert::isMySQLRessource($res);
        
        /* Initialize treshold objects */
        while($a_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $this->tresholdsArray[] = new tx_ptgsastock_stock_treshold($a_row['uid_foreign']);
        }
		
	}
	
	
	
	/**
	 * 
	 * @see res/objects/tx_pttools_rowObject#setAvailableFields()
	 */
	protected function setAvailableFields() {
		
		$this->availableFieldsArray = array(
			'uid',
			'pid',
			'tstamp',
			'crdate',
			'cruser_id',
			'description',
			'stock_treshold'
		);
		
	}
	
	
	
	/**
	 * Returns a treshold for a given quantity or null, if
	 * no treshold is set for the quantity in the treshold set
	 * 
	 * The rules for calculating the treshold are as follows:
	 * 
	 * lower_bound <= quantity < upper_bound OR
	 * lower_bound == '' && quantity < uppder_bound OR
	 * lower_Bound <= quantity && upper_bound == ''
	 * 
	 * @param $quantity
	 * @return unknown_type
	 */
	public function getTresholdForQuantity($quantity) {
		
		$tresholdForQuantity = null;
		
		foreach ($this->tresholdsArray as $treshold) {
			/* @var tx_ptgsastock_stock_treshold $treshold */
			$lowerBound = $treshold->get_lower_bound();
			$upperBound = $treshold->get_upper_bound();
			if ( ( ($lowerBound != '' && $upperBound != '') && $lowerBound <= $quantity && $quantity < $upperBound ) ||
			     ( ($lowerBound == '' && $upperBound != '') &&                             $quantity < $upperBound ) ||
			     ( ($lowerBound != '' && $upperBound == '') && $lowerBound <= $quantity                            )
			)
			{
				$tresholdForQuantity = $treshold; 
			}
		}
		
		return $tresholdForQuantity;
		
	}
	
	
	
}
 
 
 ?>