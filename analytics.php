<?php
/*
Plugin Name: GAnalytics
Plugin URI: https://github.com/CesarVerdu/GA-WordPress.git
Description: Plugin para implementación de Google Analytics en nuestra página
Version: 1.0
Author: Cesar Verdú
License: GPLv3
*/

//Agregar la funcionalidad a web en el header
add_action( 'wp_head', 'agregar_ga' );
function agregar_ga() {
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
    <p>Para conseguir el código de seguimiento de Google Analytics:
        <ol>
            <li>
                Inicia sesión en <a href= "https://analytics.google.com/">Google Analytics</a>
            </li>
            <li>
                Despliega la pestaña de <strong>Todos los datos de sitios web</strong>.
                En la ventana que se muestra verás tu ID.
            </li>
            <li>
                Para copiar tu ID, haz click en <strong>Administrar</strong> 
                y en la nueva ventana en <strong>Configuración de la Propiedad</strong>
            </li>
            <li>
                Desde aquí podrás copiar tu <strong>ID de seguimiento</strong>
            </li>
        </ol>
    </p>
<?php
}

function cv_ayuda_faq() {
    ?>
    <ul>
        <li><h5>¿Para que sirve Google Analytics?</h5>
        Es una funcionalidad para medir el tráfico en las webs y de estadísticas de sitios web
        </li>
        <li><h5>¿Es gratis conseguid el código ID?</h5>
        Si, solo necesitas una cuenta de Google para iniciar sesión
        </li>
        <li><h5>¿Es legal usar Google Analytics en Europa?</h5>
        Si, solo Austria y Francia han vetado su uso por incumplimiento del RGPD
        </li>
        <li><h5>¿Ralentizará el plugin la carga de mi web?</h5>
        El rendimiento de su página no se verá afectado, con la posible excepción de la primera carga de la página después de haber agregado el código de seguimiento. 
        Esta primera página vista llama a JavaScript en los servidores de Google, lo que puede llevar un poco más de tiempo que la carga de una página normal.
        </li>
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

            ID de Google Analytic:
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


