<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Michael Knoll <mimi@kaktusteam.de>
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
 * Module 'Stockmanagement' for the 'pt_gsastock' extension.
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since       2009-03-25
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_tools') . 'res/abstract/class.tx_pttools_beSubmodule.php';
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/staticlib/class.tx_ptgsastock_div.php';



/**
 * Inclusion of external PEAR resources: this requires PEAR to be installed on your server (see http://pear.php.net/) and the path to PEAR being part of your include path!
 */
require_once 'HTML/QuickForm.php';  // PEAR HTML_QuickForm: methods for creating, validating, processing HTML forms (see http://pear.php.net/manual/en/package.html.html-quickform.php). This requires the PEAR module to be installed on your server and the path to PEAR being part of your include path.



/**
 * Class for backend sub module 'Stockmanagement' for the 'pt_gsastock' extension.
 *
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-03-25
 * @package     TYPO3
 * @subpackage  pt_gsastock
 */
class tx_ptgsastock_mod1 extends tx_pttools_beSubmodule {

	
	
    /**
     * constructor for backend module class
     * 
     * @return      void
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-25
     */
    public function __construct() {
        
    }

    
    
    /************************************************************************
     *    INHERITED METHODS from tx_import_submodules
     ************************************************************************
    
    
    
    /**
     * Initializes the Module
     * @return  void
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-26
     */
    public function init()  {
    
    	$this->extKey = 'pt_gsastock';
    	$this->extPrefix = 'tx';
    	
        try {
            
             parent::init(); 
            
         } catch (tx_pttools_exception $excObj) {
            
             $excObj->handleException();
             die($excObj->__toString());
           
         }
        
    }

    
    
    /**
     * Adds items to the ->MOD_MENU array. Used for the function menu selector.
     *
     * @return  void
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-26
     */
    public function menuConfig() {
       
    }

    

    /**
     * "Controller": Calls the appropriate action and returns the module's HTML content
     *
     * @param       void
     * @return      string      the module's HTML content
     * @global      $GLOBALS['LANG']
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-25
     */
    public function moduleContent() {
        
        $moduleContent = '';
            
        switch (t3lib_div::GPvar('action')) {
            /*
             * Here comes the action handlers for module actions (not menu items - see below)
             */
            case 'deleteStockCount' :
            	$timestamp = t3lib_div::GPvar('timestamp');
                $moduleContent = $this->deleteStockCount($timestamp);
                break;
                
            default:
            	$moduleContent = $this->showDeleteStockCount();
            	break;
                      
        } 
        
        return $moduleContent;
    }

    
    
    /****************************************************************************
     *    BUSINESS LOGIC METHODS
     ****************************************************************************/

    
    
    /**
     * Returns Message if stock count has been deleted
     * 
     * @param       int     $timestamp      Timestamp for deleteting stock counts
     * @return      string                  HTML source of message
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-26
     */
    protected function deleteStockCount($timestamp) {
    	
    	$content = '';
    	
    	tx_ptgsastock_div::deleteStockCounts($timestamp);
    	$content .= '<br />';
    	$content .= '<br />';
    	$content .= '<div style="color:red;font-weight:bold">' . $this->ll('stock_count_deleted_message') . '</div>';
    	$content .= '<br />';
    	$content .= '<br />';
    	$content .= $this->showDeleteStockCount();
    	
    	return $content;
    	
    }
    
    
    
    /**
     * Returns message for delete stock count link
     * 
     * @return      string                  HTML source of link
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-26
     */
    protected function showDeleteStockCount() {
    	
    	$content = '';
    	
    	$content .= '<br />';
    	$content .= '<br />';
    	$content .= $this->ll('delete_stock_count_infomessage');
        $content .= '<br />';
        $content .= '<br />';
    	$content .= '<form action="index.php?action=deleteStockCount" method="post">'; 
    	$content .= 'Timestamp: <input name="timestamp" />';
    	$content .= '<br />';
    	$content .= '<br />';
    	$content .= '<input type="submit" value="'. $this->ll('delete_stockcount_submit_button') .'" />';
    	$content .= '</form>'; 
    	
    	return $content;
    	
    }
    
    

}



/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsaadmin/mod_articles/class.tx_ptgsaadmin_module2.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsaadmin/mod_articles/class.tx_ptgsaadmin_module2.php']);
}



?>
