<?php
/*
Plugin Name: GAnalytics
Plugin URI: https://github.com/CesarVerdu/GA-WordPress.git
Description: Plugin para implementación de Google Analytics en nuestra página
Version: 1.0
Author: Cesar Verdú
License: GPLv3
*/

//Activacion del pluggin con valores por defecto
register_activation_hook( __FILE__, 'cv_set_default_options' );

function cv_set_default_options() {
    if ( get_option( 'cv_ga_cuenta' ) === false )
    {
        add_option( 'cv_ga_cuenta', 'UA-0000000-0' );
    }
}

//Agregar la opcion a los ajustes de Wordpress
function cv_menu_ajustes() {
    $pagina_opciones = add_options_page( 'Configuracion Google Analytics', 
        'Codigo de Google Analytics',
        'manage_options',
        'cv-conf-ga',
        'cv_genera_pagina' );

    if ( !empty( $pagina_opciones ) ) {
        add_action( 'load-' . $pagina_opciones, 'cv_ayuda' );
    }
}

function cv_ayuda() {
    $screen = get_current_screen();
    $screen->add_help_tab( array(
        'id'       => 'cv-instruciones',
        'title'    => 'Instrucciones',
        'callback' => 'cv_ayuda_instruciones',
    ) );
    $screen->add_help_tab( array(
        'id'       => 'cv-faq',
        'title'    => 'FAQ',
        'callback' => 'cv_ayuda_faq',
    ) );
    $screen->set_help_sidebar( '<p>Ayuda e instrucciones para insertar el codigo de
                  Google Analytics</p>' );
}

function cv_ayuda_instruciones() {
    ?>
    <p>Aqui colocamos como
        conseguir el id del sitio de Google Analytics.</p>
<?php
}

function cv_ayuda_faq() {
    ?>
    <ul>
        <li>¿Google Analytics es gratis?</li>
        <li>¿Este plugin puede hacer que mi sitio sea lento?</li>
    </ul>
<?php
}

add_action( 'admin_menu', 'cv_menu_ajustes' );

//Generar la pagina de ajustes
function cv_genera_pagina() {
    $codigo_ga = get_option( 'cv_ga_cuenta' ) ;

    ?>
    <div class="wrap">
        <h2>Google Analytic</h2>

        <form method="post" action="admin-post.php">
            <input type="hidden" name="action"  value="guardar_ga" />

            <?php wp_nonce_field('token_ga'); ?>

            Código de Google Analytic:
            <input type="text" name="codigo_ga"
                   value="<?php echo esc_html($codigo_ga);
                   ?>"/>
            <br />
            <input type="submit" value="Guardar" class="button-primary"/>
        </form>
    </div>
    <?php
}

//Guardar la clave en la base de datos
add_action( 'admin_post_guardar_ga', 'cv_guardar_ga' );

function cv_guardar_ga() {

    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( 'Not allowed' );
    }

    check_admin_referer( 'token_ga' );

    $codigo_ga = sanitize_text_field( $_POST['codigo_ga'] );

    update_option( 'cv_ga_cuenta', $codigo_ga );

    wp_redirect( add_query_arg( 'page',
        'cv-conf-ga',
        admin_url( 'options-general.php' ) ) );
    exit;
}

//Agregar la funcionalidad a web en el header
add_action( 'wp_head', 'agregar_ga' );
function cv_agregar_ga() {
    $codigo_ga = get_option( 'cv_ga_cuenta' ) ;
    echo "
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;
        i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();
        a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;
        m.parentNode.insertBefore(a,m)})(window,document,'script',
        'https://www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-0000000-1', 'auto');
        ga('send', 'pageview');
    </script>";
}

