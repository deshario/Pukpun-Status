<?php
  wp_register_style('semantic_ui_css', 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css', false, '1.0.0' );
  wp_enqueue_style('semantic_ui_css');
  wp_register_script('semantic_ui_js', 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js', null, null, true );
//   wp_register_script('semantic_ui_js', plugins_url('../assets/semantic.js', __FILE__)); 
  wp_enqueue_script('semantic_ui_js');

  wp_dequeue_script('bootstrap-js');
  wp_dequeue_style('bootstrap-css');
  
?>

<?php
    global $wpdb;
    function get_include_contents($filename) {
        if (is_file($filename)) {
            ob_start();
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }
    $rooms = get_include_contents(plugin_dir_path( __FILE__ ).'../templates/tables.php');
    $events = get_include_contents(plugin_dir_path( __FILE__ ).'../templates/events.php');
?>

    <div class='ui top attached tabular menu'>
        <a class='item active' data-tab='first'>
            <i class='large users icon'></i>Rooms
        </a>
        <a class='item' data-tab='second'>
            <i class='large file alternate outline icon'></i>Events
        </a>
        <a class='item' data-tab='third'>
            <i class='large chart line icon'></i>Reports
        </a>
    </div>
    <div class='ui bottom attached tab segment active' data-tab='first'>
        <?= $rooms; ?>
    </div>
    <div class='ui bottom attached tab segment' data-tab='second'>
        <?= $events; ?>
    </div>
    <div class='ui bottom attached tab segment' data-tab='third'>
    </div>

    <script>
        jQuery(document).ready(() => {
            jQuery('.menu .item').tab();
        });
    </script>