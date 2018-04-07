<?php
/*
Plugin Name: Carla
Plugin URI: http://www.leclerc-web.fr
Description: Cette extension contient un module de cryptage de mot de passe, de sécurité, d'optimisation de vitesse ainsi qu'un module de geolocalisation par API de vos visiteurs.
Version: 1.1
Author: Leclerc-web
Author URI: http://www.leclerc-web.fr
Licence : NO

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;

/* supprime num version head*/
remove_action('wp_head', 'wp_generator'); 
/*supprimer message derreur d'identifient*/
add_filter('login_errors', create_function('$no_login_error', "return 'Erreur d\'identification';"));

/* -------------------------------------------------------- LOG IP --------------------------------------------------------*/
$ip = (isset($_SERVER)) ? $_SERVER['REMOTE_ADDR'] : $HTTP_SERVER_VARS['REMOTE_ADDR']; 

$resultats = $wpdb->get_results("SELECT ip, TIMESTAMPDIFF(HOUR, date_log, NOW()) AS nbh FROM {$wpdb->prefix}ip_log WHERE ip = '" .$ip. "'");

    if (isset($resultats)) {   
        foreach ($resultats as $donnees) {
				if (($donnees->nbh) >= 2) {
					$enregistrement = true;
				}elseif(($donnees->nbh) < 2){
					$enregistrement = false;
			}
        }
    }

    if (!isset($donnees) || ($enregistrement == true)) {
        $resultats = $wpdb->get_results("INSERT INTO {$wpdb->prefix}ip_log (ip, date_log) VALUES ('$ip', NOW())");    
    }else{
		////////////// PAS D ENREGISTREMENT
	}
/* ------------------------------------------------------ FIN LOG IP ----------------------------------------------------- */


/* ------------------------------------------------------ STOP ENUMERATION ----------------------------------------------------- */
add_filter( 'rest_endpoints', function( $e ){
    if (isset($e['/wp/v2/users'])) {
        unset($e['/wp/v2/users']);
    }
    if (isset($e['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($e['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $e;
});
if (!is_admin()) {
	if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
	add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}
function shapeSpace_check_enum($redirect, $request) {
	if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
	else return $redirect;
}
/* ------------------------------------------------------ FIN ENUMERATION ----------------------------------------------------- */



/**
 * Enqueue plugin style-file
 */
function carla_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
	if($_GET['page'] == "Cryptage" || $_GET['page'] == "Protection" || $_GET['page'] == "Optimisation" || $_GET['page'] == "Geolocalisation" || $_GET['page'] == "Carla"){
		wp_enqueue_style( 'carla-style', plugins_url('css/bootstrap.min.css', __FILE__) );
		wp_enqueue_style( 'carla-style2', plugins_url('css/stylesheet.css', __FILE__) );
	}
}		

function carla_script() {
	wp_enqueue_script('carla-script', plugins_url('js/bootstrap.min.js', __FILE__));
	wp_enqueue_script('carla-script-font', plugins_url('js/font-awesome.js', __FILE__));
}

carla_script();
carla_stylesheet();


		
if (!class_exists('carla')) {
	
/* ------------------------------------------------------ LANGUAGE ----------------------------------------------------- */
	class load_language 
	{
		public function __construct(){
		add_action('init', array($this, 'load_my_transl'));
		}

		 public function load_my_transl(){
			load_plugin_textdomain('carla', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}
	}

	$zzzz = new load_language;
/* ------------------------------------------------------ FIN LANGUAGE ----------------------------------------------------- */

    class carla {

      /**
         * Constructeur
         */
        public function __construct() {
            add_action('admin_menu', array($this, 'simple_action'));
		
				/* DETRUIT TABLE DANS BDD A LA DESACTIVATION DU PLUGIN */			
				function carla_remove_database() {
					 global $wpdb;
						 $ip_log = $wpdb->prefix . 'ip_log';
						 $sql = "DROP TABLE IF EXISTS $ip_log;";
						 $wpdb->query($sql);
						 delete_option("ip_log.01");
				} 
				
				/* AJOUTE LA TABLE DANS BDD A L ACTIVATION DU PLUGIN */
				 function carla_activate(){
					global $wpdb;
						$ip_log = $wpdb->prefix.'ip_log';
							if($wpdb->get_var('SHOW TABLES LIKE ' . $ip_log) != $ip_log){
								$sql = 'CREATE TABLE '.$ip_log.'(
														id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
														ip VARCHAR(50),
														date_log DATETIME)';
								require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
								dbDelta($sql);	
							}
				}
		}

       /**
           * Un exemple d'action  Création du menu carla dans backoffice et sous menu plugin et application des CSS/JS
           */
        public function simple_action() {
			/*add_action('init', 'traitement_des_donnes');*/
			add_action( 'wp_enqueue_css', 'carla_stylesheet' );
			add_action( 'wp_enqueue_scripts', 'carla_script' );
			add_action('admin_init','carla_activate');
            add_menu_page('Carla', 'Carla', 'manage_options', 'Carla', array($this, 'plugin_Carla'), 'dashicons-lock', 4);
			add_submenu_page( 'Carla', 'Cryptage', 'Cryptage', 'manage_options', 'Cryptage', array($this,'carla_crypt_func'));
			add_submenu_page( 'Carla', 'Protection', 'Protection', 'manage_options', 'Protection', array($this,'carla_protection_func'));
			add_submenu_page( 'Carla', 'Optimisation', 'Optimisation', 'manage_options', 'Optimisation', array($this,'carla_optimisation_func'));
			add_submenu_page( 'Carla', 'Geolocalisation', 'Geolocalisation', 'manage_options', 'Geolocalisation', array($this,'carla_geo_func'));
			register_deactivation_hook( __FILE__, 'carla_remove_database' );
        }

      /**
         * Attribuer les pages aux onglets du menu
         */
        public function plugin_Carla() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Vous n\'avez pas les droits pour accéder à cette page.'));
            }
            include(sprintf("%s/carla-conf.php", dirname(__FILE__)));	
        }
		
		public function carla_crypt_func() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Vous n\'avez pas les droits pour accéder à cette page.'));
            }
             include(sprintf("%s/modules/hashage.php", dirname(__FILE__)));	
        }
		
		public function carla_protection_func() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Vous n\'avez pas les droits pour accéder à cette page.'));
            }
             include(sprintf("%s/modules/htaccess.php", dirname(__FILE__)));	
        }
		
		public function carla_optimisation_func() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Vous n\'avez pas les droits pour accéder à cette page.'));
            }
            include(sprintf("%s/modules/optimisation.php", dirname(__FILE__)));	
        }
		
		public function carla_geo_func() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Vous n\'avez pas les droits pour accéder à cette page.'));
            }
             include(sprintf("%s/modules/geolocalisation.php", dirname(__FILE__)));	
        }
		
    }

}

$objet = new carla();