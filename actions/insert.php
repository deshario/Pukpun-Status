<?php
    require_once("../../../../wp-load.php");
    global $wpdb;
    $statusKey = isset($_POST['statusKey']) ? $_POST['statusKey'] : null;
    $statusLabel = isset($_POST['statusLabel']) ? $_POST['statusLabel'] : null;
    if($statusKey != null && $statusLabel != null){
        $pk_status_tbl = $wpdb->prefix.'pukpun_status';
        $isInsert = $wpdb->insert($pk_status_tbl,array(
          'status_key' => $statusKey,
          'status_value' => $statusLabel,
          'status_custom' => 'true',
        ),array('%s','%s','%s')
        );
        echo $isInsert;
    }else{
        echo 'Add Fail';
    }
?>