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
    wp_enqueue_script('index.js', PLUGIN_PATH . 'public/src/js/index.js', array('jquery'), 1.0, true );
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
      $port_list['ports'][$port_item->port]['terminals'][$port_item->terminal] = array('name' => $port_item->terminal);

      $port_data = array(
        'name' => $port_item->terminal,
        'daily_draft_mts' => $port_item->daily_draft_mts,
        'daily_draft_feets' => $port_item->daily_draft_feets,
        'water_density' => $port_item->water_density,
        'waiting_time' => $port_item->waiting_time,
        'waiting_time_projected' => $port_item->waiting_time_projected,
        'max_loa' => $port_item->max_loa,
        'max_beam' => $port_item->max_beam,
        'depth_alognside' => $port_item->depth_alognside,
        'max_draft' => $port_item->max_draft,
        'airdraft' => $port_item->airdraft,
        'storage_capacity' => $port_item->storage_capacity,
        'loading_rates' => $port_item->loading_rates,
        'discharging_rates' => $port_item->discharging_rates,
        'method_of_loading' => $port_item->method_of_loading,
        'method_of_discharging' => $port_item->method_of_discharging,
        'frontage_dolphins' => $port_item->frontage_dolphins,
        'tide_restriction' => $port_item->tide_restriction,
        'night_arrival_sailing_restriction' => $port_item->night_arrival_sailing_restriction,
        'average_congestion' => $port_item->average_congestion,
        'bunkers' => $port_item->bunkers,
        'water_availability' => $port_item->water_availability,
        'garbage_collection_compulsory' => $port_item->garbage_collection_compulsory,
        'crushing_capacity' => $port_item->crushing_capacity,
        'truck' => $port_item->truck,
        'lineup' => $this->get_lineup($port_item->port, $port_item->terminal, null)
      );

      $docks_list = $this->get_dock($port_item->port, $port_item->terminal);

      if(gettype($docks_list) == 'array' and !empty($docks_list)){
        $port_list['ports'][$port_item->port]['terminals'][$port_item->terminal]['docks_list'] = $docks_list;
      } else {
        $port_list['ports'][$port_item->port]['terminals'][$port_item->terminal] = $port_data;
      }
    }

    self::$port_list = $port_list;

    return self::$port_list;
  }

  public function get_dock($port, $terminal) {
    global $wpdb;
    $dock_list;
    $docks_object = [];
    $dock_query = "SELECT * from {$wpdb->prefix}wbls_ports WHERE port = '{$port}' AND terminal = '{$terminal}'";
    $dock_list = $wpdb->get_results($dock_query, 'object');

    foreach($dock_list as $dock_item) {
      if(!is_null($dock_item->dock)){
        $docks_object[$dock_item->dock] = array(
          'name' => $dock_item->dock,
          'daily_draft_mts' => $dock_item->daily_draft_mts,
          'daily_draft_feets' => $dock_item->daily_draft_feets,
          'water_density' => $dock_item->water_density,
          'waiting_time' => $dock_item->waiting_time,
          'waiting_time_projected' => $dock_item->waiting_time_projected,
          'max_loa' => $dock_item->max_loa,
          'max_beam' => $dock_item->max_beam,
          'depth_alognside' => $dock_item->depth_alognside,
          'max_draft' => $dock_item->max_draft,
          'airdraft' => $dock_item->airdraft,
          'storage_capacity' => $dock_item->storage_capacity,
          'loading_rates' => $dock_item->loading_rates,
          'discharging_rates' => $dock_item->discharging_rates,
          'method_of_loading' => $dock_item->method_of_loading,
          'method_of_discharging' => $dock_item->method_of_discharging,
          'frontage_dolphins' => $dock_item->frontage_dolphins,
          'tide_restriction' => $dock_item->tide_restriction,
          'night_arrival_sailing_restriction' => $dock_item->night_arrival_sailing_restriction,
          'average_congestion' => $dock_item->average_congestion,
          'bunkers' => $dock_item->bunkers,
          'water_availability' => $dock_item->water_availability,
          'garbage_collection_compulsory' => $dock_item->garbage_collection_compulsory,
          'crushing_capacity' => $dock_item->crushing_capacity,
          'truck' => $dock_item->truck,
          'lineup' => $this->get_lineup($dock_item->port, $dock_item->terminal, $dock_item->dock)
        );
      }
    }

    return $docks_object;
  }

  public function get_lineup($port, $terminal, $dock) {
    global $wpdb;

    // Fetch Lineup items from the database
  $lineup_query = "SELECT vessel_name, vessel_type, eta, comodity, quantity, operation, destination FROM {$wpdb->prefix}wbls_lineup WHERE port = '{$port}' AND terminal = '{$terminal}' AND dock = '{$dock}'";
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