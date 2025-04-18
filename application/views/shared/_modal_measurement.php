<div class="modal fade" style='font-size:1.4em;' id="measurementModal" tabindex="-1" role="dialog" aria-labelledby="measurementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Input Measurement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label style='font-size:1.2em;'><strong id="zone"></strong><span class='hidden' id='z'></span></label>
          <input type="text" class="form-control input-lg" name="measurement" style='font-size:1.2em;' placeholder="Enter Measurement">
        </div>
        <div class="form-group">
          <label>Remarks</label>
          <textarea name="measurements_remarks" class='form-control' rows="2" style='font-size:1.2em;' placeholder="You may enter some notes/remarks here"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveMeasurement">Save</button>
      </div>
    </div>
  </div>
</div>