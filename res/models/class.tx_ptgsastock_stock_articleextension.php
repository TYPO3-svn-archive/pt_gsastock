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
 * Row class definition file for tx_ptgsastock_stock_articleextension
 *
 * $ID:$
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-19
 */ 



/**
 * Include required ressources
 */
require_once t3lib_extMgm::extPath('pt_objectstorage') . 'res/abstract/class.tx_ptobjectstorage_ptRowObject.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock_treshold_set.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock_status.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock.php';


 
/**
 * Row class for gsa_stock_treshold_articleextension
 * 
 * Extension of GSA Article to cover stock management functionality. Main business logic
 * for stock management goes here like:
 * - Getting treshold sets for articles
 * - Calculating actual stock for article
 * 
 * @author Michael Knoll <knoll@punkt.de>
 * @since 2009-03-19
 * @package TYPO3
 * @subpackage pt_gsastock
 *
 */
class tx_ptgsastock_stock_articleextension extends tx_ptobjectstorage_ptRowObject {
	
	
	
	/*************************************************************
     * Properties
     *************************************************************/
    
    
	
	/**
	 * Holds a reference to a treshold set row object
	 * @var tx_ptgsastock_stock_treshold_set
	 */
	protected $tresholdSetObj = null;
	
	
	
	/**
	 * Holds a reference to a stock status row object
	 * @var tx_ptgsastock_stock_status
	 */
	protected $stockStatusObj = null;
	
	
	
	/**
	 * Holds a reference to a article object to handle stock for current article (sum article)
	 * @var tx_ptgsastock_stock_articleextension
	 */
	protected $stockArticleObj = null;
	
	
	
	/**
	 * Holds a reference to a gsa article object
	 * @var tx_ptgsashop_article
	 */
	protected $baseArticleObj = null;
	
	
	
	/**
	 * Holds a reference to article stock information renderer
	 * @var tx_ptgsastock_stockArticleRenderer
	 */
	protected $renderer;
	
	
	
	/**
	 * Holds a reference to a stock count object for current article
	 * @var tx_ptgsastock_stockcount
	 */
	protected $stockCountObj = null;
	
	
	
	/*************************************************************
     * Constructor
     *************************************************************/
	
	
	
	/**
	 * Constructor for articleextension for stock management
	 * 
	 * @param  int     $articleExtensionUid    UID of articleextension record (optional)
	 * @param  int     $baseArticleUid         UID of base article record (optional)
	 * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-19
	 */
	public function __construct($articleExtensionUid = 0, $baseArticleUid = 0) {

		tx_pttools_assert::isNumeric($articleExtensionUid);
		tx_pttools_assert::isNumeric($baseArticleUid);
		
		$this->tableName = 'tx_ptgsastock_stock_articleextension';
        parent::__construct($articleExtensionUid);
		
        if ($baseArticleUid > 0) {
			$this['base_article'] = $baseArticleUid;
		}
		
		$this->initProperties();
		
	}
	
	
	
	/*************************************************************
     * Initialization methods (constructor helpers)
     *************************************************************/
	
	
	
	/**
	 * Init properties of object
	 * 
	 * @return  void 
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	protected function initProperties() {

		/* Try to load base article */
		if ($this['base_article'] != '') {
			$this->baseArticleObj = new tx_ptgsashop_article($this['base_article']);
		} else {
			throw new tx_pttools_exceptionInternal('No base article set for articleextension!');
		}
		
		/* Try to load stock status object */
		if ($this['stock_status'] > 0) {
			tx_pttools_assert::isValidUid($this['stock_status'], array('message' => "Stock status is no valid UID"));
			$this->stockStatusObj = new tx_ptgsastock_stock_status($this['stock_status']);
		}
		
		/* Try to load stock article object (article to calculate stock on) */
		if ($this['stock_article'] > 0) {
			tx_pttools_assert::isValidUid($this['stock_article']);
			$this->stockArticleObj = new tx_ptgsastock_stock($this['stock_article']);
		}
		
