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
 * Renderer object for stock status rendering
 *
 * $Id: class.tx_ptgsastock_stockStatusRenderer.php,v 1.1 2009/03/20 12:42:16 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-20
 */ 



/**
 * Inclusion of required classes
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/abstract/class.tx_ptgsastock_iStockStatusRenderer.php';



/**
 * Stock status renderer
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package TYPO3
 * @subpackage pt_gsastock
 * @since 2009-03-20
 */
class tx_ptgsastock_stockStatusRenderer implements tx_ptgsastock_iStockStatusRenderer {

	
	
	/**
	 * Holds TS configuration for renderer
	 * 
	 * @var array
	 */
	protected $conf;
	
	
	
	/**
	 * Constructor for renderer
	 * 
	 * @return void
	 */
	public function __construct() {
		
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['config.']['tx_ptgsastock.']['stockStatusRenderer.'];
		
	}
	
	
	
	/**
	 * Renders stock information for status
	 *
	 * @param  tx_ptgsastock_stock_status      $status     StockStatus object for article
	 * @param  tx_ptgsastock_articleextension  $article    Article extension object
	 * @return string                                      Stock information for status and article
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since 2009-03-20
	 */ 
	public function renderStockInformation($status, $article) {
		
	    $markerArray = $this->getMarkerArray($status, $article);
    	// return prepared template to display
        $smarty = new tx_pttools_smartyAdapter($this);
        foreach ($markerArray as $markerKey=>$markerValue) {
            $smarty->assign($markerKey, $markerValue);
        }
        $templateFile = $this->conf['templateFile'];
        tx_pttools_assert::isNotEmptyString(
            $templateFile,
            array('message' => 'templateFile was empty! Make sure to set config.tx_ptgsastock.stockStatusRenderer.templateFile in your TS! Perhaps fogot to load static template?')
        );
        $filePath = $smarty->getTplResFromTsRes($templateFile);
        trace($filePath, 0, 'Smarty template resource $filePath');
        return 	$smarty->fetch($filePath);
        
	}
	
	
	
	/**
	 * Creates marker array for stock status template
	 *
	 * @return array       Array with template markers
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-03-20
	 */ 
	protected function getMarkerArray($status, $article) {
		
	    $markerArray = array();
        $markerArray['hint'] = $status['hint'];
        
        $imageFile = $this->conf['stockStatusImagePath'] . $status['image'];
        if (is_file(PATH_site . $imageFile) && (is_dir(PATH_site . $imageFile) == '')) {
            $markerArray['image'] = $imageFile;
        }
	    return $markerArray;
	    
	}
	

}
?>