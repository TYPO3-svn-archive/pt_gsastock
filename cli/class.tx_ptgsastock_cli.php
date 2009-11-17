<?php



/***************************************************************
*  Copyright notice
*
*  (c) 2009 Michael Knoll <knoll@punkt.de>
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
 * CLI script for running imports from commando-line
 *
 * @author  Michael Knoll knoll@punkt.de
 * @since   2009-03-25
 */
if (!defined('TYPO3_cliMode'))  die('You cannot run this script directly!');



/**
 * Include basis cli class
 */ 
require_once(PATH_t3lib.'class.t3lib_cli.php');



/**
 * Include further dependencies 
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/classes/staticlib/class.tx_ptgsastock_div.php';



/**
 * 
 * Command Line Interface class for pt_gsastock. 
 * Running stock management commands from command line by using the following command:
 * 
 * /typo3/cli_dispatch.phpsh tx_ptgsastock <task_name>
 * 
 * Take a look at ext_localconf.php to see how to register CLI functionality!
 * Make sure to have created a CLI backend user "_cli_user" which is no admin!
 * Based on a script from Julian Kleinhans 
 * @see http://www.typo3-tutorials.org/tutorials/entwicklung/cli-das-neue-command-line-interface.html
 * 
 * @author Michael Knoll <knoll@punkt.de>
 * @since  2009-02-18
 * @package TYPO3
 * @subpackage pt_gsastock
 *
 */
class tx_ptgsastock_cli extends t3lib_cli {
    
    
    
    /**
     * Constructor
     */
    public function tx_ptgsastock_cli () {

        // Running parent class constructor
        parent::t3lib_cli();

        // Setting help texts:
        $this->cli_help['name'] = 'pt_gsastock Command Line Interface';        
        $this->cli_help['synopsis'] = '###OPTIONS###';
        $this->cli_help['description'] = 'Class for running pt_gsastock functionality from command line';
        $this->cli_help['examples'] = '/.../cli_dispatch.phpsh EXTKEY TASK';
        $this->cli_help['author'] = 'Michael Knoll <knoll@punkt.de>, (c) 2009';
        
    }

    
    
    /**
     * CLI main controller
     *
     * @param       array   $argv       Command line arguments
     * @return      string              Output of CLI call
     * @author      Michael Knoll <knoll@punkt.de>
     * @since       2009-03-25
     */
    public function cli_main($argv) {
        
        /* get task (function given as second argument in argument list) */
        $task = (string)$this->cli_args['_DEFAULT'][1];
        
        /* Show information on usage if no task is given */
        if (!$task){
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        }
        
        /* Task controller */
        switch ($task) {
            
            case 'delete_stock_count' :
                $timestamp = $argv[2];
                $this->runImports($timestamp);
                break;

            default :
                echo "$task is no known task. Nothing to do - goodbye!\n";
                exit;
            
        }

    }
    
    
    
    /**
     * CLI function for deleting stockcounts identified by timestamp
     * 
     * @param   int     $timestamp      Timestamp to delete stockcounts with smaller timestamp
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-03-25
     */
    protected function deleteStockCount($timestamp){
        
        echo "Deleting stockcounts...\n";
        tx_ptgsastock_div::deleteStockCounts($timestamp);
        echo "Delete has finished!\n";
        
    }
    
    
    
}



/* Call the functionality */
$cliObject = t3lib_div::makeInstance('tx_ptgsastock_cli');
$cliObject->cli_main($_SERVER['argv']);



?>