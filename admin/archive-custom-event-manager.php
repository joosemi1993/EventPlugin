<?php

/*
 *
 * PLANTILLA PARA MOSTRAR TODOS LOS EVENTOS
 *
 */

get_header(); ?>

<?php
    $args = [
        'post_type' 		=> 'custom-event-manager',
        'posts_per_page'	=>	-1,
        'order'				=> 'DES',
        'orderby'			=> 'date'
    ];

    $query = new WP_Query( $args );
    global $wpdb;

    ?>

    <div class="container">
        <div class="row events_row">
            <?php
            if( $query->have_posts() ) :
                while( $query->have_posts() ) : $query->the_post(); ?>
                    <?php

                    // VARIABLES GENERALES DE LOS TIPOS DE METABOXES
                    $post = get_post();
                    $post_id = $post->ID;
                    $general_data = get_post_meta($post_id, '_cem_data', TRUE);
                    $dates = get_post_meta($post_id, '_cem_dates', TRUE);
                    $discounts = get_post_meta($post_id, '_cem_discount', TRUE);
                    $subscriptions = get_post_meta($post_id, '_cem_subscribe', TRUE);

                    // VARIABLES DEL METABOX DE FECHAS
                    $a_value_ini_day = array_values($dates)[0];
                    $str_date_ini = strtotime($a_value_ini_day);
                    $event_date_ini = date("d/m/Y",$str_date_ini);

                    $event_hour_ini = array_values($dates)[1];

                    $event_date_end = array_values($dates)[2];

                    $event_hour_end = array_values($dates)[3];

                    // VARIABLES DEL METABOX DE DATOS GENERALES
                    $status = array_values($general_data)[2];

                    $price = array_values($general_data)[0];

                    $currency = array_values($general_data)[1];

                    $url = array_values($general_data)[3];

                    $address = array_values($general_data)[4];

                    // VARIABLES DEL METABOX DE DESCUENTOS
                    $discount_date_ini = array_values($discounts)[0];
                    $discount_date_end = array_values($discounts)[1];
                    $data_discount = array_values($discounts)[2];

                    $actual_date = time();
                    $end_discount_date_time = strtotime($discount_date_end);
                    $end_date_discount_format = date("d/m/Y", $end_discount_date_time);


                    // VARIABLES DEL METABOX DE SUBSCRIPCIÓN
                    $subscribe_quantity = array_values($subscriptions)[0];

                    // VARIABLES DE IMÁGENES
                    $img_aplazado = plugins_url( '/images/aplazado.png', __FILE__ );
                    $img_cancelado = plugins_url( '/images/cancelado.png', __FILE__ );
                    $img_evento = plugins_url( '/images/evento.png', __FILE__ );
                    $img_definida = get_the_post_thumbnail_url( $post );

                    // EMPEZAMOS CON LA ESTRUCTURA

                    if(isset($status) && $status != 'Finalizado') { ?>
                        <div class="col-md-4 events-col">
                            <div class="event-container">
                             <?php
                             if ( has_post_thumbnail() ) { ?>
                                <div class="event-container-sup" style="background-image: url(<?php echo $img_definida; ?>)">
                             <?php
                             } else { ?>
                                 <div class="event-container-sup" style="background-image: url(<?php echo $img_evento; ?>)">
                             <?php
                             }
                             ?>
                                    <div class="event-container-sup-velo">
                                        <div class="event_status">
                                            <?php
                                            if ($status == 'Disponible') {
                                                if( $price != '') {
                                                    if (trim($data_discount) != '') {
                                                        if($end_discount_date_time >= $actual_date) { ?>
                                                            <p class="data-discount"><?php echo $data_discount . '% hasta ' . $end_date_discount_format;?></p>
                                                            <?php
                                                        }
                                                    }

                                                    if ($currency == 'eur') { ?>
                                                        <p><?php echo $price . ' €'; ?></p>
                                                        <?php

                                                    } elseif ($currency == 'usd') { ?>
                                                        <p><?php echo $price . ' $'; ?></p>
                                                        <?php
                                                    } elseif ($currency == 'gbp') { ?>
                                                        <p><?php echo $price . ' £'; ?></p>
                                                        <?php
                                                    } elseif ($currency == 'pln') { ?>
                                                        <p><?php echo $price . ' zł'; ?></p>
                                                     <?php
                                                    }
                                                } else { ?>
                                                    <p>Gratis</p>
                                                <?php
                                                }

                                            } elseif ($status == 'Proximamente') { ?>
                                                <p><?php echo $status; ?></p>
                                            <?php
                                            } elseif ($status == 'Aplazado') { ?>
                                                <img src="<?php echo $img_aplazado; ?>" alt="" class="status_image">
                                            <?php
                                            } elseif ($status == 'Cancelado') { ?>
                                                <img src="<?php echo $img_cancelado; ?>" alt="" class="status_image">
                                            <?php
                                            }
                                            ?>
                                        </div>
                                            <div class="event-information">
                                                <ul>
                                                    <li>
                                                        <p class="event-title"> <?php echo the_title(); ?> </p>
                                                    </li>
                                                    <?php
                                                    if ($event_hour_ini == '') { ?>
                                                        <li>
                                                            <p class="event-dates"><?php echo $event_date_ini; ?> </p>
                                                        </li>
                                                    <?php
                                                    } elseif ($event_date_ini == '01/01/1970 ') { ?>
                                                        <li>
                                                            <p class="event-dates"> Próximamente </p>
                                                        </li>
                                                    <?php
                                                    } else { ?>
                                                        <li>
                                                            <p class="event-dates"> <?php echo $event_hour_ini; ?> - <?php echo $event_date_ini; ?> </p>
                                                        </li>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($address != '') { ?>
                                                        <li>
                                                            <p class="event-location"> <?php echo $address; ?> </p>
                                                        </li>
                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <div class="event-container-inf">
                                    <?php
                                    if( $url != '' ) {
                                        if ($status != 'Cancelado') { ?>
                                            <a href="<?php echo $url; ?>" target="_blank"><button class="btn btn-primary btn-information">Más información</button></a>
                                            <?php
                                        } else { ?>
                                            <a href="<?php echo the_permalink(); ?>" target="_blank"><button class="btn btn-primary btn-information">Más información</button></a>
                                            <?php
                                        }
                                    } else { ?>
                                        <a href="<?php echo the_permalink(); ?>" target="_blank"><button class="btn btn-primary btn-information">Más información</button></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                <?php
                endwhile;
            else: ?>
                <h2>No hay publicaciones</h2>
            <?php
            endif;
            ?>
        </div>
    </div>
<?php

wp_reset_postdata();

get_footer();