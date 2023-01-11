<div class="modal fade" id="extraWorksModal" tabindex="-1" role="dialog" aria-labelledby="extraWorksModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="extraWorksModalLabel">Pilih Penggarap</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
          <div id="extraWorks">
          </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary simpan">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>

function extraWorkers(id) {
  var extra_workers = [];
  
  $(".cbextra:checkbox:checked").each(function () {
    extra_workers.push($(this).val());
  })

  
  $(document).ready(function () {
    $('.simpan').click(function (e) { 
      e.preventDefault();
      
      var transaction_id = $("#transaction_id").val();

      console.log(extra_workers);

      $.ajax({
        type: "post",
        url: "/transaction-extraworkers",
        data: {
          extra: extra_workers,
          id: transaction_id,
        },
        success: function (response) {
          
        }
      });

    });
  });
  
  
}

</script>