#map { width:650px; height:400px; overflow:hidden; }
#map.edit-location {width:698px;position:static !important;}
a.set-location {position:absolute;right:0;top:3px;font-weight:bold;}
#map > div { width:630px; }
#map >div#logocontrol {width:auto;}
.map-container {
background:#fff;
margin:-35px 0 20px 10px;
padding:10px 20px 20px;
width:698px;
-moz-border-radius:8px;
float:left;
padding:0 0 5px;
-webkit-border-radius: 8px;
}
.map-container > label{ margin-left:10px; }
.map {
  background:#E5E3DF !important;
	width: 650px !important;
	height:400px;
	-webkit-border-radius: 8px;
	-moz-border-radius:8px;
	display:none;
  border:1px solid #4690D6;
  cursor:default;
  position:absolute;
  text-align:left;
  z-index:50;
  padding: 0;
	margin-top:-360px !important;
	left:50%;
	margin-left:-340px !important;
}
.map .view-map-link.close {z-index:500;position:absolute;right:0px; top:-20px;background:url(/mod/geolocation/graphics/pop_up_logout.png) right -1px no-repeat;height:17px;width:50px;display:block;}
.map .view-map-link.close:hover {background-position:right -18px;}
.map .geosearch,
.geosearch.single {position:absolute; bottom:15px; right:7px; z-index:500;}
.map .geosearch input,
.geosearch input {border-color:#4690d6;}
#blog_edit_page .map-container {background: #BBDAF7; margin-left:230px;width:710px;}
#blog_edit_page .map-container > label{ margin-left:20px; }
#blog_edit_page .map {  }
.view-map-link { font-size:11px; }
.user_settings div.map-container { float:none; margin:0; width:100%; background:#fff; }
.user_settings div.map-container label { background:#E4E4E4; -webkit-border-radius:4px; -moz-border-radius:4px; color:#333; font-size:1.1em; line-height:1em; margin:0 0 10px; padding:5px; display:block; }
.user_settings div.map-container div.map { margin-left:10px !important ; -webkit-border-radius:4px; -moz-border-radius:4px; width:658px !important; }
.user_settings div.map-container div.map #map { }
.search-results-sidebar { -webkit-border-radius:8px; -moz-border-radius:8px 8px 8px 8px; background:#DEDEDE; float:left; margin:15px 0 0 1px; padding:0 0 5px; width:718px; }
.search-results-sidebar div#map { width:672px; }
.google-map {position:relative;}
.filter-toolbar {padding:10px;overflow:hidden;position:relative;}
.filter-toolbar .title {font-weight:bold;margin-bottom:0;}
.filter-toolbar ul {overflow:hidden;padding:0;margin:10px 0 6px;float:left;width:100%;}
.filter-toolbar ul li {list-style:none;display:block;float:left;width:180px;padding:2px 0;}
.filter-toolbar ul li label {font-size:12px;font-weight:normal;}
.filter-toolbar .map-actions {width:140px;position:absolute;top:40px;right:10px;}
.filter-toolbar .map-actions .select {text-align:left;float:left;width:180px;}
.filter-toolbar .map-actions .update-map {padding-left:0px;width:140px;float:right;}
.filter-toolbar .map-actions .update-map .upd {display:block;}
.filter-toolbar .map-actions .update-map .upd  input {margin:8px 0 5px;}
.map_home,
.map_current {float:left;margin-left:76px;}
#map_home {width:200px;height:200px;overflow: hidden;}
#map_current {width:200px;height:200px;overflow: hidden;}
#layout_map h2 {margin:0;padding-left:0;}
#facebox .popup {}
#facebox .body {width:auto;}
p.user_menu_item {margin-bottom:0;}
.direct-link {font-size:80%;padding-right:10px;}
#hidden_share_link {font-size:80%;padding:0 2px;overflow:hidden;clear:both;}
	#hidden_share_link input {width:99%;padding:1px 2px;}

#layout_map #my_location_button {top:310px;left:36px;}

#my_location_button {position:absolute;top:290px;left:26px; background-image:url('/mod/geolocation/graphics/my_location_button.png');  width: 20px; height: 20px; overflow: visible !important; z-index: 1;cursor:pointer;}

#map .gmnoprint ol {padding-left:0;margin:0;overflow:hidden;}
	#map .gmnoprint ol li {margin-bottom:5px;}

	.user_on_map {overflow:hidden;width:100%;}
		.user_on_map .user-image {float:left;width:20px;margin-right:5px;}
			.user_on_map .user-image a {display:block;}
			.user_on_map .user-image img {width:15px;margin-top:1px;margin-left:3px;}
		.user_on_map .user-description {margin-left:25px;}
			.user_on_map .user-description a.user-name {display:block;margin:0;line-height:1.3em;text-transform:capitalize;font-size:13px;font-weight:bold;}
			.user_on_map .user-description p {margin:0;line-height:1.2em;font-size:10px;}
		
	.object-on-map {overflow:hidden;width:100%;}
		.object-on-map .object-icon {float:left;width:20px;height:20px;margin-right:5px;background:url('/mod/geolocation/graphics/objects-on-map.png') 0 0 no-repeat;}
			.object-on-map.bookmarks .object-icon {background-position:0 -19px}
			.object-on-map.group .object-icon {background-position:0 -38px}
			.object-on-map.doc_activity .object-icon {background-position:0 -59px}
			.object-on-map.resourcerequest .object-icon {background-position:0 -81px}
			.object-on-map.image .object-icon {background-position:0 -99px}
			.object-on-map.album .object-icon {background-position:0 -118px}
			.object-on-map.page_top .object-icon {background-position:0 -139px}
			.object-on-map.site_activity .object-icon {background-position:0 -159px}
			.object-on-map.rubric .object-icon {background-position:0 -178px}
			.object-on-map.blog .object-icon {background-position:0 -198px}
			.object-on-map.thewire .object-icon {background-position:0 -218px}
			.object-on-map.file .object-icon {background-position:0 -238px}
			.object-on-map.video .object-icon {background-position:0 -258px}
			.object-on-map.page .object-icon {background-position:0 -278px}
			.object-on-map.todo .object-icon {background-position:0 -298px}
		.object-on-map .object-description {margin-left:25px;}
			.object-on-map .title {display:block;margin:0;line-height:1.3em;text-transform:capitalize;font-size:13px;font-weight:bold;}
			.object-on-map p {margin:0;line-height:1.2em;font-size:10px;}

