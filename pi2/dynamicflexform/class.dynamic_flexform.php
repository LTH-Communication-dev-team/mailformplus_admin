<?php
class user_dynamic_flexform
{
    function form_builder()
    {
        $bootstrapData = null;
        
        tslib_eidtools::connectDB();
        
        $tt_contentArray = $_GET['edit']['tt_content'];
        $pluginId = str_replace(',','',key($tt_contentArray));
        if(!$tt_contentArray) {
            $tt_contentArray = urldecode($_GET['TSFE_EDIT']['record']);
            $tt_contentArray = explode(':',$tt_contentArray);
            $pluginId = $tt_contentArray[1];
        }
        
        $userId = $GLOBALS["BE_USER"]->user["uid"];
        ///index.php?eID=feeditadvanced&TSFE_EDIT%5Brecord%5D=tt_content%3A527&TSFE_EDIT%5Bpid%5D=1250&TSFE_EDIT[cmd]=edit&pid=1250

        $ajaxUrl = '/typo3/ajax.php';
        $ajaxId = 'ajaxID : "mailformplus_admin::ajaxFunctions"';
        if(strstr($_SERVER["REQUEST_URI"],'feeditadvanced')) {
            $ajaxUrl = 'index.php';
            $ajaxId = 'eID : "tx_mailformplusadmin"';
        }
                
        $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid,pid,title,lang,bodytext", "tx_mailformplusadmin_standardforms", "deleted=0", "", "", "") or die("8: ".$pageId.mysql_error());
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $uid = $row["uid"];
            $formTitle = $row['title'];
            $otherForms .= "<option value=\"$uid\">$formTitle</option>";
        }
                
