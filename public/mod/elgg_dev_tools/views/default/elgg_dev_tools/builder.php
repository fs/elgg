<?php 
/*******************************************************************************
 * Plugin Builder Form View
 * 
 * This is the form that the admin uses to build a plugin skeleton
 * 
 * @package Elgg Developer Tools
 * @author Cash Costello
 ******************************************************************************/

// check session for values in case something failed in the previous submit
$params = $_SESSION['build_plugin'];

// clear session variables in case user gives up
unset($_SESSION['build_plugin']);


//******************** build form *******************************

$form_body = '<p>' . elgg_echo('elgg_dev_tools:builder:explanation') . '</p>';

// plugin name
$form_body .= "<p><b>" . elgg_echo('elgg_dev_tools:plugin_name') . ":</b> ";
$form_body .= elgg_view('input/text', array('value'=>$params['plugin_name'], 'internalname'=>'params[plugin_name]', 'class' => 'elgg_dev_tools_textbox'));
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:plugin_name:explanation') . '</em></p>';
// plugin name

// pages
$form_body .= "<p><b>" . elgg_echo('elgg_dev_tools:pages') . ":</b> ";
$form_body .= elgg_view('input/text', array('value'=>$params['pages'], 'internalname'=>'params[pages]', 'class' => 'elgg_dev_tools_textbox'));
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:pages:explanation') . '</em></p>';
// pages

// page handler
$form_body .= "<p><b>" . elgg_echo('elgg_dev_tools:page_handler') . ":</b> ";
$form_body .= elgg_view('input/text', array('value'=>$params['page_handler'], 'internalname'=>'params[page_handler]', 'class' => 'elgg_dev_tools_textbox'));
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:page_handler:explanation') . '</em></p>';
// page handler


// actions
$form_body .= "<p><b>" . elgg_echo('elgg_dev_tools:actions') . ":</b> ";
$form_body .= elgg_view('input/text', array('value'=>$params['actions'], 'internalname'=>'params[actions]', 'class' => 'elgg_dev_tools_textbox'));
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:actions:explanation') . '</em></p>';
// actions

// plugin settings
$checked = ($params['plugin_settings']) ? 'checked="checked"' : '' ;
$form_body .= "<p><input type=\"checkbox\" name=\"params[plugin_settings]\" $checked value=\"1\" class=\"elgg_dev_tools_checkbox\" /> <b>";
$form_body .= elgg_echo('elgg_dev_tools:plugin_settings') . "</b>";
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:plugin_settings:explanation') . '</em></p>';
// plugin settings

// user settings
$checked = ($params['user_settings']) ? 'checked="checked"' : '' ;
$form_body .= "<p><input type=\"checkbox\" name=\"params[user_settings]\" $checked value=\"1\" class=\"elgg_dev_tools_checkbox\" /> <b>";
$form_body .= elgg_echo('elgg_dev_tools:user_settings') . "</b>";
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:user_settings:explanation') . '</em></p>';
// user settings

// widget
$checked = ($params['widget']) ? 'checked="checked"' : '' ;
$form_body .= "<p><input type=\"checkbox\" name=\"params[widget]\" $checked value=\"1\" class=\"elgg_dev_tools_checkbox\" /> <b>";
$form_body .= elgg_echo('elgg_dev_tools:widget') . "</b>";
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:widget:explanation') . '</em></p>';
// widget

// css
$checked = ($params['css']) ? 'checked="checked"' : '' ;
$form_body .= "<p><input type=\"checkbox\" name=\"params[css]\" $checked value=\"1\" class=\"elgg_dev_tools_checkbox\" /> <b>";
$form_body .= elgg_echo('elgg_dev_tools:css') . "</b>";
$form_body .= '<br /><em>' . elgg_echo('elgg_dev_tools:css:explanation') . '</em></p>';
// css

$form_body .= "<br />";

$form_body .= elgg_view('input/submit', array('value'=>elgg_echo('elgg_dev_tools:builder:formbutton')));

echo elgg_view('input/form', array('body'=>$form_body, 'action'=>$CONFIG->wwwroot . 'action/elgg_dev_tools/buildplugin', 'internalid'=>'elgg_dev_tools_form'));

?>