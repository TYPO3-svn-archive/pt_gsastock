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
 * Class definition file for tx_ptgsastock_stock_status row objects
 *
 * $Id: class.tx_ptgsastock_stock_status.php,v 1.3 2009/03/30 15:05:24 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-18
 */ 



/**
 * Inclusion of required files
 */
require_once t3lib_extMgm::extPath('pt_tools') . '/res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_objectstorage') . '/res/abstract/class.tx_ptobjectstorage_ptRowObject.php';



/**
 * Row object class for tx_ptgsastock_stock_status
 * 
 * @package TYPO3
 * @subpackage pt_gsastock
 * @author Michael Knoll
 * @since  2009-03-18
 *
 */
class tx_ptgsastock_stock_status extends tx_ptobjectstorage_ptRowObject {
	
	
	
	/**
	 * Holds a reference to a status renderer object
	 * @var tx_ptgsastock_stockStatusRenderer
	 */
	protected $renderer = null;
	
	
	
	/**
	 * Constructor for stock status row object
	 * 
	 * @param  int     $stockStatusUid
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-03-30
	 */
	public function __construct($stockStatusUid) {
		
		tx_pttools_assert::isValidUid($stockStatusUid, array('message' => '$stockStatusUid must be an integer but was ' . $stockStatusUid));
		
		$this->tableName = 'tx_ptgsastock_stock_status';
		parent::__construct(intval($stockStatusUid));
		
	}
	
	
	
	/**
	 * Overwrite method for setting fields of current row object
	 * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-30
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
			'name',
			'hint',
			'image',
			'use_arcticle_stock_info'
		);
	}
	
	
	
	/**
	 * Renders stock information 
	 * 
	 * @param      tx_ptgsashop_baseArticle    $article    Article object to render information for
	 * @return     string  
     * @author     Michael Knoll <knoll@punkt.de>
     * @since      2009-03-30                                Rendered information for article
	 */
	public function renderStockInformation($article) {
		
		$this->initRenderer();
		return $this->renderer->renderStockInformation($this, $article);
		
	}
	
	
	
	/**
	 * Initializes the renderer from TS
	 *  
	 * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-30
	 */
	protected function initRenderer() {
		
	    /* Initialize renderer (this has to be done here, as there is no TS available in Backend!) */
        if (is_null($this->renderer)) {
            $rendererClassUserFunc = tx_pttools_div::getTS('config.tx_ptgsastock.statusRendererClass');
            tx_pttools_assert::isNotEmptyString(
                $rendererClassUserFunc, 
                array('message' => 'Set config.tx_ptgsastock.statusRendererClass in your TS (perhaps forgot to load static template?)!')
            );
            $this->renderer = t3lib_div::getUserObj($rendererClassUserFunc);
            tx_pttools_assert::isInstanceOf(
                $this->renderer, 
                'tx_ptgsastock_iStockStatusRenderer',
                array('message' => 'Returned object must implement tx_ptgsastock_iStockStatusRenderer')
            );
        }
		
	}
	
	
	
}


 
?>