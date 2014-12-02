<?php

class tx_mailformplusadmin {
    
    
    public function __construct()
    {
    }
    
    function ajaxFunctions($action,$scope,$query,$lang,$user,$pid,$firstrun)
    {
        switch($action) {
            case "saveFormStructure":
                return $this->saveFormStructure($scope,$query);
                break;
            case "getFormStructure":
                return $this->getFormStructure($scope);
                break;
            case "printList":
                echo $this->printList($scope,$firstrun);
                break;
            case "printDetail":
                return $this->printDetail($scope,$pid);
                break;
            case "printColumns":
                return $this->printColumns($scope,$pid,$firstrun);
                break;
            case "saveColumns":
                return $this->saveColumns($scope,$query,$pid,$firstrun);
                break;
            case "exportChoice":
                return $this->exportChoice($scope,$pid);
                break;
            case "exportDo":
                return $this->exportDo($scope,$query,$pid);
                break;            
            case "deleteRow":
                return $this->deleteRow();
                break;
            case "updateRow":
                return $this->updateRow();
                break;
            case "setOk":
                return $this->setOk($scope,$query);
                break;
            case "printTableHeader":
                return $this->printTableHeader($scope,$query,$pid);
                break;
	    case "getColumns":
                return $this->getColumns($scope);
                break;
        }
    }
    
    function printHeader($pageId)
    {
        global $LANG;
        if(!$pageId and !$query) {
            $disabled = " disabled=\"disabled\"";
        }
        
        $content .= "<table border=\"0\" style=\"margin-left:10px; width:700px; margin-bottom:10px; padding:5px;\"><tr>";
        $content .= "<td align=\"center\" width=\"20%\" ><input $disabled onclick=\"ajax('printList','$pageId','','$pageId','','');\" type=\"button\" name=\"btnList\" id=\"btnList\" value=\"list\" title=\"list\" /></td>";
        $content .= "<td align=\"center\" width=\"50%\"><input $disabled onclick=\"ajax('printColumns','$pageId','','$pageId','','');\" type=\"button\" name=\"btnShow\" id=\"btnShow\" value=\"choose\" title=\"choose\" /></td>";
        $content .= "<td align=\"center\" width=\"30%\"><input $disabled onclick=\"ajax('exportChoice','$pageId','','$pageId','','');\" type=\"button\" name=\"btnExport\" id=\"btnExport\" value=\"export\" title=\"export\" /></td>";
        $content .= "<a href=\"#\" class=\"paginate_button current\" aria-controls=\"mailformplus_adminTable\" data-dt-idx=\"1\" tabindex=\"0\">10000</a></tr></table>";
        return $content;
    }
    
    function getColumns($scope)
    {
	$output = array();
	$paramsArray = array();
	$params;
	
	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("params", "tx_formhandler_log", "pid=" . intval($pid)) or die("72: ".$pid.mysql_error());
	while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
	    $params = $row["params"];
	    
	    $paramsArray = unserialize($params);
	    
	    unset($paramsArray['randomID']);
	    unset($paramsArray['removeFile']);
	    unset($paramsArray['removeFileField']);
	    unset($paramsArray['step-2-next']);
	    unset($paramsArray['submitField']);
	    unset($paramsArray['submitted']);
	    unset($paramsArray['submitted']);
	    
	    array_push($output,$paramsArray);
	}
	    
	$output = array_unique($output);
	


