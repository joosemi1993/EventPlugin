<?php
/*
 *
 * PLANTILLA PARA MOSTRAR CADA EVENTO
 *
 */


get_header();



// VARIABLES GENERALES

$post_id = get_the_ID();
$general_data = get_post_meta($post_id, '_cem_data', TRUE);
$dates = get_post_meta($post_id, '_cem_dates', TRUE);
$discounts = get_post_meta($post_id, '_cem_discount', TRUE);
$subscriptions = get_post_meta($post_id, '_cem_subscribe', TRUE);


$cem_aux = plugins_url( '/ejemplo.php', __FILE__ );

global $wpdb;


// VARIABLES DE FECHA
    // VARIABLES SOBRE DIA DE INICIO Y FIN
    $a_value_ini_day = array_values($dates)[0];
    $str_date_ini = strtotime($a_value_ini_day);
    $event_date_ini = date("d/m/Y",$str_date_ini);

    $a_value_end_day = array_values($dates)[2];
    $str_date_end = strtotime($a_value_end_day);
    $event_date_end = date("d/m/Y",$str_date_end);

    // VARIABLES SOBRE LA HORA DE INICIO Y FIN
    $event_hour_ini = array_values($dates)[1];
    $event_hour_end = array_values($dates)[3];

// VARIABLES GENERALES
    $price = array_values($general_data)[0];
    $currency = array_values($general_data)[1];
    $status = array_values($general_data)[2];
    $url = array_values($general_data)[3];
    $address = array_values($general_data)[4];
    $event_latitude = array_values($general_data)[5];
    $event_longitude = array_values($general_data)[6];

// VARIABLES DEL METABOX DE DESCUENTOS
    $discount_date_ini = array_values($discounts)[0];
    $discount_date_end = array_values($discounts)[1];
    $data_discount = array_values($discounts)[2];

    $actual_date = time();
    $end_discount_date_time = strtotime($discount_date_end);
    $end_date_discount_format = date("d/m/Y", $end_discount_date_time);

// VARIABLES DEL METABOX DE SUBSCRIPCIÓN
    $subscribe_quantity = array_values($subscriptions)[0];


// VARIABLES SOBRE IMÁGENES DE LOS EVENTOS
    $img_aplazado = plugins_url( '/images/aplazado.png', __FILE__ );
    $img_cancelado = plugins_url( '/images/cancelado.png', __FILE__ );
    $img_definida = get_the_post_thumbnail_url();
// FIN DECLARACION DE VARIABLES

