<?php
	/**
	 * Resources CSS
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
?>
.resources_div {
	color: #000000;
}

.singleview {
	margin-top:10px;
}

.resources_icon {
	float:left;
	margin:3px 0 0 0;
	padding:0;
}

.resources .listingstrapline {
	margin: 0 0 0 0px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

.resources .strapline {
	margin: 0 0 0 35px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

.resources .description img[align="left"] {
	margin: 10px 10px 10px 0;
	float:left;
}

.resources p.fulltags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 7px 35px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.resources p.listingtags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0px 0px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.resources p.gallerytags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0 0;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
	text-align: left;
}


.resources .controls {
	margin-top: 5px;
}

.resources span.status_<?php echo elgg_echo('resources:status:open'); ?> {
	color: black;
	font-weight: bold;
}

.resources span.status_<?php echo elgg_echo('resources:status:approved'); ?> {
	color: green;
	font-weight: bold;
}

.resources span.status_<?php echo elgg_echo('resources:status:rejected'); ?> {
	color: red;
	font-weight: bold;
}

.resources_admin .generic_comment{
	border: 2px solid #cccccc;
	background: #eeeeee;
}

#request_form_message {
	color: #267F28;
}

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

#resource_dialog  {
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
