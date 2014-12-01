<?php
function user_removeChr($content,$conf) {
	//$content = str_replace('\n', '1', $content);
	//$content = str_replace('\r', '2', $content);
	$content = str_replace(chr(13), '/', $content);
	$content = str_replace(chr(10), '', $content);
	return $content;
}
?>