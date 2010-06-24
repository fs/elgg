
	<div id="elgg_horizontal_tabbed_nav">
		<center>
		<ul>
		<?php 
			$due = get_input('due', 'past');
						
			if (!in_array($due, array('past', 'nextweek', 'future'))) {
				$due = 'past';
			}
						
			echo "<li class='" . ($due == "past" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "?due=past", 
													'text' => elgg_echo("todo:label:pastdue"), 
													'class' => 'todo')) . 
				 "</li>"; 
				
			echo "<li class='" . ($due == "nextweek" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "?due=nextweek", 
													'text' => elgg_echo("todo:label:nextweek"), 
													'class' => 'todo')) . 
				 "</li>";
				
			echo "<li class='" . ($due == "future" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "?due=future", 
													'text' => elgg_echo("todo:label:future"), 
													'class' => 'todo')) . 
				 "</li>";
		?>
		</ul>
		</center>
	</div>
