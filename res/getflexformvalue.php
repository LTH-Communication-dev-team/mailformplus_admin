<?php
class user_getflexformvalue {
    var $cObj;// The backReference to the mother cObj object set at call time
    /**
    * Call it from a USER cObject with 'userFunc = user_randomImage->main_randomImage'
    */
    function main($content,$conf){
        //$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_devlog', array('msg'=>$conf['tt_content_uid'],'crdate'=>time())) or die("541: ".mysql_error());
        $content;
        $content_email;
        $pluginId = $_POST['pluginId'];
        $formhandlerArray = $_POST['formhandler'];
        foreach($formhandlerArray as $key => $value) {
            $content .= "<p>$key: $value</p>";
        }
        $res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("pi_flexform", 
                "tt_content", 
                "uid=" . intval($pluginId), "", "", "") or die("12: ".$pid.mysql_error());
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        $pi_flexform = $row['pi_flexform'];
        if($pi_flexform) {
            $xml = simplexml_load_string($pi_flexform);
            $test = $xml->data->sheet[0]->language;
            foreach ($test->field as $n) {
                foreach($n->attributes() as $name => $val) {
                    if ($val == 'content_email') {
                        $content_email = $n->value;
                    }
                }
            }
        }
        $GLOBALS["TYPO3_DB"]->sql_free_result($res);

        return $content_email.$content;
    }
}

