<div class="contentWrapper">
	<div id="elgg_horizontal_tabbed_nav">
		<center>
		<ul>
		<?php 
			echo "<li class='" . ((get_input('status') == null) ? 'selected ' : '') . " edt_tab_nav'>" . elgg_view('output/url', array('href' => $vars['url'] . "pg/resources/admin", 'text' => elgg_echo("All"), 'class' => 'resources')) . "</li>"; 
			foreach (get_resource_status_array() as $key => $type) {
				// Duplicate hidden for now
				if ($type == "duplicate")
					continue;
				echo "<li class='" . ((get_input('status') == $key) ? 'selected ' : '') . "edt_tab_nav'>" . elgg_view('output/url', array('href' => $vars['url'] . "mod/resources/pages/admin.php?status=" . $key , 'text' => elgg_echo("resources:status:" . strtolower($type)), 'class' => 'resources')) ."</li>"; 
			}
		?>
		</ul>
		</center>
	</div>
</div>