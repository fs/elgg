<?php
	/**
	 * Externallinks metatags
	 * 
	 * @package ExternalLinks
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
?>
<script type="text/javascript">

	String.prototype.startsWith = function(str){
	    return (this.indexOf(str) === 0);
	}
	
	// Trim HTTP or HTTPS from a url string
	function trimProtocol(str) {
		if (str.startsWith("http://"))
			return str.substr(7);
		else if (str.startsWith("https://"))
			return str.substr(8);
		else 
			return str;
	}

	$(document).ready(function() {	
		$("a").click(
			function () {
				var url = trimProtocol("<?php global $CONFIG; echo $CONFIG->wwwroot; ?>");
				var href = trimProtocol($(this).attr('href'));
			
				if (href && $(this).attr('href').startsWith("http")) {					
					if (!href.startsWith(url)) {
						window.open($(this).attr('href'));
						return false;
					} 
				}
			}
		);
	});
</script>