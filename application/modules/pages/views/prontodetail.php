<style type="text/css">
.content .row{
 margin: 0;
}
table{
 background: #dddddd;
 width: 70%;
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
.btn{
      background: #2c3338;
  background-image: -webkit-linear-gradient(top, #2c3338, #414b52);
  background-image: -moz-linear-gradient(top, #2c3338, #414b52);
  background-image: -ms-linear-gradient(top, #2c3338, #414b52);
  background-image: -o-linear-gradient(top, #2c3338, #414b52);
  background-image: linear-gradient(to bottom, #2c3338, #414b52);
  -webkit-border-radius: 9;
  -moz-border-radius: 9;
  border-radius: 9px;
  font-family: Arial;
  color: #ffffff;
  font-size: 13px;
  padding: 10px 20px 10px 20px;
  text-decoration: none;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Jsonal</h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <a href="<?php echo base_url('pages/orderList');?>" class="btn">< Back</a><br/><br/>
            <?php if($prontodetail){
				$count=1;
					foreach ($prontodetail as $prontoId) { ?>
            <table>
                <tr><td>Shopify Order No. :</td><td><?php echo $prontoId->order_id; ?></td></tr>
                <tr><td>Customer ID :</td><td><?php echo $prontoId->customer_id; ?></td></tr>
                <tr><td>Pronto Order No: </td><td><?php echo $prontoId->pronto_orderid; ?></td></tr>
                <tr><td>Status: </td><td><?php echo $prontoId->status; ?></td></tr>
                <tr><td>Message: </td><td><?php echo $prontoId->error_message; ?></td></tr>
            </table>
            <br/>
            <br/>

            <table>
            <tr><td>Request:</td><td>Response:</td></tr>
             <tr><td style="width:50%">
             <?php 
             echo '<pre>';
              echo htmlentities(formatXmlString($prontoId->error_xmlrequest));
             echo '</pre>';
             ?>
               
             </td><td style="width:50%">
             <?php 
             if(substr($prontoId->error_xmlresponse, 0, 5) =="<?xml"){
                echo '<pre>';
                echo htmlentities(formatXmlString($prontoId->error_xmlresponse));
                echo '</pre>';

             }
             else{
                echo '<pre>';
             print_r($prontoId->error_xmlresponse);
             echo '</pre>';
             }
             ?>
               
             </td></tr>
            </table>
				<?php 	} 
            } ?>
            <br/><br/>
            
         <?php if($customerdetail){
             echo '<h2>Customer Detail</h2>';
        $count=1;
          foreach ($customerdetail as $prontoId) { ?>

            <table>
            <tr><td>Request:</td><td>Response:</td></tr>
             <tr><td style="width:50%">
             <?php 
             echo '<pre>';
              echo htmlentities(formatXmlString($prontoId->customer_request));
             echo '</pre>';
             ?>
               
             </td><td style="width:50%">
             <?php 
             if(substr($prontoId->customer_response, 0, 5) =="<?xml"){
                echo '<pre>';
                echo htmlentities(formatXmlString($prontoId->customer_response));
                echo '</pre>';

             }
             else{
                echo '<pre>';
             print_r($prontoId->customer_response);
             echo '</pre>';
             }
             ?>
               
             </td></tr>
            </table>
        <?php   } 
            } ?>
            <br/><br/>
            <!-- This is the comment section. -->
               <?php if($deliverydetail){
                     echo '<h2>Delivery Detail</h2>';
        $count=1;
          foreach ($deliverydetail as $prontoId) { ?>

            <table>
            <tr><td>Request:</td><td>Response:</td></tr>
             <tr><td style="width:50%">
             <?php 
             echo '<pre>';
              echo htmlentities(formatXmlString($prontoId->delivery_request));
             echo '</pre>';
             ?>
               
             </td><td style="width:50%">
             <?php 
             if(substr($prontoId->delivery_response, 0, 5) =="<?xml"){
                echo '<pre>';
                echo htmlentities(formatXmlString($prontoId->delivery_response));
                echo '</pre>';

             }
             else{
                echo '<pre>';
             print_r($prontoId->delivery_response);
             echo '</pre>';
             }
             ?>
               
             </td></tr>
            </table>
        <?php   } 
            } ?>
        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->