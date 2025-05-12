<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">Confirm Delete</h3>
            </div>
            <div class="card-body">
                <p>Are you sure you want to delete this note?</p>
                <a href="<?php echo base_url('notes/confirmed_delete/'.$this->uri->segment(3)); ?>">
                    <button class="btn btn-danger">Delete</button>
                </a>
                <a href="<?php echo base_url('notes/listing'); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
            <div class="card-footer">
                <p><strong>Note:</strong> This action cannot be undone.</p>
            </div>
        </div>
    </div>
</div>