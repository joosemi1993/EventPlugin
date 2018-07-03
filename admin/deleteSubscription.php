<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-load.php');

global $wpdb;

$event_id = $_POST['delete_event_id'];
$user_mail = $_POST['delete_event_email'];
$user_status = $_POST['delete_event_status'];



$event_table = $wpdb->prefix . "cem_event_places";
$users_table = $wpdb->prefix . "cem_event_user";

if ($user_status == 1) {
    $event_places = $wpdb->get_var("SELECT places_num FROM $event_table WHERE event_id = '$event_id'");
    $event_places = intval($event_places + 1);
    $wpdb->update(
        $event_table,
        array('places_num' => $event_places),
        array('event_id' => $event_id),
        array('%d')
    );
}

$delete = $wpdb->get_var("DELETE FROM $users_table WHERE event_id = '$event_id' AND user_mail = '$user_mail'");

// Mostrar mensaje de borrado
header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=delete');



