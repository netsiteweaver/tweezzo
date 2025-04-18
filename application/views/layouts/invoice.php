<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo isset($page_title)?$page_title:"";?> | <?php echo $company->name; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <base href="<?php echo base_url();?>">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/font-awesome/css/font-awesome.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
  <style> @import url('https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Roboto:wght@400;700;900&display=swap'); </style>
  
  <style>
    .barcode{
      font-family: 'Libre Barcode 128', cursive;
      font-size: 48px;
    }
    .roboto{
      font-family: 'Roboto', sans-serif;
    }
    .roboto.font-weight-400{
      font-weight: 400;
    }
    .roboto.font-weight-700{
      font-weight: 700;
    }
    .roboto.font-weight-900{
      font-weight: 900;
    }
    .container{
      border:1px solid #eee;
      min-height:100vh;
    }
    .row.border-bottom{
      border-bottom:1px solid #ccc;
      margin-bottom:20px;
    }
    .red{
      color: #FF0000;
    }
    tr>th{
      text-align:center;
      font-weight:bold;
    }
    .bordered{
      border: 1px solid #ccc;
      padding:5px;
    }
    .ml-20{
      margin-left: 20px;
    }
    footer{
      display: none;
    }
    @media print {
      .btn {
        display: none;
      }
      footer{
        display: block;
        position:fixed;
        width:100%;
        bottom:25px;
        right:25px;
        border-top:1px solid #ccc;
        text-align: right;
        font-size:0.8em;
        color:#ccc;
      }
      a{display:none}
      #qrcode{
        border:2px solid #ccc;
        padding: 5px;
        width:2cm;
        height:2cm;
      }
    }
  </style>

  <script>
    var base_url = "<?php echo base_url();?>";
  </script>
</head>
<body>
  <div class="container" id='elem'>
    <div class="row border-bottom">
      <div class="col-xs-4">
        <img style='width:100%' src="./uploads/logo/<?php echo $logo;?>" alt="Logo">
      </div>
      <div class="col-xs-4">
        <div id="qrcode" style='float: right; margin-top: 10px;margin-bottom:10px;'></div>
      </div>
      <div class="col-xs-4">
        <h1 class="text-right roboto font-weight-900">INVOICE</h1>
        <div class="hidden" id="uuid">$1$<?php echo $invoice->uuid;?></div>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-4">
        <div class="row">
          <div class="col-xs-12 bordered ml-20">
            <b>Client</b>
            <p><?php echo nl2br($invoice->customer_details);?></p>
          </div>
        </div>
      </div>
      <div class="col-xs-4"></div>
      <div class="col-xs-4">
        <input type="hidden" name="referer" value="<?php echo $_SERVER['QUERY_STRING'];?>">
        <div class="row"><div class="col-xs-6 text-bold"><b>Number:</b></div><div class="col-xs-6 invoice-number"><?php echo $invoice->document_number;?></div></div>
        <div class="row"><div class="col-xs-6 text-bold"><b>Date:</b></div><div class="col-xs-6"><?php echo date_format(date_create($invoice->invoice_date),'d/m/Y');?></div></div>
        <div class="row"><div class="col-xs-6 text-bold"><b>Sales Agent:</b></div><div class="col-xs-6"><?php echo $invoice->agent;?></div></div>
        <!-- <div class="row"><div class="col-xs-6 text-bold"><b>Delivered on:</b></div><div class="col-xs-6"><?php //echo $invoice->agent;?></div></div> -->
        <!-- <div class="row"><div class="col-xs-6 text-bold"><b>Delivered by:</b></div><div class="col-xs-6"><?php //echo $invoice->agent;?></div></div> -->
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-xs-12 table-responsive">
          <table class="table table-bordered">
              <thead>
                  <tr>
                      <th>ITEM</th>
                      <th>QUANTITY</th>
                      <th>PRICE</th>    
                      <th>AMOUNT</th>                     
                  </tr>
              </thead>
              <tbody>
                  <?php $total_price=0; ;?>
                  <?php foreach ($invoice->sale_details as $row) : ?>
                  <?php $total_price += (floatval($row->quantity) * floatval($row->price));?>
                  <tr>
                      <td><?php echo $row->description; ?></td>
                      <td class='text-right'><?php echo $row->quantity; ?></td>
                      <td class='text-right'><?php echo format_number($row->price,2) ?></td>
                      <td class='text-right'><?php echo format_number((floatval($row->quantity) * floatval($row->price)),2); ?></td>
                  </tr>
                  <?php endforeach; ?>                    
              </tbody>
              <tfoot>
                <?php if($invoice->discount != 0):?>
                <tr>
                  <th class='text-right' colspan='3'>SUB TOTAL</th>
                  <th class='text-right'><?php echo format_number($invoice->sub_total,2);?></th>
                </tr>
                <tr>
                  <th class='text-right' colspan='3'>DISCOUNT</th>
                  <th class='text-right'><?php echo format_number($invoice->discount,2);?></th>
                </tr>
                <?php endif;?>
                <tr>
                  <th class='text-right' colspan='3'>TOTAL</th>
                  <th class='text-right'><?php echo format_number($invoice->total,2);?></th>
                </tr>
              </tfoot>
          </table>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <a href='<?php echo base_url("sales/listing?".$_SERVER['QUERY_STRING']);?>'><div class="btn  btn-warning"><i class="fa fa-chevron-left"></i> Back</div></a>
          <a href='<?php echo base_url("sales/pdf/".$invoice->uuid);?>'><div class="btn  btn-danger pdf"><i class="fa fa-file-pdf-o"></i> PDF</div></a>
          <div class="btn  btn-info png"><i class="fa fa-image"></i> PNG</div>
          <div class="btn  btn-default print"><i class="fa fa-print"></i> Print</div>
        </div>
      </div>
      <footer></footer>
  </div>
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url();?>node_modules/html2canvas/dist/html2canvas.min.js"></script>
<script src="<?php echo base_url();?>vendor/qrcodejs/qrcode.min.js"></script>
<script src="<?php echo base_url();?>assets/js/pages/CreditNote_view.js"></script>
<script>
  jQuery(function(){
    $('.print').on('click',function(){
      let today = new Date();
      let y = today.getFullYear();
      let m = today.getMonth()+1; if(m<10) m = '0'+m ;
      let d = today.getDate(); if(d<10) d = '0'+d;
      let h = today.getHours(); if(h<10) h = '0'+h;
      let i = today.getMinutes(); if(i<10) i = '0'+i;
      let s = today.getSeconds(); if(s<10) s = '0'+s;

      let strDate = 'Y-m-d @ H:i:s'
      .replace('Y', y)
      .replace('m', m)
      .replace('d', d)
      .replace('H', h)
      .replace('i', i)
      .replace('s', s);
      $('footer').html("Document printed on " + strDate);
      window.print();
    })
    $('.png').on('click',function(){
      const elem = document.getElementById('elem');
      const number = $('.invoice-number').text();
      $('.btn').addClass("hidden");
      html2canvas(elem).then(canvas => {
        let a = document.createElement("a");
        a.download = "Invoice " + number + ".png";
        a.href = canvas.toDataURL("image/pdf");
        a.click();
        $('.btn').removeClass("hidden");

      }); 
    })
  })
</script>
</body>
</html>
