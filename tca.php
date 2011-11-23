<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_mmreflist_projects"] = Array (
	"ctrl" => $TCA["tx_mmreflist_projects"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,name,shorttext,description,phone,fax,email,web,image,groupname"
	),
	"feInterface" => $TCA["tx_mmreflist_projects"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_mmreflist_projects',
				'foreign_table_where' => 'AND tx_mmreflist_projects.pid=###CURRENT_PID### AND tx_mmreflist_projects.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"shorttext" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.shorttext",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "3",
			)
		),
        "description" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.description",        
            "config" => Array (
                "type" => "text",
                "cols" => "30",
                "rows" => "5",
                "wizards" => Array(
                    "_PADDING" => 2,
                    "RTE" => Array(
                        "notNewRecords" => 1,
                        "RTEonly" => 1,
                        "type" => "script",
                        "title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
                        "icon" => "wizard_rte2.gif",
                        "script" => "wizard_rte.php",
                    ),
                ),
            )
        ),
      "phone" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.phone",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"fax" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.fax",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"email" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.email",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					),
				),
			)
		),
		"web" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.web",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					),
				),
			)
		),
		"image" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.image",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
//				"allowed" => "gif,png,jpeg,jpg,pdf",	
				"max_size" => 1500,	
				"uploadfolder" => "uploads/tx_mmreflist",
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 5,
			)
		),
		"groupname" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_projects.groupname",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_mmreflist_groups",	
				"foreign_table_where" => "ORDER BY tx_mmreflist_groups.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_mmreflist_groups",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tx_mmreflist_groups",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, shorttext,
		description;;4;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]::rte_transform[mode=ts_css|imgpath=uploads/tx_mmreflist/rte/], 
		//richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[flag=rte_enabled|mode=ts|imgpath=uploads/tx_mmreflist/rte/],		
		phone, fax, email, web, image, groupname")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_mmreflist_groups"] = Array (
	"ctrl" => $TCA["tx_mmreflist_groups"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,groupname"
	),
	"feInterface" => $TCA["tx_mmreflist_groups"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_mmreflist_groups',
				'foreign_table_where' => 'AND tx_mmreflist_groups.pid=###CURRENT_PID### AND tx_mmreflist_groups.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"groupname" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_reflist/locallang_db.php:tx_mmreflist_groups.groupname",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
  "types" => Array (
      "0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, shorttext, description;;;richtext[*]:rte_transform[mode=ts_css|imgpath=uploads/tx_mmreflist/rte/], phone, fax, email, web, image, groupname")
  ),
  "palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>