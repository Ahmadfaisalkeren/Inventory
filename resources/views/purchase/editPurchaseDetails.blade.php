<div class="modal fade" id="editPurchaseDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="card-title fs-5" id="exampleModalLabel">Edit Purchase Detail</h1>
        </div>
        <div class="modal-body">
            <form id="updateQuantityForm" name="updateQuantityForm">
                <input type="hidden" name="purchaseDetailId" id="purchaseDetailId">
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" name="newQuantity" id="newQuantity" class="form-control" value="" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm" id="saveNewQuantity">Update Qty</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
