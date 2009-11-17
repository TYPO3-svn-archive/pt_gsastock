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
 * Renderer object for stock treshold rendering
 *
 * $Id: class.tx_ptgsastock_stockArticleRenderer.php,v 1.4 2009/09/04 11:03:21 ry28 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-20
 */ 



/**
 * Inclusion of required classes
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/abstract/class.tx_ptgsastock_iStockArticleRenderer.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/models/class.tx_ptgsastock_stock.php';



/**
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package TYPO3
 * @subpackage pt_gsastock
 * @since 2009-03-20
 */
class tx_ptgsastock_stockArticleRenderer implements tx_ptgsastock_iStockArticleRenderer {

	
	
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
        
        $this->conf = $GLOBALS['TSFE']->tmpl->setup['config.']['tx_ptgsastock.']['stockArticleRenderer.'];
        
    }
	
	
	
	
	/**
	 * Renders stock information for treshold
	 *
	 * @param  tx_ptgsastock_articleextension  $article    Article extension object
	 * @return string                                      Stock information for treshold and article
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since 2009-03-20
	 */ 
	public function renderStockInformation($article) {
	   	
		/* Check, whether stock information should be shown */
        if ($article['show_stock'] == 0) {
            return '';
        }
        
        /* Create marker array */
		$markerArray = $this->getMarkerArray($article);
		// return prepared template to display
        $smarty = new tx_pttools_smartyAdapter($this);
        foreach ($markerArray as $markerKey=>$markerValue) {
            $smarty->assign($markerKey, $markerValue);
        }
        $templateFile = $this->conf['templateFile'];

        tx_pttools_assert::isNotEmptyString(
            $templateFile,
            array('message' => 'templateFile was empty! Make sure to set config.tx_ptgsastock.stockArticleRenderer.templateFile in your TS! Perhaps fogot to load static template?')
        );
        $filePath = $smarty->getTplResFromTsRes($templateFile);
        trace($filePath, 0, 'Smarty template resource $filePath');
        return  $smarty->fetch($filePath);

		
	}
	
	
	
    /**
     * Creates marker array for stock status template
     *
     * @param  tx_ptgsastock_stock_articleextension  $article  Article to render stock information for
     * @return array                                           Array with template markers
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-20
     */ 
    protected function getMarkerArray($article) {
        
        $markerArray = array();
        $stockObj = new tx_ptgsastock_stock($article['base_article']);
        $currStock = $stockObj->get_bestand();
        
        /* Check whether stock status is set for article */
        if (!is_null($article->getStockStatusObj())) {
            $markerArray['status_information'] = $article->getStockStatusObj()->renderStockInformation($article);
        }

        /* Check whether treshold set is set for article */
        if (!is_null($article->getTresholdSetObj())) {
            $tresholdObj = $article->getTresholdSetObj()->getTresholdForQuantity($currStock);
            /* Render stock information by treshold object */
            if (!is_null($tresholdObj)) {
                $markerArray['treshold_information'] = $tresholdObj->renderStockInformation($article);

            }
        }
        
        /* Set article specific data */
        // $markerArray['description'] = $article['description'] . ' - Bestand: ' . $stockObj->get_bestand();
        $markerArray['description'] = $stockObj->get_bestand();

        return $markerArray;
        
    }
	
	

}
?>