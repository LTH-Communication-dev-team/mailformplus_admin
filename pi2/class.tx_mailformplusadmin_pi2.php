<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Tomas Havner <tomas.havner@kansli.lth.se>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Formhandler listing' for the 'mailformplus_admin' extension.
 *
 * @author	Tomas Havner <tomas.havner@kansli.lth.se>
 * @package	TYPO3
 * @subpackage	tx_mailformplusadmin
 */
class tx_mailformplusadmin_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_mailformplusadmin_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_mailformplusadmin_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'mailformplus_admin';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)
        {
            $this->conf = $conf;
            $this->pi_setPiVarDefaults();
            $this->pi_loadLL();
            $GLOBALS["TSFE"]->additionalHeaderData["tx_mailformplus_admin_dform_js"] = '<script>if (typeof(jQuery) == "undefined") {
    var iframeBody = document.getElementsByTagName("body")[0];
    var jQuery = function (selector) { return parent.jQuery(selector, iframeBody); };
}</script>'; 

            $this->pi_initPIflexForm();
            $piFlexForm = $this->cObj->data['pi_flexform'];
            $index = $GLOBALS['TSFE']->sys_language_uid;
            $sDef = current($piFlexForm['data']);       
            $lDef = array_keys($sDef);
            $to_email = $this->pi_getFFvalue($piFlexForm, 'to_email', 'sDEF', $lDef[$index]);
            $subject = $this->pi_getFFvalue($piFlexForm, 'subject', 'sDEF', $lDef[$index]);
            $content_email = $this->pi_getFFvalue($piFlexForm, 'content_email', 'sDEF', $lDef[$index]);
            /*$contenttype = $this->pi_getFFvalue($piFlexForm, 'contenttype', 'sDEF', $lDef[$index]);
            $maxrows = $this->pi_getFFvalue($piFlexForm, 'maxrows', 'sDEF', $lDef[$index]);
            $just_these = $this->pi_getFFvalue($piFlexForm, 'just_these', 'sDEF', $lDef[$index]);*/

            $content = '<script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/dform/jquery.dform-1.1.0.min.js"></script>';
            $pluginId = $this->cObj->data['uid'];
            if($pluginId) {
                $content .= $this->showFormhandler($pluginId,$to_email,$subject,$content_email);
            } else {
                $content .= 'No data!';
            }

            return $content;
	}
        
        function showFormhandler($pluginId,$to_email,$subject,$content_email)
        {
            /*$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_formhandler_pi1.'];
            print_r($conf);
            $conf["debug"] = 1;
            $conf["templateFile"] = "typo3conf/ext/mailformplus_admin/templates/template.html";
            $conf["langFile"] = "typo3conf/ext/mailformplus_admin/templates/lang.xml";
            //$conf["name"] = "myform";
            $conf["formValuesPrefix"] = "formhandler";
            $conf["finishers"][1]["class"] = "Tx_Formhandler_Finisher_Mail";
            $conf["finishers"][2]["class"] = "Tx_Formhandler_Finisher_SubmittedOK";
            $conf["returns"] = 1; 

            // Get plugin instance
            $cObj = t3lib_div::makeInstance('tslib_cObj');
            $cObj->start(array(), '');
            $objType = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_formhandler_pi1'];
            $content = $cObj->cObjGetSingle($objType, $conf);*/
            // Formhandler einbinden
            require_once(t3lib_extMgm::extPath("formhandler")."pi1/class.tx_formhandler_pi1.php");

            // Neue Instanz erzeugen
            $formhandler = new tx_formhandler_pi1();
            // Dem Formhandler ein cObj geben
            $formhandler->cObj = t3lib_div::makeInstance('tslib_cObj');

            // Die TypoScript Konfiguration abrufen
            // Dabei zu beachten: die Punkte am Ende der Keys
            $config = $GLOBALS["TSFE"]->tmpl->setup['plugin.']['tx_mailformplusadmin_pi2.']['formhandler.'];
            $config["templateFile"] = "typo3conf/ext/mailformplus_admin/templates/step-1.html";
            $config['finishers']['1']['config']['admin']['to_email'] = $to_email;
#$config["masterTemplateFile"] = "typo3conf/ext/mailformplus_admin/templates/mastertemplate.html";
            /* 
 ''            * Rendern des Forumlars
             */
            $content = $formhandler->main("Formular",$config);
            unset($config,$formhandler);

            // Die gerenderte Form an das Template übergeben
            //$this->view->assign("form",$form);
            require_once(t3lib_extMgm::extPath('mailformplus_admin').'classes/class.tx_mailformplusadmin.php');
            $ajaxObj = new tx_mailformplusadmin();
            $formStructure = $ajaxObj->getFormStructure($pluginId.':tt_content');
            $formStructure = str_replace('{"fields"','{"html"',$formStructure);
            $formStructure = str_replace('"label"','"caption"',$formStructure);
            $formStructure = str_replace('"field_type"','"type"',$formStructure);
            //$formStructure = str_replace('[','',$formStructure);
            //$formStructure = str_replace(']','',$formStructure);
            $json = json_decode($formStructure,true);
            //echo 'sucker';
            foreach ($json['html'] as $key => $value) {
                $json['html'][$key]['name'] = 'formhandler['.$value['caption'].']';
                $json['html'][$key]['value'] = 'test'; //REmove later!
                $json['html'][$key]['id'] = $value['caption'];
            }
            
            $key++;
            $json['html'][$key]['type'] = 'p';
            $json['html'][$key]['html'] = '<img class="formhandler-ajax-submit" src="../typo3conf/ext/formhandler/Resources/Images/ajax-loader.gif"/>';
            
            $key++;
            $json['html'][$key]['type'] = 'hidden';
            $json['html'][$key]['name'] = 'to_email';
            $json['html'][$key]['value'] = $to_email;
            
            $key++;
            $json['html'][$key]['type'] = 'hidden';
            $json['html'][$key]['name'] = 'subject';
            $json['html'][$key]['value'] = $subject;
            
            $key++;
            $json['html'][$key]['type'] = 'hidden';
            $json['html'][$key]['name'] = 'pluginId';
            $json['html'][$key]['value'] = $pluginId;
            
            $formStructure = json_encode($json);
            $formStructure = ltrim($formStructure,'{');
            $formStructure = rtrim($formStructure,'}');
            //{"fields":[{"label":"fffff","field_type":"text","required":true,"field_options":{"size":"small"},"cid":"c2"},{"label":"eeeee","field_type":"text","required":true,"field_options":{"size":"small"},"cid":"c6"},{"label":"qqqqq","field_type":"text","required":true,"field_options":{"size":"small"},"cid":"c10"},{"label":"wwww","field_type":"text","required":true,"field_options":{"size":"small"},"cid":"c11"}],"formtitle":"qqq","formdescription":"qqq","user":"1"}
        $content .= '
            <style type="text/css">
            .loading_ajax-submit {
                display:none;
            }
            .ui-dform-text {
                display:block;
            }
            </style>
            <script type="text/javascript">
            
            function attachValidationEvents() {
                
            }
            
            
				
            jQuery(document).ready(function () {
                // Generate a form
                jQuery("#Formular").dform({
                    "action" : "'.$_SERVER["REQUEST_URI"].'",
                    "method" : "post",
                    "enctype" : "multipart/form-data",'.$formStructure.',
                    "id" : "newForm"
                });
		//jQuery("#newForm").append(jQuery("#receive-copy").parent().parent());
		//jQuery("#newForm").append(jQuery("#fomhandler_submit").parent().parent());
            });
            </script>';
            return $content;
        }

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailformplus_admin/pi2/class.tx_mailformplusadmin_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailformplus_admin/pi2/class.tx_mailformplusadmin_pi2.php']);
}

?>