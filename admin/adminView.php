<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/WrocLoveLive' . '/wp-load.php');
session_start();

if (is_admin()) {
    global $wpdb;
    // CREAMOS LA TABLA QUE MUESTRA TODOS LOS EVENTOS
    ?>
    <div class="admin-event-section">
        <div class="admin-events-title">
            <h1>Eventos</h1>
        </div>

        <div class="admin-events-content">
            <div class="container">
                <div class="row">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">Nº</th>
                            <th scope="col">Nombre del Evento</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Plazas Disponibles</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        // OBTENEMOS TODOS LOS POST DE TIPO CEM-EVENT-MANAGER
                        $args = [
                            'post_type' 		=> 'custom-event-manager',
                            'order'				=> 'DES',
                            'orderby'			=> 'date'
                        ];

                        $query = new WP_Query( $args );
                        $event_number = 1;
                        if( $query->have_posts() ) :
                            while( $query->have_posts() ) : $query->the_post(); ?>
                                <?php
                                // VARIABLES A USAR
                                    // Id del evento
                                    $post_id = get_the_ID();
                                    // Variables de fechas
                                        $dates = get_post_meta($post_id, '_cem_dates', TRUE);
                                        // Día de comienzo
                                            $a_value_ini_day = array_values($dates)[0];
                                            $str_date_ini = strtotime($a_value_ini_day);
                                            $event_date_ini = date("d/m/Y",$str_date_ini);
                                        // Día de fin
                                            $a_value_end_day = array_values($dates)[2];
                                            $str_date_end = strtotime($a_value_end_day);
                                            $event_date_end = date("d/m/Y",$str_date_end);


                                    // Nº de plazas
                                    $subscriptions = get_post_meta($post_id, '_cem_subscribe', TRUE);
                                    $subscribe_quantity = array_values($subscriptions)[0];

                                // Tabla a usar de la base de datos
                                $event_table = $wpdb->prefix . "cem_event_places";

                                // Comprobamos si el evento existe en la base de datos
                                $event_count = $wpdb->get_var("SELECT COUNT(*) FROM $event_table WHERE event_id = '$post_id'");
                                if ($event_count > 0) {
                                    // Si existe, cogemos el nº de plazas que ahi nos refleja
                                    $subscribe_quantity = $wpdb->get_var("SELECT places_num FROM $event_table WHERE event_id = '$post_id'");
                                } else {
                                    // Si no, cogemos el valor del metabox de ese evento
                                    $subscriptions = get_post_meta($post_id, '_cem_subscribe', TRUE);
                                    $subscribe_quantity = array_values($subscriptions)[0];
                                }
                                ?>
                                <tr>
                                    <td><?php echo $event_number; ?></td>
                                    <td><a href="<?php the_permalink() ?>" class="event_title"><?php the_title() ?></a></td>
                                    <?php
                                    if ($event_date_end == '' || $event_date_end == '01/01/1970') {
                                        ?>
                                        <td><?php echo $event_date_ini; ?></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td><?php echo $event_date_ini; ?> - <?php echo $event_date_end; ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td><?php echo $subscribe_quantity; ?></td>
                                </tr>
                                <?php
                                $event_number++;
                            endwhile;
                        endif;
                        wp_reset_query();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- TABLA QUE MUESTRA TODOS LOS USUARIOS QUE HAY INSCRITOS EN CADA EVENTO -->
    <div class="admin-users-section">
        <div class="admin-users-title">
            <h1>Usuarios de cada evento inscritos o pendientes de inscribirse</h1>
        </div>

        <?php
        // DEPENDIENDO DEL VALOR DE LA VARIABLE DEL ENLACE MOSTRAREMOS UN MENSAJE U OTRO
        $function_result = $_GET['change'];
        if ($function_result == 'error') {
            ?>
            <p class="return-message">No se ha editado ningún campo</p>
        <?php
        } elseif ($function_result == 'right') {
            ?>
            <p class="return-message">El cambio se ha efectuado con éxito</p>
            <?php
        } elseif ($function_result == 'repeat') {
            ?>
            <p class="return-message">El email que intentas cambiar ya se encuentra guardado</p>
            <?php
        } elseif ($function_result == 'delete') {
            ?>
            <p class="return-message return-delete">El usuario ha sido borrado correctamente</p>
            <?php
        } elseif ($function_result == 'noplaces') {
            ?>
            <p class="return-message return-delete">No existen plazas disponibles para este evento</p>
            <?php
        } elseif ($function_result == 'existuser') {
            ?>
            <p class="return-message return-delete">El usuario que intenta inscribir ya se encuentra</p>
            <?php
        } elseif ($function_result == 'subscorrect') {
            ?>
            <p class="return-message return-subscribe">El usuario se ha inscrito correctamente</p>
            <?php
        }
        ?>

        <!-- BOTÓN DE FILTRADO -->
        <div class="custom-search">
            <form action="" method="post">
                <select name="filter_event_name" id="">
                    <option value="select_event">Filtrar por evento</option>
                    <?php
                    $args_search = [
                        'post_type' 		=> 'custom-event-manager',
                        'order'				=> 'DES'
                    ];

                    $query_search = new WP_Query( $args_search );
                    if( $query_search->have_posts() ) :
                        while( $query_search->have_posts() ) : $query_search->the_post(); ?>
                            <option value="<?php the_ID() ?>"><?php the_title() ?></option>
                        <?php
                        endwhile;
                    endif;
                    ?>
                </select>
                <input type="submit" name="filter_events" value="Buscar">
                <input type="submit" name="filter_all_events" value="Ver todos">
            </form>
        </div>

        <?php
        if(isset($_POST['filter_events'])) {
            $filter_event_id = $_POST['filter_event_name'];
            $filter_table_users = $wpdb->prefix . "cem_event_user";
            ?>
            <div class="admin-users-content">
                <div class="container">
                    <div class="row">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre del Evento</th>
                                    <th scope="col">Email del Usuario</th>
                                    <th scope="col">Nº de Referencia</th>
                                    <th scope="col">Estado</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $users = $wpdb->get_results("SELECT * FROM $filter_table_users WHERE event_id = '$filter_event_id'");
                            if (count($users) > 0) {
                                foreach ($users as $event_user) {
                                    // Definimos el id del evento
                                    $event_id = $event_user->event_id;
                                    // Definimos el nombre del evento a través del id obtenido anteriormente
                                    $event_name = get_the_title($event_id);
                                    // Obtenemos el email del usuario
                                    $event_user_mail = $event_user->user_mail;
                                    // Obtenemos el número de referencia dle usuario
                                    $event_user_ref = $event_user->event_user_ref;
                                    // Obtenemos el estado del usuario
                                    $event_user_status = $event_user->confirm;
                                    // Si el estado es = 0 mostramos pendiente, sino, inscrito
                                    if ($event_user_status == 0) {
                                        $confirm = 'Pendiente';
                                    } else {
                                        $confirm = 'Inscrito';
                                    }

                                    $edit_file = plugins_url('editSubscription.php', __FILE__);
                                    $delete_file = plugins_url('deleteSubscription.php', __FILE__);

                                    ?>
                                    <tr>
                                        <td><?php echo $event_name; ?></td>
                                        <td><?php echo $event_user_mail; ?></td>
                                        <td><?php echo $event_user_ref; ?></td>
                                        <td><?php echo $confirm; ?></td>
                                        <td>
                                            <form action='<?php echo $edit_file; ?>' method="post" class="edit-submit">
                                                <input type="hidden" name="edit_event_id"
                                                       value="<?php echo $event_id; ?>">
                                                <input type="hidden" name="edit_original_user_email"
                                                       value="<?php echo $event_user_mail; ?>">
                                                <input type="hidden" name="edit_original_user_status"
                                                       value="<?php echo $event_user_status; ?>">
                                                <input type="email" name="edit_new_user_email"
                                                       value="<?php echo $event_user_mail; ?>">
                                                <?php
                                                if ($confirm == 'Pendiente') {
                                                    ?>
                                                    <select name="edit_new_user_status" id="new_user_status">
                                                        <option value="0">Pendiente</option>
                                                        <option value="1">Inscrito</option>
                                                    </select>
                                                    <?php
                                                } elseif ($confirm == 'Inscrito') {
                                                    ?>
                                                    <select name="edit_new_user_status" id="new_user_status">
                                                        <option value="1">Inscrito</option>
                                                        <option value="0">Pendiente</option>
                                                    </select>
                                                    <?php
                                                }
                                                ?>

                                                <input type="submit" name="submit" value="Editar">
                                            </form>
                                        </td>
                                        <td>
                                            <form action='<?php echo $delete_file; ?>' method="post"
                                                  class="delete-submit">
                                                <input type="hidden" name="delete_event_id"
                                                       value="<?php echo $event_id; ?>">
                                                <input type="hidden" name="delete_event_email"
                                                       value="<?php echo $event_user_mail; ?>">
                                                <input type="hidden" name="delete_event_status"
                                                       value="<?php echo $event_user_status; ?>">
                                                <input type="submit" name="submit" value="Eliminar">
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else { ?>
                                <div class="no-results">
                                    <p>No existen resultados para su filtrado</p>
                                </div>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php
            // SI NO FILTRAMOS SE NOS MUESTRAN TODOS
        } else { ?>
            <div class="admin-users-content">
                <div class="container">
                    <div class="row">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Nombre del Evento</th>
                                <th scope="col">Email del Usuario</th>
                                <th scope="col">Nº de Referencia</th>
                                <th scope="col">Estado</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $table_name_users = $wpdb->prefix . "cem_event_user";
                            // Obtenemos todos los campos de la tabla de usuarios de la base de datos
                            $users = $wpdb->get_results("SELECT * FROM $table_name_users");
                            foreach ($users as $event_user) {
                                // Definimos el id del evento
                                $event_id = $event_user->event_id;
                                // Definimos el nombre del evento a través del id obtenido anteriormente
                                $event_name = get_the_title($event_id);
                                // Obtenemos el email del usuario
                                $event_user_mail = $event_user->user_mail;
                                // Obtenemos el número de referencia dle usuario
                                $event_user_ref = $event_user->event_user_ref;
                                // Obtenemos el estado del usuario
                                $event_user_status = $event_user->confirm;
                                // Si el estado es = 0 mostramos pendiente, sino, inscrito
                                if ($event_user_status == 0) {
                                    $confirm = 'Pendiente';
                                } else {
                                    $confirm = 'Inscrito';
                                }

                                $edit_file = plugins_url('editSubscription.php', __FILE__);
                                $delete_file = plugins_url('deleteSubscription.php', __FILE__);

                                ?>
                                <tr>
                                    <td><?php echo $event_name; ?></td>
                                    <td><?php echo $event_user_mail; ?></td>
                                    <td><?php echo $event_user_ref; ?></td>
                                    <td><?php echo $confirm; ?></td>
                                    <td>
                                        <form action='<?php echo $edit_file; ?>' method="post" class="edit-submit">
                                            <input type="hidden" name="edit_event_id" value="<?php echo $event_id; ?>">
                                            <input type="hidden" name="edit_original_user_email"
                                                   value="<?php echo $event_user_mail; ?>">
                                            <input type="hidden" name="edit_original_user_status"
                                                   value="<?php echo $event_user_status; ?>">
                                            <input type="email" name="edit_new_user_email"
                                                   value="<?php echo $event_user_mail; ?>">
                                            <?php
                                            if ($confirm == 'Pendiente') {
                                                ?>
                                                <select name="edit_new_user_status" id="new_user_status">
                                                    <option value="0">Pendiente</option>
                                                    <option value="1">Inscrito</option>
                                                </select>
                                                <?php
                                            } elseif ($confirm == 'Inscrito') {
                                                ?>
                                                <select name="edit_new_user_status" id="new_user_status">
                                                    <option value="1">Inscrito</option>
                                                    <option value="0">Pendiente</option>
                                                </select>
                                                <?php
                                            }
                                            ?>

                                            <input type="submit" name="submit" value="Editar">
                                        </form>
                                    </td>
                                    <td>
                                        <form action='<?php echo $delete_file; ?>' method="post" class="delete-submit">
                                            <input type="hidden" name="delete_event_id"
                                                   value="<?php echo $event_id; ?>">
                                            <input type="hidden" name="delete_event_email"
                                                   value="<?php echo $event_user_mail; ?>">
                                            <input type="hidden" name="delete_event_status"
                                                   value="<?php echo $event_user_status; ?>">
                                            <input type="submit" name="submit" value="Eliminar">
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <?php
    // CREAMOS EL BOTÓN DE SUSCRIPCIÓN
    $subscribe_file = plugins_url( 'adminSubscribe.php', __FILE__ );
    ?>

    <div class="subscribe-button">
        <a href="#" data-popup-open="popup-2" class="btn btn-primary btn-information" id="popup-2">Suscribir usuario</a>
        <div class="popup" data-popup="popup-2">
            <div class="popup-inner">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Suscribir usuario...</h5>
                    </div>
                    <div class="modal-body">
                        <form method="post" name="formPrueba" action="<?php echo $subscribe_file ?>" >

                            <div class="form-row">
                                <label for="validationDefault01">Nombre:</label>
                                <input type="text" class="form-control" id="validationDefault01" placeholder="Nombre" name="subs_name" required>
                            </div>
                            <div class="form-row">
                                <label for="validationDefault02">Apellidos:</label>
                                <input type="text" class="form-control" id="validationDefault02" placeholder="Apellidos" name="subs_last_name" required>
                            </div>
                            <div class="form-row">
                                <label for="validationDefault03">Email:</label>
                                <input type="text" class="form-control" id="validationDefault03" placeholder="Email" name="subs_email" required>
                            </div>
                            <div class="form-row">
                                <label for="validationDefault04">Evento:</label>
                                <select name="subs_event" id="selectEventId">

                                    <?php
                                    $args_post_type = [
                                        'post_type' 		=> 'custom-event-manager',
                                        'order'				=> 'DES'
                                    ];

                                    $query = new WP_Query( $args_post_type );
                                    if( $query->have_posts() ) :
                                        while( $query->have_posts() ) : $query->the_post(); ?>
                                            <?php
                                            $event_last_id = get_the_ID();
                                            $general_data = get_post_meta($event_last_id, '_cem_data', TRUE);
                                            $price = array_values($general_data)[0];
                                            $status = array_values($general_data)[2];

                                            if ($status == 'Disponible') {
                                                if ($price == '' || $price == '0') {
                                                    ?>
                                                    <option value="<?php the_ID() ?>"><?php the_title() ?></option>
                                                    <?php
                                                }
                                            }
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <button class="btn btn-primary" type="submit" name="submit">Suscribir</button>
                        </form>
                    </div>
                </div>
                <a class="popup-close" data-popup-close="popup-2" href="#">x</a>
            </div>
        </div>
    </div>

    <script>
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
<?php

} else {
    ?>
    <div class="no-more-events">
        <h2>Lo siento, no dispone de servicios para ver esta página</h2>
    </div>
    <?php
}