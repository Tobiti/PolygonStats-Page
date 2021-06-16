<?php
	function hideAccountName($name) {
	  return substr($name, 0, 2) . "XXXXX" . substr($name, strlen($name)-2);
	}
?>
