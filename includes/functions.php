<?php

  function update_row_if_exists($tablename, $cells, $condition) {
    global $wpdb;

    return $wpdb->update($tablename, $cells, $condition);
  }

  function manage_file_upload() {
    if(!empty($_FILES) && array_key_exists('wbls_ports_file', $_FILES) && file_exists($_FILES['wbls_ports_file']['tmp_name'])){
      global $wpdb;
      $tablename = $wpdb->prefix . 'wbls_ports';

      $uploadedFile = $_FILES['wbls_ports_file'];
      $file = fopen($uploadedFile['tmp_name'], 'r');
      $csv = fgetcsv($file);

      while(($csvData = fgetcsv($file)) != FALSE) {

        $port = $csvData[0];
        $terminal = $csvData[1];
        $daily_draft_mts = $csvData[2];
        $daily_draft_feets = $csvData[3];
        $water_density = $csvData[4];
        $waiting_time = $csvData[5];
        $waiting_time_projected = $csvData[6];
        if(!empty($csvData[7])){
          $port_information = $csvData[7];
        }
        else {
          $port_information = "";
        };
        $truck = $csvData[8];

        $cellsToUpdate = array(
          'daily_draft_mts' => $daily_draft_mts,
          'daily_draft_feets' => $daily_draft_feets,
          'waiting_time' => $waiting_time,
          'waiting_time_projected' => $waiting_time_projected,
          'port_information' => $port_information,
          'truck' => $truck
        );

        $updateCondition = array(
          'port' => $port,
          'terminal' => $terminal
        );

        if(!update_row_if_exists($tablename, $cellsToUpdate, $updateCondition)) {
          $wpdb->insert($tablename, array(
            'port' => $port,
            'terminal' => $terminal,
            'daily_draft_mts' => $daily_draft_mts,
            'daily_draft_feets' => $daily_draft_feets,
            'water_density' => $water_density,
            'waiting_time' => $waiting_time,
            'waiting_time_projected' => $waiting_time_projected,
            'port_information' => $port_information,
            'truck' => $truck
          ));
        }
      }
    }

    if(!empty($_FILES) && array_key_exists('wbls_lineup_file', $_FILES) && file_exists($_FILES['wbls_lineup_file']['tmp_name'])){
      global $wpdb;
      $tablename = $wpdb->prefix . 'wbls_lineup';
      $uploadedFile = $_FILES['wbls_lineup_file'];
      $file = fopen($uploadedFile['tmp_name'], 'r');
      $csv = fgetcsv($file);
      $wpdb->query('TRUNCATE TABLE ' . $tablename);


      while(($csvData = fgetcsv($file)) != FALSE){

        $port = $csvData[0];
        $terminal = $csvData[1];
        $vessel = $csvData[2];
        $eta = $csvData[3];
        $comodity = $csvData[4];
        $quantity = $csvData[5];
        $operation = $csvData[6];
        $destination = $csvData[7];

        $wpdb->insert($tablename, array(
          'port' => $port,
          'terminal' => $terminal,
          'vessel' => $vessel,
          'eta' => $eta,
          'comodity' => $comodity,
          'quantity' => $quantity,
          'operation' => $operation,
          'destination' => $destination,
        ));
      }
    }
  }