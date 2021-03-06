<?php

	/**
	 * Elgg groups plugin display topic posts
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 */

	// set up breadcrumbs
	$group_guid = get_input('group_guid');
	$group = get_entity($group_guid);
	elgg_push_breadcrumb(elgg_echo('groups'), $CONFIG->wwwroot."pg/groups/world/");
	elgg_push_breadcrumb($group->name, $group->getURL());
	elgg_push_breadcrumb(elgg_echo('item:object:groupforumtopic'), $CONFIG->wwwroot."pg/groups/forum/{$vars['entity']->container_guid}");
	elgg_push_breadcrumb($vars['entity']->title);

	echo elgg_view('navigation/breadcrumbs');

	//display follow up comments
	$count = $vars['entity']->countAnnotations('group_topic_post');
	$offset = (int) get_input('offset',0);

	$baseurl = $vars['url'] . "mod/groups/topicposts.php?topic={$vars['entity']->guid}&group_guid={$vars['entity']->container_guid}";
	echo elgg_view('navigation/pagination',array(
													'limit' => 50,
													'offset' => $offset,
													'baseurl' => $baseurl,
													'count' => $count,
												));

?>
<!-- grab the topic title -->
<h2><?php echo $vars['entity']->title; ?></h2>
<?php

	foreach($vars['entity']->getAnnotations('group_topic_post', 50, $offset, "asc") as $post) {

		echo elgg_view("forum/topicposts",array('entity' => $post));

	}

	// check to find out the status of the topic and act
	if($vars['entity']->status != "closed" && page_owner_entity()->isMember($vars['user'])){

		//display the add comment form, this will appear after all the existing comments
		echo elgg_view("forms/forums/addpost", array('entity' => $vars['entity']));

	} elseif($vars['entity']->status == "closed") {

		//this topic has been closed by the owner
		echo "<h2>" . elgg_echo("groups:topicisclosed") . "</h2>";
		echo "<p>" . elgg_echo("groups:topiccloseddesc") . "</p>";

	} else {
	}

?>
