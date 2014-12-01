<?php

// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');

require_once(t3lib_extMgm::extPath('mailformplus_admin').'classes/class.tx_mailformplusadmin.php');

$id = isset($HTTP_GET_VARS['id'])?$HTTP_GET_VARS['id']:0;
$pid = htmlspecialchars(t3lib_div::_GP("pid"));
$action = htmlspecialchars(t3lib_div::_GP("action"));
$scope = htmlspecialchars(t3lib_div::_GP("scope"));
$query = htmlspecialchars(t3lib_div::_GP("query"));
$firstrun = htmlspecialchars(t3lib_div::_GP("firstrun"));
$sid = htmlspecialchars(t3lib_div::_GP("sid"));

tslib_eidtools::connectDB();

switch($action) {
    case "printList":
        echo printList($action,$scope,$query,$pid,$firstrun);
        break;
    case "getEventForm":
        echo getEventForm();
        break;
    case "saveEventForm":
        initTSFE($id);
        echo saveEventForm($query);
        break;
    case "getFormStructure":
        return getFormStructure($action,$scope,$query,$lang,$user,$pid,$firstrun);
        break;
    case "saveFormStructure":
        return saveFormStructure($action,$scope,$query,$lang,$user,$pid,$firstrun);
        break;
    default:
	$content = "<form action=\"\" name=\"mailformplus_admin_form\">";
	$content .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" id=\"mailformplus_adminTable\" class=\"display\">";
	/*$tableHeader = printTableHeader('printTableHeader',$scope,$query,$pid);
	foreach($tableHeader as $key => $value) {
	    $content .= "<th>$value</th>";
	}*/
	$content .= '<thead><th title="Untitled">untitled</th></thead>';
	$content .= '<tfoot><th title="Untitled">Untitled</th></tfoot>';
	$content .= "</table>";
	$content .= "<button id=\"btnDeleteMemRow\">Delete record</button>";
	$content .= "</form>";

	$content .= '<style type="text/css">
            th { text-align:left;}
	    .fg-button { outline: 0; margin:0 4px 0 0; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
	.fg-button .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
	

	a.fg-button { float:left; }
	

	/* remove extra button width in IE */
	button.fg-button { width:auto; overflow:visible; }
	

	.fg-button-icon-left { padding-left: 2.1em; }
	.fg-button-icon-right { padding-right: 2.1em; }
	.fg-button-icon-left .ui-icon { right: auto; left: .2em; margin-left: 0; }
	.fg-button-icon-right .ui-icon { left: auto; right: .2em; margin-left: 0; }
	

	.fg-button-icon-solo { display:block; width:8px; text-indent: -9999px; }  /* solo icon buttons must have block properties for the text-indent to work */
	

	.fg-buttonset { float:left; }
	.fg-buttonset .fg-button { float: left; }
	.fg-buttonset-single .fg-button,
	.fg-buttonset-multi .fg-button { margin-right: -1px;}
	

	.fg-toolbar { padding: .5em; margin: 0;  }
	.fg-toolbar .fg-buttonset { margin-right:1.5em; padding-left: 1px; }
	.fg-toolbar .fg-button { font-size: 1em;  }

            </style>
            <script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/datatables/js/jquery.min.js"></script>
            <script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/datatables/js/jquery.dataTables.js"></script>
            <script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/datatables/js/dataTables.tableTools.min.js"></script>
	    <script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/datatables/js/jquery.dataTables.editable.js"></script>
	    <script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/datatables/js/jquery.jeditable.js"></script>
	    <script language="JavaScript" type="text/javascript" src="/typo3conf/ext/mailformplus_admin/vendor/datatables/js/jquery.validate.js"></script>

            <link rel="stylesheet" type="text/css" href="/typo3conf/ext/mailformplus_admin/vendor/datatables/css/jquery.dataTables.min.css" />
            <link rel="stylesheet" type="text/css" href="/typo3conf/ext/mailformplus_admin/vendor/datatables/css/dataTables.tableTools.min.css" />

            <script language="JavaScript" type="text/javascript">
	    var oTable;
	    
	    function getColumns()
	    {
	    $.ajax({
		type : "POST",
		url : "index.php",
		async: false,
		data: {
		    ajaxID : "mailformplus_admin::ajaxFunctions",
		    scope : '.$scope.',
		    sid : Math.random()
		},
		//contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: function(data) {

		}
	    });
	    }
	    
	    function startTable()
	    {
		    $.ajax({
			type: "POST",
			url: "typo3/ajax.php?ajaxID=mailformplus_admin::ajaxFunctions",
			data: "&action=printList&scope='.intval($scope).'&sid"+Math.random(),
			dataType: "json",
			success: function(resultData) {
			    console.log(resultData.headers);
			    aoColumnArray = [];
			    console.log("93");
			    $.each(resultData.headers, function(index, value) {
				var aoColumns = new Object;
				aoColumns["sTitle"] = value;
				aoColumnArray.push(aoColumns);
			    });
			    console.log("99");
			    ticketTable= [];
			    ticketTable.aaData = resultData.tickets;
			    ticketTable.aaSorting = [[0, "asc"], [0, "desc"]];
			    ticketTable.aoColumns = aoColumnArray;
			    ticketTable.bJQueryUI = true;
			    ticketTable.bScrollInfinite = true;
			    ticketTable.bScrollCollapse = true;
			    ticketTable.bDestroy = true;
			    ticketTable.bDeferRender = true;
			    ticketTable.iDisplayLength = 10;
			    ticketTable.sScrollY = "400px";
			    ticketTable.sDom = "Rlfrtip";
	    
			    oTable = $("#mailformplus_adminTable").dataTable(ticketTable).makeEditable({
				sUpdateURL: "typo3/ajax.php?ajaxID=mailformplus_admin::ajaxFunctions&scope='.$scope.'&action=updateRow&query=&pid='.$pid.'&sid"+Math.random(),
				"aoColumns": [
				    {
					tooltip: "untitled",
					oValidationOptions : { rules:{ value: {minlength: 3 }  },
					messages: { value: {minlength: "Min length - 3"} } }
				    }
				],
				sDeleteURL: "typo3/ajax.php?ajaxID=mailformplus_admin::ajaxFunctions&scope='.$scope.'&action=deleteRow&query=&pid='.$pid.'&sid"+Math.random(),
				sDeleteRowButtonId: "btnDeleteMemRow",
			    });
			    
			    var tableTools = new $.fn.dataTable.TableTools( oTable, {
				"sSwfPath": "typo3conf/ext/mailformplus_admin/vendor/datatables/swf/copy_csv_xls_pdf.swf",
				"buttons": [
				    "copy",
				    "csv",
				    "xls",
				    "pdf",
				    { "type": "print", "buttonText": "Print me!" }
				]
			    } );

			    $( tableTools.fnContainer() ).insertAfter("div.dataTables_length");
			}
		    });
		
	    }
	    
            $(document).ready(function()
            {
		startTable();
            });
        
            function saveColumns(action,scope,query,pid)
            {
                boxArray = document.getElementsByName("fieldBox");
                var query="";
                for(var i = 0; i < boxArray.length; i++) {
                    if(boxArray[i].checked) {
                        if(query) query+=",";
                        query += boxArray[i].value;
                    }
                }
                ajax("saveColumns",pid,query,pid);
            }
            
            function ajax()
            {
		var oTable = $("#mailformplus_adminTable").DataTable();
		//limit = $(".paginate_button.current").text() + "," + $("select[name=mailformplus_adminTable_length]").val());
                $.ajax({
                    type : "POST",
                    url : "typo3/ajax.php",
                    data: {
                        ajaxID : "mailformplus_admin::ajaxFunctions",
                        scope : scope,
                        action : action,
                        query : query,
                        firstrun : firstrun,
                        pid : pid,
                        sid : Math.random(),
                    },
                    //contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function () {
			if(action != "setOk") {
			    $("#mailformplus_adminTable").html(\'<img src="/fileadmin/templates/images/ajax-loader.gif" />\');
			}
                    },
                    success: function(data) {
                        if(data) {
                            if(action != "setOk") {
				$("#mailformplus_adminTable").html(data.content);
				
                            }
                        }
                        //console.log("success");
                    },
                    complete: function(data) {
			if(action=="printList") {
				    startTable();
				} 
				$(".dataTables_info").toggle();
				$(".dataTables_paginate").toggle();
                        
                    },
                    failure: function(errMsg) {
                        console.log("failure:"+errMsg);
                    },
                    error: function(errMsg) {
                        console.log("Error"+JSON.stringify(errMsg));
                    }
                });
            }
        
            function deleteRow(pid)
            {
                boxArray = document.getElementsByName("deleteBox");
                var query="";
                for(var i = 0; i < boxArray.length; i++) {
                    if(boxArray[i].checked) {
                        if(query) query+=",";
                        query += boxArray[i].value;
                    }
                }
                console.log(query);
                if(confirm("Are you sure you want to delete this row/s?")) {
                    ajax("deleteRow",pid,query,pid);
                }
            }
        
            function updateRow(scope,pid)
            {
                var inputs,i;
                var myobj = "";

                inputs = document.getElementsByTagName("input");

                for (i = 0; i < inputs.length; ++i) {
                    //console.log(inputs[i].type);
                    if(inputs[i].type=="text") {
                    if(myobj) {
                        myobj += ",";
                    }
                    myobj += \'"\' + inputs[i].name + \'"\' + ":" + \'"\' + inputs[i].value + \'"\';
                    }
                }
                myobj = "{"+myobj+"}";
                console.log(myobj);
                ajax("updateRow",scope,myobj,pid,"");
            }
        
            function checkAll(checked,boxName)
            {
                if(checked) {
                    for (var i = 0; i < document.mailformplus_admin_form[boxName].length; i++) {
                        document.mailformplus_admin_form[boxName][i].checked = true;
                    }
                } else {
                    for (var i = 0; i < document.mailformplus_admin_form[boxName].length; i++) {
                        document.mailformplus_admin_form[boxName][i].checked = false;
                    }		
                }
            }
        </script>';
	echo $content;
}

