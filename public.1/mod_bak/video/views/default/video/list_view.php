<?php
/**
 * Elgg video view
 */

global $CONFIG;

$video = $vars['entity'];

if ($video) {
	$video_guid = $video->getGUID();
	$tags = $video->tags;
	$title = $video->title;
	$desc = $video->description;
	$owner = $vars['entity']->getOwnerEntity();
	$friendlytime = friendly_time($vars['entity']->time_created);
	if ($title == '') {
		$title = elgg_echo('video:untitled');
	}
	echo "<div class='videorepo_gallery_item'>";
	echo "<p class='videorepo_title'>{$title}</p>";
	if ($video->thumbnail_path) {
		//$img = "<img src=\"{$vars['url']}_graphics/spacer.gif\" />";
		$img = "<a href='{$video->getURL()}'><img src=\"{$vars['url']}pg/video_file/{$video->getGUID()}/thumbnail/std\" /></a>";
	} elseif (!$video->transcoded_std_available) {
		//$img = "<img src=\"{$vars['url']}mod/video/graphics/pleasewait.gif\" />";
		$img = "<div class='processing_video'><p>Your video is in a queue awaiting processing&hellip;<br /><img src=\"{$vars['url']}mod/video/graphics/ajax_loader_black.gif\" /></p></div>";
	} else {
		$img = 'No Thumbnail Available';
	}
	echo "<div class='videorepo_item'>$img</div>";
	echo "<p class='videorepo_timestamp'><small><a href=\"{$vars['url']}pg/file/{$owner->username}\">{$owner->name}</a> {$friendlytime}</small></p>";
	//get the number of comments
	$numcomments = elgg_count_comments($video);
	if ($numcomments) {
		echo "<p class='videorepo_comments'><a href=\"{$video->getURL()}\">" . sprintf(elgg_echo("comments")) . " (" . $numcomments . ")</a></p>";
	}

	// extend view with favourites
	echo elgg_view('file/options', array('entity' => $video));

	echo "<div class='videorepo_controls'><p>";
	if ($video->canEdit()) {

		echo "<a href='{$vars['url']}mod/video/edit.php?video={$video_guid}'>" . elgg_echo('edit') . "</a>&nbsp;";
		echo "<div class='Delete_Button'>" . elgg_view('output/confirmlink',array(
					'href' => $vars['url'] . "action/video/delete?video=" . $video_guid,
					'text' => elgg_echo("delete"),
					'confirm' => elgg_echo("video:delete:confirm"),
					)) . "</div>";

	}
	echo "</p></div>";

	echo "</div>";
}