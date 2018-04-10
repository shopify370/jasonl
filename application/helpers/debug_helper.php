<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function debug($var = false, $showHtml = false, $showFrom = true) {

	if ($showFrom) {
		$calledFrom = debug_backtrace();
		echo '<strong>' . substr(str_replace(FCPATH, '', $calledFrom[0]['file']), 0) . '</strong>';
		echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
	}
	echo "\n<pre class=\"cake-debug\">\n";

	$var = print_r($var, true);
	if ($showHtml) {
		$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
	}
	echo $var . "\n</pre>\n";
}

/* End of file debug_helper.php */
/* Location: ./application/helpers/debug_helper.php */