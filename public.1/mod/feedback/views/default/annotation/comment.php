<?php
/**
 * Feedback Comments, 
 * Slighlty modified version of the elgg generic_comment view
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 *
 */

$owner = get_user($vars['annotation']->owner_guid);

?>
<div class="generic_comment" <?php if (get_input("full") != 1) echo " style='margin: 5px; background: #efefef;' "; ?>><!-- start of generic_comment div -->

	<div class="generic_comment_icon">
		<?php
			echo elgg_view("profile/icon",
				array(
					'entity' => $owner,
					'size' => 'small'
				)
			);
		?>
	</div>
	<div class="generic_comment_details">

		<!-- output the actual comment -->
		<?php 
			
			if (is_array($comment = unserialize($vars['annotation']->value))) {
				
				$feedbackvote = '';
				if ($comment['feedbackvote'] == 1) {
					$feedbackvote = "<div id='feedbackvote'><img src='" . $vars['url'] . "mod/feedback/images/like.png' /> <span>" . elgg_echo("feedback:update:like") . "</span></div>";
				} else if ($comment['feedbackvote'] == 0) {
					$feedbackvote = "<div id='feedbackvote'><img src='" . $vars['url'] . "mod/feedback/images/dislike.png' /> <span>" . elgg_echo("feedback:update:dislike") . "</span></div>";
				}
				
				
				echo $feedbackvote;
				echo elgg_view("output/longtext",array("value" => $comment['comment']));

			} else {
				echo elgg_view("output/longtext",array("value" => $vars['annotation']->value)); 
			}
		?>

		<p class="generic_comment_owner">
			<a href="<?php echo $owner->getURL(); ?>"><?php echo $owner->name; ?></a> <?php echo friendly_time($vars['annotation']->time_created); ?>
		</p>

		<?php

			// if the user looking at the comment can edit, show the delete link
			if ($vars['annotation']->canEdit()) {

			?>
		<p>
			<?php

				echo elgg_view("output/confirmlink",array(
					'href' => $vars['url'] . "action/comments/delete?annotation_id=" . $vars['annotation']->id,
					'text' => elgg_echo('delete'),
					'confirm' => elgg_echo('deleteconfirm'),
				));

			?>
		</p>

			<?php
			} //end of can edit if statement
		?>
	</div><!-- end of generic_comment_details -->
</div><!-- end of generic_comment div -->
