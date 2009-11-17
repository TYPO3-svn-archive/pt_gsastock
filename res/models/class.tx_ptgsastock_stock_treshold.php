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
 * Row class definition file for gsa_stock_treshold
 *
 * $$Id: class.tx_ptgsastock_stock_treshold.php,v 1.3 2009/03/30 15:05:24 ry21 Exp $$
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-02-18
 */ 



/**
 * Include required ressources
 */
require_once t3lib_extMgm::extPath('pt_objectstorage') . '/res/abstract/class.tx_ptobjectstorage_ptRowObject.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/renderers/class.tx_ptgsastock_stockTresholdRenderer.php';



 
/**
 * Row class for gsa_stock_treshold
 * 
 * @author Michael Knoll <knoll@punkt.de>
 *
 */
class tx_ptgsastock_stock_treshold extends tx_ptobjectstorage_ptRowObject {

	
	
	/**
	 * Holds a reference to a associated stock status row object
	 * @var tx_ptgsastock_stock_status
	 */
	protected $stockStatusObj = null;
	
	
	
	/**
	 * Holds a reference to a treshold renderer object
	 * @var tx_ptgsastock_stockTresholdRenderer
	 */
	protected $renderer = null;
	
	
	
	/**
	 * Constructor for treshold object
	 * @param 	$tresholdUid
	 * @return 	void
 	 * @author  Michael Knoll <knoll@punkt.de>
 	 * @since   2009-03-18
	 */
	public function __construct($tresholdUid = 0) {

		$this->tableName = 'tx_ptgsastock_stock_treshold';
		parent::__construct(array('uid' => $tresholdUid));
		
		$this->initStatusTreshold();
		
	}
	
	
	
	/**
	 * Loads the corresponding stock status row object from the database
	 * 
	 * @return void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	protected function initStatusTreshold() {
		if ($this['stock_status'] > 0) {
			$this->stockStatusObj = new tx_ptgsastock_stock_status($this['stock_status']);
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
			'deleted',
			'hidden',
			'lower_bound',
			'upper_bound',
			'stock_status',
			'description'
		);
		
	}
	
	
	
	/**
	 * Returns the corresponding stock status row object or NULL if none exists
	 * 
	 * @return   mixed     Stock status row object or NULL
     * @author   Michael Knoll <knoll@punkt.de>
     * @since    2009-03-19
	 */
	public function getStockStatusObj() {
		return $this->stockStatusObj;
	}
	
	
	
	/**
	 * Renders treshold information via rendering object set in constructor
	 * 
	 * @param  tx_ptgsastock_articleextension  $article    Article to render treshold information for
	 * @return string                                      Treshold information rendered for article
	 * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-20
	 */
	public function renderStockInformation($article) {
		
		$this->initRenderer();
		$stockInformation = $this->renderer->renderStockInformation($this, $article);
		return $stockInformation;
		
	}
	
	
	
	/**
	 * Inits renderer object from TS
	 * 
	 * @return void   
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-30
	 */
	protected function initRenderer() {
		
		/* Init renderer if not yet done */
		if (is_null($this->renderer)) {
			$rendererClassUserFunc = tx_pttools_div::getTS('config.tx_ptgsastock.tresholdRendererClass');
	        tx_pttools_assert::isNotEmptyString(
	            $rendererClassUserFunc, 
	            array('message' => 'Set config.tx_ptgsastock.tresholdRendererClass in your TS (perhaps forgot to load static template?)!')
	        );
	        $this->renderer = t3lib_div::getUserObj($rendererClassUserFunc);
	        tx_pttools_assert::isInstanceOf(
	            $this->renderer, 
	            'tx_ptgsastock_iStockTresholdRenderer',
	            array('message' => 'Returned object must implement tx_ptgsastock_iStockTresholdRenderer')
	        );
		}
		
	}
	
	
	
}
 
 
 ?>