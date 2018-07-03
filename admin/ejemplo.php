<?php

/*
 *
 * DECLARACIÓN DE VARIABLES NECESARIAS
 *
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-load.php');
global $wpdb;

/*
 *
 * VARIABLES GENERALES OBTENIDAS DEL FORMULARIO
 *
 */
$user_name = $_POST['name'];
$user_las_name = $_POST['last_name'];
$user_email = $_POST['email'];
$my_custom_id = $_POST['pid'];
$event_places = $_POST['event_places'];
$event_title = $_POST['ev_title'];
$event_url = $_POST['ev_url'];

/*
 *
 * FUNCIÓN QUE NOS GENERA UN NÚMERO DE REFERENCIA RANDOM
 *
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


/*
 *
 * FUNCIÓN QUE COMPRUEBA SI EL NÚMERO RANDOM ESTÁ YA EN LA BASE DE DATOS
 *
 */
function existRef($ref, $post_id) {
    global $wpdb;
    $table_name_users = $wpdb->prefix . "cem_event_user";
    $my_custom_id = $post_id;
    $ref_rand = $ref;
    $count_ref_num = $wpdb->get_var("SELECT COUNT(*) FROM $table_name_users WHERE event_id = '$my_custom_id' AND event_user_ref = '$ref_rand'");
    if ($count_ref_num > 1) {
        $ref_rand = generateRandomString();
        existRef($ref_rand, $my_custom_id);
    }
    return $ref_rand;
}

/*
 *
 * CREAMOS EL EVENTO O ACTUALIZAMOS SU TABLA
 *
 */
$table_name = $wpdb->prefix . "cem_event_places";
$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE event_id = '$my_custom_id'" );
if ($count == 0) {
    $wpdb->insert(
        $table_name,
        array( 'event_id' => $my_custom_id, 'places_num' => $event_places),
        array('%d', '%s')
    );
    $count++;

} elseif ( $count > 0 ) {
    $wpdb->update(
        $table_name,
        array('places_num' => $event_places),
        array('event_id' => $my_custom_id),
        array('%d')
    );
}


/*
 *
 * INSERTAMOS, SI NO EXISTE YA, EL USUARIO EN EL EVENTO CORRESPONDIENTE.
 *
 */


//$event_places_bbdd = $wpdb->get_var("SELECT places_num FROM $table_name WHERE event_id = '$my_custom_id'");
$table_name_users = $wpdb->prefix . "cem_event_user";
$ref_rand = existRef(generateRandomString(), $my_custom_id);
$count_users = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name_users WHERE event_id = '$my_custom_id' AND user_mail = '$user_email'" );
// Si ese usuario (email) no está en ese evento lo metemos y modificamos la variable de plazas en la tabla de eventos
if ($count_users == 1) {
    header('Location: ' . $event_url . '?confirm=exists');
    exit();
}

$wpdb->insert(
    $table_name_users,
    array('event_id' => $my_custom_id, 'user_mail' => $user_email, 'event_user_ref' => $ref_rand),
    array('%d', '%s', '%s')
);


/*
 *
 * OBTENEMOS EL ID DEL USUARIO SUSCRITO
 *
 */

$user_id = $wpdb->get_var("SELECT id FROM $table_name_users WHERE event_id = '$my_custom_id' AND user_mail = '$user_email'");

/*
 *
 * CREAMOS EL EMAIL QUE CONTENDRÁ EL LINK DE CONFIRMACIÓN
 *
 */

$dir = plugins_url( '/ejemplo.php', __FILE__ );
$to = $user_email;
$subject = $event_title . ' Solicitud de inscripción';
$headers = "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";
$headers .= "From: WrocLoveLive" . "\r\n";
$message = '
<p>Has solicitado suscribirte al evento: '. $event_title . '</p>
<p>Actualmente quedan plazas disponibles por lo que le rogamos que haga click en el siguiente enlace para confirmar su asistencia:</p>
<a href="' . $event_url . '?confirm=true&ref=' . $user_id . '">' . $event_url . '?confirm=true&ref=' . $user_id . '</a>
<p>Esto es un mensaje automático, por favor no conteste</p>
';
$mail_confirm = mail($to, $subject, $message, $headers);
if ($mail_confirm == true) {
    header('Location: ' . $event_url . '?confirm=pend&ref=' . $my_custom_id);
} else {
    mail($to, $subject, $message, $headers);
}























