<?php
  /**
   * Uninstall PukPun Status
   *
   * @package   PukPun Status
   * @author    Deshario Sunil
   */


  if ( ! defined( 'ABSPATH' ) ) {
    exit();
  }

  if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; // Exit if accessed directly
  }

  global $wpdb;
  $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pukpun_status");