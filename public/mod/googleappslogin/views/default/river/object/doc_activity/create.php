<?php
	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);
        $user = $_SESSION['user'];


        if ($object->shared_acces) {
            $can_view=unserialize($object->show_only_for);            
            if (!in_array( $user->email, $can_view)) { echo "You do not have permission to view this item. "; return ;}
        }


	$time = !empty($object) ? strtotime($object->updated) : 0;
	$date = $time ? date('d M Y', $time) : '';
	
	$string = !empty($object->text) ? preg_replace("/\<div([^>]+)\>(.*?)\<\/div\>/", "$2", $object->text) : $object->text;
	
?>

<?php echo $string; ?>
