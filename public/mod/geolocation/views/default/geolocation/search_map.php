<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $vars['map_api'] ?>" 
		type="text/javascript"></script>
<script type="text/javascript">

	var markers = new Array();
	
	jQuery(function() {
		if (GBrowserIsCompatible()) {
			<?php
				$lt = $vars['entities'][0]->getLatitude();
				$lg = $vars['entities'][0]->getLongitude();
			?>
			var lt = <?= $lt ?> || geoip_latitude();
			var lg = <?= $lg ?> || geoip_longitude();
			
			var map = new google.maps.Map2(document.getElementById("map"));
			var center = new GLatLng(lt, lg);
			map.setCenter(center, 5);
			map.setUIToDefault();
			
			<?php
			
			$i = 1;
			foreach ($vars['entities'] as $entity) {
				
				$lt = $entity->getLatitude();
				$lg = $entity->getLongitude();
				
				$type = $entity->getType();
				$subtype = $entity->getSubtype();
				switch ($type) {
					
					case 'user':
						
						$link = '<a href="' . $entity->getURL() . '">' . $entity->name . '</a>';
						break;
					
					default:
						
						$link = '<a href="' . $entity->getURL() . '">' . $entity->title . '</a>';
						break;
				}
				
				
				switch ($subtype) {
					
					/*
					case 'album':
						
						$images = get_entities("object", "image", $entity->guid, '', 999);
						print_r($images);exit;
						$desc = '<p>' . $entity->description . '</p>';
						break;
					*/
					
					default:
						
						$desc = '<p>' . str_replace("\n","", nl2br($entity->description)) . '</p>';
						break;
					
				}
				
				echo '
				var marker_' . $i . ' = new GMarker(new GLatLng(' . $lt . ', ' . $lg .'));
				GEvent.addListener(marker_' . $i . ', "click", function() {
					var html = \'<div style="width: 210px; padding-right: 10px">\'+
							\'' . $link .'<br>\'+
							\'' . $desc . '\'+
							\'<\/div>\';
					marker_' . $i . '.openInfoWindowHtml(html);
				});
				map.addOverlay(marker_' . $i . ');
				GEvent.trigger(marker_' . $i . ', "click");
				markers[' . $i . '] = marker_' . $i . ';
				';
			
				$i++;
			}
			
			?>
			window.set_center = function (lt, lg) {
				map.setCenter(new GLatLng(lt, lg), 1);
				return false;
			}
			
		}
	});

	function selectMarker(n) {
		GEvent.trigger(markers[n], "click");
	}

</script>

<div id="map">
	<div style="padding: 1em; color: gray">Loading...</div>
</div>

