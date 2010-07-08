<?php
gatekeeper();

global $SESSION;
$user = $_SESSION['user'];

$sync_settings = get_input('sync_settings');
$user_sync_settings = unserialize($user->sync_settings);

foreach($sync_settings as $setting) {
    $user_sync_settings[$setting] = 1;
}

foreach($user_sync_settings as $user_setting => $v) {
    if (!in_array($user_setting, $sync_settings)) {
        $user_sync_settings[$user_setting] = 0;
    }
}



$user->sync_settings = serialize($user_sync_settings);
$user->save();
system_message(elgg_echo('admin:configuration:success'));
forward($_SERVER['HTTP_REFERER']);