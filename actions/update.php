<?php
    require_once("../../../../wp-load.php");
    global $wpdb;
    $statusId = isset($_POST['statusId']) ? $_POST['statusId'] : null;
    $statusKey = isset($_POST['statusKey']) ? $_POST['statusKey'] : null;
    $statusLabel = isset($_POST['statusLabel']) ? $_POST['statusLabel'] : null;
    if($statusId != null && $statusKey != null && $statusLabel != null){
        $pk_status_tbl = $wpdb->prefix.'pukpun_status';
        $affected = $wpdb->query("UPDATE $pk_status_tbl SET status_value = '$statusLabel' WHERE status_id = $statusId AND status_key = '$statusKey'");
        echo $affected;
    }else{
        echo 'status not ok';
    }
?>