
<form class="ui form" id='statusForm'>
  <div class="field">
    <label>Status Slug</label>
    <input
    type="text"
    id="slugId"
    placeholder="[wc-slug]"
    style="font-size: 90%; color: #c7254e; background-color: #f9f2f4; border-radius: 4px; cursor: pointer;"
    readOnly/>
  </div>
  <div class="field">
    <label>Status Alias</label>
    <input type="text" placeholder="Alias Name" id="slugVal" oninput="updateInput(this.value)"/>
    <div class="ui pointing blue basic label">
      No whitespace or special characters allowed.
    </div>
  </div>
  <div class="field" style="margin-bottom:40px;">
    <button
      type="button"
      id = "saveBtn"
      style="padding-top:7px;"
      class="ui icon green button right floated"
      onclick="validateForm()">
        <i class="save icon"></i> Save
    </button>
  </div>
</form>

<script>
function updateInput(alias){
  let keySlug = alias == '' ? '' : '[wc-'+alias.replace(/\s/g, '-')+']';
  document.getElementById("slugId").value = keySlug;
}

const validateForm = () => {
  let slugKey = document.getElementById("slugId").value;
  if(slugKey == '' || slugKey == null){
    alert('Invalid Form. Please try again');
  }else if(slugKey.length < 9){ //9 == 4
    alert('Alias is too short. Please try again');
  }else{
    document.getElementById("statusForm").classList.add("loading");
    document.getElementById("saveBtn").disabled = true;
    let slugVal = document.getElementById("slugVal").value
    save(slugKey,slugVal);
  }
}

const save = (key, value) => {
  jQuery.ajax({
    url: "<?php echo plugin_dir_url(__DIR__).'/actions/insert.php'; ?>",
    type: "POST",
    data: {statusKey:key,statusLabel:value},
    success: function (response){
      if(response == 1){
        location.reload();
      }else{
        alert('Something went wrong');
        console.warn(response);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);
    }
  });
}

</script>