<?php
class ux_t3lib_formmail extends t3lib_formmail {

	function start($valueList, $base64 = false) {

		$this->mailMessage = t3lib_div::makeInstance('t3lib_mail_Message');

		if ($GLOBALS['TSFE']->config['config']['formMailCharset']) {
				// Respect formMailCharset if it was set
			$this->characterSet = $GLOBALS['TSFE']->csConvObj->parse_charset($GLOBALS['TSFE']->config['config']['formMailCharset']);
		} elseif ($GLOBALS['TSFE']->metaCharset != $GLOBALS['TSFE']->renderCharset) {
				// Use metaCharset for mail if different from renderCharset
			$this->characterSet = $GLOBALS['TSFE']->metaCharset;
		}

		if ($base64 || $valueList['use_base64']) {
			$this->encoding = 'base64';
		}

		if (isset($valueList['recipient'])) {
				// convert form data from renderCharset to mail charset
			$this->subject = ($valueList['subject'])
					? $valueList['subject']
					: 'Formmail on ' . t3lib_div::getIndpEnv('HTTP_HOST');
			$this->subject = $this->sanitizeHeaderString($this->subject);

			$this->fromName = ($valueList['from_name'])
					? $valueList['from_name']
					: (($valueList['name']) ? $valueList['name'] : '');
			$this->fromName = $this->sanitizeHeaderString($this->fromName);

			$this->replyToName = ($valueList['replyto_name']) ? $valueList['replyto_name'] : $this->fromName;
			$this->replyToName = $this->sanitizeHeaderString($this->replyToName);

			$this->organisation = ($valueList['organisation']) ? $valueList['organisation'] : '';
			$this->organisation = $this->sanitizeHeaderString($this->organisation);

			$this->fromAddress = ($valueList['from_email']) ? $valueList['from_email'] : (
				($valueList['email']) ? $valueList['email'] : ''
			);
			if (!t3lib_div::validEmail($this->fromAddress)) {
				$this->fromAddress = t3lib_utility_Mail::getSystemFromAddress();
				$this->fromName = t3lib_utility_Mail::getSystemFromName();
			}

			$this->replyToAddress = ($valueList['replyto_email']) ? $valueList['replyto_email'] : $this->fromAddress;

			$this->priority = ($valueList['priority']) ? t3lib_div::intInRange($valueList['priority'], 1, 5) : 3;

				// auto responder
			$this->autoRespondMessage = (trim($valueList['auto_respond_msg']) && $this->fromAddress)
					? trim($valueList['auto_respond_msg'])
					: '';

			if ($this->autoRespondMessage !== '') {
					// Check if the value of the auto responder message has been modified with evil intentions
				$autoRespondChecksum = $valueList['auto_respond_checksum'];
				$correctHmacChecksum = t3lib_div::hmac($this->autoRespondMessage);
				if ($autoRespondChecksum !== $correctHmacChecksum) {
					t3lib_div::sysLog('Possible misuse of t3lib_formmail auto respond method. Subject: ' . $valueList['subject'],
						'Core',
						3);
					return;
				} else {
					$this->autoRespondMessage = $this->sanitizeHeaderString($this->autoRespondMessage);
				}
			}

			//Save data into tx_formhandler_log
			$this->saveFormdata($valueList, t3lib_div::_GP("locationData"));
			
			$plainTextContent = '';
			$htmlContent = '<table border="0" cellpadding="2" cellspacing="2">';

				// Runs through $V and generates the mail
			if (is_array($valueList)) {
				foreach ($valueList as $key => $val) {
					if (!t3lib_div::inList($this->reserved_names, $key)) {
						$space = (strlen($val) > 60) ? LF : '';
						$val = (is_array($val) ? implode($val, LF) : $val);

							// convert form data from renderCharset to mail charset (HTML may use entities)
						$plainTextValue = $val;
						$HtmlValue = htmlspecialchars($val);

						$plainTextContent .= strtoupper($key) . ':  ' . $space . $plainTextValue . LF . $space;
						$htmlContent .= '<tr><td bgcolor="#eeeeee"><font face="Verdana" size="1"><strong>' . strtoupper($key)
								. '</strong></font></td><td bgcolor="#eeeeee"><font face="Verdana" size="1">' . nl2br($HtmlValue)
								. '&nbsp;</font></td></tr>';
					}
				}
			}
			$htmlContent .= '</table>';

			$this->plainContent = $plainTextContent;

			if ($valueList['html_enabled']) {
				$this->mailMessage->setBody($htmlContent, 'text/html');
				$this->mailMessage->addPart($plainTextContent, 'text/plain');
			} else {
				$this->mailMessage->setBody($plainTextContent, 'text/plain');
			}

			for ($a = 0; $a < 10; $a++) {
				$variableName = 'attachment' . (($a) ? $a : '');
				if (!isset($_FILES[$variableName])) {
					continue;
				}
				if (!is_uploaded_file($_FILES[$variableName]['tmp_name'])) {
					t3lib_div::sysLog('Possible abuse of t3lib_formmail: temporary file "' . $_FILES[$variableName]['tmp_name']
							. '" ("' . $_FILES[$variableName]['name'] . '") was not an uploaded file.', 'Core', 3);
				}
				if ($_FILES[$variableName]['tmp_name']['error'] !== UPLOAD_ERR_OK) {
					t3lib_div::sysLog('Error in uploaded file in t3lib_formmail: temporary file "'
							. $_FILES[$variableName]['tmp_name'] . '" ("' . $_FILES[$variableName]['name'] . '") Error code: '
							. $_FILES[$variableName]['tmp_name']['error'], 'Core', 3);
				}
				$theFile = t3lib_div::upload_to_tempfile($_FILES[$variableName]['tmp_name']);
				$theName = $_FILES[$variableName]['name'];

				if ($theFile && file_exists($theFile)) {
					if (filesize($theFile) < $GLOBALS['TYPO3_CONF_VARS']['FE']['formmailMaxAttachmentSize']) {
						$this->mailMessage->attach(Swift_Attachment::fromPath($theFile)->setFilename($theName));
					}
				}
				$this->temporaryFiles[] = $theFile;
			}

			$from = $this->fromName ? array($this->fromAddress => $this->fromName) : array($this->fromAddress);
			$this->recipient = $this->parseAddresses($valueList['recipient']);
			$this->mailMessage->setSubject($this->subject)
					->setFrom($from)
					->setTo($this->recipient)
					->setPriority($this->priority);
			$replyTo = $this->replyToName ? array($this->replyToAddress => $this->replyToName) : array($this->replyToAddress);
			$this->mailMessage->addReplyTo($replyTo);
			$this->mailMessage->getHeaders()->addTextHeader('Organization', $this->organisation);
			if ($valueList['recipient_copy']) {
				$this->mailMessage->addCc($this->parseAddresses($valueList['recipient_copy']));
			}
			if ($this->characterSet) {
				$this->mailMessage->setCharset($this->characterSet);
			}
				// Ignore target encoding. This is handled automatically by Swift Mailer and overriding the defaults
				// is not worth the trouble

				// log dirty header lines
			if ($this->dirtyHeaders) {
				t3lib_div::sysLog('Possible misuse of t3lib_formmail: see TYPO3 devLog', 'Core', 3);
				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']) {
					t3lib_div::devLog('t3lib_formmail: ' . t3lib_div::arrayToLogString($this->dirtyHeaders, '', 200), 'Core', 3);
				}
			}
		}
	}
	
	function saveFormdata($valueList, $locationData)
	{

		unset($valueList['html_enabled']);
    	unset($valueList['subject']);
    	unset($valueList['recipient']);
    	unset($valueList['recipient_copy']);
    	/*print "<pre>";
		print_r($valueList);
		print "</pre>";
		die(serialize($valueList));*/
	/*	    [html_enabled] => 1
    [subject] => This is the subject
    [name] => Enter sssname here
    [email] => sss
    [address] => sss
    [tv] => 1
    [recipient] => webmaster@lth.se
    [recipient_copy] =>
    a:9:{s:11:"looking_for";s:2:"aa";s:7:"missing";s:2:"aa";s:8:"randomID";s:32:"38578035ffd234e003bbfaa5d776fa6b";s:10:"removeFile";s:0:"";s:15:"removeFileField";s:0:"";s:11:"step-2-next";s:6:"Skicka";s:11:"submitField";s:0:"";s:9:"submitted";s:1:"1";s:12:"visitor_type";s:8:"external";}
    a:8:{s:4:"name";s:18:"Enter sssname here";s:5:"email";s:3:"sss";s:7:"address";s:3:"sss";s:2:"tv";s:1:"1";}
    */

		$locationDataArray = explode(":", $locationData);
		/*$params = 'a:' . count($valueList) . ':{';
		$insertFlag = false;
		
		foreach($valueList as $key => $value) {
			if($key=="recipient") $insertFlag = false;
			if($insertFlag) {
				$params .= 's:' . strlen($key) . ':"' . addslashes(htmlspecialchars($key)) . '";s:' . strlen($value) . ':"' . addslashes(htmlspecialchars($value)) . '";';
			}
			if($key=="subject") $insertFlag = true;
		}
		
		$params .= '}';*/
		
		$keys = array_keys($valueList);
		$hash = md5(serialize($keys));
		
		$insertArray = array(
			"pid" => intval($locationDataArray[0]),
			"tstamp" => time(),
			"crdate" => time(),
			"ip" => t3lib_div::getIndpEnv('REMOTE_ADDR'),
			"params" => serialize($valueList),
			"key_hash" => $hash
		
		);
		$res = $GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_formhandler_log", $insertArray);	
	}
}
?>