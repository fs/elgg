<?php

if (!function_exists('params_to_url')) {
	function params_to_url(&$item, $key){
		$item = $key . '=' . $item;
	}
}

?>
