<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<script src="/mod/geolocation/js/markerclusterer.js" type="text/javascript"></script>

<?php
    $selected=get_input('selected');
?>

<?php if (is_array($selected)) {
    $str='';
    foreach($selected as $item)  {
        $str.='check_types[]='.$item.'&';
    }
    
    echo '<script src="/pg/geolocation/data?link=1&'.$str.'" type="text/javascript"></script>';
?>    
<?php } else { ?>
    <script src="/pg/geolocation/data?types=all" type="text/javascript"></script>
<?php } ?>
 
<script type="text/javascript">
	var markerClusterer = null;
	var markers = [];


	function toggleAll(toggle) {
		var allCheckboxes = $("#typesForm input:checkbox:enabled");
		if(toggle) {
			allCheckboxes.attr('checked', 'checked');
		} else {
			allCheckboxes.removeAttr('checked');
		}
	}

	function setPosition() {
		var center = bounds.getCenter();
		var zoom   = map.getBoundsZoomLevel(bounds);

		map.setCenter(center, zoom);
	}

	function loadMarkers(datajson) {
		markers = [];
		var points = datajson.marker;
		bounds = new GLatLngBounds();

		for (i in points) {
			var latlng = new GLatLng(points[i].latitude, points[i].longitude);
			
			if( typeof(points[i].icon) != "undefined") {
                            var icon = new GIcon(G_DEFAULT_ICON);
                            icon.image = "/mod/geolocation/graphics/markers/" + points[i].icon + ".png";
                            var marker = new GMarker(latlng, {icon: icon});
			} else {
				var marker = new GMarker(latlng);
			}

			bounds.extend(marker.getLatLng());
			if(points[i].desc) {
				var fn = markerClick(points[i].desc, latlng);
				GEvent.addListener(marker, "click", fn);
			}

			markers.push(marker);
		}
	}

	function refreshMarkers(datajson) {
		loadMarkers(datajson);
		
		//map.addControl(new GLargeMapControl());
		//map.addControl(new GMapTypeControl());

		if (markerClusterer != null) {
			markerClusterer.clearMarkers();
		}

		markerClusterer = new MarkerClusterer(map, markers, {gridSize: 20}, datajson.marker);
	}
       

	function refreshMap(datajson) {
		loadMarkers(datajson);

		setPosition();

		//map.addControl(new GLargeMapControl());
		//map.addControl(new GMapTypeControl());

		if (markerClusterer != null) {
			markerClusterer.clearMarkers();
		}

		markerClusterer = new MarkerClusterer(map, markers, {gridSize: 20}, datajson.marker);
	}

	jQuery(function() {
		map = new google.maps.Map2(document.getElementById("map"));
		map.setUIToDefault();

		refreshMap(data);

                <?php
                        $lat = get_input('lat');
                        $lng = get_input('lng');
                        $zoom = get_input('zoom');
                        if ($lat) {
                            ?>
                                    var link = new GLatLng(<?=$lat?>, <?=$lng?>);
                                    map.setCenter(link, <?=$zoom ?>);
                            <?php
                        }
                ?>
                        

	});


        function showMapLink(t) {
            var checkboxes = new Array();
            var current_zoom = null;
            var current_latlng = null;
            var share_url = '';

            current_zoom = map.getZoom();
            current_latlng = map.getCenter();

            $("#typesForm input:checkbox:checked").filter(':checked').each(function() {
                   checkboxes.push($(this).val());
              });

            share_url = location.protocol+'//'+location.hostname+location.pathname +'?&lat='+current_latlng.lat() + '&lng='+current_latlng.lng() + '&zoom='+current_zoom+'&';
            for ( i in checkboxes) {
                share_url += 'selected[]='+checkboxes[i]+'&';
            }
						$('#hidden_share_link').toggle().find('input').val(share_url)[0].select();
						return false;
        }


	$(document).ready(function() {
        $('#typesForm').bind('submit', function() {
            $(this).ajaxSubmit({
                url: '/pg/geolocation/data',
				dataType : "json",
				success: function (result) {
					refreshMarkers(result);
				}
            });
            return false; // <-- important!
        });

		$('#geosearch').submit(function() {
			geocode();
			return false;
		});
    });

</script>
  

<div class="filter-toolbar">
	<form method="GET" action="" id="typesForm">
		<p class="title">Include on map:</p>
		<ul>
			<?php
                            $selected=array_flip($selected);
                            foreach($vars['select_checkboxes'] as $item): ?>
			<li>
                            <?php                                                               
                                if (!is_null($selected[$item['name']])) {
                                    echo '<input id="label_'.$item['name'].'" type="checkbox" name="check_types[]" value="'.$item['name'].'" checked />';
                                } else  {
                                    echo '<input id="label_'.$item['name'].'" type="checkbox" name="check_types[]" value="'.$item['name'].'" />';
                                }
                            ?>            
                                <label for="label_<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="map-actions">
				<div class="select">
						<span>Select: <a href="javascript:toggleAll(1)">All</a> | <a href="javascript:toggleAll(0)">None</a></span>
				</div>
				<div class="update-map">
						<span class="upd"><input type="submit" name="do" value="Update map"></span>
						<div class="direct-link">
								<div id="direct_link"><a href="#" onclick="return showMapLink(this);">Get direct link to this map</a></div>								
					  </div>
				</div>
		</div>
		<div style="display:none" id="hidden_share_link"><input type="text" value="" /></div>
	</form>
    
</div>


<div class="google-map">
	<div class="geosearch single">
		<form name="geosearch" id="geosearch" onsubmit="return false;">
			<input type="text" name="query" id="query" value=""/>
			<input type="submit" id="query_submit" value="Search" />
		</form>
	</div>
	<div id="my_location_button" onclick="javascript:doGeolocation()"  title="Where am I?"></div>
	<div id="map" style="width: 100%">                
		<div style="padding: 1em; color: gray">Loading...</div>
	</div>
</div>

