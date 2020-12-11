<?php

global $wpdb;
require_once 'functions.php';

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ports_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Puerto', 'wbl' ), //singular name of the listed records
			'plural'   => __( 'Puertos', 'wbl' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
    ] );
	}

  /**
  * Fetches the db entries with pagination
  *
  * @param int $per_page
  * @param int $page_number
  *
  * @return mixed
  **/
  public static function get_ports($per_page = 20, $page_number = 1) {
    global $wpdb;

    $query = "SELECT * FROM {$wpdb->prefix}wbls_ports ";

    if( !empty($_REQUEST['orderby'])) {
      $query .= "ORDER BY " . esc_sql( $_REQUEST['orderby']);
      $query .= !empty($_REQUEST['order']) ? " " . esc_sql($_REQUEST['order'] ) : ' ASC';
    };

    $query .= " LIMIT $per_page ";
    $query .= "OFFSET " . ($page_number - 1) * $per_page;

    $result = $wpdb->get_results($query, 'ARRAY_A');
    return $result;
  }

  /**
  * Remove the required port by ID
  *
  * @param int $id Port ID
  **/

  public static function delete_port($id) {
    global $wpdb;

    $wpdb->delete(
      "{$wpdb->prefix}wbls_ports",
      [ 'id' => $id ],
      [ '%d' ]
    );
  }


  /**
  * Returns the count of items in the table
  *
  * @return null|string
  **/
  public static function record_count() {
    global $wpdb;

    $query = "SELECT COUNT(*) FROM {$wpdb->prefix}wbls_ports";

    return $wpdb->get_var($query);
  }

  /** Text displayed when no results are found **/

  public function no_items() {
    _e('No hay puertos disponibles en la base de datos, primero carga unos usando la opción de arriba', 'wbls');
  }

  /**
  *
  * Method to set the column Port
  *
  * @param array $item an array from the DB data
  *
  * @return string
  *
  **/

  function column_port( $item ) {

    // Create a nonce
    $delete_nonce = wp_create_nonce('wbls_delete_port');

    $title = "<strong>" . $item['port'] . "</strong>";

    $actions = [
      'delete' => sprintf(' <a href="?page=%s&action=%s&port=%s&_wpnonce=%s"> Eliminar</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce )
    ];

    return $title . $this->row_actions( $actions );
  }

  public function column_default ($item, $column_name) {
    return $item[$column_name];
  }

  /**
  *  Render the bulk edit checkbox
  *
  *  @param  array $item
  *
  *  @return string
  *
  **/
  function column_cb( $item ) {
    return sprintf(
      '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
    );
  }

  /**
  * Associative array of columns
  *
  * @return array
  *
  **/
  function get_columns() {
    $columns = [
      'cb' => '<input type="checkbox" />',
      'port' => __('Port', 'wbls'),
      'terminal' => __('Terminal', 'wbls'),
      'dock' => __('Dock', 'wbls'),
      'daily_draft_mts' => __('Daily Draft (METERS)', 'wbls'),
      'daily_draft_feets' => __('Daily Draft (FEET)', 'wbls'),
      'water_density' => __('Water Density', 'wbls'),
      'waiting_time' => __('Waiting Time (Current)', 'wbls'),
      'waiting_time_projected' => __('Waiting Time (2 days projection)', 'wbls'),
      'max_loa' => __('Max Loa', 'wbls'),
      'max_beam' => __('Max Beam', 'wbls'),
      'depth_alongside' => __('Depth Alongside', 'wbls'),
      'max_draft' => __('Max Draft', 'wbls'),
      'airdraft' => __('Airdraft', 'wbls'),
      'storage_capacity' => __('Storage Capacity', 'wbls'),
      'loading_rates' => __('Loading Rates', 'wbls'),
      'discharging_rates' => __('Discharging Rates', 'wbls'),
      'method_of_loading' => __('Method of Loading', 'wbls'),
      'method_of_discharging' => __('Method of Discharging', 'wbls'),
      'frontage_dolphins' => __('Frontage Dolphins', 'wbls'),
      'tide_restriction' => __('Tide Restriction', 'wbls'),
      'night_arrival_sailing_restriction' => __('Night Arrival / Sailing Restriction', 'wbls'),
      'average_congestion' => __('Average Congestion', 'wbls'),
      'bunkers' => __('Bunkers', 'wbls'),
      'water_availability' => __('Water Availability', 'wbls'),
      'garbage_collection_compulsory' => __('Garbage Collection Compulsory', 'wbls'),
      'crushing_capacity' => __('Crushing Capacity', 'wbls'),
      'truck' => __('Truck', 'wbls')
    ];

    return $columns;
  }

  public function get_sortable_columns() {
    $sortable_columns = array(
      'port' => array( 'port', true ),
      'terminal' => array('terminal', false)
    );

    return $sortable_columns;
  }

  /**
  * Get associative array of bulk actions
  *
  * @return array
  *
  **/

  public function get_bulk_actions() {
    $actions = [
      'bulk-delete' => 'Eliminar'
    ];

    return $actions;
  }

  /**
  * Handles data query and filter, sorting, pagination
  **/

  public function prepare_items() {
    $this->_column_headers = $this->get_column_info();

    /** Process bulk actions **/
    $this->process_bulk_action();
    $per_page = $this->get_items_per_page('ports_per_page', 20);
    $current_page = $this->get_pagenum();
    $total_items = self::record_count();

    $this->set_pagination_args([
      'total_items' => $total_items,
      'per_page' => $per_page
    ]);

    $this->items = self::get_ports($per_page, $current_page);
  }


  /**
  * Process the bulk actions (delete items)
  **/

  public function process_bulk_action() {
    if ('delete' === $this->current_action()) {

      $nonce = esc_attr($_REQUEST['_wpnonce']);

      if(!wp_verify_nonce($nonce, 'wbls_delete_port')) {
        die('This call is not valid');
      } else {
        self::delete_port( absint($_GET['port']) );
        wp_redirect(remove_query_arg(['action', 'port', '_wpnonce']));
        exit;
      }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'bulk-delete' ||
       isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete') {
      $delete_ids = esc_sql($_POST['bulk-delete']);

      foreach ($delete_ids as $id) {
        self::delete_port($id);
      }
      wp_redirect(add_query_arg());
      exit;
    }
  }

  public function option_page_content() {
    ?>
    <div class="wrap">
      <h2>Lista de puertos</h2>

      <form method="post" enctype='multipart/form-data'>
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('wbls_upload_file'); ?>">
        Subir lista de puertos: <input type="file" name="wbls_ports_file">
        <br>
        <?php submit_button(); ?>
      </form>

      <?php $this->review_uploaded_files(); ?>
      <div id="poststuff">
        <div id="post-body" class="metabox-holder">
          <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
              <form method="post">
                <?php
                $this->prepare_items();
                $this->display(); ?>
              </form>
            </div>
          </div>
        </div>
        <br class="clear">
      </div>
    </div>
    <?php
  }

  public function review_uploaded_files() {
    global $wpdb;

    if(!empty($_FILES) && file_exists($_FILES['wbls_ports_file']['tmp_name'])){
      $nonce = esc_attr($_POST['_wpnonce']);
      $tablename = $wpdb->prefix . 'wbls_ports';

      $uploadedFile = $_FILES['wbls_ports_file'];
      $file = fopen($uploadedFile['tmp_name'], 'r');
      $csv = fgetcsv($file);
      if(wp_verify_nonce( $nonce, 'wbls_upload_file' )) {
        $wpdb->query("TRUNCATE TABLE $tablename");

        while(($csvData = fgetcsv($file)) != FALSE) {

          $port = !empty($csvData[0]) ? $csvData[0] : "";
          $terminal = !empty($csvData[1]) ? $csvData[1] : "";
          $dock = !empty($csvData[2]) ? $csvData[2] : NULL;
          $daily_draft_mts = !empty($csvData[3]) ? $csvData[3] : NULL;
          $daily_draft_feets = !empty($csvData[4]) ? $csvData[4] : NULL;
          $water_density = !empty($csvData[5]) ? $csvData[5] : NULL;
          $waiting_time = !empty($csvData[6]) ? $csvData[6] : NULL;
          $waiting_time_projected = !empty($csvData[7]) ? $csvData[7] : NULL;
          $max_loa = !empty($csvData[8]) ? $csvData[8] : NULL;
          $max_beam = !empty($csvData[9]) ? $csvData[9] : NULL;
          $depth_alognside = !empty($csvData[10]) ? $csvData[10] : NULL;
          $max_draft = !empty($csvData[11]) ? $csvData[11] : NULL;
          $airdraft = !empty($csvData[12]) ? $csvData[12] : NULL;
          $storage_capacity = !empty($csvData[13]) ? $csvData[13] : NULL;
          $loading_rates = !empty($csvData[14]) ? $csvData[14] : NULL;
          $discharging_rates = !empty($csvData[15]) ? $csvData[15] : NULL;
          $method_of_loading = !empty($csvData[16]) ? $csvData[16] : NULL;
          $method_of_discharging = !empty($csvData[17]) ? $csvData[17] : NULL;
          $frontage_dolphins = !empty($csvData[18]) ? $csvData[18] : NULL;
          $tide_restriction = !empty($csvData[19]) ? $csvData[19] : NULL;
          $night_arrival_sailing_restriction = !empty($csvData[20]) ? $csvData[20] : NULL;
          $average_congestion = !empty($csvData[21]) ? $csvData[21] : NULL;
          $bunkers = !empty($csvData[22]) ? $csvData[22] : NULL;
          $water_availability = !empty($csvData[23]) ? $csvData[23] : NULL;
          $garbage_collection_compulsory = !empty($csvData[25]) ? $csvData[25] : NULL;
          $crushing_capacity = !empty($csvData[25]) ? $csvData[25] : NULL;
          $truck = !empty($csvData[26]) ? $csvData[26] : NULL;

          $wpdb->insert($tablename, array(
            'port' => $port,
            'terminal' => $terminal,
            'dock' => $dock,
            'daily_draft_mts' => $daily_draft_mts,
            'daily_draft_feets' => $daily_draft_feets,
            'water_density' => $water_density,
            'waiting_time' => $waiting_time,
            'waiting_time_projected' => $waiting_time_projected,
            'max_loa' => $max_loa,
            'max_beam' => $max_beam,
            'depth_alognside' => $depth_alognside,
            'max_draft' => $max_draft,
            'airdraft' => $airdraft,
            'storage_capacity' => $storage_capacity,
            'loading_rates' => $loading_rates,
            'discharging_rates' => $discharging_rates,
            'method_of_loading' => $method_of_loading,
            'method_of_discharging' => $method_of_discharging,
            'frontage_dolphins' => $frontage_dolphins,
            'tide_restriction' => $tide_restriction,
            'night_arrival_sailing_restriction' => $night_arrival_sailing_restriction,
            'average_congestion' => $average_congestion,
            'bunkers' => $bunkers,
            'water_availability' => $water_availability,
            'garbage_collection_compulsory' => $garbage_collection_compulsory,
            'crushing_capacity' => $crushing_capacity,
            'truck' => $truck
          ));
        }
      }
    }
  }
}