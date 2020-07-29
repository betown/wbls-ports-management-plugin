<?php
global $wpdb;

class Layout_Builder {

  static $port_list;

  public function __construct() {
    add_action('init', [$this, 'create_page_rule']);
    add_action('wp_enqueue_scripts', [$this, 'map_scripts_list']);
    add_action('template_include', [$this, 'change_template']);
    add_shortcode('render_map',[$this, 'render_map_shortcode']);
    add_shortcode('map_template', [$this, 'set_map_template']);
  }

  public function map_scripts_list() {
    wp_enqueue_style('bootstrap_styles', PLUGIN_PATH . 'public/src/css/bootstrap.min.css', false, 4.5, false);
    wp_enqueue_style('map_styles', PLUGIN_PATH . 'public/src/css/theme.css', false, 1.0, false);
    wp_enqueue_script('bootstrap_popper', PLUGIN_PATH . 'public/src/js/popper.min.js', array('jquery'), 4.5, true );
    wp_enqueue_script('bootstrap_js', PLUGIN_PATH . 'public/src/js/bootstrap.min.js', array('bootstrap_popper'), 4.5, true );
  }

  public function render_map_shortcode() {
    $port_list = $this->get_info();
    require_once 'src/map_page_layout.php';
  }

  public function get_info() {
    global $wpdb;
    $port_list;
    // Fetch ports list from the database
    $port_query = "SELECT * FROM {$wpdb->prefix}wbls_ports";
    $ports_list = $wpdb->get_results($port_query, 'object');
    // Organize the port object into a more easy to traverse object.
    foreach($ports_list as $port_item) {
     $port_list['ports'][$port_item->port]['terminals'][] = array(
        'name' => $port_item->terminal,
        'daily_draft_mts' => $port_item->daily_draft_mts,
        'daily_draft_feets' => $port_item->daily_draft_feets,
        'water_density' => $port_item->water_density,
        'waiting_time' => $port_item->waiting_time,
        'waiting_time_projected' => $port_item->waiting_time_projected,
        'max_loa' => $port_item->max_loa,
        'max_beam' => $port_item->max_beam,
        'depth_alognside' => $port_item->depth_alognside,
        'max_sailing_draft' => $port_item->max_sailing_draft,
        'airdraft' => $port_item->airdraft,
        'storage_capacity' => $port_item->storage_capacity,
        'loading_rate' => $port_item->loading_rate,
        'method_of_loading' => $port_item->method_of_loading,
        'frontage' => $port_item->frontage,
        'tide_restriction' => $port_item->tide_restriction,
        'sailing_restriction' => $port_item->sailing_restriction,
        'average_congestion' => $port_item->average_congestion,
        'bunkers' => $port_item->bunkers,
        'water_availability' => $port_item->water_availability,
        'garbage_disposal' => $port_item->garbage_disposal,
        'crushing' => $port_item->crushing,
        'truck' => $port_item->truck,
        'lineup' => $this->get_lineup($port_item->port, $port_item->terminal)
      );
    }

    self::$port_list = $port_list;

    return self::$port_list;
  }

  public function get_lineup($port, $terminal) {
    global $wpdb;

    // Fetch Lineup items from the database
    $lineup_query = "SELECT vessel_name, vessel_type, eta, comodity, quantity, operation, destination FROM {$wpdb->prefix}wbls_lineup WHERE port = '{$port}' AND terminal = '{$terminal}'";
    $lineup_object = $wpdb->get_results($lineup_query, 'ARRAY_A');

    return $lineup_object;
  }

  public function set_map_template() {
    echo set_query_var('print_map_template', true);
  }

  public function create_page_rule() {
    add_rewrite_endpoint('dump', EP_PERMALINK);
    add_rewrite_rule('^the-page$', '?ports_list', 'top');

    if(get_transient('wbls_flush')) {
			delete_transient( 'vpt_flush' );
			flush_rewrite_rules();
    }
  }

  function change_template( $template ) {

		if( strpos(get_permalink(), 'interactive-map') ) {
      $newTemplate = plugin_dir_path( __FILE__ ) . 'map_template.php';
      return $newTemplate;
    }

		//Fall back to original template
		return $template;

	}

}