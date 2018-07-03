<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-load.php');
session_start();
global $wpdb;

// CAPTURAMOS LAS VARIABLES OBTENIDAS DEL FORMULARIO

$event_id = $_POST['edit_event_id'];
$user_ori_mail = $_POST['edit_original_user_email'];
$user_new_mail = $_POST['edit_new_user_email'];
$user_ori_status = $_POST['edit_original_user_status'];
$user_new_status = $_POST['edit_new_user_status'];

$event_table = $wpdb->prefix . "cem_event_places";
$users_table = $wpdb->prefix . "cem_event_user";

$change = 0;

if ($user_ori_status != $user_new_status) {
    $event_places = $wpdb->get_var("SELECT places_num FROM $event_table WHERE event_id = '$event_id'");
    if ($user_new_status == 1) {
        $event_places = intval($event_places - 1);
        $wpdb->update(
            $event_table,
            array('places_num' => $event_places),
            array('event_id' => $event_id),
            array('%d')
        );
    } else {
        $event_places = intval($event_places + 1);
        $wpdb->update(
            $event_table,
            array('places_num' => $event_places),
            array('event_id' => $event_id),
            array('%d')
        );
    }


    $wpdb->update(
        $users_table,
        array('confirm' => $user_new_status),
        array('event_id' => $event_id, 'user_mail' => $user_ori_mail),
        array('%d')
    );
    $change = 1;
}


$count_mail = $wpdb->get_var("SELECT COUNT(*) FROM $users_table WHERE event_id = '$event_id' AND user_mail = '$user_new_mail'");

if ($user_ori_mail != $user_new_mail) {
    if ($count_mail == 1) {
        $change = 2;
    } elseif ($count_mail == 0) {
        $wpdb->update(
            $users_table,
            array('user_mail' => $user_new_mail),
            array('event_id' => $event_id, 'user_mail' => $user_ori_mail),
            array('%s')
        );
        $change = 1;
    }
}


if ($change == 1) {
    header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=right');
} elseif ($change == 0) {
    header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=error');
} elseif ($change == 2) {
    header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=repeat');
}











