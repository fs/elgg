<?php
/**
 * Elgg TGS REST API Plugin
 *
 * @package ElggTGSAPI
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

function tgsapi_init() {
	expose_function("test.echo", "my_echo", array("string" => array("type" => 'string')), "Test Method", 'GET', false, false);
	expose_function("users.active", "count_active_users", array("minutes" => array("type" => 'int', "required" => false)), "Counts active users who have used the site in the last X minutes", 'GET', true, false);
	expose_function("thewire.post", "api_post_to_wire", array("text" => array('type' => 'string')), 'Post to the wire!', 'POST', true, true);
	
}


function my_echo($string) {
	return $string;
}

function count_active_users($minutes=10) {
	$seconds = 60 * $minutes;
	$count = count(find_active_users($seconds, 9999));
	return $count;
}

function api_post_to_wire($text) {
	$text = substr($text, 0, 140);
	$access = ACCESS_PUBLIC;
	return thewire_save_post($text, $access, 0, "tgsapi");
}

// Register event 
register_elgg_event_handler('init', 'system', 'tgsapi_init');

?>