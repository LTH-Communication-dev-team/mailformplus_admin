<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <tomas.havner@kansli.lth.se>
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
 * Module 'Mailformplus Admin' for the 'mailformplus_admin' extension.
 *
 * @author	 <tomas.havner@kansli.lth.se>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$LANG->includeLLFile("EXT:mailformplus_admin/mod1/locallang.xml");
if (!defined(PATH_tslib)) {
    if (file_exists(PATH_site.'tslib/'."class.tslib_content.php")) {
	define('PATH_tslib', PATH_site.'tslib/');
    } else {
	define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
    }
}
require_once (PATH_tslib."class.tslib_content.php"); # for getting the cObj (needed for reading the template file

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
	
if (t3lib_extMgm::isLoaded('fpdf')) require(t3lib_extMgm::extPath('fpdf').'class.tx_fpdf.php');

class tx_mailformplusadmin_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

		/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
				"2" => $LANG->getLL("function2"),
				"3" => $LANG->getLL("function3"),
				"4" => $LANG->getLL("function3"),
				"5" => $LANG->getLL("function3"),
				"6" => $LANG->getLL("function3"),
			)
		);
		parent::menuConfig();
	}*/

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form name="tx_mailformplus_admin_form" method="POST">';
		
				// JavaScript
			$this->doc->JScode = '
				<style type="text/css">
				<!--
				.mailformplus_adminTable { 
					margin-left:10px; width:700px; border:1px #a2aab8 solid;
				}
				.mailformplus_adminTable td { 
					padding:4px; margin:0px; 
				}
				-->
				</style>
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						//alert(URL);
						document.location = URL;
					}
					
					function checkAll(checked,boxName) {
						//alert(checked);
						if(checked) {
							for (var i = 0; i < document.tx_mailformplus_admin_form[boxName].length; i++) {
								document.tx_mailformplus_admin_form[boxName][i].checked = true;
							}
						} else {
							for (var i = 0; i < document.tx_mailformplus_admin_form[boxName].length; i++) {
								document.tx_mailformplus_admin_form[boxName][i].checked = false;
							}		
						}
					}
					
					function deleteRow(field, page_id, myScreen, type) {
						//alert(field + page_id + myScreen);
						if(confirm("' . $LANG->getLL("confirm_delete") . '")) {
							var i=0;
							var uid = "";
							var fieldArray = new Array();
							fieldArray = document.getElementsByName(field);
							//alert(fieldArray.length);
							for (i = 0; i < fieldArray.length; i++) {
								if(fieldArray[i].checked) {
									if(uid) uid += ",";
									uid += fieldArray[i].value;
								}
							}
							
							jumpToUrl("index.php?&id=" + page_id + "&uid=" + uid + "&screen=" + myScreen + "&type="+type+"&SET[function]=4&log_type="+type);
						}
					}
					
					function exportDo(pageid, screen, type) {
						var i = 0;
						var ii = 0;
						var fi = 0;
						
						//Get fields
						var fields = "";
						var fieldArray = new Array();
						fieldArray = document.getElementsByName("fieldBox");
						//alert(fieldArray.length);
						for (i = 0; i < fieldArray.length; i++) {
							if(fieldArray[i].checked) {
								if(fields) fields += ",";
								fields += fieldArray[i].value;
								fi++;
							}
						}		
						
						if(fi > 0) {
							var checkboxes = 0;
							var rvalue = "";
							var cvalue = "";
							var lextra = "";
							
							//Get choices
							var choices = "";
							var choiceArray = new Array();
							choiceArray = document.getElementsByName("rblExportFormat");
							//alert(choiceArray.length);
							for (ii = 0; ii < choiceArray.length; ii++) {
								if(choiceArray[ii].checked) {
									if(choices) choices += ",";
									choices += choiceArray[ii].value;
									if(choiceArray[ii].value=="cblLabels") lextra = document.getElementById("txtLabelExtra").value;
									if(choiceArray[ii].value=="cblPdfList") lextra = document.getElementById("txtHeader").value;
								}
							}
							
							var url = "index.php?&id=" + pageid + "&screen=" + screen + "&fields=" + fields + "&choices=" + choices + "&lextra=" + lextra + "&type=" + type + "&SET[function]=6";
							var newwindow = window.open(url,"dok","height=700,width=700,scrollbars=yes, status=no");
							if (window.focus) {newwindow.focus()}
						} else {
							alert("' . $LANG->getLL("export_alert") . '");	
						}
					}
					
					function stateChanged() { 
						if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
							alert(xmlHttp.responseText);
						}
					}
					
					function GetXmlHttpObject() {
						var xmlHttp=null;
						try
						 {
						 // Firefox, Opera 8.0+, Safari
						 xmlHttp=new XMLHttpRequest();
						 }
						catch (e)
						 {
						 //Internet Explorer
						 try
						  {
						  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
						  }
						 catch (e)
						  {
						  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
						  }
						}
						return xmlHttp;
					}
					
					function ajax(action,scope,query) {
						xmlHttp=GetXmlHttpObject();
						if (xmlHttp==null) {
							alert ("Browser does not support HTTP Request");
						 	return;
						}
						var url="/typo3/ajax.php?ajaxID=mailformplus_admin::setOk";
						url=url+"&uid="+uid;
						url=url+"&okChecked="+okChecked;
						url=url+"&sid="+Math.random();
						//alert(url);
						
						xmlHttp.onreadystatechange=stateChanged;
						xmlHttp.open("GET",url,true);
						xmlHttp.send(null);
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_cs($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		
		$pageid = t3lib_div::_GP("id");
		$set = t3lib_div::_GP("SET");
		$screen = t3lib_div::_GP("screen");
		$type = t3lib_div::_GP("type");
		$sid = t3lib_div::_GP("sid");
		if(!$screen) $screen = "0";
		//print $set["function"];
		
		$pageTSConfig = t3lib_BEfunc::getPagesTSconfig($this->id);
		$pidList = $pageTSConfig["mod."]["tx_mailformplusadmin_module1."]["pidList"];

		if($set) {
			//$switchCore = (string)$this->MOD_SETTINGS["function"];
			$switchCore = (string)$set["function"];
		} else {
			$switchCore = 1;
		}

		switch($switchCore)	{
			case 1:
				$content .= $this->printList($pageid, $pidList);
				$this->content .= $content;
			break;
			case 2:
				$content .= $this->printHeader($pageid, $screen, $type);
				if($_POST) {
					$content .= $this->saveColumns($pageid, $_POST);
				} else {
					$content .= $this->printColumns($pageid, $pidList);
				}
				
				$this->content .= $content;
			break;
			case 3:
				$content .= $this->printHeader($pageid, $screen, $type);
				$content .= $this->printDetail($pageid);
				$this->content .= $content;
			break;
			case 4:
				$content .= $this->printHeader($pageid, $screen, $type);
				$content .= $this->deleteRow($pageid, $type);
				$this->content .= $content;
			break;
			case 5:
				$content .= $this->printHeader($pageid, $screen, $type);
				$content .= $this->exportChoice($pageid, $type, $pidList);
				$this->content .= $content;
			break;
			case 6:
				$content .= $this->exportDo($pageid, $type, $pidList);
				$this->content .= $content;
			break;
			case 7:
				$content .= $this->update($pageid, $type, $pidList);
				$this->content .= $content;
			break;
		}
	}
	
	function printHeader($pageid, $screen, $type, $query) {
		global $LANG;
		if(!$pageid and !$query) $disabled = " disabled=\"disabled\"";
			$content .= "<table border=\"0\" style=\"margin-left:10px; width:700px; margin-bottom:10px; padding:5px;\"><tr>";
			$content .= "<td align=\"center\" width=\"20%\" ><input $disabled onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;type=$type&amp;SET[function]=1');\" type=\"button\" name=\"btnList\" id=\"btnList\" value=\"" . $LANG->getLL("list_items") . "\" title=\"" . $LANG->getLL("list_items") . "\" /></td>";
			$content .= "<td align=\"center\" width=\"50%\"><input $disabled onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;type=$type&amp;SET[function]=2');\" type=\"button\" name=\"btnShow\" id=\"btnShow\" value=\"" . $LANG->getLL("choose_fields") . "\" title=\"" . $LANG->getLL("choose_fields") . "\" /></td>";
			$content .= "<td align=\"center\" width=\"30%\"><input $disabled onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;type=$type&amp;SET[function]=5');\" type=\"button\" name=\"btnExport\" id=\"btnExport\" value=\"" . $LANG->getLL("export") . "\" title=\"" . $LANG->getLL("export") . "\" /></td>";
			$content .= "</tr></table>";
		return $content;
	}
	
	function printTableHeader() {
		$content = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"mailformplus_adminTable\">";
		return $content;
	}
	
	function printList($pageid, $pidList) {
		
		global $LANG;
		if($pageid) {
				//return $content;
			$i = 0;
			$ii = 0;
			$iii = 0;
			$x = 0;
			$xx=0;
			$maxContent = 0;
			$where_formhandler = "";
			$where_mailformplus = "";
			
			$screen = t3lib_div::_GP("screen");
			$query = addslashes(htmlspecialchars(t3lib_div::_GP("query")));

			if(!$screen) $screen = 0;
			if(!$start) $start = 0;
			$rows_per_page = 20;
			$start = $screen * $rows_per_page;
						
			$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_mailformplusadmin_fields", "pid=" . intval($pageid), "", "", "") or die("339: ".mysql_error());
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$chosenFields = $row["submittedfields"];
			}

			if($chosenFields) $chosenFieldsArray = explode(",", $chosenFields);
			$chosenFields = "";
			
			$content .= $this->printTableHeader();

			if($pidList) {
				$where_formhandler = " WHERE pid IN($pidList)";
				$where_mailformplus = " WHERE pid IN($pidList)";
			} else {
				$where_formhandler = " WHERE pid=" . intval($pageid);
				$where_mailformplus = " WHERE pid=" . intval($pageid);
			}
					
			if(trim($query)) {
				$where_formhandler .= " AND params LIKE '%$query%'";
				$where_mailformplus .= " AND submittedfields LIKE '%$query%'";
			}

			$sql = "SELECT SQL_CALC_FOUND_ROWS uid, pid, FROM_UNIXTIME(crdate,'%Y-%m-%d') AS logdate, params, '' AS downloaded, ok, 'formhandler' AS  log_type ";
			$sql .= "FROM tx_formhandler_log$where_formhandler ";
			$sql .= "UNION ";
			$sql .= "SELECT  uid, pid, FROM_UNIXTIME(logdate,'%Y-%m-%d'), submittedfields AS params, downloaded, ok, 'mailformplus' AS  log_type ";
			$sql .= "FROM tx_thmailformplus_log$where_mailformplus ";
			$sql .= "ORDER BY logdate DESC LIMIT $start,$rows_per_page";
			//die($sql);
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			$totRows = mysql_query("SELECT FOUND_ROWS()");
			$total_records = mysql_result($totRows, 0);
			
			$type="mailformplus";

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$i=0;
				$uid = $row["uid"];
				$pid = $row["pid"];
				$logdate = $row["logdate"];
				$log_type = $row["log_type"];
				$params = $row["params"];
				//echo $params . "<br />";
				if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);
				//$params = substr($params, strpos($params,"{")+1, strlen($params)-strpos($params,"{")-2);
				$downloaded = $row["downloaded"];
				$ok = $row["ok"];

				$paramsArray = unserialize($params);
				/*print "<pre>";
				print_r($paramsArray);
				print "<pre>";
				die(is_array($paramsArray));*/
				unset($paramsArray['randomID']);
				unset($paramsArray['removeFile']);
				unset($paramsArray['removeFileField']);
				unset($paramsArray['step-2-next']);
				unset($paramsArray['submitField']);
				unset($paramsArray['submitted']);
				unset($paramsArray['id']);
				unset($paramsArray['submitted']);
				unset($paramsArray['L']);
				unset($paramsArray['type']);
				unset($paramsArray['formToken']);

				//a:11:{s:11:"contact_via";s:5:"email";s:5:"email";s:26:"tomas.havner@kansli.lth.se";s:9:"firstname";s:2:"dd";s:9:"interests";a:2:{i:0;s:6:"sports";i:1;s:5:"music";}s:8:"lastname";s:2:"dd";s:8:"randomID";s:32:"8f15a9f644088512510b7c8f3edeb742";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:4:"Send";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";}
				foreach($paramsArray as $key => $value) {
					//echo $ii;
					//echo $key . $value;
					$arrayValue = "";
					if(is_array($value)) {
						foreach($value as $key1 => $value1) {
							if($arrayValue) $arrayValue .= ", ";
							$arrayValue .= $value1;
						}
					} else {
						$arrayValue = $value;
					}

					if(is_array($chosenFieldsArray) and array_search($key, $chosenFieldsArray)!==false) {
						$myArray[$uid][$key] = $arrayValue;
						$myArray[$uid]['ok'] = $ok;
						$myArray[$uid]['pid'] = $pid;
						$myArray[$uid]['log_type'] = $log_type;
						$myArray[$uid]['logdate'] = $logdate;
					} elseif(!is_array($chosenFieldsArray) and $i < 16) {
						$myArray[$uid][$key] = $arrayValue;
						$myArray[$uid]['ok'] = $ok;
						$myArray[$uid]['pid'] = $pid;
						$myArray[$uid]['log_type'] = $log_type;
						$myArray[$uid]['logdate'] = $logdate;
						if($ii==0){
							if($chosenFields) $chosenFields .= ",";
							$chosenFields .= $key;
						}
					}
					//echo $ii;
					$i++;
				}
				//echo $ii;
				$ii++;			
			}
			if($chosenFields) $chosenFieldsArray = explode(",", $chosenFields);
			$pages = ceil($total_records / $rows_per_page);
			//print_r($myArray);
				
			$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		

		
				

			/*print "<pre>";
			print_r($myArray);
			print "<pre>";
			*/
                        $myContent = '';
		if(is_array($myArray)) {
			foreach($myArray as $key => $value) {
				$myContent .= "<tr>";
				$xx=0;
				if($x%2) {
						$bgcolor = "";
				} else {
						$bgcolor = "#ffffff";
				}
				if($x==0) {
					$header .= "<td style=\"width:40px; background-color:#cccccc; font-weight:bold;\">Date</td>";
				}
				$myContent .= "<td style=\"background-color:$bgcolor;\">" . $myArray[$key]['logdate'] . "</td>";
				foreach($chosenFieldsArray as $key1 => $value1) {
					//if(in_array($key1, $chosenFieldsArray)) {
					if($x==0) $header .= "<td style=\"background-color:#cccccc; font-weight:bold;\">$value1</td>";
					$myContent .= "<td style=\"background-color:$bgcolor;\">" . $value[$value1] . "</td>";
					//}
					$xx++;
				}
				if($x==0) {
					$header .= "<td style=\"width:40px; background-color:#cccccc; font-weight:bold;\"></td>";
					$header .= "<td style=\"background-color:#cccccc; font-weight:bold;\">
					<input type=\"checkbox\" name=\"\" id=\"\" value=\"\" onclick=\"checkAll(this.checked,'deleteBox');\" />
					<img src=\"/typo3conf/ext/mailformplus_admin/mod1/garbage.gif\" border=\"0\" onclick=\"deleteRow('deleteBox', '$pageid', '$screen', '$type'); return false; \" />
					</td>";
					//Novo begin
					$header .= "<td style=\"background-color:#cccccc; font-weight:bold;\">
					<img src=\"/typo3conf/ext/mailformplus_admin/mod1/ok.png\" border=\"0\" />
					</td>";
					//Novo ends
				}
				$okChecked="";
				$pid = $myArray[$key]['pid'];
				$log_type = $myArray[$key]['log_type'];
				if($myArray[$key]['ok']) $okChecked=" checked=\"checked\"";
				$myContent .= "<td style=\"background-color:$bgcolor;\"><a href=\"#\" onclick=\"jumpToUrl('index.php?id=$pageid&amp;uid=$key&amp;pid=$pid&amp;screen=$screen&amp;type=$type&amp;SET[function]=3');\"><img src=\"/typo3conf/ext/mailformplus_admin/mod1/edit2.gif\" border=\"0\" title=\"" . $LANG->getLL("view_item") . "\" /></a></td>";
				$myContent .= "<td style=\"background-color:$bgcolor;\"><input name=\"deleteBox\" type=\"checkbox\" value=\"$log_type:$key\" /></td>";
				//Novo begin
				$myContent .= "<td style=\"background-color:$bgcolor;\"><input name=\"okBox\" type=\"checkbox\" value=\"$key\"$okChecked onclick=\"lista('$log_type:$key',this.checked);\" /></td>";
				//Novo end
				$xx = $xx+3;
				$myContent .= "</tr>";
				$x++;
			}
                }
			
			for($x == $x; $x < $rows_per_page; $x++) {
				if($x%2) {
					$bgcolor = "";
				} else {
					$bgcolor = "#ffffff";
				}
				$myContent .= "<tr><td colspan=\"$xx\" style=\"background-color:$bgcolor; height:26px;\">&nbsp;</td></tr>";
			}
			
			$content .= "<tr>$header</tr>$myContent";
			
			/*if($type=="mailformplus") {
				for ($ii = $i; $ii < $rows_per_page; $ii++) {
					$content .= "<tr><td colspan=\"8\" style=\"height:22px;\"></td></tr>";
				}
			}
			*/
			if($pages > 1){
				$content .= "<tr><td colspan=\"8\" align=\"center\">";
				if ($screen > 0) {
					$myScreen = $screen - 1;
					$content .= "<a href=\"#\" onClick=\"jumpToUrl('index.php?&amp;id=$pageid&amp;type=$type&amp;screen=$myScreen');\"><< " . $LANG->getLL("previous") . "</a>";
				} else {
					$content .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			
				if($pages > 1) {
					for ($i = 0; $i < $pages; $i++) {
						$ii = $i + 1;
						if($i == $screen) {
							$content .= " <b>$ii</b>";
						} else {
							$content .= " <a href=\"#\" onClick=\"jumpToUrl('index.php?&amp;id=$pageid&amp;type=$type&amp;screen=$i');\">$ii</a>";
						}
						if ($i < $pages-1) $content .= " |";
					}
				}
				
				if ($screen + 1 < $pages) {
					$myScreen = $screen + 1;
					$content .= "<a href=\"#\" onclick=\"jumpToUrl('index.php?&amp;id=$pageid&amp;type=$type&amp;screen=$myScreen');\"> " . $LANG->getLL("next") . " >></a>";
				}
			
				$content .= "</td></tr>";
			}
			
			//Footer
			$randomVar = md5(time());
			$content .= "<tr><td colspan=\"9\" align=\"center\" style=\"background-color:#cccccc;\">";
			$content .= "Number of records: <b>$total_records</b> | Search: ";
			$content .= " <input name=\"query\" id=\"query\" type=\"text\" value=\"$query\" />";
			$content .= " <input onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;sid=$randomVar&amp;type=$type&amp;query='+document.getElementById('query').value+'&amp;SET[function]=1');\" type=\"button\" name=\"btnSearch\" id=\"btnSearch\" value=\"Search\" title=\"Search\" />";
			$disabled = "";
			if(!$query) $disabled = " disabled=\"disabled\""; 
			$content .= " <input$disabled onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;type=$type&amp;SET[function]=1');\" type=\"button\" name=\"btnShowall\" id=\"btnShowall\" value=\"Show all\" title=\"Show all\" />";
			
			$content .= "</td></tr>";
	
			$content .= "</table>";
			
		
		} else {
			$content .= "<div align=\"center\" style=\"margin-left:10px; width:700px; height:200px; padding-top:90px; border:1px black solid;\"><h1>" . $LANG->getLL("choose_page") . "</h1></div>";
		}
		if($pages == 0) $pageid = "";
		$content = $this->printHeader($pageid, $screen, $type, $query) . $content;
		return $content;
	}
	
	function printDetail($pageid) {
		//die("$pageid");
		global $LANG;
		$screen = t3lib_div::_GP("screen");
		$i=0;
		$ii=0;
		$uid = t3lib_div::_GP("uid");
		$pid = t3lib_div::_GP("pid");
		$myArray = array();
		$sql = "SELECT SQL_CALC_FOUND_ROWS uid, pid, FROM_UNIXTIME(crdate,'%Y-%m-%d') AS logdate, params, '' AS downloaded, ok, 'formhandler' AS  log_type ";
		$sql .= "FROM tx_formhandler_log WHERE uid=" . intval($uid) . " AND pid = " . intval($pid);
		$sql .= " UNION ";
		$sql .= "SELECT  uid, pid, FROM_UNIXTIME(logdate,'%Y-%m-%d'), submittedfields AS params, downloaded, ok, 'mailformplus' AS  log_type ";
		$sql .= "FROM tx_thmailformplus_log WHERE uid=" . intval($uid) . " AND pid = " . intval($pid);
		//die($sql);
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		$uid = $row["uid"];
		$pid = $row["pid"];
		$logdate = $row["logdate"];
		$log_type = $row["log_type"];
		$params = $row["params"];
		//echo $params;
		if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);
		
		$paramsArray = unserialize($params);
		unset($paramsArray['randomID']);
		unset($paramsArray['removeFile']);
		unset($paramsArray['removeFileField']);
		unset($paramsArray['step-2-next']);
		unset($paramsArray['submitField']);
		unset($paramsArray['submitted']);
		unset($paramsArray['id']);
		unset($paramsArray['submitted']);
		unset($paramsArray['L']);
		unset($paramsArray['formToken']);
		
		/*print "<pre>";
		print_r($paramsArray);
		print "<pre>";*/
			
		foreach($paramsArray as $key => $value) {
			/*$arrayValue = "";
			if(is_array($value)) {
				foreach($value as $key1 => $value1) {
					if($arrayValue) $arrayValue .= ", ";
					$arrayValue .= $value1;
				}
			} else {
				$arrayValue = $value;
			}*/
			$myArray[$key] = $value;
		}
		//die($uid);
		//if($type=="mailformplus") {
		/**	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_thmailformplus_log", "uid=" . intval($uid), "", "", "") or die("646: ".mysql_error());
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$submittedfields = $row["submittedfields"];
			}
			if(substr($submittedfields, 0, 5) == "test;") {
				$submittedfields = substr($submittedfields, 5);
				$submittedfields = str_replace("\ntest;", ";", $submittedfields);
			}
					
			$submittedfields = str_replace("~", "", $submittedfields);
			$submittedfieldsArray = explode(";", $submittedfields);
			$addVar = count($submittedfieldsArray) / 2;
			
			for($i == 0; $i < $addVar; $i++) {
				$myArray[$submittedfieldsArray[$i]] = $submittedfieldsArray[$i+$addVar];
			}
		} else {
			$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("field_name, field_value", "tx_pksaveformmail_fields", "email_uid=" . intval($uid), "", "", "") or die("652: ".mysql_error());
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$email_uid = $row["email_uid"];
				$field_name = $row["field_name"];
				$field_value = $row["field_value"];
				$myArray[$field_name] = $field_value;
			}
		}
		*/
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		

		$content .= $this->printTableHeader();
		
		$content .= "<tr><td colspan=\"2\" style=\"background-color:#cccccc; height:28px;\"><b>" . $LANG->getLL("display_item") . "</b></td></tr>";
		
		foreach($myArray as $key => $value) {
			if($key!='type'){
				if($i%2) {
					$bgcolor = "#ffffff";
				} else {
					$bgcolor = "";
				}
				$contentsArray[$i][1] = "<td style=\"background-color:$bgcolor; width:500px; height:28px;\">";
				$contentsArray[$i][0] = "<td style=\"background-color:$bgcolor; width:200px; height:28px;\"><b>$key</b></td>";
				if(is_array($value)) {
					foreach($value as $key1 => $value1) {
						$contentsArray[$i][1] .= "<input type=\"checkbox\" name=\"$key" . "[]\" value=\"$value1\" checked=\"checked\" /> <label>$value1</label><br />";
					}
				} else {
					if (preg_match("/\n/", $value)) {
						//$contentsArray[$i][1] .= "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"$value\" size=\"50\" /></td>";
						$contentsArray[$i][1] .= "<textarea name=\"textarea\" id=\"textarea\" cols=\"45\" rows=\"5\">$value</textarea>";
					} else {
						$contentsArray[$i][1] .= "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"$value\" size=\"50\" /></td>";
					}
				}
				$i++;
			}
		}
		
		while($ii <= $i) {
			$content .= "<tr>";
			$content .= $contentsArray[$ii][0];
			$content .= $contentsArray[$ii][1];
			$content .= "</tr>";
			$ii++;
		}
		$content .= "<tr><td colspan=\"2\">
		<input type=\"hidden\" name=\"screen\" value=\"$screen\" />
		<input type=\"hidden\" name=\"SET[function]\" value=\"7\" />
		<input type=\"hidden\" name=\"id\" value=\"$pageid\" />
		<input type=\"hidden\" name=\"uid\" value=\"$uid\" />
		<input type=\"hidden\" name=\"pid\" value=\"$pid\" />
		<input type=\"hidden\" name=\"log_type\" value=\"$log_type\" />
		<input onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;SET[function]=1');\" type=\"button\" name=\"btnBack\" id=\"btnBack\" value=\"" . $LANG->getLL("back") . "\" title=\"" . $LANG->getLL("back") . "\" />
		<input type=\"button\" name=\"btnSave\" id=\"btnBack\" value=\"Save\" title=\"Save\" onclick=\"document.forms['tx_mailformplus_admin_form'].submit();\" />
		</td></tr>";
		$content .= "</table>";	
		return $content;	
	}
		
	function printColumns($pageid, $pidList) {
		if($pidList) {
			$where = " WHERE pid IN($pidList)";
		} else {
			$where = " WHERE pid=" . intval($pageid);
		}
			
		global $LANG;
		$i=0;
		$resultArray = array();
		$screen = t3lib_div::_GP("screen");
		
			//Ta ut ev sparade v�rden
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_mailformplusadmin_fields", "pid=" . intval($pageid), "", "", "") or die("475: ".mysql_error());
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$submittedfields = $row["submittedfields"];
		}
		if($submittedfields != "") $chosenFieldsArray = explode(",", $submittedfields);
		$submittedfields = "";
		
			//L�s fr�n databasen
		$sql = "SELECT params, 'formhandler' AS  log_type, ok ";
		$sql .= "FROM tx_formhandler_log $where";
		$sql .= " UNION ";
		$sql .= "SELECT submittedfields AS params, 'mailformplus' AS log_type, ok ";
		$sql .= "FROM tx_thmailformplus_log $where";
		//die($sql);
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$log_type = $row["log_type"];
			$params = $row["params"];
			$ok = $row["ok"];
			if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);
			$paramsArray = unserialize($params);

			unset($paramsArray['randomID']);
			unset($paramsArray['removeFile']);
			unset($paramsArray['removeFileField']);
			unset($paramsArray['step-2-next']);
			unset($paramsArray['submitField']);
			unset($paramsArray['submitted']);
			unset($paramsArray['id']);
			unset($paramsArray['submitted']);
			unset($paramsArray['L']);
			unset($paramsArray['formToken']);
			/*print "<pre>";
			print_r(array_keys($paramsArray));
			print "</pre>";*/
			if(is_array($paramsArray)) $resultArray = array_merge($resultArray, array_keys($paramsArray));
		}
		
		//a:9:{s:11:"looking_for";s:2:"aa";s:7:"missing";s:2:"aa";s:8:"randomID";s:32:"38578035ffd234e003bbfaa5d776fa6b";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:6:"Skicka";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";s:12:"visitor_type";s:8:"external";}

		//$paramsArray = explode(";", $params);
		
		$resultArray = array_unique($resultArray);

		asort($resultArray);
		
		/* print "<pre>";
		print_r($resultArray);
		print "</pre>"; */
		
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		
		$content .= $this->printTableHeader();
		
		$content .= "<tr><td style=\"background-color:#cccccc; height:28px;\" colspan=\"2\"><b>" . $LANG->getLL("choose_fields") . "</b><input type=\"hidden\" name=\"screen\" value=\"$screen\" /></td></tr>";
		
		$i=0;
		foreach($resultArray as $key => $value) {
			if($value != "id"  and $value != "submitted" and $value != "L" and $value != "type") {
				if($i%2) {
					$bgcolor = "#ffffff";
				} else {
					$bgcolor = "";
				}
				if(is_array($chosenFieldsArray)) {
					if(in_array($value, $chosenFieldsArray)) {
						$checked = "checked=\"checked\"";
					} else {
						$checked = "";	
					}
				} elseif($i < 6) {
					$checked = "checked=\"checked\"";
				} else {
					$checked = "";	
				}
				
				$content .= "<tr><td style=\"background-color:$bgcolor; width:600px;\">$value</td><td style=\"background-color:$bgcolor; width:100px;\"><input name=\"fieldBox[]\" type=\"checkbox\" value=\"$value\" $checked /></td></tr>";
			}
			$i++;
		}
		$content .= "<tr><td colspan=\"2\"><input name=\"submit\" type=\"submit\" value=\"" . $LANG->getLL("save") . "\" title=\"" . $LANG->getLL("save") . "\" />&nbsp;&nbsp;<input onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;SET[function]=1');\" type=\"button\" name=\"btnCancel\" id=\"btnCancel\" value=\"" . $LANG->getLL("cancel") . "\" title=\"" . $LANG->getLL("cancel") . "\" /></td></tr>";
		$content .= "</table>";
		return $content;
	}
	
	function saveColumns($pageid, $post) {
		//die($pageid);
		global $LANG;
		$screen = t3lib_div::_GP("screen");
		
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid", "tx_mailformplusadmin_fields", "pid=" . intval($pageid), "", "", "") or die("525: ".mysql_error());
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$uid = $row["uid"];
		}
		//die($uid);
		if(is_array($post)) {
			foreach($post AS $key => $value) {
				if(is_array($value)) {
					foreach($value AS $key1 => $value1) {
						if($saveContent) $saveContent .= ",";
						if($key=="fieldBox") $saveContent .= addslashes($value1);
					}
				}
			}
		}
		//die($saveContent);
		if($uid) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_mailformplusadmin_fields','uid='.intval($uid), array('submittedfields'=>$saveContent)) or die("539: ".mysql_error());
		} else {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mailformplusadmin_fields', array('pid'=>intval($pageid),'submittedfields'=>$saveContent)) or die("541: ".mysql_error());
		}
		
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		
		$content .= $this->printTableHeader();
		$content .= "<tr><td align=\"center\" style=\"height:100px;\"><h1>" . $LANG->getLL("items_saved") . "</h1></td></tr>";
		$content .= "<tr><td align=\"center\" style=\"height:100px;\"><input onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;SET[function]=1');\" type=\"button\" name=\"btnBack\" id=\"btnBack\" value=\"" . $LANG->getLL("back") . "\" title=\"" . $LANG->getLL("back") . "\" /></td></tr>";
		$content .= "</table>";
		return $content;	
	}
		
	function deleteRow($pageid, $type) {
		//$log_type = t3lib_div::_POST("log_type");
		global $LANG;
		$uid = t3lib_div::_GP("uid");
		//die("$uid-$pageid-$type");
		$screen = t3lib_div::_GP("screen");
		$uidArray = explode(",", $uid);
		foreach($uidArray as $myUid) {
			$myUidArray = explode(':', $myUid);
			$log_type = $myUidArray[0];
			$myUid = $myUidArray[1];

			if($log_type=="formhandler") {
				$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_formhandler_log", "uid=" . intval($myUid)) or die("917: ".mysql_error());
			} else {
				$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_thmailformplus_log", "uid=" . intval($myUid)) or die("915: ".mysql_error());
			}
		}
		
		$content .= $this->printTableHeader();
		$content .= "<tr><td colspan=\"2\" align=\"center\" style=\"height:100px;\"><h1>Posten/posterna &auml;r raderade!</h1></td></tr>";
		$content .= "<tr><td colspan=\"2\" align=\"center\" style=\"height:100px;\"><input onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;SET[function]=1');\" type=\"button\" name=\"btnBack\" id=\"btnBack\" value=\"" . $LANG->getLL("back") . "\" title=\"" . $LANG->getLL("back") . "\" /></td></tr>";
		$content .= "</table>";
		return $content;
	}
	
	function exportChoice($pageid, $type, $pidList)
	{
		global $LANG;
		$screen = t3lib_div::_GP("screen");
		$resultArray = array();
		$big_one = array();
		$halfArray = array();
		
		if($pidList) {
			$where = " WHERE pid IN($pidList)";
		} else {
			$where = " WHERE pid=" . intval($pageid);
		}
		
		//if($type=="mailformplus") {
			//$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_thmailformplus_log", "pid=" . intval($pageid), "", "", "") or die("626: ".mysql_error());
		$sql = "SELECT params, 'formhandler' AS  log_type ";
		$sql .= "FROM tx_formhandler_log $where";
		$sql .= " UNION ";
		$sql .= "SELECT submittedfields AS params, 'mailformplus' AS  log_type ";
		$sql .= "FROM tx_thmailformplus_log $where";
		//die($sql);
		
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$log_type = $row["log_type"];
			$params = $row["params"];
			if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);
			$paramsArray = unserialize($params);
			unset($paramsArray['randomID']);
			unset($paramsArray['removeFile']);
			unset($paramsArray['removeFileField']);
			unset($paramsArray['step-2-next']);
			unset($paramsArray['submitField']);
			unset($paramsArray['submitted']);
			unset($paramsArray['id']);
			unset($paramsArray['submitted']);
			unset($paramsArray['L']);
			unset($paramsArray['formToken']);
			
			if(is_array($paramsArray)) $resultArray = array_merge($resultArray, array_keys($paramsArray));
		}
		
		$resultArray = array_unique($resultArray);

		asort($resultArray);		
		
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		
		$content .= $this->printTableHeader();
		
		$content .= "<tr><td style=\"height:28px; background-color:#cccccc\"><b>" . $LANG->getLL("choose_fields") . "</b></td>
		<td style=\"height:28px; background-color:#cccccc\"><input name=\"allBox\" type=\"checkbox\" value=\"\" checked=\"checked\" onclick=\"checkAll(this.checked,'fieldBox');\" /></td>
		</tr>";
		
		foreach($resultArray as $key => $value) {
			if($i%2) {
				$bgcolor = "#ffffff";
			} else {
				$bgcolor = "";
			}
			if($value != "id" and $value != "submitted" and $value != "L") $content .= "<tr><td style=\"background-color:$bgcolor; width:400px;\">$value</td><td style=\"background-color:$bgcolor; width:300px;\"><input name=\"fieldBox\" type=\"checkbox\" value=\"$value\" checked=\"checked\" /></td></tr>";
			$i++;
		}
		
		$content .= "
		<tr>
			<td colspan=\"2\">&nbsp;</td>
		</tr>		
		<tr>
			<td colspan=\"2\" style=\"height:28px; background-color:#cccccc;\"><b>" . $LANG->getLL("choose_format") . "</b></td>
		</tr>
		
		<tr>
			<td style=\"background-color:#ffffff;\"><input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat_pdfList\" value=\"cblPdfList\" checked /> " . $LANG->getLL("pdf_list") . "</td>
			<td style=\"background-color:#ffffff;\">" . $LANG->getLL("header") . ": <input type=\"text\" name=\"txtHeader\" id=\"txtHeader\" /></td>
		</tr>
		  
		<tr>
			<td><input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblTabSep\" /> " . $LANG->getLL("tab_file") . "</td>
			<td></td>
		</tr>
		  
		<tr>
			<td style=\"background-color:#ffffff;\"> <input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblKommaSep\" /> " . $LANG->getLL("comma_file") . "</td>
			<td style=\"background-color:#ffffff;\"></td>
		</tr>
		  
		<tr>
			<td> <input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblCsv\" /> " . $LANG->getLL("csv_file") . "</td>
			<td></td>
		</tr>
		  
		<tr>
			<td style=\"background-color:#ffffff;\"> <input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblLabels\" /> " . $LANG->getLL("pdf_labels") . "</td>
			<td style=\"background-color:#ffffff;\">" . $LANG->getLL("extra") . ": <input type=\"text\" name=\"txtLabelExtra\" id=\"txtLabelExtra\" /></td>
		</tr>
		
		<tr>
			<td colspan=\"2\">&nbsp;</td>
		</tr>
		  
		<tr>
			<td colspan=\"2\"><input onclick=\"exportDo('$pageid', '$screen', '$type');\" type=\"button\" name=\"btnExport\" id=\"btnExport\" value=\"" . $LANG->getLL("export") . "\" title=\"" . $LANG->getLL("export") . "\" />
			  &nbsp;&nbsp;
			  <input onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;SET[function]=1');\" type=\"button\" name=\"btnCancel\" id=\"btnCancel\" value=\"" . $LANG->getLL("cancel") . "\" title=\"" . $LANG->getLL("cancel") . "\" /></td>
		</tr>	
		</table>";
		return $content;	
	}
	
	function exportDo($pageid, $type, $pidList) {
		session_cache_limiter('private'); 
		session_start();
		if (!t3lib_extMgm::isLoaded('fpdf')) return "fpdf library not loaded!";
		$i = 0;
		$ii = 0;
		$i = 0;
		$p = 1;
		$x = 0;
		$y = 0;
		$z = 0;
		$zz = 0;
		$keyArray = array();
		$submittedfieldsArray = array();
		$resultArray = array();
		
		$screen = t3lib_div::_GP("screen");
		$fields = t3lib_div::_GP("fields");
		$choices = t3lib_div::_GP("choices");
		$lextra = t3lib_div::_GP("lextra");
		//echo $choices;
		$chosenFieldsArray = explode(",", $fields);
		array_unshift($chosenFieldsArray, 'date');
		array_unshift($chosenFieldsArray, 'ok');
		//if($type=="mailformplus") {
			
		if($pidList) {
			$where = " WHERE pid IN($pidList)";
		} else {
			$where = " WHERE pid=" . intval($pageid);
		}
			
		//	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid, submittedfields", "tx_thmailformplus_log", $where, "", "", "") or die("723: ".mysql_error());
		$sql = "SELECT uid, params, 'formhandler' AS  log_type, FROM_UNIXTIME(crdate,'%Y-%m-%d') AS logdate, ok ";
		$sql .= "FROM tx_formhandler_log $where";
		$sql .= " UNION ";
		$sql .= "SELECT uid, submittedfields AS params, 'mailformplus' AS  log_type, FROM_UNIXTIME(logdate,'%Y-%m-%d'), ok ";
		$sql .= "FROM tx_thmailformplus_log $where";
		$sql .= " ORDER BY logdate DESC";
		//die($sql);
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$log_type = $row["log_type"];
			$logdate = $row["logdate"];
			$params = $row["params"];
			$uid = $row["uid"];
			$ok = $row["ok"];
			if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);
			$paramsArray = unserialize($params);
						/*print "<pre>";
			print_r($paramsArray);
			print "</pre>";*/
			unset($paramsArray['randomID']);
			unset($paramsArray['removeFile']);
			unset($paramsArray['removeFileField']);
			unset($paramsArray['step-2-next']);
			unset($paramsArray['submitField']);
			unset($paramsArray['submitted']);
			unset($paramsArray['id']);
			unset($paramsArray['submitted']);
			unset($paramsArray['L']);
			//Add date
			
		//	$paramsArray['date'] = $logdate;
			//array_unshift( $paramsArray, '[date]=>'.$logdate );
			$paramsArray=array_merge(array("date"=>$logdate),$paramsArray);
			$paramsArray=array_merge(array("ok"=>$ok),$paramsArray);  
			
			
			array_push($resultArray, $paramsArray);
			
		}
	/*	print '<pre>';
		print_r($resultArray);
		print '</pre>';
		die();*/
		/*	while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$i=0;
				$uid = $row["uid"];
				$pid = $row["pid"];
				$submittedfields = $row["submittedfields"];
				$submittedfields = preg_replace("/~/", ";~", $submittedfields, 1);
				$logdate = $row["logdate"];
				$downloaded = $row["downloaded"];
				$submittedfields = str_replace("\ntest;", "", $submittedfields);
				$submittedfields = str_replace("test;", "", $submittedfields);
				
				$submittedfields = str_replace("~", "", $submittedfields);
				$submittedfieldsArray = explode(";", $submittedfields);
				$addVar = count($submittedfieldsArray) / 2;
				
				for($i == 0; $i < $addVar; $i++) {
					$myArray[$ii][$submittedfieldsArray[$i]] = $submittedfieldsArray[$i+$addVar];
				}
				$ii++;
			}
		} else {
			$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("field_name, field_value, email_uid", "tx_pksaveformmail_fields", "pid=" . intval($pageid), "", "", "") or die("884: ".mysql_error());
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$uid = $row["uid"];
				$pid = $row["pid"];
				$crdate = $row["crdate"];
				$email_uid = $row["email_uid"];
				$field_name = $row["field_name"];
				$field_value = $row["field_value"];
				$myArray[$email_uid][$field_name] = $field_value;
			}			
		}*/
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);

		switch($choices) {
			case "cblPdfList":
				$pdf = new PDF();
				$pdf->AddPage("L", "A4");
				$pdf->SetAutoPageBreak(false);
				
				if($lextra) {
					$pdf->SetFont('Helvetica', 'B', 14);
					$pdf->Cell(300,10,$lextra,0,1,C,0);
					$pdf->Ln(2);
				}
				
				$pdf->SetMargins(10,10);
				$pdf->SetFont('Helvetica', '', 9);
				break;
			case "cblLabels":
				$pdf = new PDF();
				$pdf->AddPage("P", "A4");
				$pdf->SetMargins(0,0);
				$pdf->SetAutoPageBreak(false);
				$pdf->SetFont('Helvetica', '', 9);		
				break;
		}
		
		$antalPoster = count($resultArray);
		/*print "<pre>";
		print_r($resultArray);
		print "</pre>";
		die();*/
		ksort($resultArray);
		ksort($chosenFieldsArray);
		foreach($resultArray as $key => $value) {
			$zz++;
			foreach($chosenFieldsArray as $key1 => $value1) {
				switch($choices) {
					case "cblPdfList":
						$pdf->SetMargins(10,10);
						//Ny rad
						if($key != $old_key and $z > 0) {
							$pdf->Ln(6);
							$y++;
						//rubriker
						} elseif($z==0) {
							foreach($chosenFieldsArray as $key2 => $value2) {
								$pdf->SetFont('Helvetica', 'B', 10);
								if($key2=='date'){
									$pdf->Cell(20,6,$value2,1,0,L,0);	
								} else {
									$pdf->Cell(50,6,$value2,1,0,L,0);
								}
							}
							$pdf->Ln(6);
							$y++;
							$pdf->SetFont('Helvetica', '', 9);
						}
						
						//Ny sida
						if($y==28) {
							$pdf->Cell(300,20,$p,0,1,C,0);
							$pdf->AddPage("L", "A4");
							if($lextra) {
								$pdf->SetFont('Helvetica', 'B', 14);
								$pdf->Cell(300,15,$lextra,0,1,C,0);
								$pdf->Ln(2);
							}
							$y = 0;
							$p++;
						}
						//L�gg in v�rdet
						if($key1=='date'){
							$pdf->Cell(20,6,utf8_decode($value[$value1]),1,0,L,0);
						} else {
							$pdf->Cell(50,6,utf8_decode($value[$value1]),1,0,L,0);
						}
						break;
					case "cblTabSep":
						if($z==0) {
							foreach($chosenFieldsArray as $key2 => $value2) {
								if($s) $s .= "\t";
								$s .= '"' . $value2 . '"';
							}
							$s .= "\r\n";
							$expContent .= $s;
							$s = "";
						}
						//if($z==0) $s .=
						if($s) $s .= "\t";
						$s .= '"' . utf8_decode($value[$value1]) . '"';
						break;
					case "cblKommaSep":
						if($z==0) {
							foreach($chosenFieldsArray as $key2 => $value2) {
								if($s) $s .= ",";
								$s .= '"' . $value2 . '"';
							}
							$s .= "\r\n";
							$expContent .= $s;
							$s = "";
						}
						if($s) $s .= ",";
						$s .= '"' . utf8_decode($value[$value1]) . '"';					
						break;			
					case "cblCsv":
						if($z==0) {
							$s .= $this->getHeaders($chosenFieldsArray, ';');
							$s .= "\r\n";
						}
						if($s) $s .= ";";
						$s .= '"' . utf8_decode($value[$value1]) . '"';
						//preg_replace('/\n/', ';', $params, 1);
						break;
					case "cblLabels":
						if($s) $s .= "\n";
						$s .= $value[$value1];
						break;
					}
					$old_key = $key;
					$z++;
				}
				//Ny rad
				switch($choices) {
					case "cblTabSep":
						if(substr($s, -2) == "\t") $s = substr($s, 0, strlen($s) - 2);			
						$s .= "\r\n";
						$expContent .= $s;
						$s = "";
						break;
					case "cblKommaSep":
						if(substr($s, -1) == ",") $s = substr($s, 0, strlen($s) - 1);			
						$s .= "\r\n";
						$expContent .= $s;
						$s = "";
						break;
					case "cblCsv":
						if(substr($s, -1) == ";") $s = substr($s, 0, strlen($s) - 1);			
						$s .= "\r\n";
						$expContent .= $s;
						$s = "";
						break;
					case "cblLabels";
						//Ny etikett
						if($lextra) $s .= "$lextra\n";
						if(substr($s, -2) == "\n") $s = substr($s, 0, strlen($s) - 2);
						$this->Avery7160($x,$y,$pdf,$s);
						$x++;
						//$y++;
						if($x == 3) {
							$x = 0;
							$y++;
						}
						if($y == 7) {
							$y = 0;
							$pdf->AddPage();
						}
						$s = "";
						break;
				}
			}
				//////////////////
			switch($choices) {
				case "cblPdfList":
					$i++;
					$pdf->SetY(-15); 
					$pdf->Cell(300,15,$p,0,1,C,0);
					$pdf->Output();
					break;
				case "cblTabSep":
					header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
					header("Content-type: text/plain;");
					header("Content-disposition: attachment; filename=mailform_" . time(). ".txt");
					print $expContent;
					break;
				case "cblKommaSep":
					header("Cache-Control: pre-check=0, post-check=0, max-age=0");
					header("Content-type: text/plain;");
					header("Content-disposition: attachment; filename=mailform_" . time(). ".txt");
					print $expContent;
					break;			
				case "cblCsv":
					header("Cache-Control: pre-check=0, post-check=0, max-age=0");
					header("Content-type: application/vnd.ms-excel; ");
					header("Content-disposition: attachment; filename=mailform_" . time(). ".csv");
					print $expContent;
					break;
				case "cblLabels":
					$pdf->Output();
					break;
			}
				
			exit;

	}
	
	function getHeaders($chosenFieldsArray, $separator)
	{
		$s = '';
		foreach($chosenFieldsArray as $key2 => $value2) {
			if($s) $s .= "$separator";
			$s .= '"' . $value2 . '"';
		}
		return $s;
	}
	
	function newRow()
	{
		
	}
	
	function update()
	{
		global $LANG;
		//onclick=\"jumpToUrl('index.php?id=$pageid&amp;uid=$uid&amp;pid=$pid&amp;screen=$screen&amp;SET[function]=7');\"
		//a:9:{s:11:"looking_for";s:2:"aa";s:7:"missing";s:2:"aa";s:8:"randomID";s:32:"38578035ffd234e003bbfaa5d776fa6b";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:6:"Skicka";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";s:12:"visitor_type";s:8:"external";}
		//screen, SETArray, id
		$postArray = t3lib_div::_POST();
		$pageId = t3lib_div::_POST("id");
		$screen = t3lib_div::_POST("screen");
		$uid = t3lib_div::_POST("uid");
		$pid = t3lib_div::_POST("pid");
		$log_type = t3lib_div::_POST("log_type");
				
		unset($postArray["id"]);
		unset($postArray["uid"]);
		unset($postArray["pid"]);
		unset($postArray["screen"]);
		unset($postArray["SET"]);
		unset($postArray["log_type"]);

		if($log_type=="formhandler") {
			$table = "tx_formhandler_log";
			$params = 'a:' . count($postArray) . ':{';
			foreach($postArray as $key => $value) {
				if(is_array($value)) {
					$params .= 's:' . strlen($key) . ':"' . addslashes(htmlspecialchars($key)) . '";a:' . count($value) . ':{';
					foreach($value as $key1 => $value1) {
						//i:0;s:6:"sports";i:1;s:5:"music";
						$params .= 'i:' . addslashes(htmlspecialchars($key1)) . ';s:' . strlen($value1) . ':"' . addslashes(htmlspecialchars($value1)) . '";';
					}
					$params .= '}';
				} else {
					$params .= 's:' . strlen($key) . ':"' . addslashes(htmlspecialchars($key)) . '";s:' . strlen($value) . ':"' . addslashes(htmlspecialchars($value)) . '";';
				}
				//a:11:{s:11:"contact_via";s:5:"email";s:5:"email";s:26:"tomas.havner@kansli.lth.se";s:9:"firstname";s:2:"��";s:9:"interests";a:2:{i:0;s:6:"sports";i:1;s:5:"music";}s:8:"lastname";s:2:"��";s:8:"randomID";s:32:"8acaf7d1f532afcca7d82d72d1603c26";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:4:"Send";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";}
			}
			$params .= '}';
			$keys = array_keys($valueList);
			$hash = md5(serialize($keys));
			$updateArray = array(
				"tstamp" => time(),
				"ip" => t3lib_div::getIndpEnv('REMOTE_ADDR'),
				"params" => $params,
				"key_hash" => $hash
			);
		} else {
			$table = "tx_thmailformplus_log";
			$updateKeys = "id;submitted;L;type";
			$updateValues = "~$uid;~1;~;~";
			foreach($postArray as $key => $value) {
				$updateKeys .= ";$key";
				$updateValues .= ";~$value";
			}
			$updateArray = array(
				"submittedfields" => "$updateKeys\n$updateValues"
			);
			//test;id;submitted;L;type;name;subject;program;datum;deltagare;innehall;reflektioner;moment;tanka
			//test;~5341;~1;~;~;~tt;~tt;~tt;~tt;~tt;~tt;~tt;~tt;~tt
		}
		
		/*print "<pre>";
		print_r($updateArray);
		print "</pre>";*/
		//die($table . "uid=" . intval($uid) . " AND pid=" . intval($pid));
		$res = $GLOBALS["TYPO3_DB"]->exec_UPDATEquery($table, "uid=" . intval($uid) . " AND pid=" . intval($pid), $updateArray);
		$result = mysql_affected_rows();
		$message = "";
		if($result == -1) {
			$message = "An error occured";
		} elseif($result == 0) {
			$message = "No rows affected";
		} else {
			$message = "$result rows affected";
		}
		
		$content .= $this->printTableHeader();
		$content .= "<tr><td colspan=\"2\" align=\"center\" style=\"height:100px;\"><h1>$message</h1></td></tr>";
		$content .= "<tr><td colspan=\"2\" align=\"center\" style=\"height:100px;\"><input onclick=\"jumpToUrl('index.php?id=$pageId&amp;screen=$screen&amp;SET[function]=1');\" type=\"button\" name=\"btnBack\" id=\"btnBack\" value=\"" . $LANG->getLL("back") . "\" title=\"" . $LANG->getLL("back") . "\" /></td></tr>";
		$content .= "</table>";
		
		return $content;
	}
	
	function convert_mailformplusdata($params)
	{
		//test;id;submitted;L;type;name;subject;program;datum;deltagare;innehall;reflektioner;moment;tanka;kontakt
		//test;id;submitted;name;email;telephone;comment test;~5469;~1;~ss;~tomas.havner@kansli.lth.se;~ff;~ff
		//test;~5345;~1;~;~;~ww;~ww;~ww;~ww;~ww;~ww;~ww;~ww;~ww;~ja
		//					$submittedfields = preg_replace("/~/", ";~", $submittedfields, 1);

		if(substr($params, 0, 5) == "test;") $params = str_replace("test;", "", $params);
		//$count = 1;
		//$params = str_replace("\n", "---", $params, $count);
		$params = preg_replace('/\n/', ';', $params, 1);
		//$params = preg_replace('/\n/', 'hhh', $params);
		//if(strstr($params, '�')) die($params);
		//$submittedfields = str_replace("~", "", $submittedfields);

		$paramsArray = explode(";", $params);
		$resultArray = array();
		$i=0;
		$content = "";

		$half_array = count($paramsArray) / 2;
		foreach($paramsArray as $value) {
			if(strstr($value,"~")) {
				$resultArray[$paramsArray[$i - $half_array]] = substr($value, 1);
			}
			$i++;
		}
		/*echo "<pre>";
		print_r($resultArray);
		echo "</pre>";*/
		
		$content = 'a:' . count($resultArray) . ':{';
		foreach($resultArray as $key => $value) {
			$content .= 's:' . strlen($key) . ':"' . addslashes(htmlspecialchars($key)) . '";s:' . strlen($value) . ':"' . addslashes(htmlspecialchars($value)) . '";';
		}
		$content .= '}';
		
		return $content;
	}
	
	function Avery7160($x, $y, &$pdf, $Data) {

		$LeftMargin = 10;
		$TopMargin = 20;
		$LabelWidth = 63;
		$LabelHeight = 40;
		// Create Co-Ords of Upper left of the Label
		$AbsX = $LeftMargin + (($LabelWidth + 4.22) * $x);
		$AbsY = $TopMargin + ($LabelHeight * $y);
		
		// Fudge the Start 3mm inside the label to avoid alignment errors
		$pdf->SetXY($AbsX+3,$AbsY+3);
		$pdf->MultiCell($LabelWidth-8,4.5,$Data);
		
		return;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailformplus_admin/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailformplus_admin/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_mailformplusadmin_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>