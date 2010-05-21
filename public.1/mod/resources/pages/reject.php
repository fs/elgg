<?php
	/**
	 * View Request Page 
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// include the Elgg engine
	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php"; 

	// admins only
	if (!isresourceadminloggedin())
		exit;

	$request_guid = get_input('guid');
	
	// create content for main column
 	echo elgg_view('resources/forms/rejectedcomment', array('guid' => $request_guid));
	
?>