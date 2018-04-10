<style type="text/css">
.content .row{
 margin: 0;
}
table{
 max-width: 500px;
 background: #dddddd;
 width: 100%;
}
table tbody tr:nth-child(even){
 background: #e1e1e1;
}
table tbody tr > th{
 text-align: left;
 height: 45px;
 background: #598bbe;
 color: #ffffff;
 padding: 10px;
}
table tbody tr > td{
 padding: 10px;
}
#generatebutton{
  display: block;margin: auto;background: #479568;width: 250px;text-align: center;padding: 20px;color: #fff;border-radius: 6px;font-size: 16px;
}.row.wrapper-download {
    margin-top: 200px;
}.row.wrapper-download a {
    display: block;
    text-align: center;
}#generatebutton {
    display: block;
    margin-top: 25px;
    background: #309830;
    width: 220px;
    margin: auto;
        margin-top: auto;
    padding: 16px;
    margin-top: 20px;
    color: white;
    font-size: 17px!important;
    border-radius: 5px;
    box-shadow: 6px 6px 0 0 #ccc;
}#ready-file{
  font-size: 17px;
}

</style>

<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            JasonL to Catch Marketplace integeration
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <div class="row wrapper-download">
            <?php if($state==1){
              echo '<a id="ready-file" href="'.base_url().'assets/products.xml" download>'."Your file is ready to Donwload<br/>Click here to download".'</a>';
            } ?>
            <a href="<?php echo base_url('dashboard/catchapp/generate') ?>" id="generatebutton">Generate XML</a>  
          </div><!-- /.row (main row) -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('#generatebutton').click(function(){
      jQuery(this).text('generating...');
      jQuery(this).css('background','#eda232');
    });
  });
</script>