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
 * Class for stock information objects of gsa shop articles.
 * 
 * The class does not reference its own records in the database but extends the
 * pt_gsashop_article with additional stock information required by pt_gsastock
 *
 * $Id: class.tx_ptgsastock_stock.php,v 1.5 2009/04/01 14:43:04 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-02-16
 */ 
 


/**
 * Inclusion of extension specific resources
 */
require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_article.php';  // GSA Shop abstract base class for articles
require_once t3lib_extMgm::extPath('pt_gsastock').'res/models/class.tx_ptgsastock_stockAccessor.php';



/**
 * Inclusion of external resources
 */
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general helper library class



/**
 * Class for extended gsa_article with stock information
 *
 * @todo ADD functionality to store stock information to database (only update, no insert, since article needs to be existent!)
 * @author Michael Knoll <knoll@punkt.de>
 * @package TYPO3
 * @subpackage pt_gsastock
 * @since 2009-02-16
 */
class tx_ptgsastock_stock {

	
	
	/**
	 * UID of gsa_article
	 * @var int
	 */
	protected $articleUid;
	
	
	
	/**
	 * @var tx_ptgsashop_baseArticle Reference to pt_gsashop_article
	 */
	protected $baseArticle;
	
	
	
	/**
	 * Article "BESTAND" is stock count of article
	 * @var float
	 */
	protected $bestand;
	
	
	
	/**
	 * Article "RESERVIERT" is number of articles that are used on non-finished orders
	 * @var float
	 */
	protected $reserviert;
	
	
	
	/**
	 * Article "VERFUEGBAR" is available number of articles
	 * @var float
	 */
	protected $verfuegbar;
	
	
	
	/**
	 * Article "MINDEST" is minimum number of articles
	 * @var float
	 */
	protected $mindest;
	
	
	
	/**
	 * Article "MELDE" is stock count for which a message is dropped to shop owner in GS-Auftrag (does not work in shop yet!)
	 * @todo use this field for stock information message
	 * @var float
	 */
	protected $melde;
	
	
	
	/**
	 * Constructor for stock information obejct
	 * 
	 * @param $gsaUid			UID of gsa_article
	 * @param $articleObj		article Object to be connected with stock information
	 * @return void		
	 */
	public function __construct($gsaUid, $articleObj = null) {
        tx_pttools_assert::isNumeric($gsaUid, array('message' => '__construct $gsaUid should be integer but was ' . $gsaUid));
        tx_pttools_assert::isTrue($gsaUid > 0, array('message' => '__construct $gsaUid should be > 0 but was ' . $gsaUid));
		$this->articleUid = $gsaUid;
        
		/* Load basic data from database */
        $this->setStockBasicData();
        
        /* Set connected article object */
        if (is_null($articleObj)) {
        	$this->baseArticle = new tx_ptgsashop_article($articleUid);
        }
        elseif (is_a($articleObj, 'tx_ptgsashop_baseArticle') || is_subclass_of($articleObj, 'tx_ptgsashop_baseArticle')) {
        	$this->baseArticle = $articleObj;
        } else {
        	throw new tx_pttools_exceptionInternal('$articleObj is no subclass of tx_ptgsashop_baseArticle', 3, '$articleObj is no subclass of tx_ptgsashop_baseArticle');
        }
        
	}
	
	
	
	/**
	 * Sets properties from database for a given article_uid
	 *
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since 2009-02-16
	 */
	protected function setStockBasicData() {
		
		$stockDataArray = tx_ptgsastock_stockAccessor::getInstance()->selectStockData($this->articleUid);
        if (!is_array($stockDataArray)) {
            throw new tx_pttools_exception('No valid stock data found', 3,
                                           'tx_ptgsashop_articleAccessor::getInstance()->selectArticleData('.$this->id.') did not return any data.');
        }
                
        if (!is_null($stockDataArray['BESTAND']))     		$this->bestand = (float)$stockDataArray['BESTAND'];
        if (!is_null($stockDataArray['RESERVIERT']))     	$this->reserviert = (float)$stockDataArray['RESERVIERT'];
        if (!is_null($stockDataArray['VERFUEGBAR']))     	$this->verfuegbar = (float)$stockDataArray['VERFUEBAR'];
        if (!is_null($stockDataArray['MINDEST']))     		$this->mindest = (float)$stockDataArray['MINDEST'];
        if (!is_null($stockDataArray['MELDE']))     		$this->melde = (float)$stockDataArray['MELDE'];
	}
	
	
	
