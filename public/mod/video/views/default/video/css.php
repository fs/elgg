/* video mod (enterprise default) */


/* video gallery view */
#video #video_gallery_container {
	padding:5px;
}
#video .search_gallery_item {
	float:left;
	margin:5px;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	background:white;
	border:2px solid white;
	width:222px;
	height:264px;
}
#video .search_gallery_item:hover {
	border-color: #4690D6;
	/*background-color: white !important; over-rule default v1.5 css until it's removed */
	color:#333333;
}
#video #video_gallery_container .pagination {
	clear:both;
}

.videorepo_gallery_item {
	margin:0;
	padding:0;
	text-align:center;
	position: relative;
	height:256px;
}
.videorepo_gallery_item p {
	margin:0;
	padding:0;
}
.videorepo_gallery_item .videorepo_timestamp {
	margin-top:5px;
}
.videorepo_gallery_item .videorepo_controls {
	height:32px;
	position: absolute;
	bottom:0;
	left:2px;
	display: block;
	width:218px;
}
.videorepo_gallery_item p.videorepo_title {
	display:block;
	font-weight:bold;
	height:32px;
	line-height:1.1em;
	margin:5px auto 3px auto;
	overflow:hidden;
	white-space:normal;
	width:200px;
}
/* IE7 */
*:first-child+html .videorepo_gallery_item p.videorepo_title {
	height:37px;
	margin:3px 0 0 0;
	padding:0;
	line-height:1.0em;
}
.videorepo_controls .Delete_Button {
	margin:0;
	float:left;
}
.videorepo_item {
	height:155px;
	width:210px;
	margin:0 auto;
	text-align:center;
	vertical-align:middle;
	background-color: black;
}
.processing_video {
	margin:0 auto;
	text-align:center;
	vertical-align:middle;
}
.processing_video p {
	text-align:center;
	vertical-align:middle;
	color:white;
	padding:10px;
}
.processing_video img {
	text-align:center;
	vertical-align:middle;
	margin:10px auto 0 auto;
	width:33px;
	height:33px;
}


/* video individual resource page */
#video .ContentWrapper #content_area_user_title h2,
#video .ContentWrapper #content_area_group_title h2 {
	border-bottom:1px solid #CCCCCC;
	margin:0 0 5px;
	padding:0 0 5px;
}
#video .ContentWrapper #content_area_user_title {
	margin-top:5px;
}
.longtext_nextfield {
	margin-top:15px;
}
.videorepo_tags {
	display:table;
	margin:0 auto;
	padding:0 0 10px 10px;
	width:auto;
}
.videorepo_specialcontent {
	clear:both;
	margin:0 0 20px;
	text-align:center;
}
.videorepo_description p {
	margin:0;
	padding:0 0 5px;
}
.videorepo_maincontent {
	border-top:1px solid #CCCCCC;
	margin-top:2px;
	padding-top:8px;
	position:relative;
}
.videorepo_owner_details {
	color:#AAAAAA;
	line-height:0.8em;
	margin:0 0 0 30px;
	padding:0;
}
.videorepo_owner {
	padding:0;
}
.videorepo_owner .usericon {
	float:left;
	margin:0;
}
.videorepo_owner .videorepo_owner_details .generic_access {
	line-height:1.4em;
}
.video_download {
	text-align: center;
}






/* video edit and upload pages */
#upload_video {
	padding-top:10px;
}





/* video entry in river */
.river_object_video_create {
	background:transparent url(<?php echo $CONFIG->wwwroot; ?>mod/video/graphics/river_icon_video.gif) no-repeat scroll left -1px;
}
.river_object_video_comment {
	background: url(<?php echo $vars['url']; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_video_update {
	background:transparent url(<?php echo $CONFIG->wwwroot; ?>mod/video/graphics/river_icon_video.gif) no-repeat scroll left -1px;
}



