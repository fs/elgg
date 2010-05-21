<?php

$lt = 0;
$lg = 0;

foreach ($vars['entities'] as $entity) {
	$lt = $entity->getLatitude();
	$lg = $entity->getLongitude();
	
	if ($lg && $lg) {
		break;
	}
}

?>
<script type="text/javascript">

	var <?=$vars['prefix']?>_markers = new Array();
	
	jQuery(function() {
		
		<?php
			
			$i = 1;
			foreach ($vars['entities'] as $entity) {
				
				$lt = $entity->getLatitude();
				$lg = $entity->getLongitude();
				
				if (!$lt || !$lg) {
					continue;
				}
				
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
						
						$desc = '<p>' . str_replace("'","\\'", str_replace("\r","", str_replace("\n","", nl2br($entity->description)))) . '</p>';
						break;
					
				}
				
				echo '
				var point_' . $i . ' = new GLatLng(' . $lt . ', ' . $lg .');
				var marker_' . $i . ' = new GMarker(point_' . $i . ');
				GEvent.addListener(marker_' . $i . ', "click", function() {
					var html = \'<div style="width: 210px; padding-right: 10px">\'+
							\'' . $link .'<br>\'+
							\'' . $desc . '\'+
							\'<\/div>\';
					marker_' . $i . '.openInfoWindowHtml(html);
				});
				' . $vars['prefix'] . '_markers[' . $i . '] = marker_' . $i . ';
				';
			
				$i++;
			}
		
		?>
		
		all_markers['<?=$vars['prefix']?>_markers'] = <?=$vars['prefix']?>_markers;
		
	});

</script>
