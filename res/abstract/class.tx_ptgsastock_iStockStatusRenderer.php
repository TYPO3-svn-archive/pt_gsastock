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
 * Interface definition for status renderer
 *
 * $Id: class.tx_ptgsastock_iStockStatusRenderer.php,v 1.1 2009/03/20 12:42:16 ry21 Exp $
 *
 * @author  Michael Knoll <knoll@punkt.de>
 * @since   2009-03-20
 * 
 */ 




/**
 * Interface for rendering status information for stock
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package TYPO3
 * @subpackage pt_gsastock
 * @since 2009-03-20
 */
interface tx_ptgsastock_iStockStatusRenderer {

    
    
    /**
     * Returns rendered stock information for given treshold
     *
     * @param  tx_ptgsastock_stock_status      $status   Treshold row object for article
     * @param  tx_ptgsastock_articleextension  $article    Article to get stock information for
     * @return string                                      Article information string
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-03-20
     */
    public function renderStockInformation($status, $article);
    
    

}
 


?>