	while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
	    $i=0;
	    $uid = $row["uid"];
	    $pid = $row["pid"];
	    $logdate = $row["logdate"];
	    $params = $row["params"];
	    //echo $params . "<br />";
	    //if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);
	    //$params = substr($params, strpos($params,"{")+1, strlen($params)-strpos($params,"{")-2);
	    $ok = $row["ok"];

	    
	    /*print "<pre>";
	    print_r($paramsArray);
	    print "<pre>";*/
	    //a:11:{s:11:"contact_via";s:5:"email";s:5:"email";s:26:"tomas.havner@kansli.lth.se";s:9:"firstname";s:2:"dd";s:9:"interests";a:2:{i:0;s:6:"sports";i:1;s:5:"music";}s:8:"lastname";s:2:"dd";s:8:"randomID";s:32:"8f15a9f644088512510b7c8f3edeb742";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:4:"Send";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";}
	    if(is_array($paramsArray)) {
		foreach($paramsArray as $key => $value) {

		    //echo $ii;
		    $arrayValue = "";
		    if(is_array($value)) {
			foreach($value as $key1 => $value1) {
			    if($arrayValue) {
				$arrayValue .= ",";
			    }
			    $arrayValue .= $key1;
			}
		    } else {
			if($arrayValue) {
			    $arrayValue .= ',';
			}
			$arrayValue = $key;
		    }

		    if(is_array($chosenFieldsArray) and array_search($key, $chosenFieldsArray)!==false) {
			$myArray[$uid][$key] = $arrayValue;
			$myArray[$uid]['ok'] = $ok;
			$myArray[$uid]['pid'] = $pid;
			$myArray[$uid]['logdate'] = $logdate;
		    } else if($i < 16) {
			$myArray[$uid][$key] = $arrayValue;
			$myArray[$uid]['ok'] = $ok;
			$myArray[$uid]['logdate'] = $logdate;
			$myArray[$uid]['pid'] = $pid;
			if($ii==0){
			    if($chosenFields) {
				$chosenFields .= ",";
			    }
			    $chosenFields .= $key;
			}
		    }
		    //echo $ii;
		    $i++;
		}
		$aItem = array($arrayValue);
		$output['aaData'][] = $aItem;
	    }
	    //echo $ii;
	    $ii++;			
	}
	return json_encode($output);
    }
	
    function printList($pid,$firstrun)
    {

        /*global $LANG;
        $backpath = 'typo3/';
        require (PATH_typo3 . 'init.php');
        require (PATH_typo3 . 'template.php');*/
        //$GLOBALS['LANG']->includeLLFile('EXT:setup/mod/locallang.xml');
        //$GLOBALS['LANG']->includeLLFile("EXT:mailformplus_admin/mod1/locallang.xml");
        if($pid) {

	    // SQL limit
	    $sLimit = '';
	    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
		$sLimit = (int)$_GET['iDisplayStart'] . ', ' . (int)$_GET['iDisplayLength'];
	    }
    // SQL order
	    /*$aColumns = array('untitled');
	    $sOrder = '';
	    if (isset($_GET['iSortCol_0'])) {
		for ($i=0 ; $i<(int)$_GET['iSortingCols'] ; $i++) {
		    if ( $_GET[ 'bSortable_'.(int)$_GET['iSortCol_'.$i] ] == 'true' ) {
			$sOrder .= '`'.$aColumns[ (int)$_GET['iSortCol_'.$i] ].'` '.
			    ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .', ';
		    }
		}

		$sOrder = substr_replace($sOrder, '', -2);
		if ($sOrder == 'ORDER BY') {
		    $sOrder = '';
		}
	    }*/

	    // SQL where
	    //$sWhere = 'WHERE 1';
	    $sWhere = "pid=" . intval($pid);
	    if (isset($_GET['sSearch']) && $_GET['sSearch'] != '') {
		$sWhere .= ' AND (';
		for ($i=0; $i<count($aColumns) ; $i++) {
		    if (isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == 'true') {
			$sWhere .= '`' . $aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' OR ";
		    }
		}
		$sWhere = substr_replace( $sWhere, '', -3 );
		$sWhere .= ')';
	    }

            $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_mailformplusadmin_fields", "pid=" . intval($pid), "", "", "") or die("258: ".$pid.mysql_error());
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $chosenFields = $row["submittedfields"];
            }

            if($chosenFields) $chosenFieldsArray = explode(",", $chosenFields);
            $chosenFields = "";
	    
	    $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("SQL_CALC_FOUND_ROWS uid, pid, FROM_UNIXTIME(crdate,'%Y-%m-%d') AS logdate, params, ok", "tx_formhandler_log", "$sWhere", "", "logdate DESC", "$sLimit") or die("258: ".$pid.mysql_error());
            $totRows = mysql_query("SELECT FOUND_ROWS()");
            $total_records = mysql_result($totRows, 0);
	    
	    $output = array(
		'sEcho' => intval($_GET['sEcho']),
		'iTotalRecords' => count($res),
		'iTotalDisplayRecords' => $total_records,
		'aaData' => array(),
	    );
	    
