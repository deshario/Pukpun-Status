<?php
  require_once("../../../../wp-load.php");
  global $wpdb;
  $action = isset($_POST['action']) ? $_POST['action'] : null;
  $status_id = isset($_POST['status_id']) ? $_POST['status_id'] : null;
  $status_key = isset($_POST['status_key']) ? $_POST['status_key'] : null;
  if($action == 'delete' && $status_id != null && $status_key != null){
    $mStatusTbl = $wpdb->prefix.'pukpun_status';
    $key = trim($status_key,"[]");
    $args = array(
      'limit' => -1,
      'type'=> 'shop_order',
      'post_status' => array($key)
    );
    $orders = wc_get_orders($args);
    $readyToDelete = count($orders) > 0 ? false : true;
    if(!$readyToDelete){
      foreach($orders as $eachOrder){
        $order = new WC_Order($eachOrder->id);
          if(!empty($order)){
            $order->update_status('on-hold');
          }
      }
      $readyToDelete = true;
    }
    $result = $readyToDelete ? 1 : 0;
    if($result == 1){
      $wpdb->delete($mStatusTbl, array('status_id' => $status_id));
    }
    echo $result;
  }
?>