        $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid,bodytext", "tt_content", "list_type='mailformplus_admin_pi2' AND deleted=0 AND bodytext LIKE '%\"user\":\"$userId\"}%'", "", "", "") or die("8: ".$pageId.mysql_error());
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $uid = $row["uid"];
            $bodytext = $row["bodytext"];
            $jsonArray = json_decode( $bodytext, true );
            $formTitle = $jsonArray['formtitle'];
            $formDescription = $jsonArray['formdescription'];
            $json = json_encode($jsonArray['fields']);
            if($uid===$pluginId) {
                $bootstrapData = $json;
            } else {
                $otherForms .= "<option value=\"$uid\">$formTitle $uid $pluginId $userId</option>";
            }
            
        }
        $GLOBALS["TYPO3_DB"]->sql_free_result($res);
                
        if(!$bootstrapData) {
            $bootstrapData = '[{
                        "label": "Förnamn",
                        "field_type": "text",
                        "required": true,
                        "field_options": {"size":"medium"},
                        "cid": "c1"
                      },
                      {
                        "label": "Efternamn",
                        "field_type": "text",
                        "required": true,
                        "field_options": {"size":"medium"},
                        "cid": "c2"
                      },
                      {
                        "label": "Epostadress",
                        "field_type": "text",
                        "required": true,
                        "field_options": {"size":"medium"},
                        "cid": "c3"
                      },
                      {
                        "label": "Telefon",
                        "field_type": "text",
                        "required": true,
                        "field_options": {"size":"medium"},
                        "cid": "c4"
                      }
                      ]';
        }
        $otherForms = "Standard Forms<br /><select id=\"otherforms\" size=\"4\" onchange=\"changeForm('tx_mailformplusadmin_standardforms');\">$otherForms</select>";
        $formTitle = "Form Title<br /><input type=\"text\" name=\"formtitle\" id=\"formtitle\" value=\"$formTitle\" />";
        $formDescription = "Form Description<br /><textarea name=\"formdescription\" id=\"formdescription\" />$formDescription</textarea>";
        
        $fbghpPath = '/typo3conf/ext/mailformplus_admin/vendor/fbghp';
        $content = '<link rel="stylesheet" href="'.$fbghpPath.'/vendor/css/vendor.css" />
        <link rel="stylesheet" href="'.$fbghpPath.'/dist/formbuilder.css" />
            <link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
        <style>
            * {
                box-sizing: border-box;
            }

            body {
              font-family: sans-serif;
            }
            
            .ui-dialog {
                z-index:3600;
            }
            
            .fb-dialog {
                padding:
                background-color: #ccc;
            }

            .fb-main {
                background-color: #fff;
                border-radius: 5px;
                min-height: 600px;
                display: none;
            }
            
            .fb-button-container {
                position:relative;
                bottom:40px;
                padding:10px;
                background-color: green;
                border:1px black solid;
            }
            
            a.fb-button-container:link, a.fb-button-container:visited {
                text-decoration: none !important;
                color:#fff !important;
            }

            input[type=text] {
                height: 26px;
                margin-bottom: 3px;
            }

            select {
                margin-bottom: 5px;
                /*font-size: 40px;*/
            }
            
            .fb-field-wrapper {
                margin-bottom:0px;
            }
            
            .fb-left {
                padding-top: 0px;
            }
            
            .fb-right {
                padding-top: 40px;
            }

            .fb-head-cols {
                float:left;
                padding:10px;
            }
        </style>
        <div class="fb-button-container"><a href=#" onclick="loadFormBuilder();">Show Form Builder</a></div>
        <div class="fb-dialog"><div class="fb-head"><div class="fb-head-cols">'.$otherForms.'</div><div class="fb-head-cols">'.$formTitle.'</div><div class="fb-head-cols">'.$formDescription.'</div></div><div class="fb-main"></div></div>

            <script src="'.$fbghpPath.'/vendor/js/vendor.js"></script>
            <script src="'.$fbghpPath.'/vendor/js/jquery-ui-1.10.4.custom.min.js"></script>
            <script src="'.$fbghpPath.'/dist/formbuilder.js"></script>
                
            <script>
            
                function loadFormBuilder()
                {
                    
                    jQuery(".fb-dialog").dialog({
                        height: 700,
                        width: 800,
                        title: "Success",
                        modal: false
                     });
                     jQuery(".fb-dialog").dialog("open");
                     jQuery(".fb-head-display").show();
                     jQuery(".fb-main").show();
                }
                if (typeof(jQuery) == "undefined") {
                    var iframeBody = document.getElementsByTagName("body")[0];
                    var jQuery = function (selector) { return parent.jQuery(selector, iframeBody); };
                    var $ = jQuery;
                }
                if (typeof($) == "undefined") {
                    var iframeBody = document.getElementsByTagName("body")[0];
                    var $ = function (selector) { return parent.jQuery(selector, iframeBody); };
                    var jQuery = $;
                }
                jQuery(function(){
                    fb = new Formbuilder({
                        selector: ".fb-main",
                        bootstrapData: '.$bootstrapData.'
                        
                    });
                    
                    jQuery(".fb-main").before(\'<div class="fb-head-display" style="padding:10px;clear:both;width:100%;height:120px;display:none;">\'+jQuery(".fb-head").html()+\'</div>\');
                    jQuery(".fb-head").remove();
                    

                    fb.on("save", function(payload){
                    //console.log(payload);
                        if(payload) {
                            ajaxFunction("saveFormStructure","'.$pluginId.':'.$userId.'",payload);
                        }
                    })
                });
                
                function ajaxFunction(action,scope,json_str)
                {
                    if(json_str) {
                        var strformtitle = jQuery("#formtitle").val();
                        var strformdescription = jQuery("#formdescription").val();
                        var json_obj = JSON.parse(json_str);
                        json_obj.formtitle = strformtitle;
                        json_obj.formdescription = strformdescription;
                        json_str = JSON.stringify( json_obj );
                        //console.log("json_str"+json_str);
                    }
                    
                    jQuery.ajax({
                        type : "POST",
                        url : "'.$ajaxUrl.'",
                        data: {
                            '.$ajaxId.',
                            action : action,
                            scope : scope,
                            query : json_str,
                            sid : Math.random(),
                        },
                        dataType: "json",
                        /*beforeSend: function () {
                            $("#txtContent").html("<img src="/fileadmin/templates/images/ajax-loader.gif" />");
                        },*/
                        success: function(data) {
                            if(data) {
                                if(action=="getFormStructure") {
                                    var bootstrapData = data.fields;
                                    var formtitle = JSON.stringify(data.formtitle);
                                    var formdescription = JSON.stringify(data.formdescription);
                                    jQuery("#formtitle").val(formtitle);
                                    jQuery("#formdescription").val(formdescription);
                                    fb = new Formbuilder({
                                        selector: ".fb-main",
                                        bootstrapData: bootstrapData
                                    });
                                    //console.log(bootstrapData);
                                } else if(data=="saveFormStructure") {
                                    console.log(json_str);
                                }
                            }
                        },
                        complete: function(data) {
                            //console.log("complete"+data.content);
                        },
                        failure: function(errMsg) {
                            //console.log("failure"+errMsg);
                        },
                        error: function(errMsg) {
                            //console.log("error"+JSON.stringify(errMsg)+errMsg.content);
                        }
                    });
                }
                
                function changeForm(table)
                {
                    if(confirm("All your changes will be lost. Are you sure you want to do this?")) {
                        scope = jQuery("#otherforms").val();
                        //console.log("266: "+scope+":"+table);
                        ajaxFunction("getFormStructure",scope+":"+table,"")
                    }
                }
            </script>

        ';
        
        return $content;
    }
}