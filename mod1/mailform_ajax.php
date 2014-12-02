<?php
class mailform_ajax {
    
    public function __construct() {
        
    }
    
    public function ajaxFunctions($params=array(), $ajaxObj)
    {
        require_once(t3lib_extMgm::extPath('mailformplus_admin').'classes/class.tx_mailformplusadmin.php');

        $scope = htmlspecialchars(t3lib_div::_GP("scope"));
        $query = htmlspecialchars(t3lib_div::_GP("query"));
        $action = htmlspecialchars(t3lib_div::_GP("action"));
        $pid = htmlspecialchars(t3lib_div::_GP("pid"));
        $firstrun = htmlspecialchars(t3lib_div::_GP("firstrun"));
        $user = $GLOBALS["BE_USER"]->user["uid"];
        $lang = 'se'; //$GLOBALS['LANG'];
        $sid = htmlspecialchars(t3lib_div::_GP("sid"));

        $myAjaxObj = new tx_mailformplusadmin();
        $content = $myAjaxObj->ajaxFunctions($action,$scope,$query,$lang,$user,$pid,$firstrun);
        if($action==='getFormStructure' or $action==='printDetail' or $action==='printList' or $action==='printColumns' 
                or $action==='exportChoice' or $action==='deleteRow' or $action==='updateRow' or $action==='saveColumns' or $action==='updateRow') {
            echo $content;
        } else {
            return $content;
        }
    }
}
?>