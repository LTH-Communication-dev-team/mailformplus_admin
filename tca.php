<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_mailformplusadmin_fields'] = array(
	'ctrl' => $TCA['tx_mailformplusadmin_fields']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,submittedfields'
	),
	'feInterface' => $TCA['tx_mailformplusadmin_fields']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'submittedfields' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_mailformplusadmin_fields.submittedfields',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, submittedfields')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_mailformplusadmin_standardforms'] = array(
	'ctrl' => $TCA['tx_mailformplusadmin_standardforms']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,title,lang,bodytext'
	),
	'feInterface' => $TCA['tx_mailformplusadmin_standardforms']['feInterface'],
	'columns' => array(
		'sys_language_uid' => array(		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array(		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table'       => 'tx_mailformplusadmin_standardforms',
				'foreign_table_where' => 'AND tx_mailformplusadmin_standardforms.pid=###CURRENT_PID### AND tx_mailformplusadmin_standardforms.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array(		
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_mailformplusadmin_standardforms.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'lang' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_mailformplusadmin_standardforms.lang',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'bodytext' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_mailformplusadmin_standardforms.bodytext',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;;;2-2-2, lang;;;;3-3-3, bodytext')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>