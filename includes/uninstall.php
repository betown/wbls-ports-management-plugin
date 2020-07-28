<?php
  if ( ! current_user_can( 'activate_plugins' ) ) {
    return;
  }
  check_admin_referer( 'bulk-plugins' );

  if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
    return;
  }

  global $wpdb;

  $ports_db = $wpdb->prefix . 'wbls_ports';
  $lineup_db = $wpdb->prefix . 'wbls_lineup';

  $wpdb->query("DROP TABLE IF EXISTS $ports_db");
  $wpdb->query("DROP TABLE IF EXISTS $lineup_db");