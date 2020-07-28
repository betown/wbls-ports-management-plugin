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
        terminal tinytext NOT NULL,
        daily_draft_mts tinytext NOT NULL,
        daily_draft_feets tinytext NOT NULL,
        water_density tinytext NOT NULL,
        waiting_time tinytext NOT NULL,
        waiting_time_projected tinytext NOT NULL,
        max_loa tinytext NOT NULL,
        max_beam tinytext NOT NULL,
        depth_alognside tinytext NOT NULL,
        max_sailing_draft tinytext NOT NULL,
        airdraft tinytext NOT NULL,
        storage_capacity tinytext NOT NULL,
        loading_rate tinytext NOT NULL,
        method_of_loading tinytext NOT NULL,
        frontage tinytext NOT NULL,
        tide_restriction tinytext NOT NULL,
        sailing_restriction tinytext NOT NULL,
        average_congestion tinytext NOT NULL,
        bunkers tinytext NOT NULL,
        water_availability tinytext NOT NULL,
        garbage_disposal tinytext NOT NULL,
        truck tinyint NOT NULL,
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
      port tinytext NOT NULL,
      terminal tinytext NOT NULL,
      vessel_name tinytext NOT NULL,
      vessel_type tinytext NOT NULL,
      eta tinytext NOT NULL,
      comodity tinytext NOT NULL,
      quantity tinytext NOT NULL,
      operation tinytext NOT NULL,
      destination tinytext NOT NULL,
      PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('wbls_ports_db_version', $wbls_lineup_db_version);

    }
  }

  // run the install scripts upon plugin activation