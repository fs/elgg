<?php
/**
 * Elgg JS pageshell
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

$entities = $GLOBALS['my_search_result'];

//header("Content-type: application/xml; charset=UTF-8");
header("Content-type: application/force-download");
header("Content-disposition: attachment; filename=search_results_locations.kml"); 

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<kml xmlns="http://www.opengis.net/kml/2.2"
 xmlns:gx="http://www.google.com/kml/ext/2.2">
 <Document>
    <name>Elgg locations</name>
    <open>1</open>
    <description>Locations by the searching results</description>
    <Folder>
      <name>Placemarks</name>
      <description></description>
<?php
  if (is_array($entities) && sizeof($entities) > 0) {
	foreach ($entities as $entity) {
		
		$lt = $entity->getLatitude();
		$lg = $entity->getLongitude();
		
		if ($lg && $lt) {
			?>

      <Placemark>
        <name><?= $entity->name ? $entity->name : $entity->title ?></name>
        <description>
	    <![CDATA[<?= $entity->description ?>]]>
        </description>
        <Point>
          <coordinates><?=$lg?>,<?=$lt?>,0</coordinates>
        </Point>
      </Placemark>
<?php
		}
	}
}
?>

    </Folder>
  </Document>
</kml>