$aItem = array();

            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $i=0;
                $uid = $row["uid"];
                $pid = $row["pid"];
                $logdate = $row["logdate"];
                $params = $row["params"];
                $ok = $row["ok"];

                $paramsArray = unserialize($params);
                /*die(is_array($paramsArray));*/
                unset($paramsArray['randomID']);
                unset($paramsArray['removeFile']);
                unset($paramsArray['removeFileField']);
                unset($paramsArray['step-2-next']);
                unset($paramsArray['submitField']);
                unset($paramsArray['submitted']);
                /*print "<pre>";
                print_r($chosenFieldsArray);
                print "</pre>";*/
                //a:11:{s:11:"contact_via";s:5:"email";s:5:"email";s:26:"tomas.havner@kansli.lth.se";s:9:"firstname";s:2:"dd";s:9:"interests";a:2:{i:0;s:6:"sports";i:1;s:5:"music";}s:8:"lastname";s:2:"dd";s:8:"randomID";s:32:"8f15a9f644088512510b7c8f3edeb742";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:4:"Send";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";}
                if(is_array($paramsArray)) {
		    $aItem = array();
		    $arrayValue = "";
		    foreach($paramsArray as $key => $value) {
			if($arrayKey) {
				    $arrayKey .= ",";
				}
				$arrayKey .= $key;
				
				$aItem[] = $value;
			//$aItem[] = $value;
			//echo $ii;
			/*if(is_array($value)) {
			    foreach($value as $key1 => $value1) {
				if($arrayKey) {
				    $arrayKey .= ", ";
				}
				$arrayKey .= "{sTitle:$key1}";
				
				if($arrayValue) {
				    $arrayValue .= ", ";
				}
				$arrayValue .= $value1;
			    }
			} else {*/
			    
			    
			//}
			/*if(is_array($chosenFieldsArray) and array_search($key, $chosenFieldsArray)!==false) {
			    $myArray[$uid][$key] = $arrayValue;
			    $myArray[$uid]['ok'] = $ok;
			    $myArray[$uid]['pid'] = $pid;
			    $myArray[$uid]['logdate'] = $logdate;
			} else if($i < 16) {
			    $myArray[$uid][$key] = $arrayValue;
			    $myArray[$uid]['ok'] = $ok;
			    $myArray[$uid]['logdate'] = $logdate;
			    $myArray[$uid]['pid'] = $pid;
			    if($ii==0){
				if($chosenFields) $chosenFields .= ",";
				$chosenFields .= $key;
			    }
			}*/
			//echo $ii;
		    }
		    //$paramsArray['DT_RowId'] = $uid;
		    //$aItem[] = array($uid,$arrayValue);
		}
		$aItem['DT_RowId'] = $uid;
                //echo $ii;
		$output['aaData'][] = $aItem;
		
                $ii++;			
            }
	    //echo $arrayKey;
	    /*print "<pre>";
            print_r($output);
            print "<pre>";*/
	    //$tickets = array(array(1, 'Options 1'), array(2, 'Options 2'
	    $headers = implode(',',array_unique(explode(',', $arrayKey)));
	    return json_encode(array('tickets' => $output, 'headers' => $headers));
            if($chosenFields) $chosenFieldsArray = explode(",", $chosenFields);
            $pages = ceil($total_records / $rows_per_page);
            //print_r($myArray);

            $GLOBALS["TYPO3_DB"]->sql_free_result($res);

           

	    $myContent = '';
            if(is_array($myArray)) {
                foreach($myArray as $key => $value) {
                    $myContent .= "<tr>";
                    $xx=0;
                   
                    if($x==0) {
                        $header .= "<th style=\"width:40px;\">Date</th>";
                    }
                    $myContent .= "<td>" . $value['logdate'] . "</th>";
                    
                    
                    foreach($value as $key1 => $value1) {
                        if($key1!='logdate') {
                        if($x==0) {
                            $header .= "<th>$key1</th>";
                        }
                        $myContent .= "<td>" . $value1 . "</td>";
                        }
                        $xx++;
                    }
                    
                    if($x==0) {
                        $header .= "<th style=\"width:40px;\"></th>";
                        $header .= "<th>
                        <input type=\"checkbox\" name=\"\" id=\"\" value=\"\" onclick=\"checkAll(this.checked,'deleteBox');\" />
                        <img src=\"/typo3conf/ext/mailformplus_admin/mod1/garbage.gif\" border=\"0\" onclick=\"deleteRow('$pid'); return false; \" />
                        </th>";
                        //Novo begin
                        $header .= "<th>
                        <img src=\"/typo3conf/ext/mailformplus_admin/mod1/ok.png\" border=\"0\" />
                        </th>";
                        //Novo ends
                    }
                    $okChecked="";
                    $pid = $myArray[$key]['pid'];
                    if($myArray[$key]['ok']) {
                        $okChecked=" checked=\"checked\"";
                    }
                    $myContent .= "<td><a href=\"#\" onclick=\"ajax('printDetail','$key','','$pid','');\"><img src=\"/typo3conf/ext/mailformplus_admin/mod1/edit2.gif\" border=\"0\" title=\"\" /></a></td>";
                    $myContent .= "<td><input name=\"deleteBox\" type=\"checkbox\" value=\"$key\" /></td>";
                    //Novo begin
                    $myContent .= "<td><input name=\"okBox\" type=\"checkbox\" value=\"$key\"$okChecked onclick=\"ajax('setOk','$key',this.checked,'','');\" /></td>";
                    //Novo end
                    $xx = $xx+3;
                    $myContent .= "</tr>";
                    $x++;
                }
        }

        /*for($x == $x; $x < $rows_per_page; $x++) {
                if($x%2) {
                        $bgcolor = "";
                } else {
                        $bgcolor = "#ffffff";
                }
                $myContent .= "<tr><td colspan=\"$xx\" style=\"background-color:$bgcolor; height:26px;\">&nbsp;</td></tr>";
        }*/

        $content .= "<thead><tr>$header</tr></thead><tbody>$myContent</tbody>";

        /*if($type=="mailformplus") {
                for ($ii = $i; $ii < $rows_per_page; $ii++) {
                        $content .= "<tr><td colspan=\"8\" style=\"height:22px;\"></td></tr>";
                }
        }
        */
        /*if($pages > 1){
            $content .= "<tr><td align=\"center\">";
            if ($screen > 0) {
                    $myScreen = $screen - 1;
                    $content .= "<a href=\"#\" onClick=\"ajax('index.php?&amp;id=$pid&amp;type=$type&amp;screen=$myScreen');\"><< " . $LANG->getLL("previous") . "</a>";
            } else {
                    $content .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }

            if($pages > 1) {
                for ($i = 0; $i < $pages; $i++) {
                    $ii = $i + 1;
                    if($i == $screen) {
                            $content .= " <b>$ii</b>";
                    } else {
                            $content .= " <a href=\"#\" onClick=\"ajax('index.php?&amp;id=$pid&amp;type=$type&amp;screen=$i');\">$ii</a>";
                    }
                    if ($i < $pages-1) $content .= " |";
                }
            }

            if ($screen + 1 < $pages) {
                $myScreen = $screen + 1;
                $content .= "<a href=\"#\" onclick=\"ajax('index.php?&amp;id=$pid&amp;type=$type&amp;screen=$myScreen');\"> " . $LANG->getLL("next") . " >></a>";
            }

            $content .= "</td></tr>";
        }*/

        //Footer
        /*$randomVar = md5(time());
        $content .= "<tr><td align=\"center\" style=\"background-color:#cccccc;\">";
        $content .= "Number of records: <b>$total_records</b> | Search: ";
        $content .= " <input name=\"query\" id=\"query\" type=\"text\" value=\"$query\" />";
        $content .= " <input onclick=\"ajax('index.php?id=$pid&amp;screen=$screen&amp;sid=$randomVar&amp;type=$type&amp;query='+document.getElementById('query').value+'&amp;SET[function]=1');\" type=\"button\" name=\"btnSearch\" id=\"btnSearch\" value=\"Search\" title=\"Search\" />";
        $disabled = "";
        if(!$query) $disabled = " disabled=\"disabled\""; 
            $content .= " <input$disabled onclick=\"ajax('index.php?id=$pid&amp;screen=$screen&amp;type=$type&amp;SET[function]=1');\" type=\"button\" name=\"btnShowall\" id=\"btnShowall\" value=\"Show all\" title=\"Show all\" />";

            $content .= "</td></tr>";*/

        } else {
            $content .= "<div align=\"center\" style=\"margin-left:10px; width:700px; height:200px; padding-top:90px; border:1px black solid;\"><h1>Choose page</h1></div>";
        }
        if($pages == 0) $pid = "";
        //$content = $this->printHeader($pid, $screen, $type, $query) . $content;
        $firstContent = '';
        if($firstrun and 1+1==654) {
            $firstContent =  "<div id=\"formAdminContent\">" . $this->printTableHeader();
            $lastContent = $this->printTableFooter() . "</div>";
            return $firstContent . $content . $lastContent . $this->printScriptHeader();
        } else {
            //$returnArray = array();
            $returnArray['content'] = $content;
            return json_encode($content);
        }

    }
	
    function printDetail($scope,$pid)
    {
        //die("$pid");
        global $LANG;
        //$screen = t3lib_div::_GP("screen");
        $i=0;
        $ii=0;

        $myArray = array();
        $sql = "SELECT SQL_CALC_FOUND_ROWS uid, pid, FROM_UNIXTIME(crdate,'%Y-%m-%d') AS logdate, params, '' AS downloaded, ok ";
        $sql .= "FROM tx_formhandler_log WHERE uid=" . intval($scope);
        /*$sql .= " UNION ";
        $sql .= "SELECT  uid, pid, FROM_UNIXTIME(logdate,'%Y-%m-%d'), submittedfields AS params, downloaded, ok, 'mailformplus' AS  log_type ";
        $sql .= "FROM tx_thmailformplus_log WHERE uid=" . intval($uid) . " AND pid = " . intval($pid);*/
        //die($sql);
        $res = $GLOBALS['TYPO3_DB']->sql_query($sql);
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

        $logdate = $row["logdate"];
        $params = $row["params"];
        //$content .= $params;
        if($log_type=="mailformplus") $params = $this->convert_mailformplusdata($params);

        $paramsArray = unserialize($params);
        unset($paramsArray['randomID']);
        unset($paramsArray['removeFile']);
        unset($paramsArray['removeFileField']);
        unset($paramsArray['step-2-next']);
        unset($paramsArray['submitField']);
        unset($paramsArray['submitted']);
        unset($paramsArray['submitted']);
        /*print "<pre>";
        print_r($paramsArray);
        print "<pre>";*/

        foreach($paramsArray as $key => $value) {
            if($key!='type'){
                //if(is_array($value)) {
                    //foreach($value as $key1 => $value1) {
                        if($i%2) {
                            $bgcolor = "#ffffff";
                        } else {
                            $bgcolor = "";
                        }
                        $contentsArray[$i][1] = "<td style=\"background-color:$bgcolor; width:500px; height:28px;\">";
                        $contentsArray[$i][0] = "<td style=\"background-color:$bgcolor; width:200px; height:28px;\"><b>$key</b></td>";

                                        //$contentsArray[$i][1] .= "<input type=\"checkbox\" name=\"$key" . "[]\" value=\"$value1\" checked=\"checked\" /> <label>$value1</label><br />";
                            /*}
                        } else {*/
                        if (preg_match("/\n/", $value)) {
                            //$contentsArray[$i][1] .= "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"$value\" size=\"50\" /></td>";
                            $contentsArray[$i][1] .= "<textarea name=\"$key\" id=\"$key\" cols=\"45\" rows=\"5\">$value</textarea></td>";
                        } else {
                            $contentsArray[$i][1] .= "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"$value\" size=\"50\" /></td>";
                        }
                        $i++;
                    //}
                //}   
            }
        }

        $GLOBALS["TYPO3_DB"]->sql_free_result($res);

        //$content .= $this->printTableHeader();

        while($ii <= $i) {
            $content .= "<tr class=\"detailRows\">";
            $content .= $contentsArray[$ii][0];
            $content .= $contentsArray[$ii][1];
            $content .= "</tr>";
            $ii++;
        }
        
        $content .= "<tr><td colspan=\"2\">
        <input type=\"hidden\" name=\"screen\" value=\"$screen\" />
        <input type=\"hidden\" name=\"SET[function]\" value=\"7\" />
        <input type=\"hidden\" name=\"id\" value=\"$pid\" />
        <input type=\"hidden\" name=\"uid\" value=\"$uid\" />
        <input type=\"hidden\" name=\"pid\" value=\"$pid\" />
        <input onclick=\"ajax('printList','$pid','','$pid','');\" type=\"button\" name=\"btnBack\" id=\"btnBack\" value=\"Back\" title=\"back\" />
        <input type=\"button\" name=\"btnSave\" id=\"btnBack\" value=\"Save\" title=\"Save\" onclick=\"updateRow('$scope','$pid');\" />
        </td></tr>";
        //$content .= $this->printTableFooter();
        //return $content;
        $returnArray = array();
        $returnArray['content'] = $content;
        return json_encode($returnArray);
    }
		
    function printColumns($scope,$pid)
    {
        if($pidList) {
                $where = " WHERE pid IN($pidList)";
        } else {
                $where = " WHERE pid=" . intval($pid);
        }

        global $LANG;
        $i=0;
        $resultArray = array();
        $screen = t3lib_div::_GP("screen");

                //Ta ut ev sparade v�rden
        $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_mailformplusadmin_fields", "pid=" . intval($pid), "", "", "") or die("475: ".mysql_error());
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $submittedfields = $row["submittedfields"];
        }
        if($submittedfields != "") {
            $chosenFieldsArray = explode(",", $submittedfields);
        }
        $submittedfields = '';

                //L�s fr�n databasen
        $sql = "SELECT params, 'formhandler' AS  log_type, ok ";
        $sql .= "FROM tx_formhandler_log $where";
        /*$sql .= " UNION ";
        $sql .= "SELECT submittedfields AS params, 'mailformplus' AS log_type, ok ";
        $sql .= "FROM tx_thmailformplus_log $where";*/
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
            if(is_array($paramsArray)) {
                $resultArray = array_merge($resultArray, array_keys($paramsArray));
            }
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

        $content .= "<tr><td style=\"background-color:#cccccc; height:28px;\" colspan=\"2\"><b>Chosen fields</b><input type=\"hidden\" name=\"screen\" value=\"$screen\" /></td></tr>";

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

                $content .= "<tr><td style=\"background-color:$bgcolor; width:600px;\">$value</td><td style=\"background-color:$bgcolor; width:100px;\"><input name=\"fieldBox\" type=\"checkbox\" value=\"$value\" $checked /></td></tr>";
            }
            $i++;
        }
        $content .= "<tr><td colspan=\"2\"><input name=\"button\" type=\"button\" value=\"Save\" title=\"Save\" onclick=\"saveColumns('saveColumns','$pid','','$pid');\" />";
        $content .= "&nbsp;&nbsp;";
        $content .= "<input onclick=\"ajax('saveColumns','$pid','','$pid';\" type=\"button\" name=\"btnCancel\" id=\"btnCancel\" value=\"Cancel\" title=\"Cancel\" /></td></tr>";
        $content .= $this->printTableFooter();
        
        $returnArray = array();
        $returnArray['content'] = $content;
        return json_encode($returnArray);
    }
	
    function saveColumns($scope,$query,$pid)
    {
        global $LANG;
        $screen = t3lib_div::_GP("screen");
        $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid", "tx_mailformplusadmin_fields", "pid=" . intval($pid), "", "", "") or die("525: ".mysql_error());
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        $uid = $row["uid"];
        $GLOBALS["TYPO3_DB"]->sql_free_result($res);

        //die($uid);
        /*if($query) {
            $post = explode(',',$query);
            if(is_array($post)) {
                foreach($post AS $key => $value) {
                    if(is_array($value)) {
                        foreach($value AS $key1 => $value1) {
                            if($saveContent) {
                                $saveContent .= ",";
                            }
                            if($key==='fieldBox') {
                                $saveContent .= addslashes($value1);
                            }
                        }
                    }
                }
            }
        }*/
        //die($saveContent);

        if($uid) {
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_mailformplusadmin_fields','uid='.intval($uid), array('submittedfields'=>addslashes($query),'tstamp'=>time())) or die("539: ".mysql_error());
        } else {
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mailformplusadmin_fields', array('pid'=>intval($pid),'submittedfields'=>addslashes($query),'crdate'=>time(),'tstamp'=>time())) or die("541: ".mysql_error());
        }

        //$content .= $this->printTableHeader();
        $content .= "<tr><td align=\"center\" style=\"height:100px;\"><h1>Sparade</h1></td></tr>";
        $content .= "<tr><td align=\"center\" style=\"height:100px;\"><input onclick=\"ajax('printList','$pid','','$pid');\" type=\"button\" name=\"btnBack\" id=\"btnBack\" value=\"Back\" title=\"Back\" /></td></tr>";
        //$content .= $this->printTableFooter();
        
        $returnArray = array();
        $returnArray['content'] = $content;
        return json_encode($returnArray);
    }
		
    function deleteRow()
    {
	$iId = (int)$_POST['id'];
	if ($iId) {
	    //$GLOBALS['MySQL']->res("DELETE FROM `pd_profiles` WHERE `id`='{$iId}'");
	    $res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_formhandler_log", "uid=".intval($iId)) or die("692: ".mysql_error());
	    return;
	}
	
        $returnArray = array();
        $returnArray['content'] = $content;
        return json_encode($returnArray);
    }
	
    function exportChoice($scope,$pid)
    {
            global $LANG;
            $screen = t3lib_div::_GP("screen");
            $resultArray = array();
            $big_one = array();
            $halfArray = array();

            if($pidList) {
                    $where = " WHERE pid IN($pidList)";
            } else {
                    $where = " WHERE pid=" . intval($pid);
            }

            //if($type=="mailformplus") {
                    //$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("submittedfields", "tx_thmailformplus_log", "pid=" . intval($pageId), "", "", "") or die("626: ".mysql_error());
            $sql = "SELECT params, 'formhandler' AS  log_type ";
            $sql .= "FROM tx_formhandler_log $where";
            /*$sql .= " UNION ";
            $sql .= "SELECT submittedfields AS params, 'mailformplus' AS  log_type ";
            $sql .= "FROM tx_thmailformplus_log $where";*/
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
                    <td style=\"background-color:#ffffff;\">Header: <input type=\"text\" name=\"txtHeader\" id=\"txtHeader\" /></td>
            </tr>

            <tr>
                    <td><input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblTabSep\" /> Tab</td>
                    <td></td>
            </tr>

            <tr>
                    <td style=\"background-color:#ffffff;\"> <input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblKommaSep\" /> Comma sep</td>
                    <td style=\"background-color:#ffffff;\"></td>
            </tr>

            <tr>
                    <td> <input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblCsv\" /> CSV</td>
                    <td></td>
            </tr>

            <tr>
                    <td style=\"background-color:#ffffff;\"> <input type=\"radio\" name=\"rblExportFormat\" id=\"rblExportFormat\" value=\"cblLabels\" /> Labels</td>
                    <td style=\"background-color:#ffffff;\">Extra: <input type=\"text\" name=\"txtLabelExtra\" id=\"txtLabelExtra\" /></td>
            </tr>

            <tr>
                    <td colspan=\"2\">&nbsp;</td>
            </tr>

            <tr>
                    <td colspan=\"2\"><input onclick=\"exportDo('exportDo','$pid','','$pid');\" type=\"button\" name=\"btnExport\" id=\"btnExport\" value=\"export\" title=\"export\" />
                      &nbsp;&nbsp;
                      <input onclick=\"ajax('printList','$pid','','$pid');\" type=\"button\" name=\"btnCancel\" id=\"btnCancel\" value=\"Cancel\" title=\"Cancel\" /></td>
            </tr>";
            $content .= $this->printTableFooter();
            
            $returnArray = array();
            $returnArray['content'] = $content;
            return json_encode($returnArray);
    }

    function exportDo($scope,$query,$pid)
    {
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
                    $where = " WHERE pid=" . intval($pageId);
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
                    $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("field_name, field_value, email_uid", "tx_pksaveformmail_fields", "pid=" . intval($pageId), "", "", "") or die("884: ".mysql_error());
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
		$returnArray = array();
            $returnArray['content'] = '$query';
            return $returnArray;
	}
	
	function updateRow()
	{
	    $sVal = $_POST['value'];
	    $iId = (int)$_POST['id'];
	    $columnName = $_POST['columnName'];
	    
	    if ($iId && $sVal !== FALSE) {
		//Read post
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("params", "tx_formhandler_log", "uid=".intval($iId)) or die("1167: ".$mysql_error());
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$params = $row['params'];
		$paramsArray = unserialize($params);
		$paramsArray[$columnName] = addslashes($sVal);
		$params = serialize($paramsArray);
	    }
	    
            $updateArray = array(
                    "tstamp" => time(),
                    "ip" => t3lib_div::getIndpEnv('REMOTE_ADDR'),
                    "params" => $params
            );

            $res = $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("tx_formhandler_log", "uid=" . intval($iId), $updateArray);
            $result = mysql_affected_rows();
            $message = "";
            if($result == -1) {
                    $message = "An error occured";
            } elseif($result == 0) {
                    $message = "No rows affected";
            } else {
                    $message = "$result rows affected";
            }
	    
	    $returnArray = array();
            $returnArray['content'] = $message;

            return $sVal;
	}
        
        function saveFormStructure($scope,$query)
        {
            $json = $query;
            $scopeArray= explode(':',$scope);
            $scope= $scopeArray[0];
            $user = $scopeArray[1];
            $json = str_replace('&quot;','"',$json);
            if (get_magic_quotes_gpc() == 1) {
                $json = stripslashes($json);
            }
            $json = str_replace('\\', '', $json);
            $jsonArray = json_decode($json,true);
            //$jsonArray['lang'] = $lang;
            $jsonArray['user'] = $user;
            $bodytext = json_encode($jsonArray);
    
            $updateArray = array(
                "bodytext" => $bodytext, "tstamp" => time()
            );
            $res = $GLOBALS["TYPO3_DB"]->exec_UPDATEquery('tt_content', "uid=" . intval($scope), $updateArray);
            $result = mysql_affected_rows();
            $message = "";
            if($result == -1) {
                $message = "An error occured";
            } elseif($result == 0) {
                $message = "No rows affected";
            } else {
                $message = "$result rows affected";
            }

            $returnArray = array();
            $returnArray['content'] = $message;
            return json_encode($returnArray);
        }
        
        function getFormStructure($scope)
        {
            $scopeArray = explode(':',$scope);
            $scope = $scopeArray[0];
            $table = $scopeArray[1];
            $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery('bodytext', $table, "uid=" . intval($scope));
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
            $bodytext = $row["bodytext"];
            $GLOBALS["TYPO3_DB"]->sql_free_result($res);
            return $bodytext;
        }
        
        public function setOk($scope,$query)
        {
            $okChecked = $query;
            $uid = $scope;
            if($okChecked=='false') {
                $updateCheck = 0;
            } else {
                $updateCheck = 1;
            }
            $fields_values = array('ok' => $updateCheck);

            if($GLOBALS['TYPO3_DB']->exec_UPDATEquery("tx_formhandler_log", "uid=" . intval($uid), $fields_values)) {
                return 'ok';
            } else {
                return 'not ok';
            }
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

