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
 * @since   2009-03-25
 */



/*
 * Default module initialization (according to TYPO3 API in 'EXAMPLE PROTOTYPE' in t3lib_SCbase) 
 */ 
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:pt_gsastock/mod1/locallang.xml");
$LANG->includeLLFile("EXT:pt_gsastock/mod1/locallang_mod.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);  // This checks permissions and exits if the users has no permission for entry.



/**
 * Module class inclusion
 */
require_once t3lib_extMgm::extPath('pt_gsastock').'mod1/class.tx_ptgsastock_mod1.php';



/**
 * Default module finalization (according to TYPO3 API in 'EXAMPLE PROTOTYPE' in t3lib_SCbase)
 */
// Make instance:
$SOBE = t3lib_div::makeInstance('tx_ptgsastock_mod1');
$SOBE->init();


// Include files?
foreach($SOBE->include_once as $INC_FILE)   
    include_once($INC_FILE);

// call main() method (this should spark the creation of the module output) and output the accumulated content
$SOBE->main();
$SOBE->printContent();

?>