/**
* Interface for all objects that need real-time feed updates.
**/


import com.elggmediaplayer.feeds.*;


interface com.elggmediaplayer.feeds.FeedListener {


	/** invoked when the feed object has updated **/
	function onFeedUpdate();


}