<?php

$item  = $vars['item'];
$performed_by = get_entity($item->subject_guid);
$object = get_entity($item->object_guid);
$url = $object->getURL();
$videoTitle .= '<a href="' . $url . '">' . $object->title . '</a>';
$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
$string .= sprintf(elgg_echo('video:river:created'),$url,$videoTitle);
echo $string;