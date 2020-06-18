<?php
   global $wpdb;
   $roomTbl = $wpdb->prefix.'de_meeting_rooms';
   $rooms = $wpdb->get_results("SELECT * FROM $roomTbl");
?>

  <table class='ui striped celled table'>
    <thead>
      <tr>
        <th>#</th>
        <th>Room Name</th>
        <th>Room Description</th>
        <th>Room Creator</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
        <?php
            foreach($rooms as $eachRoom){ ?> 
        <tr>
            <td>1</td>
            <td><?= $eachRoom->room_name; ?></td>
            <td><?= $eachRoom->room_description; ?></td>
            <td><?= $eachRoom->room_creator; ?></td>
            <td class='center aligned'>
                <i onclick="createEditRoom(
                    '<?= $eachRoom->room_id; ?>',
                    '<?= $eachRoom->room_name; ?>',
                    '<?= $eachRoom->room_description; ?>')"
                    class='large edit icon'></i>
                <i class='large trash icon'></i>
            </td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot class='full-width'>
        <tr>
        <th></th>
        <th colspan='4'>
            <div class='ui right floated small primary labeled icon button' onclick='createEditRoom()'>
                <i class='edit icon'></i> Create
            </div>
        </th>
        </tr>
    </tfoot>
  </table>

  <div class="ui tiny modal manageRoom" style="top:unset; left:unset; right:unset; bottom:unset;">
   <i class="close icon"></i>
   <div class="header manageRoomTitle"></div>
   <div class="content">
        <form class="ui form" style="margin-bottom:0">
            <div class="field">
                <label>Room Name</label>
                <input type="text" name="roomName">
            </div>
            <div class="field">
                <label>Room Description</label>
                <input type="text" name="roomDesc">
            </div>
        </form>
   </div>
   <div class="actions">
      <button class="ui negative button" type="submit">Dismiss</button>
      <button class="ui positive button" type="submit">Submit</button>
   </div>
</div>

<script type="text/javascript"> 

    const createEditRoom = (id,name,desc) => {

        let isCreate = id == undefined || name == undefined || desc == undefined;
        let nameElem = document.querySelector('input[name="roomName"]');
        let descElem = document.querySelector('input[name="roomDesc"]');

        nameElem.value = isCreate ? '' : name;
        descElem.value = isCreate ? '' : desc;
        
        document.querySelector('.manageRoomTitle').innerText = isCreate ? 'Create Room' : 'Edit Room';

        jQuery('.manageRoom').modal({
            centered: true,
            onDeny: function(){
                return true;
            },
            onApprove: function() {
                let readyToInsert = nameElem.value != '' && descElem.value != '';
                if(readyToInsert){
                    if(isCreate){ // create
                        console.log('create');
                    }else{ // update
                        console.log('update : ',id);
                    }
                }
                console.log('readyToInsert : ',readyToInsert);
                return readyToInsert;
            }
        }).modal('show');
    }

</script>