<?php
/*
 * Css for rubric
 */
?>
.rubric_div {
	color: #000000;
}

.singleview {
	margin-top:10px;
}

.rubric_icon {
	float:left;
	margin:3px 0 0 0;
	padding:0;
}

.rubric_description img[align="left"] {
	margin: 10px 10px 10px 0;
	float:left;
}

textarea.rubric_input, input.rubric_input {
	width: 119px;
	height: 80px;
	padding: 5px;
	border: 1px solid #dddddd;
	font-family: Tahoma, sans-serif;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
	border-radius: 5px;
	font-size: 90%;
}

td.rubric_td {
	width: 119px;
	height: 100;
	padding: 5px;
	margin-top: 10px;
	//border: 1px solid #eeeeee;
	font-family: Tahoma, sans-serif;
	border-radius: 5px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

textarea.alt, td.alt {
	background: #eeeeee;
}

input.rubric_header, td.rubric_header {
	font-weight: bold;
	text-align: center;
	background: #bbdaf7;
	border: 1px solid #bbdaf7;
	width: 119px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
	border-radius: 5px;
	height: 15px;
}

td.rubric_col {
	border-top: 1px solid red;
	border-left: 1px solid blue;
}

td.rubric_col_last {
	border-bottom: 1px solid red;
	border-right: 1px solid blue;
}

table.rubric_table {
	width: 98%;	
	border-spacing: 1px;
}


table.rubric_table td {
	height: 80px;
}

#rubric .tags {
    padding:0 0 0 16px;
    margin:10px 0 4px 0;
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
}

#rubric .strapline {
    text-align:right;
    border-top:1px solid #efefef;
    margin:10px 0 10px 0;
    color:#666666;
}
#rubric .categories {
    border:none !important;
    padding:0 !important;
}


.rubric_icon {
	float:left;
	margin:3px 0 0 0;
	padding:0;
}

.rubric h3 {
	font-size: 150%;
	margin:0 0 10px 0;
	padding:0;
}

.rubric h3 a {
	text-decoration: none;
}

.rubric p {
	margin: 0 0 5px 0;
}

.rubric .strapline {
	margin: 0 0 0 35px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

.rubric .listingstrapline {
	margin: 0 0 0 0px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

.rubric p.tags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 7px 35px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.rubric p.listingtags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0 0;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.rubric p.gallerytags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0 0;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
	text-align: left;
}

.rubric .controls {
	margin-top: 5px;
}

.rubric .options {
	margin:0;
	padding:0;
}

.rubric_body img[align="left"] {
	margin: 10px 10px 10px 0;
	float:left;
}
.rubric_body img[align="right"] {
	margin: 10px 0 10px 10px;
	float:right;
}
.rubric_body img {
	margin: 10px !important;
}

a.remove_over {
	opacity:1;
	filter:alpha(opacity=100);
}

a.remove {
	opacity:0.2;
	filter:alpha(opacity=20);
}

.remove_img {
	opacity:0.2;
	filter:alpha(opacity=20);
	width: 20px;
	height: 20px;
	background-color: #000000;
	background-image: url("<?php echo $vars['url'] . "mod/rubricbuilder/images/minus.gif"; ?>");
}

.remove_img_over {
	opacity:1;
	filter:alpha(opacity=100);
	width: 20px;
	height: 20px;
	background-color: #000000;
	background-image: url("<?php echo $vars['url'] . "mod/rubricbuilder/images/minus.gif"; ?>");
}

div#rubric_revision_menu {
	display: none;
	width: 100%;
}

div#rubric_revision_menu table#revision_menu_table {
	width: 100%;
}

table#revision_menu_table td.revision_desc {
	text-align: center;
	width: auto;
	vertical-align: middle;
}

table#revision_menu_table td.revision_prev {
	text-align: right;
	width: 35%;
}


table#revision_menu_table td.revision_select {
	text-align: center;
	width: auto;
	margin-left: auto;
	margin-right: auto;
	vertical-align: middle;
}


table#revision_menu_table td.revision_next {
	text-align: left;
	width: 35%;
}

select#select_revision {
	font-size: 100%;
}

/* For the river! */


.river_object_rubric_create {
	background: url(<?php echo $vars['url']; ?>mod/rubricbuilder/images/rubric_river.gif) no-repeat left -1px;
}
.river_object_rubric_comment {
	background: url(<?php echo $vars['url']; ?>mod/rubricbuilder/images/rubric_river.gif) no-repeat left -1px;
}
.river_object_rubric_update {
	background: url(<?php echo $vars['url']; ?>mod/rubricbuilder/images/rubric_river.gif) no-repeat left -1px;
}