	/* ***************************************************************************
	 * Getters and setters
	 * ***************************************************************************/
	
	
	
	/**
	* Sets the bestand value
	* @param float $bestand		Value for bestand
	* @since 2009-02-16
	* @author Michael Knoll <knoll@punkt.de>
	*/
	public function set_bestand($bestand) {
		tx_pttools_assert::isNumeric($bestand, array('message' => '$bestand should be numeric but was ' . $bestand));
	    $this->bestand = $bestand;
	}
	
	
	
	/**
	* Returns the bestand value.
	* @return float
	* @since 2009-02-16
	* @author Michael Knoll <knoll@punkt.de>
	*/
	public function get_bestand() {
	    return $this->bestand;
	}
	
	
	
	/**
	 * Sets the verfuegbar value
	 * @param float $verfuegbar		Article verfuegbar value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function set_verfuegbar($verfuegbar) {
		tx_pttools_assert::isNotNumeric($verfuegbar, array('message' => '$verfuegbar should be numeric but was ' . $verfuegbar));
	    $this->verfuegbar = $verfuegbar;
	}
	
	
	
	/**
	 * Returns the verfuegbar value.
	 * @return float	Article verfuegbar value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function get_verfuegbar() {
	    return $this->verfuegbar;
	}
	
	
	
	/**
	 * Sets the reserviert value
	 * @param float $reserviert		Article reserviert value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function set_reserviert($reserviert) {
		tx_pttools_assert::isNotNumeric($reserviert, array('message' => '$reserviert should be numeric but was ' . $reserviert));
	    $this->reserviert = $reserviert;
	}
	
	
	
	/**
	 * Returns the reserviert value.
	 * @return float	Article reserviert value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function get_reserviert() {
	    return $this->reserviert;
	}
	
	
	
	/**
	 * Sets the mindest value
	 * @param float $mindest	Article mindest value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function set_mindest($mindest) {
		tx_pttools_assert::isNotNumeric($mindest, array('message' => '$mindest should be numeric but was ' . $mindest));
	    $this->mindest = $mindest;
	}
	
	
	
	/**
	 * Returns the mindest value.
	 * @return float	Article mindest value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function get_mindest() {
	    return $this->mindest;
	}
	
	
	
	/**
	 * Sets the melde value
	 * @param float $melde	Article melde value
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function set_melde($melde) {
		tx_pttools_assert::isNotNumeric($melde, array('message' => '$melde should be numeric but was ' . $melde));
	    $this->melde = $melde;
	}
	
	
	
	/**
	 * Returns the melde value.
	 * @return float	Article melde value
	 */
	public function get_melde() {
	    return $this->melde;
	}
		
	
	
	/**
	 * Returns related article for stock information
	 * 
	 * @return tx_ptgsashop_baseArticle		Related article for stock information
	 * @since 2009-02-16
	 * @author Michael Knoll <knoll@punkt.de>
	 */
	public function getRelatedArticle() {
		return $this->baseArticle;
	}
	
	
	
	/**
	 * Returns article uid of related article
	 * 
	 * @return void
     * @since 2009-02-16
     * @author Michael Knoll <knoll@punkt.de>
	 */
	public function get_articleUid() {
		return $this->articleUid;
	}
	
	
	
	/* ***************************************************************************
     * Business Logic Methods
     * ***************************************************************************/
	
	
	
	public function increaseStockBy($quantity) {
		
		$this->set_bestand($this->bestand + $quantity);
		return $this->bestand;
		
	}
	
	
	
	public function decreaseStockBy($quantity) {
		
		return $this->increaseStockBy(-$quantity);
		
	}

	
	
}
 
?>