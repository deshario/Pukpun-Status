<style>
    .code{
        padding: 2px 4px;
        font-size: 90%;
        color: #c7254e;
        background-color: #f9f2f4;
        border-radius: 4px;
        cursor:pointer;
    }
    .update-nag{display:none;}
    body{background:#f5f5f5;}
</style>

<div class="ui one column grid" style="margin-right:5px; margin-top:5px; padding:10px;">
    <table class="ui very basic collapsing celled table" style="width:100%">
        <thead class="full-width">
            <tr>
                <th style='width:50px;'></th>
                <th>Slug</th>
                <th>Alias</th>
                <th class="one wide" style="text-align:center">Options</th>
            </tr>
        </thead>
        <tbody>
            <script>var eachResult = [];</script>
            <?php

                function getLabelColor($key){
                    switch($key){
                        case '[wc-pending]':
                            return 'ui grey label';
                        case '[wc-processing]':
                            return 'ui teal label';
                        case '[wc-checking-payment]':
                            return 'ui blue label';
                        case '[wc-completed]':
                            return 'ui green label';
                        case '[wc-failed]':
                        case '[wc-cancelled]':
                            return 'ui red label';
                        case '[wc-on-hold]':
                            return 'ui orange label';
                        case '[wc-refunded]':
                            return 'ui olive label';
                        default:
                            return 'ui default label';
                    }
                }
                
                function isDisabled($isCustomStatus){
                    if($isCustomStatus == 'false'){
                        return 'disabled';
                    }
                }

                global $wpdb;
                $tbl_pukpun_status = $wpdb->prefix.'pukpun_status';
                $result = $wpdb->get_results("SELECT * FROM $tbl_pukpun_status");
                $iterator = 1;
                foreach($result as $result){ ?>
                    <script>
                        eachResult.push(`<?php echo json_encode($result); ?>`);
                    </script>
                    <tr>
                        <td style='text-align:center'><?= $iterator; ?></td>
                        <td style='height: 40px;'><code class='code'><?= $result->status_key; ?></code></td>
                        <td><a class="<?= getLabelColor($result->status_key) ?>"><?= $result->status_value; ?></a></td>
                        <td style="text-align:center;">
                            <button style="padding-top:7px;" class="ui icon button"
                                onclick="viewStatus(eachResult[`<?= $iterator-1; ?>`])">
                                <i class="pencil icon"></i>
                            </button>
                            <button style="padding-top:7px;" class="ui icon button" <?= isDisabled($result->status_custom); ?>
                                onclick="deleteStatus(eachResult[`<?= $iterator-1; ?>`])">
                                <i class="trash icon"></i>
                            </button>
                        </td>
                    </tr>
                <?php $iterator++; }
            ?>
        </tbody>
    </table>
</div>

<div class="ui tiny modal editStatus">
  <i class="close icon"></i>
  <div class="header" style="line-height:1;"></div>
  <div class="content">
    <form class="ui form">
        <div class="field">
            <label>Alias</label>
            <input type="text" name="statusLabel"/>
            <input type="hidden" name="statusKey"/>
            <input type="hidden" name="statusId"/>
        </div>
    </form>
  </div>
  <div class="actions">
    <div class="ui cancel button" style="padding-top:8px;">Cancel</div>
    <div class="ui button" style="padding-top:8px;" onclick="updateStatus()">OK</div>
  </div>
</div>

<div class="ui tiny modal delStatus">
  <i class="close icon"></i>
  <div class="header">Are you sure want to delete ?</div>
  <div class="image content">
  <label>◉  All orders that used this status gonna be replace with <code class='code'>on-hold</code></label>
  </div>
  <div class="actions">
      <div class="ui negative button" style="padding-top:7px;">Dismiss</div>
      <div class="ui positive button" style="padding-top:7px;">Delete</div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
    jQuery.noConflict();
    const viewStatus = (statusObj) => {
        statusObj = JSON.parse(statusObj);
        console.log(statusObj);
        jQuery('.header').html(`<code class='code'>${statusObj.status_key}</code>`);
        jQuery("input[name='statusLabel']").attr('placeholder',statusObj.status_value);
        jQuery("input[name='statusId']").val(statusObj.status_id);
        jQuery("input[name='statusKey']").val(statusObj.status_key);
        jQuery("input[name='statusLabel']").val(statusObj.status_value);
        jQuery('.editStatus').modal('show');
    }

    const updateStatus = () => {
        let sId = jQuery("input[name='statusId']").val();
        let sKey = jQuery("input[name='statusKey']").val();
        let sLabel = jQuery("input[name='statusLabel']").val();
        if(sId != '' && sKey != '' && sLabel != '' ){
            jQuery.ajax({
                url: "<?php echo plugin_dir_url( __FILE__ ).'../actions/update.php'; ?>",
                type: "POST",
                data : {
                    statusId : sId,
                    statusKey : sKey,
                    statusLabel : sLabel,
                },
                success: function (response){
                    if(response == 1){
                        jQuery('.tiny.modal').modal('hide');
                        location.reload(true);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                }
            });
        }else{
            alert('Invalid Alias');
        }
    }

    const deleteStatus = (statusObj) => {
        statusObj = JSON.parse(statusObj);
        let isCustom = statusObj.status_custom;
        if(isCustom == 'false' || isCustom == false){
            alert('Permission Denied');
        }else{
            jQuery('.delStatus')
            .modal({
                closable : false,
                onDeny  : function(){
                    return true;
                },
                onApprove : function() {
                    delGranted(statusObj);
                }
            }).modal('show');
        }
    }

    const delGranted = (statusObj) => {
        let status_id = parseInt(statusObj.status_id);
        let status_key = statusObj.status_key;
        jQuery.ajax({
            type: "POST",
            url: "<?php echo plugin_dir_url( __FILE__ ).'../actions/delete.php'; ?>",
            data: {action: 'delete', status_id, status_key},
            success: function (data, status){
                jQuery('.deleteHub').modal('hide');
                let result = parseInt(data);
                if(result != 1){
                    alert('Something went wrong');
                }
                location.reload();
            }
        });
    }

</script>