while ( have_posts() ) : the_post(); ?>
    <?php


    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="single-event-title">
                    <h1><?php the_title(); ?></h1>
                </div>
            </div>
        </div>
        <?php if ($status == 'Aplazado') { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="single-status-img">
                    <img src="<?php echo $img_aplazado; ?>" alt="">
                </div>
            </div>
        </div>
        <?php
        } elseif ($status == 'Cancelado') { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="single-status-img">
                    <img src="<?php echo $img_cancelado; ?>" alt="">
                </div>
            </div>
        </div>
        <?php
        }
        /* ESTRUCTURA GENERAL */

        if (has_post_thumbnail()) { ?>
            <?php if ($event_latitude == '' || $event_longitude == '') {?>
                <div class="row single-img-map-row">
                    <div class="col-md-4">
                        <div class="event-back-img" class="single-event-img-bck"
                             style="background-image: url(<?php echo $img_definida; ?>)"></div>
                    </div>
                    <div class="col-md-8">
                        <div class="row no-map-row">
                            <ul>
                                <li class="single-general-list single-state-event">
                                    <?php
                                    if ($status == 'Cancelado') { ?>
                                        <span class="single-information-title">Estado:</span> <span
                                                class="single-event-status-cancelled"><?php echo ' ' . $status; ?> </span>
                                        <?php
                                    } elseif ($status == 'Aplazado') { ?>
                                        <span class="single-information-title">Estado:</span> <span
                                                class="single-event-status-aplazado"><?php echo ' ' . $status; ?> </span>
                                        <?php
                                    } elseif ($status == 'Disponible') { ?>
                                        <span class="single-information-title">Estado:</span> <span
                                                class="single-event-status-disponible"><?php echo ' ' . $status; ?> </span>
                                        <?php
                                    } else { ?>
                                        <span class="single-information-title">Estado:</span> <?php echo ' ' . $status;
                                    }
                                    ?>
                                </li>
                                <li class="single-general-list single-places-event">
                                    <?php
                                    if($status=='Disponible') {
                                        if ($price == '' || $price == '0') {
                                            $count_places_table = $wpdb->prefix . "cem_event_places";
                                            $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                                            if ($count_places > 0) {
                                                $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                                                ?>
                                                <span class="single-information-title">Plazas:</span>  <?php echo ' ' . $event_places; ?>
                                                <?php
                                            } elseif ($count_places == 0) {
                                                ?>
                                                <span class="single-information-title">Plazas:</span> No existen plazas disponibles
                                                <?php
                                            }
                                            ?>

                                        <?php
                                        }
                                    }
                                    ?>
                                </li>
                                <li class="single-general-list single-date-event">
                                    <?php
                                    if ($event_date_ini == '01/01/1970') { ?>
                                        <span class="single-information-title">Fecha:</span>
                                        <?php
                                    } elseif ($event_date_end == '01/01/1970') { ?>
                                        <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini; ?>
                                        <?php
                                    } else { ?>
                                        <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini . ' - ' . $event_date_end; ?>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <li class="single-general-list single-time-event">
                                    <?php
                                    if ($event_hour_ini == '') { ?>
                                        <span class="single-information-title">Horario:</span>
                                        <?php
                                    } elseif ($event_hour_end == '') { ?>
                                        <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini; ?>
                                        <?php
                                    } elseif ($event_hour_end != '') { ?>
                                        <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini . ' - ' . $event_hour_end;
                                    }
                                    ?>
                                </li>
                                <li class="single-general-list single-price-event">
                                    <?php
                                    if ($price != '') {
                                        if ($currency == 'eur') { ?>
                                            <?php if(trim($data_discount) != '') {
                                                if($end_discount_date_time >= $actual_date) { ?>
                                                    <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '€</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                                } else { ?>
                                                    <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                                }
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                            }
                                        } elseif ($currency == 'usd') { ?>
                                            <?php if(trim($data_discount) != '') {
                                                if($end_discount_date_time >= $actual_date) { ?>
                                                    <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '$</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                                } else { ?>
                                                    <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                                }
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                            }
                                        } elseif ($currency == 'gbp') { ?>
                                            <?php if(trim($data_discount) != '') {
                                                if($end_discount_date_time >= $actual_date) { ?>
                                                    <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '£</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                                } else { ?>
                                                    <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                                }
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                            }
                                        } elseif ($currency == 'pln') { ?>
                                            <?php if(trim($data_discount) != '') {
                                                if($end_discount_date_time >= $actual_date) { ?>
                                                    <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . 'zł</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                                } else { ?>
                                                    <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . 'zł';
                                                }
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . 'zł';
                                            }
                                        }
                                    } else { ?>
                                        <span class="single-information-title">Precio:</span> Gratis
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                                if ($url != '') { ?>
                                    <li class="single-general-list single-url-event">
                                        <span class="single-information-title">URL:</span> <a
                                                href="<?php echo $url; ?>"><?php echo the_title(); ?></a>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li class="single-general-list single-address-event">
                                    <span class="single-information-title">Dirección:</span> <?php echo ' ' . $address; ?>
                                </li>
                            </ul>
                        </div>
                        <div class="row no-map-row">
                            <?php
                            if (!empty(get_the_content())) { ?>
                                <div class="single-event-content">
                                    <h4>Información</h4>
                                    <p><?php the_content(); ?></p>
                                </div>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($status == "Disponible") {
                    if ($price == '') {
                        if($subscribe_quantity != '') {?>
                            <div class="subscribe-button">
                                <?php
                                $count_places_table = $wpdb->prefix . "cem_event_places";
                                $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                                if ($count_places > 0) {
                                    $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                                } else {
                                    $event_places = $subscribe_quantity;
                                }
                                $event_places = intval($event_places);
                                if ($event_places > 0) {
                                    ?>
                                    <a href="#" data-popup-open="popup-1" class="btn btn-primary btn-information" id="popup-1">Suscribirme al Evento</a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="#" class="btn btn-primary btn-information btn-information-no-places" id="popup-2">Suscribirme al Evento</a>
                                    <?php
                                }
                                ?>
                                <div class="popup" data-popup="popup-1">
                                    <div class="popup-inner">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Suscribirme...</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" name="formPrueba" action="<?php echo $cem_aux ?>" >
                                                    <input type="hidden" name="pid" value="<?php echo get_the_ID(); ?>">
                                                    <input type="hidden" name="ev_title" value="<?php echo the_title(); ?>">
                                                    <input type="hidden" name="ev_url" value="<?php echo the_permalink(); ?>">

                                                    <?php
                                                    $table_event = $wpdb->prefix . "cem_event_places";
                                                    $event_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_event WHERE event_id = '$post_id'");
                                                    if ($event_count == 0) {
                                                        ?>
                                                        <input type="hidden" name="event_places" value="<?php echo $subscribe_quantity; ?>">
                                                        <?php
                                                    } elseif ($event_count > 0) {
                                                        $event_places_bbdd = $wpdb->get_var("SELECT places_num FROM $table_event WHERE event_id = '$post_id'");
                                                        ?>
                                                        <input type="hidden" name="event_places" value="<?php echo $event_places_bbdd; ?>">
                                                        <?php
                                                    }

                                                    ?>

                                                    <div class="form-row">
                                                        <label for="validationDefault01">Nombre:</label>
                                                        <input type="text" class="form-control" id="validationDefault01" placeholder="Nombre" name="name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault02">Apellidos:</label>
                                                        <input type="text" class="form-control" id="validationDefault02" placeholder="Apellidos" name="last_name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault03">Email:</label>
                                                        <input type="text" class="form-control" id="validationDefault03" placeholder="Email" name="email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="invalidCheck2" required>
                                                            <label for="invalidCheck2" class="form-check-label">Acepto los términos y condiciones</label>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary" type="submit" name="submit">Enviar</button>
                                                </form>
                                            </div>
                                        </div>
                                        <a class="popup-close" data-popup-close="popup-1" href="#">x</a>
                                    </div>
                                </div>
                            </div>

                            <?php

                            $confirm = $_GET['confirm'];
                            $id_ref = $_GET['ref'];
                            $table_name_users = $wpdb->prefix . "cem_event_user";
                            $table_name_events = $wpdb->prefix . "cem_event_places";
                            $event_id = get_the_ID();
                            $event_url = get_the_permalink();
                            $event_title = get_the_title();

                            if($confirm == 'true') {
                                $confirm_var = $wpdb->get_var("SELECT confirm FROM $table_name_users WHERE id = '$id_ref'");
                                if ($confirm_var != 1) {
                                    $user_mail = $wpdb->get_var("SELECT user_mail FROM $table_name_users WHERE id = '$id_ref'");
                                    $wpdb->update(
                                        $table_name_users,
                                        array('confirm' => 1),
                                        array('id' => $id_ref),
                                        array('%d')
                                    );
                                    $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                    $places_quantity = intval($event_places_bbdd - 1);
                                    $wpdb->update(
                                        $table_name_events,
                                        array('places_num' => $places_quantity),
                                        array('event_id' => $event_id),
                                        array('%d')
                                    );

                                    $num_ref = $wpdb->get_var("SELECT event_user_ref FROM $table_name_users WHERE id = '$id_ref'");


                                    // Creamos el código QR
                                    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qr'.DIRECTORY_SEPARATOR;
                                    $PNG_WEB_DIR = 'qr/';
                                    include "phpqrcode/qrlib.php";
                                    if (!file_exists($PNG_TEMP_DIR))
                                        mkdir($PNG_TEMP_DIR);
                                    $filename = $PNG_TEMP_DIR.'qr_' . $post_id . '_' . $num_ref . '.png';
                                    $qr_content = $event_url . '?qr=' . $num_ref;

                                    QRcode::png($qr_content, $filename, QR_ECLEVEL_L, 5);


                                    $imageData = base64_encode(file_get_contents($filename));
                                    $src = 'data: '.mime_content_type($filename).';base64,'.$imageData;



                                    $subject = $event_title . ' Confirmación de inscripción';
                                    $headers = "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
                                    $headers .= "From: WrocLoveLive" . "\r\n";
                                    $message = '<html><head></head><body>
                                    <p>Enhorabuena ! Te has inscrito en el evento: ' . $event_title . ' el cual será el día ' . $event_date_ini . '</p>
                                    <p>Tu número de referencia es: ' . $num_ref . '</p>
                                    <p>Si deseas ver la información del evento, por favor, visita el siguiente enlace: </p>
                                    <a href="' . $event_url . '">' . $event_url . '</a>
                                    <p>Para acceder al evento se requerirá mostrar el siguiente código QR:</p>
                                    <img src="' . $src . '" alt="" />
                                    <p>Por el contrario, si deseas cancelar tu suscripción al evento, haz click en el siguiente enlace:</p>
                                    <a href="' . $event_url . '?confirm=del&ref=' . $user_id . '&num=' . $num_ref . '">' . $event_url . '?confirm=del&ref=' . $id_ref . '&num=' . $num_ref . '</a>
                                    <p>Un cordial saludo</p>
                                    <p>WrocLoveLive</p>
                                    <p>Esto es un mensaje automático, por favor no conteste</p></body></html>
                                    ';
                                    $mail_confirm = mail($user_mail, $subject, $message, $headers);
                                    if ($mail_confirm == true) {
                                        ?>
                                        <div class="message-pers"">
                                        <p>Enhorabuena !! Te has suscrito correctamente en el evento</p>
                                        </div>
                                        <?php
                                    }
                                }
                            } elseif ($confirm == 'del') {
                                $user_ref = $_GET['num'];
                                $user = $wpdb->get_var("DELETE FROM $table_name_users WHERE event_user_ref = '$user_ref' AND event_id = '$event_id'");
                                $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                $places_quantity = intval($places_quantity + 1);
                                $wpdb->update(
                                    $table_name_events,
                                    array('places_num' => $places_quantity),
                                    array('event_id' => $event_id),
                                    array('%d')
                                );
                                ?>
                                <div class="message-pers">
                                    <p>Su petición de desinscribirse se ha efectuado con éxito</p>
                                </div>
                                <?php

                            } elseif ($confirm == 'pend') {
                                ?>
                                <div class="message-pers">
                                    <p>Hemos enviado a su correo un email de confirmación. Por favor, revise su bandeja de entrada</p>
                                </div>
                                <?php
                            } elseif ($confirm == 'exists') {
                                ?>
                                <div class="message-pers message-pers-error">
                                    <p>El email que intentas introducir ya se encuentra registrado, por favor compuebe su bandeja de entrada</p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                        }
                    }
                }
            } else { ?>
                <div class="row single-img-map-row">
                    <div class="col-md-4">
                        <div class="event-back-img" class="single-event-img-bck"
                             style="background-image: url(<?php echo $img_definida; ?>)"></div>
                    </div>
                    <div class="col-md-8">
                        <div class="event_map">
                            <div id="mapita" class="map"></div>
                        </div>
                    </div>
                </div>
                <div class="row single-information-event">
                    <div class="col-md-4">
                        <ul>
                            <li class="single-general-list single-state-event">
                                <?php
                                if ($status == 'Cancelado') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-cancelled"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } elseif ($status == 'Aplazado') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-aplazado"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } elseif ($status == 'Disponible') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-disponible"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } else { ?>
                                    <span class="single-information-title">Estado:</span> <?php echo ' ' . $status;
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-date-event">
                                <?php
                                if ($event_date_ini == '01/01/1970') { ?>
                                    <span class="single-information-title">Fecha:</span>
                                    <?php
                                } elseif ($event_date_end == '01/01/1970') { ?>
                                    <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini; ?>
                                    <?php
                                } else { ?>
                                    <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini . ' - ' . $event_date_end; ?>
                                    <?php
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-places-event">
                                <?php
                                if($status=='Disponible') {
                                    if ($price == '' || $price == '0') {
                                        $count_places_table = $wpdb->prefix . "cem_event_places";
                                        $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                                        if ($count_places > 0) {
                                            $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                                            ?>
                                            <span class="single-information-title">Plazas:</span>  <?php echo ' ' . $event_places; ?>
                                            <?php
                                        } elseif ($count_places == 0) {
                                            ?>
                                            <span class="single-information-title">Plazas:</span> No existen plazas disponibles
                                            <?php
                                        }
                                        ?>

                                        <?php
                                    }
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-time-event">
                                <?php
                                if ($event_hour_ini == '') { ?>
                                    <span class="single-information-title">Horario:</span>
                                    <?php
                                } elseif ($event_hour_end == '') { ?>
                                    <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini; ?>
                                    <?php
                                } elseif ($event_hour_end != '') { ?>
                                    <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini . ' - ' . $event_hour_end;
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-price-event">
                                <?php
                                if ($price != '') {
                                    if ($currency == 'eur') { ?>
                                        <?php if (trim($data_discount) != '') {
                                            if ($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '€</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                        }
                                    } elseif ($currency == 'usd') { ?>
                                        <?php if (trim($data_discount) != '') {
                                            if ($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '$</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                        }
                                    } elseif ($currency == 'gbp') { ?>
                                        <?php if (trim($data_discount) != '') {
                                            if ($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '£</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                        }
                                    } elseif ($currency == 'pln') { ?>
                                        <?php if (trim($data_discount) != '') {
                                            if ($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . 'zł</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . 'zł';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . 'zł';
                                        }
                                    }
                                } else { ?>
                                    <span class="single-information-title">Precio:</span> Gratis
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                            if ($url != '') { ?>
                                <li class="single-general-list single-url-event">
                                    <span class="single-information-title">URL:</span> <a
                                            href="<?php echo $url; ?>"><?php echo the_title(); ?></a>
                                </li>
                                <?php
                            }
                            ?>
                            <li class="single-general-list single-address-event">
                                <span class="single-information-title">Dirección:</span> <?php echo ' ' . $address; ?>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <?php
                        if (!empty(get_the_content())) { ?>
                            <div class="single-event-content">
                                <h4>Información</h4>
                                <p><?php the_content(); ?></p>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <?php
                if ($status == "Disponible") {
                    if ($price == '') {
                        if ($subscribe_quantity != '') { ?>
                            <div class="subscribe-button">
                            <?php
                            $count_places_table = $wpdb->prefix . "cem_event_places";
                            $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                            if ($count_places > 0) {
                                $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                            } else {
                                $event_places = $subscribe_quantity;
                            }
                            $event_places = intval($event_places);
                            if ($event_places > 0) {
                                ?>
                                <a href="#" data-popup-open="popup-1" class="btn btn-primary btn-information" id="popup-1">Suscribirme al Evento</a>
                                <?php
                            } else {
                                ?>
                                <a href="#" class="btn btn-primary btn-information btn-information-no-places" id="popup-2">Suscribirme al Evento</a>
                                <?php
                            }
                            ?>
                                <div class="popup" data-popup="popup-1">
                                    <div class="popup-inner">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Suscribirme...</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" name="formPrueba" action="<?php echo $cem_aux ?>">
                                                    <input type="hidden" name="pid" value="<?php echo get_the_ID(); ?>">
                                                    <input type="hidden" name="ev_title"
                                                           value="<?php echo the_title(); ?>">
                                                    <input type="hidden" name="ev_url"
                                                           value="<?php echo the_permalink(); ?>">

                                                    <?php
                                                    $table_event = $wpdb->prefix . "cem_event_places";
                                                    $event_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_event WHERE event_id = '$post_id'");
                                                    if ($event_count == 0) {
                                                        ?>
                                                        <input type="hidden" name="event_places"
                                                               value="<?php echo $subscribe_quantity; ?>">
                                                        <?php
                                                    } elseif ($event_count > 0) {
                                                        $event_places_bbdd = $wpdb->get_var("SELECT places_num FROM $table_event WHERE event_id = '$post_id'");
                                                        ?>
                                                        <input type="hidden" name="event_places"
                                                               value="<?php echo $event_places_bbdd; ?>">
                                                        <?php
                                                    }

                                                    ?>

                                                    <div class="form-row">
                                                        <label for="validationDefault01">Nombre:</label>
                                                        <input type="text" class="form-control" id="validationDefault01"
                                                               placeholder="Nombre" name="name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault02">Apellidos:</label>
                                                        <input type="text" class="form-control" id="validationDefault02"
                                                               placeholder="Apellidos" name="last_name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault03">Email:</label>
                                                        <input type="text" class="form-control" id="validationDefault03"
                                                               placeholder="Email" name="email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                   id="invalidCheck2" required>
                                                            <label for="invalidCheck2" class="form-check-label">Acepto
                                                                los términos y condiciones</label>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary" type="submit" name="submit">Enviar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <a class="popup-close" data-popup-close="popup-1" href="#">x</a>
                                    </div>
                                </div>
                            </div>

                            <?php

                            $confirm = $_GET['confirm'];
                            $id_ref = $_GET['ref'];
                            $table_name_users = $wpdb->prefix . "cem_event_user";
                            $table_name_events = $wpdb->prefix . "cem_event_places";
                            $event_id = get_the_ID();
                            $event_url = get_the_permalink();
                            $event_title = get_the_title();

                            if ($confirm == 'true') {
                                $confirm_var = $wpdb->get_var("SELECT confirm FROM $table_name_users WHERE id = '$id_ref'");
                                if ($confirm_var != 1) {
                                    $user_mail = $wpdb->get_var("SELECT user_mail FROM $table_name_users WHERE id = '$id_ref'");
                                    $wpdb->update(
                                        $table_name_users,
                                        array('confirm' => 1),
                                        array('id' => $id_ref),
                                        array('%d')
                                    );
                                    $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                    $places_quantity = intval($event_places_bbdd - 1);
                                    $wpdb->update(
                                        $table_name_events,
                                        array('places_num' => $places_quantity),
                                        array('event_id' => $event_id),
                                        array('%d')
                                    );

                                    $num_ref = $wpdb->get_var("SELECT event_user_ref FROM $table_name_users WHERE id = '$id_ref'");


                                    // Creamos el código QR
                                    $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'qr' . DIRECTORY_SEPARATOR;
                                    $PNG_WEB_DIR = 'qr/';
                                    include "phpqrcode/qrlib.php";
                                    if (!file_exists($PNG_TEMP_DIR))
                                        mkdir($PNG_TEMP_DIR);
                                    $filename = $PNG_TEMP_DIR . 'qr_' . $post_id . '_' . $num_ref . '.png';
                                    $qr_content = $event_url . '?qr=' . $num_ref;

                                    QRcode::png($qr_content, $filename, QR_ECLEVEL_L, 5);


                                    $imageData = base64_encode(file_get_contents($filename));
                                    $src = 'data: ' . mime_content_type($filename) . ';base64,' . $imageData;


                                    $subject = $event_title . ' Confirmación de inscripción';
                                    $headers = "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
                                    $headers .= "From: WrocLoveLive" . "\r\n";
                                    $message = '<html><head></head><body>
                                                <p>Enhorabuena ! Te has inscrito en el evento: ' . $event_title . ' el cual será el día ' . $event_date_ini . '</p>
                                                <p>Tu número de referencia es: ' . $num_ref . '</p>
                                                <p>Si deseas ver la información del evento, por favor, visita el siguiente enlace: </p>
                                                <a href="' . $event_url . '">' . $event_url . '</a>
                                                <p>Para acceder al evento se requerirá mostrar el siguiente código QR:</p>
                                                <img src="' . $src . '" alt="" />
                                                <p>Por el contrario, si deseas cancelar tu suscripción al evento, haz click en el siguiente enlace:</p>
                                                <a href="' . $event_url . '?confirm=del&ref=' . $user_id . '&num=' . $num_ref . '">' . $event_url . '?confirm=del&ref=' . $id_ref . '&num=' . $num_ref . '</a>
                                                <p>Un cordial saludo</p>
                                                <p>WrocLoveLive</p>
                                                <p>Esto es un mensaje automático, por favor no conteste</p></body></html>
                                                ';
                                    $mail_confirm = mail($user_mail, $subject, $message, $headers);
                                    if ($mail_confirm == true) {
                                        ?>
                                        <div class="message-pers"">
                                        <p>Enhorabuena !! Te has suscrito correctamente en el evento</p>
                                        </div>
                                        <?php
                                    }
                                }


                            } elseif ($confirm == 'del') {
                                $user_ref = $_GET['num'];
                                $user = $wpdb->get_var("DELETE FROM $table_name_users WHERE event_user_ref = '$user_ref' AND event_id = '$event_id'");
                                $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                $places_quantity = intval($places_quantity + 1);
                                $wpdb->update(
                                    $table_name_events,
                                    array('places_num' => $places_quantity),
                                    array('event_id' => $event_id),
                                    array('%d')
                                );
                                ?>
                                <div class="message-pers">
                                    <p>Su petición de desinscribirse se ha efectuado con éxito</p>
                                </div>
                                <?php

                            } elseif ($confirm == 'pend') {
                                ?>
                                <div class="message-pers">
                                    <p>Hemos enviado a su correo un email de confirmación. Por favor, revise su bandeja
                                        de entrada</p>
                                </div>
                                <?php
                            } elseif ($confirm == 'exists') {
                                ?>
                                <div class="message-pers message-pers-error">
                                    <p>El email que intentas introducir ya se encuentra registrado, por favor compuebe su bandeja de entrada</p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                        }
                    }
                }
            }
        } else { ?>
            <?php if ($event_latitude == '' || $event_longitude == '') { ?>
                <div class="row single-information-event no-map-information">
                    <div class="col-md-4">
                        <ul>
                            <li class="single-general-list single-state-event">
                                <?php
                                if ($status == 'Cancelado') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-cancelled"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } elseif ($status == 'Aplazado') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-aplazado"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } elseif ($status == 'Disponible') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-disponible"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } else { ?>
                                    <span class="single-information-title">Estado:</span> <?php echo ' ' . $status;
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-places-event">
                                <?php
                                if($status=='Disponible') {
                                    if ($price == '' || $price == '0') {
                                        $count_places_table = $wpdb->prefix . "cem_event_places";
                                        $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                                        if ($count_places > 0) {
                                            $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                                            ?>
                                            <span class="single-information-title">Plazas:</span>  <?php echo ' ' . $event_places; ?>
                                            <?php
                                        } elseif ($count_places == 0) {
                                            ?>
                                            <span class="single-information-title">Plazas:</span> No existen plazas disponibles
                                            <?php
                                        }
                                        ?>

                                        <?php
                                    }
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-date-event">
                                <?php
                                if ($event_date_ini == '01/01/1970') { ?>
                                    <span class="single-information-title">Fecha:</span>
                                    <?php
                                } elseif ($event_date_end == '01/01/1970') { ?>
                                    <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini; ?>
                                    <?php
                                } else { ?>
                                    <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini . ' - ' . $event_date_end; ?>
                                    <?php
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-time-event">
                                <?php
                                if ($event_hour_ini == '') { ?>
                                    <span class="single-information-title">Horario:</span>
                                    <?php
                                } elseif ($event_hour_end == '') { ?>
                                    <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini; ?>
                                    <?php
                                } elseif ($event_hour_end != '') { ?>
                                    <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini . ' - ' . $event_hour_end;
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-price-event">
                                <?php
                                if ($price != '') {
                                    if ($currency == 'eur') { ?>
                                        <?php if(trim($data_discount) != '') {
                                                if($end_discount_date_time >= $actual_date) { ?>
                                                    <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '€</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                                } else { ?>
                                                    <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                                }
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                            }

                                    } elseif ($currency == 'usd') { ?>
                                        <?php if(trim($data_discount) != '') {
                                            if($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '$</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                        }
                                    } elseif ($currency == 'gbp') { ?>
                                        <?php if(trim($data_discount) != '') {
                                            if($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . '£</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                        }
                                    } elseif ($currency == 'pln') { ?>
                                        <?php if (trim($data_discount) != '') {
                                            if ($end_discount_date_time >= $actual_date) { ?>
                                                <span class="single-information-title discount-price-data">Precio:</span> <?php echo ' <span class="price-discount">' . $price . 'zł</span> - <span class="discout-value">' . $data_discount . '% hasta ' . $end_date_discount_format . '</span>';
                                            } else { ?>
                                                <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . 'zł';
                                            }
                                        } else { ?>
                                            <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . 'zł';
                                        }
                                    }
                                } else { ?>
                                    <span class="single-information-title">Precio:</span> Gratis
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                            if ($url != '') { ?>
                                <li class="single-general-list single-url-event">
                                    <span class="single-information-title">URL:</span> <a
                                            href="<?php echo $url; ?>"><?php echo the_title(); ?></a>
                                </li>
                                <?php
                            }
                            ?>
                            <li class="single-general-list single-address-event">
                                <span class="single-information-title">Dirección:</span> <?php echo ' ' . $address; ?>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <?php
                        if (!empty(get_the_content())) { ?>
                            <div class="single-event-content">
                                <h4>Información</h4>
                                <p><?php the_content(); ?></p>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <?php
                if ($status == "Disponible") {
                    if ($price == '') {
                        if($subscribe_quantity != '') {?>
                            <div class="subscribe-button">
                            <?php
                            $count_places_table = $wpdb->prefix . "cem_event_places";
                            $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                            if ($count_places > 0) {
                                $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                            } else {
                                $event_places = $subscribe_quantity;
                            }
                            $event_places = intval($event_places);
                            if ($event_places > 0) {
                                ?>
                                <a href="#" data-popup-open="popup-1" class="btn btn-primary btn-information" id="popup-1">Suscribirme al Evento</a>
                                <?php
                            } else {
                                ?>
                                <a href="#" class="btn btn-primary btn-information btn-information-no-places" id="popup-2">Suscribirme al Evento</a>
                                <?php
                            }
                            ?>
                                <div class="popup" data-popup="popup-1">
                                    <div class="popup-inner">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Suscribirme...</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" name="formPrueba" action="<?php echo $cem_aux ?>" >
                                                    <input type="hidden" name="pid" value="<?php echo get_the_ID(); ?>">
                                                    <input type="hidden" name="ev_title" value="<?php echo the_title(); ?>">
                                                    <input type="hidden" name="ev_url" value="<?php echo the_permalink(); ?>">

                                                    <?php
                                                    $table_event = $wpdb->prefix . "cem_event_places";
                                                    $event_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_event WHERE event_id = '$post_id'");
                                                    if ($event_count == 0) {
                                                        ?>
                                                        <input type="hidden" name="event_places" value="<?php echo $subscribe_quantity; ?>">
                                                        <?php
                                                    } elseif ($event_count > 0) {
                                                        $event_places_bbdd = $wpdb->get_var("SELECT places_num FROM $table_event WHERE event_id = '$post_id'");
                                                        ?>
                                                        <input type="hidden" name="event_places" value="<?php echo $event_places_bbdd; ?>">
                                                        <?php
                                                    }

                                                    ?>

                                                    <div class="form-row">
                                                        <label for="validationDefault01">Nombre:</label>
                                                        <input type="text" class="form-control" id="validationDefault01" placeholder="Nombre" name="name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault02">Apellidos:</label>
                                                        <input type="text" class="form-control" id="validationDefault02" placeholder="Apellidos" name="last_name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault03">Email:</label>
                                                        <input type="text" class="form-control" id="validationDefault03" placeholder="Email" name="email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="invalidCheck2" required>
                                                            <label for="invalidCheck2" class="form-check-label">Acepto los términos y condiciones</label>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary" type="submit" name="submit">Enviar</button>
                                                </form>
                                            </div>
                                        </div>
                                        <a class="popup-close" data-popup-close="popup-1" href="#">x</a>
                                    </div>
                                </div>
                            </div>

                            <?php

                            $confirm = $_GET['confirm'];
                            $id_ref = $_GET['ref'];
                            $table_name_users = $wpdb->prefix . "cem_event_user";
                            $table_name_events = $wpdb->prefix . "cem_event_places";
                            $event_id = get_the_ID();
                            $event_url = get_the_permalink();
                            $event_title = get_the_title();

                            if($confirm == 'true') {
                                $confirm_var = $wpdb->get_var("SELECT confirm FROM $table_name_users WHERE id = '$id_ref'");
                                if ($confirm_var != 1) {
                                    $user_mail = $wpdb->get_var("SELECT user_mail FROM $table_name_users WHERE id = '$id_ref'");
                                    $wpdb->update(
                                        $table_name_users,
                                        array('confirm' => 1),
                                        array('id' => $id_ref),
                                        array('%d')
                                    );
                                    $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                    $places_quantity = intval($event_places_bbdd - 1);
                                    $wpdb->update(
                                        $table_name_events,
                                        array('places_num' => $places_quantity),
                                        array('event_id' => $event_id),
                                        array('%d')
                                    );

                                    $num_ref = $wpdb->get_var("SELECT event_user_ref FROM $table_name_users WHERE id = '$id_ref'");


                                    // Creamos el código QR
                                    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qr'.DIRECTORY_SEPARATOR;
                                    $PNG_WEB_DIR = 'qr/';
                                    include "phpqrcode/qrlib.php";
                                    if (!file_exists($PNG_TEMP_DIR))
                                        mkdir($PNG_TEMP_DIR);
                                    $filename = $PNG_TEMP_DIR.'qr_' . $event_id . '_' . $num_ref . '.png';
                                    $qr_content = $event_url . '?qr=' . $num_ref;

                                    QRcode::png($qr_content, $filename, QR_ECLEVEL_L, 5);


                                    $imageData = base64_encode(file_get_contents($filename));
                                    $src = 'data: '.mime_content_type($filename).';base64,'.$imageData;



                                    $subject = $event_title . ' Confirmación de inscripción';
                                    $headers = "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
                                    $headers .= "From: WrocLoveLive" . "\r\n";
                                    $message = '<html><head></head><body>
                                    <p>Enhorabuena ! Te has inscrito en el evento: ' . $event_title . ' el cual será el día ' . $event_date_ini . '</p>
                                    <p>Tu número de referencia es: ' . $num_ref . '</p>
                                    <p>Si deseas ver la información del evento, por favor, visita el siguiente enlace: </p>
                                    <a href="' . $event_url . '">' . $event_url . '</a>
                                    <p>Para acceder al evento se requerirá mostrar el siguiente código QR:</p>
                                    <img src="' . $src . '" alt="" />
                                    <p>Por el contrario, si deseas cancelar tu suscripción al evento, haz click en el siguiente enlace:</p>
                                    <a href="' . $event_url . '?confirm=del&ref=' . $user_id . '&num=' . $num_ref . '">' . $event_url . '?confirm=del&ref=' . $id_ref . '&num=' . $num_ref . '</a>
                                    <p>Un cordial saludo</p>
                                    <p>WrocLoveLive</p>
                                    <p>Esto es un mensaje automático, por favor no conteste</p></body></html>
                                    ';
                                    $mail_confirm = mail($user_mail, $subject, $message, $headers);
                                    if ($mail_confirm == true) {
                                        ?>
                                        <div class="message-pers"">
                                        <p>Enhorabuena !! Te has suscrito correctamente en el evento</p>
                                        </div>
                                        <?php
                                    }
                                }





                            } elseif ($confirm == 'del') {
                                $user_ref = $_GET['num'];
                                $user = $wpdb->get_var("DELETE FROM $table_name_users WHERE event_user_ref = '$user_ref' AND event_id = '$event_id'");
                                $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                $places_quantity = intval($places_quantity + 1);
                                $wpdb->update(
                                    $table_name_events,
                                    array('places_num' => $places_quantity),
                                    array('event_id' => $event_id),
                                    array('%d')
                                );
                                ?>
                                <div class="message-pers">
                                    <p>Su petición de desinscribirse se ha efectuado con éxito</p>
                                </div>
                                <?php

                            } elseif ($confirm == 'pend') {
                                ?>
                                <div class="message-pers">
                                    <p>Hemos enviado a su correo un email de confirmación. Por favor, revise su bandeja de entrada</p>
                                </div>
                                <?php
                            } elseif ($confirm == 'exists') {
                                ?>
                                <div class="message-pers message-pers-error">
                                    <p>El email que intentas introducir ya se encuentra registrado, por favor compuebe su bandeja de entrada</p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                        }
                    }
                }
            } else { ?>
                <div class="row single-img-map-row">
                    <div class="col-md-12">
                        <div class="event_map">
                            <div id="mapita" class="map"></div>
                        </div>
                    </div>
                </div>
                <div class="row single-information-event no-map-information">
                    <div class="col-md-4">
                        <ul>
                            <li class="single-general-list single-state-event">
                                <?php
                                if ($status == 'Cancelado') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-cancelled"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } elseif ($status == 'Aplazado') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-aplazado"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } elseif ($status == 'Disponible') { ?>
                                    <span class="single-information-title">Estado:</span> <span
                                            class="single-event-status-disponible"><?php echo ' ' . $status; ?> </span>
                                    <?php
                                } else { ?>
                                    <span class="single-information-title">Estado:</span> <?php echo ' ' . $status;
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-places-event">
                                <?php
                                if($status=='Disponible') {
                                    if ($price == '' || $price == '0') {
                                        $count_places_table = $wpdb->prefix . "cem_event_places";
                                        $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                                        if ($count_places > 0) {
                                            $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                                            ?>
                                            <span class="single-information-title">Plazas:</span>  <?php echo ' ' . $event_places; ?>
                                            <?php
                                        } elseif ($count_places == 0) {
                                            ?>
                                            <span class="single-information-title">Plazas:</span> No existen plazas disponibles
                                            <?php
                                        }
                                        ?>

                                        <?php
                                    }
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-date-event">
                                <?php
                                if ($event_date_ini == '01/01/1970') { ?>
                                    <span class="single-information-title">Fecha:</span>
                                    <?php
                                } elseif ($event_date_end == '01/01/1970') { ?>
                                    <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini; ?>
                                    <?php
                                } else { ?>
                                    <span class="single-information-title">Fecha:</span>  <?php echo ' ' . $event_date_ini . ' - ' . $event_date_end; ?>
                                    <?php
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-time-event">
                                <?php
                                if ($event_hour_ini == '') { ?>
                                    <span class="single-information-title">Horario:</span>
                                    <?php
                                } elseif ($event_hour_end == '') { ?>
                                    <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini; ?>
                                    <?php
                                } elseif ($event_hour_end != '') { ?>
                                    <span class="single-information-title">Horario:</span> <?php echo ' ' . $event_hour_ini . ' - ' . $event_hour_end;
                                }
                                ?>
                            </li>
                            <li class="single-general-list single-price-event">
                                <?php
                                if ($price != '') {
                                    if ($currency == 'eur') { ?>
                                        <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '€';
                                    } elseif ($currency == 'usd') { ?>
                                        <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '$';
                                    } elseif ($currency == 'gbp') { ?>
                                        <span class="single-information-title">Precio:</span> <?php echo ' ' . $price . '£';
                                    }
                                } else { ?>
                                    <span class="single-information-title">Precio:</span> Gratis
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                            if ($url != '') { ?>
                                <li class="single-general-list single-url-event">
                                    <span class="single-information-title">URL:</span> <a
                                            href="<?php echo $url; ?>"><?php echo the_title(); ?></a>
                                </li>
                                <?php
                            }
                            ?>
                            <li class="single-general-list single-address-event">
                                <span class="single-information-title">Dirección:</span> <?php echo ' ' . $address; ?>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <?php
                        if (!empty(get_the_content())) { ?>
                            <div class="single-event-content">
                                <h4>Información</h4>
                                <p><?php the_content(); ?></p>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <?php
                if ($status == "Disponible") {
                    if ($price == '') {
                        if($subscribe_quantity != '') {?>
                            <div class="subscribe-button">
                            <?php
                            $count_places_table = $wpdb->prefix . "cem_event_places";
                            $count_places = $wpdb -> get_var("SELECT COUNT(*) FROM $count_places_table WHERE event_id = '$post_id'");
                            if ($count_places > 0) {
                                $event_places = $wpdb->get_var("SELECT places_num FROM $count_places_table WHERE event_id = '$post_id'");
                            } else {
                                $event_places = $subscribe_quantity;
                            }
                            $event_places = intval($event_places);
                            if ($event_places > 0) {
                                ?>
                                <a href="#" data-popup-open="popup-1" class="btn btn-primary btn-information" id="popup-1">Suscribirme al Evento</a>
                                <?php
                            } else {
                                ?>
                                <a href="#" class="btn btn-primary btn-information btn-information-no-places" id="popup-2">Suscribirme al Evento</a>
                                <?php
                            }
                            ?>
                                <div class="popup" data-popup="popup-1">
                                    <div class="popup-inner">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Suscribirme...</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" name="formPrueba" action="<?php echo $cem_aux ?>" >
                                                    <input type="hidden" name="pid" value="<?php echo get_the_ID(); ?>">
                                                    <input type="hidden" name="ev_title" value="<?php echo the_title(); ?>">
                                                    <input type="hidden" name="ev_url" value="<?php echo the_permalink(); ?>">

                                                    <?php
                                                    $table_event = $wpdb->prefix . "cem_event_places";
                                                    $event_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_event WHERE event_id = '$post_id'");
                                                    if ($event_count == 0) {
                                                        ?>
                                                        <input type="hidden" name="event_places" value="<?php echo $subscribe_quantity; ?>">
                                                        <?php
                                                    } elseif ($event_count > 0) {
                                                        $event_places_bbdd = $wpdb->get_var("SELECT places_num FROM $table_event WHERE event_id = '$post_id'");
                                                        ?>
                                                        <input type="hidden" name="event_places" value="<?php echo $event_places_bbdd; ?>">
                                                        <?php
                                                    }

                                                    ?>

                                                    <div class="form-row">
                                                        <label for="validationDefault01">Nombre:</label>
                                                        <input type="text" class="form-control" id="validationDefault01" placeholder="Nombre" name="name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault02">Apellidos:</label>
                                                        <input type="text" class="form-control" id="validationDefault02" placeholder="Apellidos" name="last_name" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <label for="validationDefault03">Email:</label>
                                                        <input type="text" class="form-control" id="validationDefault03" placeholder="Email" name="email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="invalidCheck2" required>
                                                            <label for="invalidCheck2" class="form-check-label">Acepto los términos y condiciones</label>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary" type="submit" name="submit">Enviar</button>
                                                </form>
                                            </div>
                                        </div>
                                        <a class="popup-close" data-popup-close="popup-1" href="#">x</a>
                                    </div>
                                </div>
                            </div>

                            <?php

                            $confirm = $_GET['confirm'];
                            $id_ref = $_GET['ref'];
                            $table_name_users = $wpdb->prefix . "cem_event_user";
                            $table_name_events = $wpdb->prefix . "cem_event_places";
                            $event_id = get_the_ID();
                            $event_url = get_the_permalink();
                            $event_title = get_the_title();

                            if($confirm == 'true') {
                                $confirm_var = $wpdb->get_var("SELECT confirm FROM $table_name_users WHERE id = '$id_ref'");
                                if ($confirm_var != 1) {
                                    $user_mail = $wpdb->get_var("SELECT user_mail FROM $table_name_users WHERE id = '$id_ref'");
                                    $wpdb->update(
                                        $table_name_users,
                                        array('confirm' => 1),
                                        array('id' => $id_ref),
                                        array('%d')
                                    );
                                    $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                    $places_quantity = intval($event_places_bbdd - 1);
                                    $wpdb->update(
                                        $table_name_events,
                                        array('places_num' => $places_quantity),
                                        array('event_id' => $event_id),
                                        array('%d')
                                    );

                                    $num_ref = $wpdb->get_var("SELECT event_user_ref FROM $table_name_users WHERE id = '$id_ref'");


                                    // Creamos el código QR
                                    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qr'.DIRECTORY_SEPARATOR;
                                    $PNG_WEB_DIR = 'qr/';
                                    include "phpqrcode/qrlib.php";
                                    if (!file_exists($PNG_TEMP_DIR))
                                        mkdir($PNG_TEMP_DIR);
                                    $filename = $PNG_TEMP_DIR.'qr_' . $post_id . '_' . $num_ref . '.png';
                                    $qr_content = $event_url . '?qr=' . $num_ref;

                                    QRcode::png($qr_content, $filename, QR_ECLEVEL_L, 5);


                                    $imageData = base64_encode(file_get_contents($filename));
                                    $src = 'data: '.mime_content_type($filename).';base64,'.$imageData;



                                    $subject = $event_title . ' Confirmación de inscripción';
                                    $headers = "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
                                    $headers .= "From: WrocLoveLive" . "\r\n";
                                    $message = '<html><head></head><body>
                                    <p>Enhorabuena ! Te has inscrito en el evento: ' . $event_title . ' el cual será el día ' . $event_date_ini . '</p>
                                    <p>Tu número de referencia es: ' . $num_ref . '</p>
                                    <p>Si deseas ver la información del evento, por favor, visita el siguiente enlace: </p>
                                    <a href="' . $event_url . '">' . $event_url . '</a>
                                    <p>Para acceder al evento se requerirá mostrar el siguiente código QR:</p>
                                    <img src="' . $src . '" alt="" />
                                    <p>Por el contrario, si deseas cancelar tu suscripción al evento, haz click en el siguiente enlace:</p>
                                    <a href="' . $event_url . '?confirm=del&ref=' . $user_id . '&num=' . $num_ref . '">' . $event_url . '?confirm=del&ref=' . $id_ref . '&num=' . $num_ref . '</a>
                                    <p>Un cordial saludo</p>
                                    <p>WrocLoveLive</p>
                                    <p>Esto es un mensaje automático, por favor no conteste</p></body></html>
                                    ';
                                    $mail_confirm = mail($user_mail, $subject, $message, $headers);
                                    if ($mail_confirm == true) {
                                        ?>
                                        <div class="message-pers"">
                                        <p>Enhorabuena !! Te has suscrito correctamente en el evento</p>
                                        </div>
                                        <?php
                                    }
                                }





                            } elseif ($confirm == 'del') {
                                $user_ref = $_GET['num'];
                                $user = $wpdb->get_var("DELETE FROM $table_name_users WHERE event_user_ref = '$user_ref' AND event_id = '$event_id'");
                                $places_quantity = $wpdb->get_var("SELECT places_num FROM $table_name_events WHERE event_id = '$event_id'");
                                $places_quantity = intval($places_quantity + 1);
                                $wpdb->update(
                                    $table_name_events,
                                    array('places_num' => $places_quantity),
                                    array('event_id' => $event_id),
                                    array('%d')
                                );
                                ?>
                                <div class="message-pers">
                                    <p>Su petición de desinscribirse se ha efectuado con éxito</p>
                                </div>
                                <?php

                            } elseif ($confirm == 'pend') {
                                ?>
                                <div class="message-pers">
                                    <p>Hemos enviado a su correo un email de confirmación. Por favor, revise su bandeja de entrada</p>
                                </div>
                                <?php
                            } elseif ($confirm == 'exists') {
                                ?>
                                <div class="message-pers message-pers-error">
                                    <p>El email que intentas introducir ya se encuentra registrado, por favor compuebe su bandeja de entrada</p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                        }
                    }
                }
            }
            ?>
        <?php
        }?>
    </div>
<?php
endwhile;
?>

<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('mapita'), {
            zoom: 13,
            center: {lat: parseFloat('<?php echo $event_latitude;?>'), lng: parseFloat('<?php echo $event_longitude;?>')}
        });

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng( -34.397,150.644),
            map: map,
            title: 'Hello World!'
        });

        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            position: {lat: parseFloat('<?php echo $event_latitude;?>'), lng: parseFloat('<?php echo $event_longitude;?>')}
        });
        marker.addListener('click', toggleBounce);
    }

    jQuery(function() {
//----- OPEN
        jQuery('[data-popup-open]').on('click', function(e)  {
            var targeted_popup_class = jQuery(this).attr('data-popup-open');
            jQuery('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
            e.preventDefault();
        });
//----- CLOSE
        jQuery('[data-popup-close]').on('click', function(e)  {
            var targeted_popup_class = jQuery(this).attr('data-popup-close');
            jQuery('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
            e.preventDefault();
        });
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAypdpHW1-ENvAZRjteinZINafSBpAYxDE&callback=initMap" async defer></script>
<?php
get_footer();















































