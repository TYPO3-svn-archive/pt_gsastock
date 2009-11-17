<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_ptgsastock_stockcount'] = array (
	'ctrl' => $TCA['tx_ptgsastock_stockcount']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'order_state,qty,artikel_nummer'
	),
	'feInterface' => $TCA['tx_ptgsastock_stockcount']['feInterface'],
	'columns' => array (
		'order_state' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stockcount.order_state',		
			'config' => array (
				/* Replace with 'type' => 'none', */
			    'type' => 'input',  
                'size' => '30', 
                'max' => '250', 
			)
		),
		'qty' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stockcount.qty',		
			'config' => array (
		        /* Replace with 'type' => 'none', */
				'type' => 'input',  
                'size' => '30', 
                'max' => '250', 
			)
		),
		'artikel_nummer' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stockcount.artikel_nummer',		
			'config' => array (
		        /* Replace with 'type' => 'none', */
				'type' => 'input',  
                'size' => '30', 
                'max' => '250', 
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'order_state;;;;1-1-1, qty, artikel_nummer')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_ptgsastock_stock_status'] = array (
	'ctrl' => $TCA['tx_ptgsastock_stock_status']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,hint,image,use_arcticle_stock_info'
	),
	'feInterface' => $TCA['tx_ptgsastock_stock_status']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_status.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'max' => '250',	
				'eval' => 'required,trim',
			)
		),
		'hint' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_status.hint',		
			'config' => array (
				'type' => 'text',
				'cols' => '40',	
				'rows' => '5',
			)
		),
		'image' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_status.image',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_ptgsastock',
				'show_thumbs' => 1,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'use_arcticle_stock_info' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_status.use_arcticle_stock_info',		
			'config' => array (
				'type' => 'check',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, hint, image, use_arcticle_stock_info')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_ptgsastock_stock_treshold_set'] = array (
	'ctrl' => $TCA['tx_ptgsastock_stock_treshold_set']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'description,stock_treshold'
	),
	'feInterface' => $TCA['tx_ptgsastock_stock_treshold_set']['feInterface'],
	'columns' => array (
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold_set.description',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '250',	
				'eval' => 'required,trim',
			)
		),
		'stock_treshold' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold_set.stock_treshold',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ptgsastock_stock_treshold',	
				'size' => 10,	
				'minitems' => 1,
				'maxitems' => 100,	
				"MM" => "tx_ptgsastock_stock_treshold_set_stock_treshold_mm",
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'description;;;;1-1-1, stock_treshold')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_ptgsastock_stock_treshold'] = array (
	'ctrl' => $TCA['tx_ptgsastock_stock_treshold']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,lower_bound,upper_bound,stock_status,description'
	),
	'feInterface' => $TCA['tx_ptgsastock_stock_treshold']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'lower_bound' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold.lower_bound',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '8',
				'checkbox' => '0',
			)
		),
		'upper_bound' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold.upper_bound',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '8',
				'checkbox' => '0',
			)
		),
		'stock_status' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold.stock_status',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ptgsastock_stock_status',	
				'size' => 1,	
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '40',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, lower_bound, upper_bound, stock_status, description')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_ptgsastock_stock_articleextension'] = array (
    'ctrl' => $TCA['tx_ptgsastock_stock_articleextension']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'base_article'
    ),
    'feInterface' => $TCA['tx_ptgsastock_stock_articleextension']['feInterface'],
    'columns' => array (
        'base_article' => array (        
            'exclude' => 0,     
            'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_articleextension.base_article',        
            'config' => array (
                'type' => 'group',  
                'internal_type' => 'db',    
                'allowed' => 'tx_ptgsashop_cache_articles',  
                'size' => 1,    
                'minitems' => 1,
                'maxitems' => 1,
            )
        ),
        'stock_treshold_set' => array (       
            'exclude' => 0,     
            'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_treshold_set',        
            'config' => array (
                'type' => 'group',  
                'internal_type' => 'db',    
                'allowed' => 'tx_ptgsastock_stock_treshold_set',  
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'show_stock' => array (     
            'exclude' => 1,
            'label'   => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_articleextension.show_stock',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'stock_status' => array (       
            'exclude' => 0,     
            'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_status',        
            'config' => array (
                'type' => 'group',  
                'internal_type' => 'db',    
                'allowed' => 'tx_ptgsastock_stock_status',  
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'description' => array (        
            'exclude' => 0,     
            'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_articleextension.description',     
            'config' => array (
                'type' => 'text',
                'cols' => '40', 
                'rows' => '5',
            )
        ),
        'stock_article' => array (        
            'exclude' => 0,     
            'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_articleextension.stock_article',        
            'config' => array (
                'type' => 'group',  
                'internal_type' => 'db',    
                'allowed' => 'tx_ptgsashop_cache_articles',  
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
//        'stock_category' => array (        
//            'exclude' => 0,     
//            'label' => 'LLL:EXT:pt_gsastock/locallang_db.xml:tx_ptgsastock_stock_articleextension.stock_category',        
//            'config' => array (
//                'type' => 'group',  
//                'internal_type' => 'db',    
//                'allowed' => 'tx_ptgsacategories_cat',  
//                'size' => 1,    
//                'minitems' => 0,
//                'maxitems' => 1,
//            )
//        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'base_article, stock_treshold_set, show_stock, stock_status, description, stock_articles, stock_article')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);
?>