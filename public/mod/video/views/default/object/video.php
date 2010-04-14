<?php
/**
 * Elgg video browser.
 */

global $CONFIG;

$video = $vars['entity'];

//get required info
$video_guid = $video->getGUID();
$tags = $video->tags;
$title = $video->title;
$desc = $video->description;
$owner = $vars['entity']->getOwnerEntity();
$friendlytime = friendly_time($vars['entity']->time_created);

//for search results, get the correct layout
if (get_context() == "search") {
	echo "<div class='search_gallery_item'>";
	echo elgg_view("video/list_view", array("entity" => $video));
	echo "</div>";
}else{
	//set the title
	if($video->title)
		$video_title .= elgg_view_title($video->title);
	else
		$video_title .= elgg_view_title(elgg_echo('video:untitled'));
?>
<div id="Page_Header">
	<div class="Page_Header_Title">
		<div id="content_area_user_title"><h2><?php echo $owner->name; ?>'s Videos</h2></div>
	</div>
	<div class="Page_Header_Options">
	<?php
		if ($vars['entity']->canEdit()) {
	?>
		<a class="Action_Button" href="<?php echo $vars['url']; ?>mod/video/edit.php?video=<?php echo $video_guid; ?>"><?php echo elgg_echo('video:edit'); ?></a>
	<?php
		echo elgg_view('output/confirmlink',array(
				'href' => $vars['url'] . "action/video/delete?video=" . $vars['entity']->getGUID(),
				'text' => elgg_echo("delete"),
				'is_action' => true,
				'confirm' => elgg_echo("file:delete:confirm"),
				'class' => "Action_Button Disabled",));
		}
	?>
	</div>
	<div class='clearfloat'></div>
</div>
<div class="ContentWrapper">
	<?php
		// Allow plugins to extend
		echo elgg_view("file/options",array('entity' => $video));
		echo $video_title;
	?>
	<div class="videorepo_title_owner_wrapper">
		<div class="videorepo_owner">
			<?php
				echo elgg_view("profile/icon",array('entity' => $owner, 'size' => 'tiny'));
			?>
			<p class="videorepo_owner_details"><a href="<?php echo $vars['url']; ?>pg/video/<?php echo $owner->username; ?>"><?php echo $owner->name; ?></a> <?php echo $friendlytime; ?>
			<!-- display the comments link -->
			<?php
				if($comments_on && $vars['entity'] instanceof ElggObject){
				//get the number of comments
					$num_comments = elgg_count_comments($vars['entity']);
				?>
				<a href="<?php echo $url; ?>"><?php echo sprintf(elgg_echo("comments")) . " (" . $num_comments . ")"; ?></a>
			<?php
				}
				//sort out the access level for display
				$object_acl = get_readable_access_level($vars['entity']->access_id);
				//files with these access level don't need an icon
				$general_access = array('Public', 'Logged in users', 'Friends');
				//set the right class for access level display - need it to set on groups and shared access only
				$is_group = get_entity($vars['entity']->container_guid);
				if($is_group instanceof ElggGroup){
					//get the membership type open/closed
					$membership = $is_group->membership;
						if($membership == 2)
								$access_level = "class='group_open'";
							else
								$access_level = "class='group_closed'";
						}elseif($object_acl == 'Private'){
							$access_level = "class='private'";
						}else{
							if(!in_array($object_acl, $general_access))
								$access_level = "class='shared_collection'";
							else
								$access_level = "class='generic_access'";
						}

						echo "<br /><span {$access_level}>" . $object_acl . "</span>";

					?>
					</p>
		</div><div class="clearfloat"></div>
	</div>
	<div class="videorepo_maincontent">
		<?php
			//display description
			if (!empty($desc)) {
				echo "<div class=\"videorepo_description\">";
				echo autop($desc);
				echo "</div>";
			}
		?>
	</div>
	<div class='videorepo_specialcontent'>
	<?php
		if ($video->transcoded_std_available != TRUE) {
			echo "<a href=\"{$video->getURL()}\"><img src=\"{$vars['url']}mod/video/graphics/pleasewait.gif\" /></a>";
		} else {
			// grab the std transcoded ElggVideoFile
			$e_tmp = elgg_get_entities_from_relationship(array(
				'relationship' => 'transcoded_std',
				'relationship_guid' => $video->getGUID(),
				'inverse_relationship' => true)
			);
			$std_tx = $e_tmp[0];

			$swf_file = "{$vars['url']}mod/video/player/elggMediaPlayer.swf";
			$js_file = "{$vars['url']}mod/video/player/swfobject.js";

			// must end in video.flv and image.jpg
			$flv_file = "{$vars['url']}pg/video_file/{$video->getGUID()}/flv/std/video.flv";
			$ss = "{$vars['url']}pg/video_file/{$video->getGUID()}/thumbnail/full/image.jpg";
			// @todo pull this out into a view
			echo <<<___END
<script src="$js_file" type="text/javascript"></script>
<p id="elggMediaPlayer3"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this movie.</p>

<script type="text/javascript">
	var ecmp = new SWFObject("$swf_file","ecmp","502","336","9");
	/* function(swf, id, w, h, ver, bgColor, [quality, xiRedirectUrl, redirectUrl, detectKey]) */
	/* http://blog.deconcept.com/swfobject/#howitworks */
	ecmp.addParam("allowfullscreen","true");
	ecmp.addVariable("file","$flv_file"); /* url of flv */
	ecmp.addVariable("image","$ss");
	ecmp.write("elggMediaPlayer3");
</script>
___END;
		}
		//display any tags for the video
		if (!empty($tags)) {
			echo "<div class='videorepo_tags'><p class='tags'>";
			echo elgg_view('output/tags',array('value' => $tags));
			echo "</p></div>";
		}
	?>
	</div>
<?php

	// original video download link
	$input = elgg_view('input/hidden', array('internalname' => 'video', 'value' => $video_guid));
	$submit = elgg_view('input/submit', array('value' => elgg_echo('video:download')));
	$form = "<div class=\"video_download\">$submit$input</div>";
	echo elgg_view('input/form', array('body' => $form, 'action' => "{$vars['url']}action/video/download"));

?>
</div>
<?php
	echo elgg_view_comments($video);
}//end of search check
?>
