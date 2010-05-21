[1mdiff --git a/public/mod/geolocation/views/default/geolocation/css.php b/public/mod/geolocation/views/default/geolocation/css.php[m
[1mindex 3ff79da..b804069 100644[m
[1m--- a/public/mod/geolocation/views/default/geolocation/css.php[m
[1m+++ b/public/mod/geolocation/views/default/geolocation/css.php[m
[36m@@ -1,4 +1,5 @@[m
 #map { width:650px; height:400px; overflow:hidden; }[m
[32m+[m[32m#map.edit-location {width:698px;}[m[41m[m
 #map > div { width:630px; }[m
 #map >div#logocontrol {width:auto;}[m
 .map-container { background:#fff; margin:-35px 0 20px 10px; padding:10px 20px 20px; width:698px; -moz-border-radius:8px; float:left; padding:0 0 5px; -webkit-border-radius: 8px; }[m
[1mdiff --git a/public/mod/geolocation/views/default/geolocation/edit_location.php b/public/mod/geolocation/views/default/geolocation/edit_location.php[m
[1mindex b765d74..45e8339 100644[m
[1m--- a/public/mod/geolocation/views/default/geolocation/edit_location.php[m
[1m+++ b/public/mod/geolocation/views/default/geolocation/edit_location.php[m
[36m@@ -16,14 +16,14 @@[m
 	}[m
 [m
 	?>[m
[31m-<div style="position:relative;width:650px;margin:0 10px;">[m
[32m+[m[32m<div style="position:relative;width:698px;margin:0 10px;">[m
 <div class="geosearch single">[m
 	<form name="geosearch" id="geosearch" onsubmit="return false;">[m
 		<input type="text" name="query" id="query" value=""/>[m
 		<input type="submit" id="query_submit" value="Search" />[m
 	</form>[m
 </div>[m
[31m-<div id="map">[m
[32m+[m[32m<div id="map" class="edit-location">[m
 	<div style="padding: 1em; color: gray">Loading...</div>[m
 </div></div>[m
 <form action="<?php echo $vars['url']; ?>action/profile/edit" method="post" id="location_form" style="margin:0 10px;">[m
[36m@@ -31,14 +31,15 @@[m
 	<input type="hidden" value="<?php echo $lat; ?>" name="<?php echo $vars['page']; ?>_latitude" id="<?php echo $vars['page']; ?>_geolocation_latitude" />[m
 	<input type="hidden" value="<?php echo $lng; ?>" name="<?php echo $vars['page']; ?>_longitude" id="<?php echo $vars['page']; ?>_geolocation_longitude" />[m
 	<?php if ($vars['page'] == 'current'): ?>[m
[31m-	<input type="hidden" value="1" name="set_geolocation_auto_current_location" />	[m
[31m-	<input type="checkbox" value="yes" name="geolocation_auto_current_location" <?php if($user->geolocation_auto_current_location == 'yes') echo ' checked=checked' ;?>/> Set auto current location by ip after login<br />[m
[32m+[m		[32m<input type="hidden" value="1" name="set_geolocation_auto_current_location" />[m[41m	[m
[32m+[m		[32m<input type="checkbox" value="yes" name="geolocation_auto_current_location" <?php if($user->geolocation_auto_current_location == 'yes') echo ' checked=checked' ;?>/> Set auto current location by ip after login<br />[m
[32m+[m	[32m<?php endif; ?>[m
[32m+[m	[32m<?php if ($vars['page'] == 'current'): ?>[m
[32m+[m		[32m<a href="javascript:setLocation()">Set current location by my current ip-address</a>[m
 	<?php endif; ?>[m
 	<input type="submit" id="save_location" name="save" value="Save" />[m
 </form>[m
[31m-<?php if ($vars['page'] == 'current'): ?>[m
[31m-<a href="javascript:setLocation()">Set current location by my current ip-address</a>[m
[31m-<?php endif; ?>[m
[32m+[m
 [m
 <script type="text/javascript">[m
 	var form = $('#location_form');[m
[1mdiff --git a/public/mod/googleappslogin/models/functions.php b/public/mod/googleappslogin/models/functions.php[m
[1mindex edd33a0..2be96cb 100644[m
[1m--- a/public/mod/googleappslogin/models/functions.php[m
[1m+++ b/public/mod/googleappslogin/models/functions.php[m
[36m@@ -1,5 +1,4 @@[m
 <?php[m
[31m-//error_reporting(-1);[m
 /**[m
  * Functions for use OAuth[m
  *[m
