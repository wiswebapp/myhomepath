<div class="modal fade" id="addWalletModel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Money to <span id="walletusertype"></span> Wallet</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" name="userId" value="" id="walletUserId">
          <div class="form-group">
            <label>Enter Amount</label>
            <input type="number" id="addWalletIp" class="form-control" placeholder="0" required="" title="Add Money Value you want to add">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="button" id="walletmdlbtn" class="btn btn-primary">Add Amount</button>
      </div>
    </div>
  </div>
</div>