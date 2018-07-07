<?php
/*
Plugin Name: Custom Events Manager
Plugin URI:
Description: Plugin para la creación de eventos personalizados. Estos eventos los podremos definir con su título, imágen, fechas de comienzo y fin, estado, precio, etc. Muy recomendable si deseas crear un portal web dedicado al turismo.
Version:     1.0
Author:      José Miguel Calvo Vílchez
Author URI:  www.linkedin.com/in/josé-miguel-calvo-vílchez-282014148
Text Domain: custom-event-manager
Domain Path: /languages/
License:     GPL2

Custom Events Manager is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Custom Events Manager is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Custom Events Manager. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


/*
 *
 *  CREACIÓN DEL CUSTOM POST TYPE DE EVENTOS
 *
 */
if( !function_exists( 'cem_cpt_event' ) ) {
    function cem_cpt_event() {
        // Etiqueta de los nombres que vamos a tener en nuestro custom_post_type
        $labels = [
            'name'                  => 'Eventos',
            'singular_name'         => 'Evento',
            'add_new'               => 'Añadir nuevo evento',
            'add_new_item'          => 'Añadir nuevo evento',
            'edit_item'             => 'Editar evento',
            'view_item'             => 'Ver evento',
            'view_items'            => 'Ver eventos',
            'search_items'          => 'Buscar eventos',
            'not_found'             => 'No encontrado',
            'not_found_in_trash'    => 'No encontrado en la papelera',
            'all_items'             => 'Eventos',
            'insert_into_item'      => 'Insertar en el evento',
            'uploaded_to_this_post' => 'Cargado a este evento',
            'featured_image'        => 'Imagen del evento',
            'set_featured_image'    => 'Definir imagen del evento',
            'use_featured_image'    => 'Usar como imagen del evento',
            'remove_featured_image' => 'Borrar imagen del evento',
        ];

        // Argumentos que le vamos a pasar como parámetros a la función register_post_type()
        $args = [
            'labels'                => $labels,
            'description'           => 'Tipo de post para crear nuevos eventos con sus respectivos campos personalizados',
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'menu_position'         => 15,
            'menu_icon'             => plugin_dir_url(__FILE__) . 'admin/images/event-icon.png',
            'capability_type'       => 'post',
            'supports'              => [
                                            'title',
                                            'editor',
                                            'revisions',
                                            'thumbnail'
                                        ],
            'has_archive'           => true,
            'rewrite'               => ['slug' => 'eventos']
        ];

        register_post_type('custom-event-manager', $args);
        flush_rewrite_rules(); // Limpia los enlaces permanentes de los tipos de publicaciones asi ya nos mostrará el contenido de nuestro custom post type.
    }

    add_action('init', 'cem_cpt_event');
}

/*
 *
 * CREACIÓN DE LA TABLA DE BASE DE DATOS SOBRE LOS USUARIOS DE UN EVENTO
 *
 */
function cem_event_create_bbdd_tables() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'cem_event_user';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                          id mediumint(9) NOT NULL AUTO_INCREMENT,
                          event_id mediumint(9) NOT NULL,
                          user_mail varchar(255) NOT NULL,
                          event_user_ref varchar(255) NOT NULL,
                          confirm bit NOT NULL,
                          UNIQUE KEY id (id)
                         ) $charset_collate;";
    $wpdb->query($sql);

    $table_name_places = $wpdb->prefix . 'cem_event_places';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name_places(
                                id mediumint(9) NOT NULL AUTO_INCREMENT,
                                event_id mediumint(9) NOT NULL,
                                places_num mediumint(9) NOT NULL,
                                UNIQUE KEY id(id)
            ) $charset_collate;";
    $wpdb->query($sql);
}
register_activation_hook(__FILE__,'cem_event_create_bbdd_tables');


/*
 *
 *  CREACIÓN DE LOS METABOXES DE FECHA DE INICIO Y FIN COLOCADOS EN LA PARTE DERECHA
 *
 */

abstract class CEM_Dates_Metacaja {
    public static function add() {
        add_meta_box(
            'cem_event_dates',
            '¿Cuándo será el evento?',
            [self::class, 'html'],
            'custom-event-manager', // CUSTOM_POST_TYPE
            'side' // Con este valor se nos colocará por defecto en la parte derecha.
        );
    }

