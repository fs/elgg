<?php

	/**
     * Elgg Feedback plugin
     * Feedback interface for Elgg sites
     * 
     * @package Feedback
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Prashant Juvekar
     * @copyright Prashant Juvekar
     * @link http://www.linkedin.com/in/prashantjuvekar
	 */

?>

#feedbackWrapper {
	position: fixed;
	top: 113px;
	left: 0px;
	width: 450px;
	z-index:1; 
}

#feedBackToggler {
	float: left;
}

#feedBackContent {
	width: 400px;
	display: none;
	overflow: hidden;
	float: left;
	border: solid #ccc 1px;
	background-color: #ffffe0;
}

#feedbackError {
	color: #ff0000;
}

#feedbackSuccess {
	color: #00bb00;
	font-weight: bold;
}

.feedbackLabel {
}

.feedbackText {
	width:350px;  
}

.feedbackTextbox {
	width:350px;  
	height:75px;
}
 
.captcha {
	padding:10px;
}
.captcha-left {
	float:left;
	border:1px solid #0000ff;
}
.captcha-middle {
	float:left;
}
.captcha-right {
	float:left;
}
.captcha-input-text {
	width:100px;
}

form#updatestatus {
	display: inline;
}

#feedback_comment {
	-moz-border-radius:6px 6px 6px 6px;
	-webkit-border-radius: 6px 6px 6px 6px;
	width: 590px; 
	border:1px solid lightgrey;
	padding: 10px;
	margin-top:4px;
}

#feedback_comments_parent {
	width: 600px !important;
}

#feedback_comments_parent  table.mceLayout {
	width: 600px !important;
}

#feedbackvote {
	display: block; 
	height: 35px;'>
}

#feedbackvote img {
	vertical-align: middle;
}

#feedbackvote span {
	font-weight: bold;
}

#feedbackinfo .listingstrapline {
	margin: 0 0 0 0px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

/* River */
.river_object_feedback_create {
	background: url(<?php echo $vars['url']; ?>mod/feedback/images/feedback_river.gif) no-repeat left -1px;
}

.river_object_feedback_update {
	background: url(<?php echo $vars['url']; ?>mod/feedback/images/feedback_river.gif) no-repeat left -1px;
}
.river_object_feedback_comment {
	background: url(<?php echo $vars['url']; ?>mod/feedback/images/feedback_river.gif) no-repeat left -1px;
}

