<!-- <div class='card'> -->
    <!-- <div class="card-body"> -->
        <div id="meeting-notes">
            <div class="row border-bottom">
                <div class="col-md-6">
                    <img src="assets/images/logo-NETSITEWEAVER-CLICKED-200px5.jpg" alt="" class="mb-3 mt-3">
                </div>
                <div class="col-md-6 text-right">
                    <img src="assets/images/LOGO-TWEEZZO-HORIZONTAL-TRANSPARENT-50PX-TextLight.png" alt="" style="height:40px;" class="mb-3 mt-3">
                </div>
            </div>
            
            <h2 class='text-center'>Meeting Minutes</h2>
            <p><strong>Customer:</strong> <?= $note->customer_name ?></p>
            <p><strong>Date & Time:</strong> <?= substr($note->meeting_datetime,0,10) ?> @ <?= substr($note->meeting_datetime,11,5) ?></p>
            <p><strong>Attendees:</strong> <?= str_replace("\r\n", ", ", $note->attendees) ?></p>
            <p class="border-bottom pb-3"><strong>Lieu:</strong> <?= ucwords($note->lieu) ?></p>

            <p class="text-bold">Notes:</p>
            <?= $note->notes ?>
            <?php if (!empty($note->next_meeting_date)): ?>
            <p class="mt-3"><strong>Next Meeting:</strong> <?= $this->Meeting_note_model->formatNextMeeting($note->next_meeting_date) ?></p>
            <?php endif; ?>
        </div>
   <!-- </div> -->
<!-- </div> -->
<div class="btn btn-danger" onclick="downloadPDF()"><i class="fa fa-download"></i> Download PDF</div>
