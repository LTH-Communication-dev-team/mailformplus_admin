<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_mailformplusadmin_standardforms=1
');

//Novo starts
//$TYPO3_CONF_VARS['BE']['AJAX']['mailformplus_admin::setOk'] = t3lib_extMgm::extPath('mailformplus_admin').'mod1/mailform_ajax.php:mailform_ajax->setOk';
$TYPO3_CONF_VARS['BE']['AJAX']['mailformplus_admin::ajaxFunctions'] = t3lib_extMgm::extPath('mailformplus_admin').'mod1/mailform_ajax.php:mailform_ajax->ajaxFunctions';

$TYPO3_CONF_VARS['FE']['eID_include']['tx_mailformplusadmin'] = 'EXT:mailformplus_admin/ajax.php';

$TYPO3_CONF_VARS['FE']['eID_include']['formhandler'] = 'EXT:formhandler/Classes/Utils/Tx_Formhandler_Utils_AjaxValidate.php';
$TYPO3_CONF_VARS['FE']['eID_include']['formhandler-removefile'] = 'EXT:formhandler/Classes/Utils/Tx_Formhandler_Utils_AjaxRemoveFile.php';
$TYPO3_CONF_VARS['FE']['eID_include']['formhandler-ajaxsubmit'] = 'EXT:formhandler/Classes/Utils/Tx_Formhandler_Utils_AjaxSubmit.php';
//Novo ends

//t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_mailformplusadmin_pi2.php', '_pi2', 'list_type', 1);
//Add as a new type
t3lib_extMgm::addPlugin(array(
    'LLL:EXT:mailformplus_admin/locallang_db.xml:tt_content.CType_pi2',
    $_EXTKEY . '_pi2',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'CType');
?>