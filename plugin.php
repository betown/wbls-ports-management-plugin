<?php
/**
 * Plugin Name: WBLS Port Manager
 * Description: Port management plugin for WBLS.
 * Version: 1.0
 * Author: Joe Gomez
 * Author URI: http://www.joegomezweb.com
 */

define('PLUGIN_PATH', plugin_dir_url( __FILE__ ));

require_once 'includes/install.php';
require_once 'includes/port_list_page.php';
require_once 'includes/lineup_list_page.php';
require_once 'public/layout_builder.php';


class wbls_plugin {
  static $instance;

  public $ports_object;
  public $lineup_object;
  public $shortcode_object;

  public function __construct() {
    add_filter('set-screen-option', [$this, 'set_screen'] , 9, 3);
    add_action('admin_menu', [$this, 'menu_option']);
    add_action('admin_menu', [$this, 'ports_page']);
    add_action('admin_menu', [$this, 'lineup_page']);

    $this->shortcode_options();
  }

	public static function set_screen( $status, $option, $value ) {
    return $value;
  }

  public function menu_option() {
    $hook = add_menu_page(
      'Administracion de Puertos',
      'Administracion de puertos WBLS',
      'manage_options',
      'wbls_options',
      null,
      'dashicons-location-alt'
    );
  }

  public function ports_page_options() {
    $this->ports_object = new Ports_List();
  }

  public function lineup_page_options() {
    $this->lineup_object = new Lineup_List();
  }

  public function shortcode_options() {
    $shortcode_object = new Layout_Builder();
  }

  public function ports_page() {
    $hook = add_submenu_page(
      'wbls_options',
      'Lista de puertos',
      'Lista de puertos',
      'manage_options',
      'wbls_options',
      [$this, 'ports_list_page']
    );
    add_action("load-$hook", [$this, 'ports_page_options']);
  }

  public function ports_list_page() {
    $this->ports_object->option_page_content();
  }

  public function lineup_page() {
    $hook = add_submenu_page(
      'wbls_options',
      'Lineup de puertos',
      'Lineup de puertos',
      'manage_options',
      'wbls_lineup',
      [$this, 'lineup_list_page']
    );

    add_action("load-$hook", [$this, 'lineup_page_options']);
  }

  public function lineup_list_page() {
    $this->lineup_object->option_page_content();
  }

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}

if(ob_get_length() > 0) {
  ob_clean();
  ob_start();
}

add_action( 'plugins_loaded', function () {
    wbls_plugin::get_instance();
}, 10, 0 );

register_activation_hook(__FILE__,'wbls_port_db_install');
register_activation_hook(__FILE__,'wbls_lineup_db_install');