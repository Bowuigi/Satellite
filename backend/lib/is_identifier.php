<?php
// This pattern is very common, though the file is quite short
function is_identifier(string $str) {
	return preg_match('/^[a-z0-9\-_\\.]{1,50}$/', $str);
}
?>
