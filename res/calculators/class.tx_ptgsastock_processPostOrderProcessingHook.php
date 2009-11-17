<?php



/**
 * Inclusion of required classes
 */
require_once t3lib_extMgm::extPath('pt_gsastock') . 'res/abstract/class.tx_ptgsastock_iProcessPostOrderProcessingHook.php';
require_once t3lib_extMgm::extPath('pt_gsashop') . 'res/class.tx_ptgsashop_cart.php';



class tx_ptgsastock_processPostOrderProcessingHook implements tx_ptgsastock_iProcessPostOrderProcessingHook {
	
	
	
	public function processPostOrderProcessingHook($params) {
		
		#$GLOBALS['trace'] = 1;
		
		t3lib_div::devLog('In ' . __METHOD__ , 'tx_ptgsastock', 1, array('params' => print_r($params,true)));
		
        $orderWrapperObj = $params['orderWrapperObj'];  /* @var $orderWrapperObj tx_ptgsashop_orderWrapper */	
        $orderObj = $orderWrapperObj->get_orderObj(); /* @var $orderObj tx_ptgsashop_order */
        $articleCollection = $orderObj->getCompleteArticleCollection(); /* @var $articleCollection tx_ptgsashop_articleCollection */
        
        foreach($articleCollection as $processedArticle) {
        	/* @var $processedArticle tx_ptgsashop_article */
	        if (tx_ptgsastock_stock_articleextension::existsArticleextensionForBaseArticle($processedArticle->get_id())) {
	            $articleExtensionUid = tx_ptgsastock_stock_articleextension::getArticleextensionUidByArticleUid($processedArticle->get_id());
	            $articleExtensionObj = new tx_ptgsastock_stock_articleextension($articleExtensionUid);
	            $articleExtensionObj->decreaseTempStockCountBy($processedArticle->get_quantity());
	        }
        }
        
        #$GLOBALS['trace'] = 0;
		
	}
	
	
	
}

?>