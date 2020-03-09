<?php
/**
 * Plugin Name: PukPun Status
 * Plugin URI:  https://github.com/deshario/wocommerce-custom-status
 * Description: Modify woocommerce status label to your own alias
 * Version:     1.0
 * Author:      Deshario Sunil
 * Author URI:  https://github.com/deshario
 * Text Domain: deshario
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

  if (!defined( 'ABSPATH')){
    exit; // Exit if accessed directly
  }

  function activate_pukpun_status(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tbl_pukpun_status = $wpdb->prefix.'pukpun_status';
    if($wpdb->get_var("SHOW TABLES LIKE '$tbl_pukpun_status'") != $tbl_pukpun_status){
      $queryHub = "CREATE TABLE $tbl_pukpun_status (
        status_id int(11) NOT NULL AUTO_INCREMENT,
        status_key varchar(255) NOT NULL,
        status_value varchar(255) NOT NULL,
        UNIQUE KEY (status_id)
      );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($queryHub);
      $wpdb->query("ALTER TABLE $tbl_pukpun_status CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
      $wpdb->query("INSERT INTO $tbl_pukpun_status (`status_id`, `status_key`, `status_value`) VALUES
      (1, '[wc-pending]', 'Pending payment'),
      (2, '[wc-processing]', 'Processing'),
      (3, '[wc-checking-payment]', 'Checking Payment'),
      (4, '[wc-on-hold]', 'On hold'),
      (5, '[wc-completed]', 'Completed'),
      (6, '[wc-cancelled]', 'Cancelled'),
      (7, '[wc-refunded]', 'Refunded'),
      (8, '[wc-failed]', 'Failed');");
    }
  }
  register_activation_hook( __FILE__, 'activate_pukpun_status');

  add_action('admin_menu', 'initializePlugin');
  function initializePlugin(){
    if (!is_plugin_active('woocommerce/woocommerce.php') && !class_exists('WooCommerce')){
      add_action('admin_notices',function(){
        echo '<div id="message" class="error notice is-dismissible">
          <p>Pukpun Status is enabled, but it requires <b>WooCommerce</b> in order to work properly.</p>
        </div>';
      });
    }
    init_pukpunStatusMenu();
  }

  function init_pukpunStatusMenu(){
    add_menu_page(
      'Pukpun Status', 'PukPun Status', 'manage_options', 'pukpun_status', 'statusIndex', 'dashicons-image-filter' 
    );
  }

  add_action('admin_enqueue_scripts','pukpun_status_style'); 
  function pukpun_status_style(){
    $page = isset($_GET['page']) ? $_GET['page'] : null;
    if($page != null){
      if($page == 'pukpun_status'){
        wp_register_style('semantic_ui_css', 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css', false, '1.0.0' );
        wp_enqueue_style('semantic_ui_css'); 
        wp_register_script('semantic_ui_js', 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js', null, null, true );
        wp_enqueue_script('semantic_ui_js');
      }
    }
  }

  function statusIndex(){
    include(plugin_dir_path( __FILE__ ).'/templates/pk-status.php');
  }
  
  add_filter( 'wc_order_statuses', 'wc_renaming_order_status' );
  function wc_renaming_order_status($order_statuses){
    global $wpdb;
    $tbl_pukpun_status = $wpdb->prefix.'pukpun_status';
    $results = $wpdb->get_results("SELECT * FROM $tbl_pukpun_status");
    foreach ($order_statuses as $key => $status){
      foreach($results as $eResult){
        $eachKey = trim($eResult->status_key,"[]");
        if($key == $eachKey){
          $order_statuses[$eachKey] = _x($eResult->status_value, 'Order status', 'woocommerce' );
        }
      }
    }
    return $order_statuses;
  }

  add_filter( 'bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1 );
  function custom_dropdown_bulk_actions_shop_order($actions){
    global $wpdb;
    $tbl_pukpun_status = $wpdb->prefix.'pukpun_status';
    $results = $wpdb->get_results("SELECT * FROM $tbl_pukpun_status");
    foreach ($results as $key => $eachStatus){
      $status = trim($eachStatus->status_key,"[]");
      $sData = explode("-",$status);
      $counter = count($sData);
      $res = $counter == 3 ? $sData[1].'-'.$sData[2] : $sData[1];
      $processingLabel = explode("to ",$actions['mark_processing']);
      $onHoldLabel = explode("to ",$actions['mark_on-hold']);
      $onCompleteLabel = explode("to ",$actions['mark_completed']);
      if($res == $processingLabel[1]){
        $actions['mark_processing'] = str_replace('processing', __($eachStatus->status_value, 'woocommerce'),$actions['mark_processing']);
      }
      if($res == $onHoldLabel[1]){
        $actions['mark_on-hold'] = str_replace('on-hold', __($eachStatus->status_value, 'woocommerce'),$actions['mark_on-hold']);
      }
      if($res == $onCompleteLabel[1]){
        $actions['mark_completed'] = str_replace('completed', __($eachStatus->status_value, 'woocommerce'),$actions['mark_completed']);
      }
    }
    return $actions;
  }

  foreach(array('post', 'shop_order') as $hook)
  add_filter( "views_edit-$hook", 'shop_order_modified_views' );

  function shop_order_modified_views($views){
    global $wpdb;
    $tbl_pukpun_status = $wpdb->prefix.'pukpun_status';
    $results = $wpdb->get_results("SELECT * FROM $tbl_pukpun_status");
    foreach ($results as $key => $eachStatus){
      $status = trim($eachStatus->status_key,"[]");
      if(isset($views[$status])){
        $views[$status] = str_replace('Pending payment', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('Processing', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('Checking Payment', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('On hold', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('Completed', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('Cancelled', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('Refunded', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
        $views[$status] = str_replace('Failed', __($eachStatus->status_value, 'woocommerce'), $views[$status]);
      }
    }
    return $views;
  }
  
?>