    public static function html( $post, $metabox ) {

        $cem_dates = get_post_meta( $post->ID, '_cem_dates', true );
        $date_ini = isset($cem_dates['date_ini']) ? $cem_dates['date_ini'] : '';
        $date_end = isset($cem_dates['date_end']) ? $cem_dates['date_end'] : '';
        $hour_ini = isset($cem_dates['hour_ini']) ? $cem_dates['hour_ini'] : '';
        $hour_end = isset($cem_dates['hour_end']) ? $cem_dates['hour_end'] : '';

        $html = "
			<div class='event-metabox'>
				<label for='event_dates_ini' class='dates_title'>Inicio</label><br>
				<input type='date' name='cem_dates[date_ini]' id='event_dates_ini' value='$date_ini'>
				<input type='time' name='cem_dates[hour_ini]' id='event_dates_ini' value='$hour_ini'>
			</div>
			<div class='event-metabox'>
				<label for='event_dates_end' class='dates_title'>Fin</label><br>
				<input type='date' name='cem_dates[date_end]' id='event_dates_end' value='$date_end'>
				<input type='time' name='cem_dates[hour_end]' id='event_dates_end' value='$hour_end'>
			</div>
			
		";

        echo $html;



    }

    public static function save( $post_id ) {

        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }


        if( array_key_exists('cem_dates', $_POST) ) {
            update_post_meta(
                $post_id,
                '_cem_dates', // Nombre que utilizamos en el get_post_meta
                $_POST['cem_dates']
            );
        }
    }
}

add_action('add_meta_boxes', ['CEM_Dates_Metacaja', 'add']);
add_action( 'save_post', ['CEM_Dates_Metacaja', 'save']);

/*
 *
 *  CREACIÓN DE LOS METABOXES DE DATOS GENERALES DEL EVENTO
 *
 */

abstract class CEM_Event_Data_Metacaja {
    public static function add() {
        add_meta_box(
            'cem_event_data',
            'Datos del Evento',
            [self::class, 'html'],
            'custom-event-manager'// CUSTOM_POST_TYPE
        );
    }

    public static function html( $post, $metabox ) {

        $cem_data = get_post_meta( $post->ID, '_cem_data', true );
        $event_status = isset($cem_data['event_status']) ? $cem_data['event_status'] : '';
        $event_price = isset($cem_data['event_price']) ? $cem_data['event_price'] : '';
        $event_currency = isset($cem_data['event_currency']) ? $cem_data['event_currency'] : '';
        $event_url = isset($cem_data['event_url']) ? $cem_data['event_url'] : '';
        $event_address = isset($cem_data['event_address']) ? $cem_data['event_address'] : '';
        $event_latitude = isset($cem_data['event_latitude']) ? $cem_data['event_latitude'] : '';
        $event_longitude = isset($cem_data['event_longitude']) ? $cem_data['event_longitude'] : '';




        $html = "
            <div class='event-metabox'>
                <label for='event_price_data' class='data_title'>Precio: </label>
                <input type='text' name='cem_data[event_price]' id='event_price_data' value='$event_price'>
                <select name='cem_data[event_currency]' id='event_price_data'>
                    <option value='eur' " .selected($event_currency, 'eur', false ).">EUR</option>
                    <option value='gbp' " .selected($event_currency, 'gbp', false ).">GBP</option>
                    <option value='usd' " .selected($event_currency, 'usd', false ).">USD</option>
                    <option value='pln' " .selected($event_currency, 'pln', false ).">PLN</option>
                </select>
            </div>    
            <div class='event-metabox'>
                <label for='cem_event_status' class='data_title'>Estado del Evento: </label>
                <select name='cem_data[event_status]' id='cem_event_status'>
                    <option value=''>Estado</option>
                    <option value='Disponible' " .selected($event_status, 'Disponible', false ).">Disponible</option>
                    <option value='Finalizado' " .selected($event_status, 'Finalizado', false ).">Finalizado</option>
                    <option value='Proximamente' " .selected($event_status, 'Proximamente', false ).">Próximamente</option>
                    <option value='Aplazado' " .selected($event_status, 'Aplazado', false ).">Aplazado</option>
                    <option value='Cancelado' " .selected($event_status, 'Cancelado', false ).">Cancelado</option>
                </select>
            </div class='event-metabox'>    
            <div class='event-metabox'>
                <label for='event_url_data' class='data_title'>URL del Evento: </label>
                <input type='text' name='cem_data[event_url]' id='event_url_data' value='$event_url'>
            </div>   
            <div class='event-metabox'>
                <label for='event_address_data' class='data_title'>Dirección: </label>
                <input type='text' name='cem_data[event_address]' id='event_address_data' value='$event_address'>
            </div>     
            <div class='event-metabox'>
                <label for='event_latitude_data' class='data_title'>Latitud: </label>
                <input type='text' name='cem_data[event_latitude]' id='event_latitude_data' value='$event_latitude'>
            </div> 
            <div class='event-metabox'>
                <label for='event_longitude_data' class='data_title'>Longitud: </label>
                <input type='text' name='cem_data[event_longitude]' id='event_longitude_data' value='$event_longitude'>
            </div> 
        ";

        echo $html;

    }

