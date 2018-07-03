<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-load.php');

global $wpdb;

$user_name = $_POST['subs_name'];
$user_lastname = $_POST['subs_last_name'];
$user_email = $_POST['subs_email'];
$event_id = $_POST['subs_event'];

$event_table = $wpdb->prefix . "cem_event_places";
$users_table = $wpdb->prefix . "cem_event_user";


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
    $ref_rand = $ref;
    $count_ref_num = $wpdb->get_var("SELECT COUNT(*) FROM $table_name_users WHERE event_id = '$post_id' AND event_user_ref = '$ref_rand'");
    if ($count_ref_num > 1) {
        $ref_rand = generateRandomString();
        existRef($ref_rand, $post_id);
    }
    return $ref_rand;
}

// COMPROBAMOS SI EL EVENTO YA ESTÁ METIDO EN LA BASE DE DATOS
$exist_event = $wpdb->get_var("SELECT COUNT(*) FROM $event_table WHERE event_id = '$event_id'");

// SI NO ESTÁ METIDO LO INSERTAMOS Y OBTENEMOS EL VALOR DEL METABOX DE PLAZAS DEL EVENTO
if ($exist_event == 0) {
    $event_places_metabox = get_post_meta($event_id, '_cem_subscribe', TRUE);
    $event_places = array_values($event_places_metabox)[0];
    $wpdb->insert(
        $event_table,
        array( 'event_id' => $event_id, 'places_num' => $event_places),
        array('%d', '%s')
    );
}

// Una vez insertamos el evento, procedemos a inscribir al usuario
// Obtenemos de la base de datos el nº de plazas disponibles
$event_places = $wpdb->get_var("SELECT places_num FROM $event_table WHERE event_id = '$event_id'");

// Si existen plazas, avanzamos
if ($event_places > 0) {
    $exist_user = $wpdb->get_var("SELECT COUNT(*) FROM $users_table WHERE event_id = '$event_id' AND user_mail = '$user_email'");
    // Si el usuario no está inscrito en el evento
    if ($exist_user == 0) {
        $user_ref_num = existRef(generateRandomString(), $event_id);
        $wpdb->insert(
            $users_table,
            array('event_id' => $event_id, 'user_mail' => $user_email, 'event_user_ref' => $user_ref_num, 'confirm' => 1),
            array('%d', '%s', '%s', '%d')
        );
        $event_places = intval($event_places - 1);
        $wpdb->update(
          $event_table,
          array('places_num' => $event_places),
          array('event_id' => $event_id),
          array('%d')
        );



        $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qr'.DIRECTORY_SEPARATOR;
        $PNG_WEB_DIR = 'qr/';
        include "phpqrcode/qrlib.php";
        if (!file_exists($PNG_TEMP_DIR))
            mkdir($PNG_TEMP_DIR);
        $filename = $PNG_TEMP_DIR.'qr_' . $event_id . '_' . $user_ref_num . '.png';
        $event_url = get_the_permalink($event_id);
        $qr_content = $event_url . '?qr=' . $num_ref;
        QRcode::png($qr_content, $filename, QR_ECLEVEL_L, 5);
        $imageData = base64_encode(file_get_contents($filename));
        $src = 'data: '.mime_content_type($filename).';base64,'.$imageData;


        $event_title = get_the_title($event_id);
        $admin_email = get_option( 'admin_email' );

        $subject = $event_title . ' Confirmación de inscripción';
        $headers = "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
        $headers .= "From: WrocLoveLive" . "\r\n";
        $message = '<html><head></head><body>
                    <p>Enhorabuena ! Has sido inscrito por el administrador en el evento: ' . $event_title . '</p>
                    <p>Tu número de referencia es: ' . $user_ref_num . '</p>
                    <p>Si deseas ver la información del evento, por favor, visita el siguiente enlace: </p>
                    <a href="' . $event_url . '">' . $event_url . '</a>
                    <p>Para acceder al evento se requerirá mostrar el siguiente código QR:</p>
                    <img src="' . $src . '" alt="" />
                    <p>Por el contrario, si deseas cancelar tu suscripción al evento, contacta con el administrador: '. $admin_email . '</p>
                    <p>Un cordial saludo</p>
                    <p>WrocLoveLive</p>
                    <p>Esto es un mensaje automático, por favor no conteste</p></body></html>
                ';
        mail($user_email, $subject, $message, $headers);
        // Mostrar mensaje de exito
        header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=subscorrect');
    } else {
        // Mostrar mensaje de error
        header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=existuser');
    }
} else {
    header('Location: http://localhost:8888/wroclovelive/wp-admin/admin.php?page=ceventmanager%2Fadmin%2FadminView.php&change=noplaces');
}



