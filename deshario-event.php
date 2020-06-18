<?php
/**
 * Plugin Name: Deshario Events
 * Plugin URI:  https://github.com/deshario/wocommerce-status-manager
 * Description: Create and manage events
 * Version:     1.0
 * Author:      Deshario Sunil
 * WC tested up to: 3.6.5
 * Author URI:  https://github.com/deshario
 * Text Domain: deshario
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

  if (!defined( 'ABSPATH')){
    exit; // Exit if accessed directly
  }

  function activiate_deshario_event(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tbl_meeting_rooms = $wpdb->prefix.'de_meeting_rooms';

    if($wpdb->get_var("SHOW TABLES LIKE '$tbl_meeting_rooms'") != $tbl_meeting_rooms){
      $createRoom = "CREATE TABLE $tbl_meeting_rooms (
        room_id int(11) NOT NULL AUTO_INCREMENT,
        room_name varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        room_description varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        room_creator int(11) NOT NULL,
        UNIQUE KEY (room_id)
      );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($createRoom);
      $wpdb->query("INSERT INTO $tbl_meeting_rooms (`room_id`, `room_name`, `room_description`, `room_creator`) VALUES
      (1, 'Room 1', 'Room 1 Descrition',1),
      (8, 'Room 2', 'Room 2 Descrition',1);");
    }
  }
  register_activation_hook( __FILE__, 'activiate_deshario_event');

  add_shortcode('deshario_event', 'deshario_event');
  function deshario_event(){
    ob_start();
    include(plugin_dir_path( __FILE__ ).'/shortcode/deshario_event.php');
    $layout = ob_get_clean();
    return $layout;
  }
   

?>