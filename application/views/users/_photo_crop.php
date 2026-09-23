<?php
/**
 * Preview + crop for the user photo field.
 * Include once on any page holding an <input type="file" data-photo-crop>.
 */
?>
<style>
    .photo-crop-preview-box {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        overflow: hidden;
        background: #f4f4f4;
        border: 1px solid #ddd;
    }
    .photo-crop-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .photo-crop-stage {
        max-height: 60vh;
    }
    /* Cropper needs a block-level image it can measure. */
    .photo-crop-stage img {
        display: block;
        max-width: 100%;
    }
</style>

<div class="row photo-crop-preview d-none mt-2">
    <div class="col-12">
        <label>New photo preview</label>
        <div class="photo-crop-preview-box">
            <img class="photo-crop-preview-img" src="" alt="Preview of the photo about to be uploaded">
        </div>
        <div class="mt-2">
            <button type="button" class="btn btn-xs btn-info photo-crop-open">
                <i class="fa fa-crop"></i> Crop
            </button>
            <button type="button" class="btn btn-xs btn-default photo-crop-clear">
                <i class="fa fa-times"></i> Clear
            </button>
            <span class="photo-crop-meta text-muted small ml-2"></span>
        </div>
    </div>
</div>

<div class="modal fade" id="photoCropModal" tabindex="-1" role="dialog" aria-labelledby="photoCropModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="photoCropModalLabel">Crop photo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="photo-crop-stage">
                    <img id="photoCropImage" alt="Photo being cropped">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default" data-crop-action="zoom-in" title="Zoom in"><i class="fa fa-search-plus"></i></button>
                    <button type="button" class="btn btn-sm btn-default" data-crop-action="zoom-out" title="Zoom out"><i class="fa fa-search-minus"></i></button>
                    <button type="button" class="btn btn-sm btn-default" data-crop-action="rotate-left" title="Rotate left"><i class="fa fa-undo"></i></button>
                    <button type="button" class="btn btn-sm btn-default" data-crop-action="rotate-right" title="Rotate right"><i class="fa fa-redo"></i></button>
                    <button type="button" class="btn btn-sm btn-default" data-crop-action="reset" title="Reset"><i class="fa fa-sync"></i></button>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-info" id="photoCropApply"><i class="fa fa-check"></i> Apply crop</button>
                </div>
            </div>
        </div>
    </div>
</div>
