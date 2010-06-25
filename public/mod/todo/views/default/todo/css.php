<?php
	/**
	 * Todo CSS
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
?>

.todo {
	
}

.todo .right {
	float: right;
	text-align: right;
}

.todo .left {
	float: left;
	text-align: left;
}

.todo .multiselect {
	border: 2px solid #bbbbbb;
	font-size: 120%;
	width: 150px;
	height: auto;
	padding: 10px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .submission_content_select {
	border: 2px solid #bbbbbb;
	font-size: 120%;
	width: 100%;
	height: auto;
	padding: 10px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo_icon {
	float:left;
	margin:3px 0 0 0;
	padding:0;
}

.todo .listingstrapline {
	margin: 0 0 0 0px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

.todo .strapline {
	padding: 10px;
	height: 16px;
	background: #bbdaf7;
	margin: 0 0 0 0px;
	line-height:1em;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .description img[align="left"] {
	margin: 10px 10px 10px 0;
	float:left;
}

.todo p.fulltags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 7px 0px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.todo p.listingtags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0px 0px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.todo p.gallerytags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0 0;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.todo .todo_header {
	width: 98%;
}

.todo .todo_header .todo_header_title {
	width: 50%;
	float: left;
}

.todo .todo_header .todo_header_controls {
	float: left;
	width: 50%;
	text-align: right;
}

.todo .assignee_table {
	width: 98%;
	margin: 4px;

	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .assignee_table td.assignee {
	padding: 5px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .assignee_table td.alt {
	background: #eeeeee;
}

.todo .status_table {
	width: 98%;
	margin: 4px;
	border: 3px solid #aaaaaa;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .status_table td {
	padding: 5px;
}

.todo .status_table th {
	padding: 5px;
	background: #bbdaf7;
	font-weight: bold;
	color: #666666;
	border-bottom: 1px solid #aaaaaa;
}

.todo .status_table td.alt {
	background: #eeeeee;	
}

#assign_individual_container {
	display: none;
}

#assign_group_container {
	display: none;
}

#rubric_picker_container {
	display: none;
}

.todo_listing {
	margin: 4px;
	width: 98%;
}

.todo_listing .todo_listing_icon {
	width: 25px;
	height: 25px;
	float: left;
}

.todo_listing .todo_listing_info {
	height: 25px;
	width: auto;
	padding-left: 10px;
	float: left;
}

.todo_listing .todo_listing_options {
	float: right;
	width: 100px;
}

.todo span.complete {
	color: green;
	font-weight: bold;
}

.todo span.incomplete {
	color: red;
	font-weight: bold;
}

.todo #add_content_area {
	width: 100%;
}

.todo #add_content_area .content_menu {
	width: 20%;
	float: left;
	display: none;
}

.todo #add_content_area .content_menu  a {
	font-size: 120%;
}

.todo #add_content_area #content_container {
	width: 79%;
	float: left;
}

.todo .content_div {
	display:none;
}

.todo #submission_error_message {
	color: red;
	font-weight: bold;
	display:none;
}

#submission_ajax_spinner {
	float: right;
	margin-right: 340px;
	display: none;
}

/** POPUP DIALOG **/

/** Popups **/

.ui-widget-overlay
{
	position: fixed;
	top: 0px;
	left: 0px;
    background-color: #000000;
    opacity: 0.5;
	-moz-opacity: 0.5; 
	z-index: 1001 !important;
}

#submission_dialog  {
	border: 8px solid #555555;
	background: #ffffff;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}



.ui-dialog .ui-dialog-buttonpane {
	position: absolute; 
	right: .3em; 
	top: 30px; 
	width: 19px; 
	margin: -10px 0 0 0; 
	padding: 1px; height: 18px; 
}

.ui-dialog .ui-dialog-buttonpane button { 
	
	cursor: pointer; 
	padding: .2em .6em .3em .6em; 
	line-height: 1.4em; 
	width:auto; 
	overflow:visible; 

}

.ui-dialog .ui-dialog-buttonpane button {
	-moz-border-radius:4px 4px 4px 4px;
	-webkit-border-radius: 5px 5px 5px 5px;
	background:none repeat scroll 0 0 #4690D6;
	border:1px solid #4690D6;
	color:#FFFFFF;
	cursor:pointer;
	font:bold 12px/100% Arial,Helvetica,sans-serif;
	height:25px;
	float: right; margin: .5em .4em .5em 0; 
	padding:2px 6px;
	width:auto;
}
