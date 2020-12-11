<?php

  // function to create the DB / Options / Defaults
  function wbls_port_db_install() {
    global $wpdb;
    $wbls_ports = $wpdb->prefix . 'wbls_ports';
    $wbls_ports_db_version = 1.0;

    if($wpdb->get_var("show tables like '$wbls_ports'") != $wbls_ports)
    {
      $charset_collate = $wpdb->get_charset_collate();
      $sql = "CREATE TABLE $wbls_ports (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        port tinytext NOT NULL,
        terminal tinytext,
        dock tinytext,
        daily_draft_mts tinytext,
        daily_draft_feets tinytext,
        water_density tinytext,
        waiting_time tinytext,
        waiting_time_projected tinytext,
        max_loa tinytext,
        max_beam tinytext,
        depth_alognside tinytext,
        max_draft tinytext,
        airdraft tinytext,
        storage_capacity tinytext,
        loading_rates tinytext,
        discharging_rates tinytext,
        method_of_loading tinytext,
        method_of_discharging tinytext,
        frontage_dolphins tinytext,
        tide_restriction tinytext,
        night_arrival_sailing_restriction tinytext,
        average_congestion tinytext,
        bunkers tinytext,
        water_availability tinytext,
        garbage_collection_compulsory tinytext,
        crushing_capacity tinytext,
        truck tinyint,
        PRIMARY KEY (id)
        ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('wbls_ports_db_version', $wbls_ports_db_version);

    }
  }

  function wbls_lineup_db_install() {
    global $wpdb;
    $wbls_lineup = $wpdb->prefix . 'wbls_lineup';
    $wbls_lineup_db_version = 1.0;

    if($wpdb->get_var("show tables like ' $wbls_lineup'") != $wbls_lineup ){
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $wbls_lineup (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      port tinytext,
      dock tinytext,
      terminal tinytext,
      vessel_name tinytext,
      vessel_type tinytext,
      eta tinytext,
      comodity tinytext,
      quantity tinytext,
      operation tinytext,
      destination tinytext,
      PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('wbls_ports_db_version', $wbls_lineup_db_version);

    }
  }

  // run the install scripts upon plugin activation