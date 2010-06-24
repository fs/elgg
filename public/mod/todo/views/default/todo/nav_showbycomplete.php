<div class="contentWrapper">
	<div id="elgg_horizontal_tabbed_nav">
		<center>
		<ul>
		<?php 
			$status = get_input('status', 'incomplete');
			
			if (!in_array($status, array('complete', 'incomplete'))) {
				$status = 'incomplete';
			}
			
			echo "<li class='" . ($status == "incomplete" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "?status=incomplete", 
													'text' => elgg_echo("todo:label:incomplete"), 
													'class' => 'todo')) . 
				 "</li>"; 
				
			echo "<li class='" . ($status == "complete" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "?status=complete", 
													'text' => elgg_echo("todo:label:complete"), 
													'class' => 'todo')) . 
				 "</li>";
		?>
		</ul>
		</center>
	</div>
</div>