    public static function save( $post_id ) {

        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }


        if( array_key_exists('cem_data', $_POST) ) {
            update_post_meta(
                $post_id,
                '_cem_data', // Nombre que utilizamos en el get_post_meta
                $_POST['cem_data']
            );
        }
    }
}

add_action('add_meta_boxes', ['CEM_Event_Data_Metacaja', 'add']);
add_action( 'save_post', ['CEM_Event_Data_Metacaja', 'save']);

/*
 *
 *  CREACIÓN DE LOS METABOXES DE LOS DESCUENTOS
 *
 */

abstract class CEM_Event_Discount_Metacaja {
    public static function add() {
        add_meta_box(
            'cem_event_discount',
            'Descuentos',
            [self::class, 'html'],
            'custom-event-manager'

        );
    }

    public static function html( $post, $metabox ) {
        $cem_discount = get_post_meta( $post->ID, '_cem_discount', true);
        $date_ini_dis = isset($cem_discount['date_ini_dis']) ? $cem_discount['date_ini_dis'] : '';
        $date_end_dis = isset($cem_discount['date_end_dis']) ? $cem_discount['date_end_dis'] : '';
        $data_discount = isset($cem_discount['data_discount']) ? $cem_discount['data_discount'] : '';

        $html = "
			<div class='event-metabox'>
				<label for='event_date_ini_dis' class='dates_title'>Inicio</label><br>
				<input type='date' name='cem_discount[date_ini_dis]' id='event_date_ini_dis' value='$date_ini_dis'>
			</div>
			<div class='event-metabox'>
				<label for='event_date_end_dis' class='dates_title'>Fin</label><br>
				<input type='date' name='cem_discount[date_end_dis]' id='event_date_end_dis' value='$date_end_dis'>
			</div>
			<div class='event-metabox'>
				<label for='event_dis_data' class='data_title'>Descuento (%): </label>
                <input type='text' name='cem_discount[data_discount]' id='event_dis_data' value='$data_discount'>
			</div>
		";

        echo $html;
    }

    public static function save( $post_id ) {
        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        if( array_key_exists('cem_discount', $_POST) ) {
            update_post_meta(
                $post_id,
                '_cem_discount', // Nombre que utilizamos en el get_post_meta
                $_POST['cem_discount']
            );
        }
    }
}

add_action('add_meta_boxes', ['CEM_Event_Discount_Metacaja', 'add']);
add_action( 'save_post', ['CEM_Event_Discount_Metacaja', 'save']);

/*
 *
 *  CREACIÓN DE LOS METABOXES DE LAS SUBSCRIPCIONES
 *
 */

abstract class CEM_Event_Subscription_Metacaja {
    public static function add() {
        add_meta_box(
            'cem_event_subscription',
            'Plazas del Evento (Sólo si el evento es gratuito)',
            [self::class, 'html'],
            'custom-event-manager'// CUSTOM_POST_TYPE
        );
    }

    public static function html( $post, $metabox ) {
        $cem_subscribe = get_post_meta( $post->ID, '_cem_subscribe', true );
        $event_subscribe = isset($cem_subscribe['event_subscribe']) ? $cem_subscribe['event_subscribe'] : '';

        $html = "
            <div class='event-metabox'>
                <label for='event_subscribe_data' class='data_title'>Nº de Plazas Disponibles: </label>
                <input type='number' name='cem_subscribe[event_subscribe]' id='event_subscribe_data' value='$event_subscribe'>
            </div>
    	";

        echo $html;
    }

    public static function save( $post_id ) {

        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }


        if( array_key_exists('cem_subscribe', $_POST) ) {
            update_post_meta(
                $post_id,
                '_cem_subscribe',
                $_POST['cem_subscribe']
            );
        }
    }
}

add_action('add_meta_boxes', ['CEM_Event_Subscription_Metacaja', 'add']);
add_action( 'save_post', ['CEM_Event_Subscription_Metacaja', 'save']);

/*
 *
 * CREACIÓN DE LAS COLUMNAS PARA MOSTRAR EN LA VISTA GENERAL DE EVENTOS
 *
 */

add_filter( 'manage_custom-event-manager_posts_columns', 'cem_set_columns' );

