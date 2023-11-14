<div class="modal fade" id="formModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="salesForm" name="salesForm" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="sales_id" id="sales_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="number" class="col-sm-2 control-label">Number</label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="number" name="number"
                                         value="{{ $number }}" required="" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="date" class="col-sm-2 control-label">Date</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="date" name="date"
                                         value="{{ $date }}" required="" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user_id" class="col-sm-2 control-label">User</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="user_id" name="user_id"
                                         value="{{ $user_id }}" required="" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveSales">Save</button>
                            <button type="submit" class="btn btn-primary" id="updateSales">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
