<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModulePath('web_txmailformplusadminM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
	t3lib_extMgm::addModule('web', 'txmailformplusadminM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

// Add plugin to new element wizard
if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mailformplusadmin_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'class.tx_mailformplusadmin_wizicon.php';
}

$TCA['tx_mailformplusadmin_fields'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_mailformplusadmin_fields',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_mailformplusadmin_fields.gif',
	),
);

$TCA['tx_mailformplusadmin_standardforms'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_mailformplusadmin_standardforms',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l10n_parent',	
		'transOrigDiffSourceField' => 'l10n_diffsource',	
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_mailformplusadmin_standardforms.gif',
	),
);


//Novo starts
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';                  // new!
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:mailformplus_admin/flexform_ds_pi2.xml');            // new!

t3lib_extMgm::addStaticFile($_EXTKEY,'configuration/settings/', 'Formhandler easy setup');

include_once(t3lib_extMgm::extPath($_EXTKEY).'pi2/dynamicflexform/class.dynamic_flexform.php');
//Novo ends

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2'] = 'layout,select_key,pages';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:mailformplus_admin/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE === 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mailformplusadmin_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'pi2/class.tx_mailformplusadmin_pi2_wizicon.php';
}

$tempColumns = array(
	'tx_mailformplusadmin_ok' => array(		
		'exclude' => 0,		
		'label' => 'LLL:EXT:mailformplus_admin/locallang_db.xml:tx_formhandler_log.tx_mailformplusadmin_ok',		
		'config' => array(
			'type' => 'check',
		)
	),
);


t3lib_div::loadTCA('tx_formhandler_log');
t3lib_extMgm::addTCAcolumns('tx_formhandler_log',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_formhandler_log','tx_mailformplusadmin_ok;;;;1-1-1');
?>