function initTSFE($pageUid=1)
{
    require_once(PATH_tslib.'class.tslib_fe.php');
    require_once(PATH_t3lib.'class.t3lib_userauth.php');
    require_once(PATH_tslib.'class.tslib_feuserauth.php');
    require_once(PATH_t3lib.'class.t3lib_cs.php');
    require_once(PATH_tslib.'class.tslib_content.php');
    require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
    require_once(PATH_t3lib.'class.t3lib_page.php');

    //$TSFEclassName = t3lib_div::makeInstance('tslib_fe');

    if (!is_object($GLOBALS['TT'])) {
        $GLOBALS['TT'] = new t3lib_timeTrack;
        $GLOBALS['TT']->start();
    }

    // Create the TSFE class.
    //$GLOBALS['TSFE'] = new $TSFEclassName($GLOBALS['TYPO3_CONF_VARS'],$pageUid,'0',1,'','','','');
    $GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe');
    $GLOBALS['TSFE']->connectToDB();
    $GLOBALS['TSFE']->initFEuser();
    $GLOBALS['TSFE']->fetch_the_id();
    $GLOBALS['TSFE']->getPageAndRootline();
    $GLOBALS['TSFE']->initTemplate();
    $GLOBALS['TSFE']->tmpl->getFileName_backPath = PATH_site;
    $GLOBALS['TSFE']->forceTemplateParsing = 1;
    $GLOBALS['TSFE']->getConfigArray();
}

function printList($action,$scope,$query,$pid,$firstrun)
{
    $ajaxObj = new tx_mailformplusadmin();
    $content = $ajaxObj->ajaxFunctions($action,$scope,$query,$lang,$user,$pid,$firstrun);
    //echo $content;
}

function printTableHeader($action,$scope,$query,$pid)
{
    $ajaxObj = new tx_mailformplusadmin();
    $content = $ajaxObj->ajaxFunctions($action,$scope,$query,$lang,$user,$pid,$firstrun);
    
    $content = json_decode($content,true);
    
    foreach($content as $key => $value) {
	echo $value;
    }
    
    return $content;
}

function getFormStructure($action,$scope,$query,$lang,$user,$pid,$firstrun)
{
    $ajaxObj = new tx_mailformplusadmin();
    $content = $ajaxObj->ajaxFunctions($action,$scope,$query,$lang,$user,$pid,$firstrun);
    echo $content;
}

function saveFormStructure($action,$scope,$query,$lang,$user,$pid,$firstrun)
{
    $ajaxObj = new tx_mailformplusadmin();
    $content = $ajaxObj->ajaxFunctions($action,$scope,$query,$lang,$user,$pid,$firstrun);
    echo $content;
}