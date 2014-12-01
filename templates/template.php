<?php

require_once(t3lib_extMgm::extPath('mailformplus_admin').'classes/class.tx_mailformplusadmin.php');
$ajaxObj = new tx_mailformplusadmin();
$id=$_GET['id'];
$formStructure = $ajaxObj->getFormStructure(intval($id));
        
$template_form1 = '<!-- ###TEMPLATE_FORM1### begin -->
<div id="form_body" >
	<form class="form-horizontal" name="Formular" id="Formular" method="post" action="###REL_URL###" enctype="multipart/form-data">
		<input type="hidden" name="id" id="id" value="###PID###" />
		<input type="hidden" name="submitted" value="1" />
		
		<input type="hidden" name="L" value="###value_L###" />
	
		<label>* = ###LLL:mandatory###</label>
		<img class="formhandler-ajax-submit" src="../typo3conf/ext/formhandler/Resources/Images/ajax-loader.gif"/>
		<div class="control-group">
			<label class="control-label" for="first_name">###LLL:first_name### ###required_first_name###</label>
			<div class="controls">
				<input class="formhandler_input" type="text" placeholder="###LLL:first_name###" name="formhandler[first_name]" id="first_name" value="###value_first_name###" tabindex="1"/>
				###error_first_name###
			</div>
		</div>
	    
		<div class="control-group">
			<label class="control-label" for="last_name">###LLL:last_name### ###required_last_name###</label>
			<div class="controls">
				<input class="formhandler_input" type="text" placeholder="###LLL:last_name###" name="formhandler[last_name]" id="last_name" value="###value_last_name###" tabindex="2"/>
				###error_last_name###
			</div>
		</div>
	    
	   	 <div class="control-group">
			<label class="control-label" for="organisation">###LLL:organisation###</label>
			<div class="controls">
				<input class="formhandler_input" type="text" placeholder="###LLL:organisation###" name="formhandler[organisation]" id="organisation" value="###value_organisation###" tabindex="3"/>
			</div>
		</div>
	
		<div class="control-group">
			<label class="control-label" for="email">###LLL:email### ###required_email###</label>
			<div class="controls">
				<input class="formhandler_input" type="text" placeholder="###LLL:email###" name="formhandler[email]" id="email" value="###value_email###" tabindex="4"/>
				###error_email###
			</div>
		</div>
	    
	   	<div class="control-group">
			<label class="control-label" for="address">###LLL:address###</label>
			<div class="controls">
				<input class="formhandler_input" type="text" placeholder="###LLL:address###" name="formhandler[address]" id="address" value="###value_address###" tabindex="5"/>
			</div>
		</div> 
	    
		<div class="control-group">
			<label class="control-label" for="telephone">###LLL:telephone###</label>
			<div class="controls">
				<input class="formhandler_input" type="text" placeholder="###LLL:telephone###" name="formhandler[telephone]" id="telephone" value="###value_telephone###" tabindex="6"/>
			</div>
		</div>   
	
	   	<div class="control-group">
	    	<label class="control-label" for="comment">###LLL:comment###</label>
			<div class="controls">
				<textarea class="formhandler_input" cols="50" rows="5" placeholder="###LLL:comment###" name="formhandler[comment]" id="comment" tabindex="7">###value_comment###</textarea>
			</div>
		</div>
		
		<div class="control-group">
			<input type="submit" class="btn btn-primary" value="###LLL:send###" tabindex="8" ###submit_nextStep### />
		</div>
	</form>
</div>
<!-- ###TEMPLATE_FORM1### end -->';

$template_email_admin_plain = '
<!-- ###TEMPLATE_EMAIL_ADMIN_PLAIN### begin -->
###value_first_name###, ###LLL:your_information_saved###<br /><br />
###LLL:you_have_registered###:<br />
###LLL:first_name### : ###value_first_name###<br />
###LLL:last_name### : ###value_last_name###<br />
###LLL:organisation### : ###value_organisation###<br />
###LLL:email###: ###value_email###<br />
###LLL:address###: ###value_address###<br />
###LLL:telephone###: ###value_telephone###<br />
###LLL:comment###: ###value_comment###<br />
<!-- ###TEMPLATE_EMAIL_ADMIN_PLAIN### end-->';
echo $template_email_admin_plain;


$template_email_admin_html = '<!-- ###TEMPLATE_EMAIL_ADMIN_HTML### begin -->
###value_first_name###, ###LLL:your_information_saved###<br /><br />
###LLL:you_have_registered###:<br />
###LLL:first_name### : ###value_first_name###<br />
###LLL:last_name### : ###value_last_name###<br />
###LLL:organisation### : ###value_organisation###<br />
###LLL:email###: ###value_email###<br />
###LLL:address###: ###value_address###<br />
###LLL:telephone###: ###value_telephone###<br />
###LLL:comment###: ###value_comment###<br />
<!-- ###TEMPLATE_EMAIL_ADMIN_HTML### end -->';
echo $template_email_admin_html;

$template_email_user_plain = '
<!-- ###TEMPLATE_EMAIL_USER_PLAIN### begin -->
Ny anm&auml;lan:<br /><br />
###LLL:first_name### : ###value_first_name###<br />
###LLL:last_name### : ###value_last_name###<br />
###LLL:organisation### : ###value_organisation###<br />
###LLL:email###: ###value_email###<br />
###LLL:address###: ###value_address###<br />
###LLL:telephone###: ###value_telephone###<br />
###LLL:comment###: ###value_comment###<br />
<!-- ###TEMPLATE_EMAIL_USER_PLAIN### end -->';
echo $template_email_user_plain;

$template_email_user_html = '<!-- ###TEMPLATE_EMAIL_USER_HTML### begin -->
Ny anm&auml;lan:<br /><br />
###LLL:first_name### : ###value_first_name###<br />
###LLL:last_name### : ###value_last_name###<br />
###LLL:organisation### : ###value_organisation###<br />
###LLL:email###: ###value_email###<br />
###LLL:address###: ###value_address###<br />
###LLL:telephone###: ###value_telephone###<br />
###LLL:comment###: ###value_comment###<br />
<!-- ###TEMPLATE_EMAIL_USER_HTML### end-->';
echo $template_email_user_html;

$template_submittedok = '<!-- ###TEMPLATE_SUBMITTEDOK### begin-->
<div class="submitted" align="left" >
<p class="bodytext">
<b>###value_first_name###, ###LLL:your_information_saved###</b><br /><br />
###LLL:you_have_registered###:<br />
###LLL:first_name### : ###value_first_name###<br />
###LLL:last_name### : ###value_last_name###<br />
###LLL:organisation### : ###value_organisation###<br />
###LLL:email###: ###value_email###<br />
###LLL:address###: ###value_address###<br />
###LLL:telephone###: ###value_telephone###<br />
###LLL:comment###: ###value_comment###
</p>
</div>
<!-- ###TEMPLATE_SUBMITTEDOK### end -->';
echo $template_submittedok;