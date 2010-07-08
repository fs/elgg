/**
 * This file defines functions that mimic the system_message functionality of
 * Elgg without requiring that the page be refreshed in order to process the PHP
 * functions.  They are called by the same names as their PHP counterparts, but
 * have an extra 'delay' parameter which specifies how long that specific
 * message should remain visible.
 */

var elgg = elgg || {};

/**
 * Displays system messages via javascript rather than php. 
 * This is useful in case we want to use the message system, but would rather
 * not refresh the page! Unlike its php counterpart, this function does not
 * handle message arrays.
 *
 * @param msg The message we want to display
 * @param delay The amount of time to display the message in milliseconds
 * @param type The type of message (either 'errors' or 'messages')
 * @return true
 */
elgg.system_messages = function(msg, delay, type) {
        //validate type input.  Must be 'errors' or 'messages'
        if (type != 'errors' && type != 'messages') {
			type = 'messages';
		}
		var sec_type = type == 'messages' ? 'errors' : 'messages';
        //validate delay.  Must be a positive integer. Default to 3000ms.
        delay = parseInt(delay);
        if (isNaN(delay) || delay <= 0) {
			delay = 3000;
		}
        var new_msg = $("<p/>").append(msg);
		$('#custom-messages').removeClass('hidden');
		$('#custom-messages .' + type)
                .append(new_msg)
				.show();
				
		$('#custom-messages .' + sec_type).hide();
                //.animate({opacity:'1.0'},delay)
                //.fadeOut('slow', function() {
                //        new_msg.hide();
                //});
        return new_msg;
};

/**
 * Wrapper function for system_messages. Specifies "messages" as the type of message
 * @param msg The message to display
 * @param delay The amount of time to display the message
 * @return true
 */
elgg.system_message = function(msg, delay) {
        return elgg.system_messages(msg, delay, "messages");
};

/**
 * Wrapper function for system_messages.  Specifies "errors" as the type of message
 * @param error The error message to display
 * @param delay The amount of time to display the error message
 * @return true
 */
elgg.register_error = function(error, delay) {
        return elgg.system_messages(error, delay, "errors");
};
