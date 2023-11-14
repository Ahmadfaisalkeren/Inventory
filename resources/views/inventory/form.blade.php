<div class="modal fade" id="formModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="inventoryForm" name="inventoryForm" class="form-horizontal">
                    <input type="hidden" name="inventory_id" id="inventory_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="price" class="col-sm-2 control-label">Price</label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="price" name="price"
                                        placeholder="Price" value="" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="stock" class="col-sm-2 control-label">Stock</label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="stock" name="stock"
                                        placeholder="Stock" value="" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveInventory"><i class="far fa-save"></i> Save</button>
                            <button type="submit" class="btn btn-primary" id="updateInventory"><i class="far fa-save"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