		/* Init treshold set (complicate mechanism) */
        $this->initTresholdSet();
        
	}
	
	
	
	/**
	 * Business logic for setting treshold set for article
	 * 
	 * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	protected function initTresholdSet() {
		
		/* Exists a treshold set for current article? */
		if ($this['stock_treshold_set'] != '' && $this['stock_treshold_set'] > 0) {
            tx_pttools_assert::isValidUid($this['stock_treshold_set']);
			$this->tresholdSetObj = new tx_ptgsastock_stock_treshold_set($this['stock_treshold_set']);
		} 
		
		/* @todo Implement TS configuration for stock management */
		
	}
	
	
	
	/**
	 * Initialize renderer object from TS
	 * 
	 * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-30
	 */
	protected function initRenderer() {
		
		/* Initialize renderer if not yet done */
		if (is_null($this->renderer)) {
	        $rendererClassUserFunc = tx_pttools_div::getTS('config.tx_ptgsastock.articleRendererClass');
	        tx_pttools_assert::isNotEmptyString(
	            $rendererClassUserFunc, 
	            array('message' => 'Set config.tx_ptgsastock.articleRendererClass in your TS (perhaps forgot to load static template?)!')
	        );
	        $this->renderer = t3lib_div::getUserObj($rendererClassUserFunc);
	        tx_pttools_assert::isInstanceOf(
	            $this->renderer, 
	            'tx_ptgsastock_iStockArticleRenderer',
	            array('message' => 'Returned object must implement tx_ptgsastock_iStockArticleRenderer')
	        );
		}
		
	}
	
	
	
	/*************************************************************
     * Row object methods (getters / setters etc.)
     *************************************************************/
    
    
	
	/**
	 * Template method for setting fields of database table
	 * 
	 * @return void 
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	protected function setAvailableFields() {
		
		$this->availableFieldsArray = array(
			'uid',
			'pid',
			'tstamp',
			'crdate',
			'cruser_id',
			'base_article',
			'stock_article',
			'stock_treshold_set',
			'stock_status',
			'description',
		    'show_stock'
		);
	}
	
	
	
	/**
	 * Returns the base article object
	 * 
	 * @return  tx_ptgsashop_article    Base article of articleextension 
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	public function getBaseArticleObj() {
		return $this->baseArticleObj;
	}
	
	
	
	/**
	 * Returns stock article object
	 * 
	 * @return  tx_ptgsastock_stock_articleextension 
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	public function getStockArticleObj() {
		return $this->stockArticleObj;
	}

	
	
	/**
	 * Returns stock status object
	 * 
	 * @return  tx_ptgsastock_stock_status 
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
	public function getStockStatusObj() {
		return $this->stockStatusObj;
	}
	
	
   
	/**
	 * Returns treshold set object
	 * 
	 * @return  tx_ptgsastock_stock_treshold_set 
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-19
	 */
    public function getTresholdSetObj() {
        return $this->tresholdSetObj;
    }
	
    
    
    /*************************************************************
     * Business logic methods
     *************************************************************/
    
    
    
    /**
     * Increase stock of article by given value
     * 
     * @param   float   $quantity   Quantity to increase stock by
     * @return  float               New total quantity of article
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function increaseStockBy($quantity) {
    	
    	/* If article stock depends on other article, use this one instead */
    	if (!is_null($this->stockArticleObj)) {
    		$newStockCount = $this->stockArticleObj->increaseStockBy($quantity);
    		tx_ptgsastock_stockAccessor::getInstance()->updateStockData($this->stockArticleObj); 
    		return $newStockCount;
    	}
    	
    	$stockObject = new tx_ptgsastock_stock($this['base_article']);
    	$newStockCount = $stockObject->increaseStockBy($quantity);
    	tx_ptgsastock_stockAccessor::getInstance()->updateStockData($stockObject);
    	return $newStockCount;
    	
    } 
    
    
    
    
    /**
     * Decrease stock of article by given value
     * 
     * @param   float   $quantity   Quantity to decrease stock by
     * @return  float               New total quantity of article
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function decreaseStockBy($quantity) {
    	
    	return $this->increaseStockBy(-$quantity);
    	
    }
    
    
    
    /**
     * Increase temporary stockcount of article by given value
     * 
     * @param   float   $quantity   Quantity to increase stock by
     * @return  float               New stockcount quantity of article
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function increaseTempStockCountBy($quantity) {
    	
    	#$GLOBALS['trace'] = 1;
    	
    	/* Use stock article to calculate stock if article uses foreign article for stock calculation */
    	if (!is_null($this->stockArticleObj)) {
    		// TODO ugly...
	    	if (self::existsStockCountRecordForArticle($this['stock_article'])) {
	            $stockCountUid = self::getStockCountUidForArticle($this['stock_article']);
	            $stockArticleStockCountObject = new tx_ptgsastock_stockcount($stockCountUid);
	        } else {
	            $stockArticleStockCountObject = new tx_ptgsastock_stockcount();
	            $stockArticleStockCountObject['artikel_nummer'] = $this['stock_article'];
	        }
	        $updatedQuantity = $stockArticleStockCountObject->increaseStockBy($quantity);
	        $stockArticleStockCountObject->save();
    		return $updatedQuantity;
    	}
    	
        /* Try to load stock count object (temporary stock count for article) */
        if (self::existsStockCountRecordForArticle($this['base_article'])) {
            $stockCountUid = self::getStockCountUidForArticle($this['base_article']);
            $this->stockCountObj = new tx_ptgsastock_stockcount($stockCountUid);
        } else {
            $this->stockCountObj = new tx_ptgsastock_stockcount();
            $this->stockCountObj['artikel_nummer'] = $this['base_article'];
        }
    	tx_pttools_assert::isInstanceOf(
    	    $this->stockCountObj,
    	    'tx_ptgsastock_stockcount', 
    	    array('message' => '$this->stockCountObj should be instance of tx_ptgsastock_stockcount')
    	);
    	$stockCount = $this->stockCountObj->increaseStockBy($quantity);
    	$this->stockCountObj->save();
    	
    	#$GLOBALS['trace'] = 0;
    	
        return $stockCount;
        
    }
    
    
    
    /**
     * Decrease temporary stockcount of article by given value
     * 
     * @param   float   $quantity   Quantity to decrease stock by
     * @return  float               New temporary quantity of article
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-31
     */
    public function decreaseTempStockCountBy($quantity) {
    	
    	return $this->increaseTempStockCountBy(-$quantity);
    	
    }
    
    
    
    /**
     * Renders stock information for article 
     * 
     * @return  string   HTML source of rendered information
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-26
     */
    public function renderStockInformation() {
    	
    	$this->initRenderer();
    	return $this->renderer->renderStockInformation($this);
    	
    }
    
    
	
	/**
	 * Selects an UID of an articleextension record for a given base article UID
	 * 
	 * @param  int     $baseArticleUid     UID of base article record
	 * @return int                         UID of articleextension record
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-19
	 */
	public static function getArticleextensionUidByArticleUid($baseArticleUid) {
		
		tx_pttools_assert::isValidUid($baseArticleUid);
		
        /* Select treshold rows from database */
        $rows = array();
        $select = 'uid';
        $from = 'tx_ptgsastock_stock_articleextension';
        $where = 'base_article = ' . intval($baseArticleUid);
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
        tx_pttools_assert::isMySQLRessource($res);
        
        /* Initialize treshold objects */
        if ($a_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))        
            return $a_row['uid'];
        else
            return 0;
            
	}

	
	
	/**
	 * Checks, whether an extension article exists for a given base article uid
	 * 
	 * @param  int     $baseArticleUid     Uid of base article 
	 * @return bool                        True, if article extension exists
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-19
	 */
	public static function existsArticleextensionForBaseArticle($baseArticleUid) {
		
		if (self::getArticleextensionUidByArticleUid($baseArticleUid) > 0)
		    return true;
		else 
		    return false;
		    
	}
	
	
	
	/**
	 * Selects an UID of a stockcount record for a given article uid
	 * 
	 * @param      int     $baseArticleUid     UID of base article
	 * @return     int                         UID of stockcount record
     * @author     Michael Knoll <knoll@punkt.de>
     * @since      2009-03-31
	 */
	public static function getStockCountUidForArticle($baseArticleUid) {
		
		$select = 'uid';
        $from = 'tx_ptgsastock_stockcount';
        $where = 'artikel_nummer = ' . intval($baseArticleUid);
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
        tx_pttools_assert::isMySQLRessource($res);
        
        /* Initialize treshold objects */
        if ($a_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))        
            return $a_row['uid'];
        else
            return 0;
		
	} 
	
	
	
	/**
	 * Checks, whether a stockcount record exists for a given article uid
	 * 
	 * @param      int     $baseArticleUid     UID of article
	 * @return     bool                        True, if stockcount record exists for article UID
	 */
	public static function existsStockCountRecordForArticle($baseArticleUid) {
		
		if (self::getStockCountUidForArticle($baseArticleUid) > 0) {
			return true;
		} else {		
		  return false;
		} 
		
	}
	
	
}
?>