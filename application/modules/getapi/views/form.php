<style type="text/css">
.content .row{
 margin: 0;
}
</style>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Jasonl
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
<?php echo validation_errors(); ?>
          <div class="row">
        <span>Enter the order Id to process manually</span></br>
        <?php echo form_open('getapi/manual/getorder'); 
        echo form_input('orderid', ''); ?>
        
        <input type="submit" name="submit" value="submit" class="button-submit" />
        <?php echo form_close(); ?>

          </div><!-- /.row (main row) -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->