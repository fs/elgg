<?php
/*******************************************************************************
 * The Admin Options Index Page
 *
 * This shows a form to update all of the plugin development tools you may need.
 *
 * @package Elgg Developer Tools
 * @author Aaron Saray (102degrees.com)
 * @author Cash Costello
 ******************************************************************************/

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	admin_gatekeeper();
	set_context('admin');
	
	$tab = get_input("tab");
	
	$title = elgg_view_title(elgg_echo('elgg_dev_tools:title'));
	$body = elgg_view('elgg_dev_tools/index', array("tab" => $tab));
	
	
	page_draw(elgg_echo('elgg_dev_tools:title'), elgg_view_layout("two_column_left_sidebar", '', $title . $body));
?>