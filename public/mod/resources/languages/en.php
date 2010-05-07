<?php
	/**
	 * Resources English language translation
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
$english = array(
	
	// Generic Title
	'resources:title' => 'Resources',
	'item:object:resourcerequest' => 'Resource Requests',
	
	// Page titles 
	'resources:title:yourresources'	=> 'Your Resource Requests',
	'resources:title:admin'	=> 'Resource Request Admin',
	'resources:title:create' => 'Create New Request',
	'resources:title:edit' => 'Edit Request',
	
	// Menu items
	'resources:menu:yourresources' => 'Your Requests',
	'resources:menu:createresource' => 'Create Request',
	'resources:menu:admin' => 'Resources Admin',
	
	// Request types
	'resources:type:curriculum' => 'Curriculum',
	'resources:type:technology' => 'Technology',
	'resources:type:pd' => 'Professional Development',
	'resources:type:other' => 'Other',
	
	// Request status'
	'resources:status:open' => 'Opened',
	'resources:status:opened' => 'Opened',
	'resources:status:approved' => 'Approved',
	'resources:status:rejected' => 'Rejected',
	
	// Labels 
	'resources:label:types' => 'Request Type',
	'resources:label:approve' => 'Approve',
	'resources:label:reject' => 'Reject',
	'resources:label:adminusers' => 'Admin users: ',
	'resources:label:noresults' => 'No results', 
	'resources:label:commentviewlevel' => 'Comment Access',
	'resources:label:notifyuser' => 'Notify User',
	'resources:label:setstatus' => 'Change Status',
	'resources:label:commentrequired' => 'A comment is required when rejecting a request.',
	'resources:label:publiccomment' => 'Allow submitter to see this comment',
	
	// Messages
	'resources:success:create' => 'Request successfully submitted',
	'resources:success:edit' => 'Request successfully edited',
	'resources:success:delete' => 'Request successfully deleted',
	'resources:success:update' => 'Request successfully updated',
	'resources:error:titleblank' => 'Request title cannot be blank',
	'resources:error:create' => 'There was an error creating your request',
	'resources:error:edit' => 'There was an error editing the request',
	'resources:error:delete' => 'There was an error deleting the request',
	'resources:error:status' => 'There was an error updating the request\'s status',
	'resources:error:noaccess' => 'You do not have permission to view this item.',
	'resources:question:approve' => 'Approve this request?',
	'resources:question:reject' => 'Reject this request?',
	
	// River
	'resourcerequest:river:annotate' => "a comment on a request titled",
	
	// Other content
	'resources:strapline' => '%s',
	
	// Email Notifications 
	'resources:email:subject' => 'Your resource request has been updated',
	'resources:email:body' => "Your request regarding \"%s\" has been %s by %s. 

To reply or view your request, click here:

%s

You cannot reply to this email.",

);

add_translation('en',$english);

?>