function cem_set_columns( $columns ) {
    $cem_new_columns = array();
    $cem_new_columns['title'] = 'Nombre del Evento';
    $cem_new_columns['location'] = 'Dirección';
    $cem_new_columns['event_status'] = 'Estado del Evento';
    $cem_new_columns['date_ini'] = 'Inicio';
    $cem_new_columns['date_end'] = 'Fin';
    $cem_new_columns['date'] = 'Fecha de Publicación';

    return $cem_new_columns;

}

add_action('manage_custom-event-manager_posts_custom_column', 'cem_custom_column', 10, 2);

function cem_custom_column( $column, $post_id ) {
    $dates = get_post_meta($post_id, '_cem_dates', TRUE);
    $general_data = get_post_meta($post_id, '_cem_data', TRUE);
    switch( $column ) {

        case 'date_ini' :
            echo array_values($dates)[0];
            break;

        case 'date_end':
            echo array_values($dates)[2];
            break;
        case 'event_status':
            echo array_values($general_data)[2];
            break;
        case 'location' :
            echo array_values($general_data)[4];
            break;
    }
}

/*
 *
 * AÑADIR BOOTSTRAP A NUESTRO PLUGIN
 *
 */

add_action('wp_head','head_code');

function head_code() {
    $output = '<script  src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" 
                        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" 
                        crossorigin="anonymous">
               </script>';

    $output .= '<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" 
                      rel="stylesheet" 
                      integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" 
                      crossorigin="anonymous">';
    echo $output;
}

/*
 *
 * CREACIÓN DE LA PÁGINA QUE MUESTRA TODOS LOS EVENTOS
 *
 */

function booklistTpl_archive($template){
    if(is_post_type_archive('custom-event-manager')){
        $theme_files = array('archive-custom-event-manager.php');
        $exists_in_theme = locate_template($theme_files, false);
        if($exists_in_theme == ''){
            return plugin_dir_path(__FILE__) . '/admin/archive-custom-event-manager.php';
        }
    }
    return $template;
}

add_filter('archive_template','booklistTpl_archive');

/*
 *
 * CREACIÓN DE LA PÁGINA QUE MUESTRA CADA EVENTO DE FORMA INDIVIDUAL
 *
 */

function cpte_force_template( $template ) {

    if( is_singular( 'custom-event-manager' ) ) {
        $template = plugin_dir_path(__FILE__) . '/admin/single-custom-event-manager.php';
    }

    return $template;
}

add_filter( 'template_include', 'cpte_force_template' );


/*
 *
 * CREACIÓN DEL ARCHIVO QUE CONTENDRÁ EL CSS DE LOS EVENTOS
 *
 */

function wpdocs_theme_name_scripts() {
    wp_enqueue_style( 'style-name', plugins_url( 'admin/css/myCSS.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );

function wpdocs_theme_name_admin_scripts() {
    wp_enqueue_style( 'style-name', plugins_url( 'admin/css/adminCSS.css', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'wpdocs_theme_name_admin_scripts' );


/*
 *
 * FUNCIÓN QUE ELIMINA LAS TABLAS DE LOS EVENTOS CUANDO DESACTIVAMOS EL PLUGIN
 *
 */

function cem_event_delete_bbdd_tables() {
    global $wpdb;

    $table_name_users = $wpdb->prefix . 'cem_event_user';
    $table_name_events = $wpdb->prefix . 'cem_event_places';

    $sql = "DROP TABLE $table_name_users";
    $wpdb->query($sql);

    $sql = "DROP TABLE $table_name_events";
    $wpdb->query($sql);

}

register_deactivation_hook( __FILE__, 'cem_event_delete_bbdd_tables' );

/*
 *
 *  CREACIÓN DE UN MENÚ DE ADMINISTRACIÓN QUE CONTENDRÁ LA INFORMACIÓN NECESARIA PARA EL USO DE NUESTRO PLUGIN.
 *  EL CÓDIGO HTML DE ESTA PÁGINA SE ENCUENTRA EN EL ARCHIVO /ADMIN/VIEW.PHP
 *
 */
if( !function_exists( 'cem_options_page' ) ) {
    add_action( 'admin_menu', 'cem_options_page' );
    function cem_options_page() {
        add_menu_page(
            'CEM_Admin',
            'CEM_Admin',
            'manage_options',
            plugin_dir_path(__FILE__) . 'admin/adminView.php',
            null, // Función en la cual incluiremos el HTML
            plugin_dir_url(__FILE__) . 'admin/images/event-settings.png'
        );
    }
}



