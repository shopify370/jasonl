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
		<?php $urlSegment=($this->uri->segment(3)) ? $this->uri->segment(3) : 0; ?>
            <a href="<?php echo base_url('pages/orderList');?>" class="btn">< Back</a><br/><br/>
            <?php if($orderdetail){
				$count=1;
					foreach ($orderdetail as $order) {
						if($order->status==1){ ?>
                            <table><tr><td>Order ID:</td><td><?php echo $order->order_id;?></td></tr>
                            <tr><td>Status: </td><td>Success</td></tr>
                            </table>
							<?php 
							$one=json_decode($order->order_detail,true);
							echo '<pre>';
							print_r($one);
							echo '</pre>';
						}
                        else{ 

                            $error_sku='Sku: '.$order->sku.' of order '.$order->order_id.' with product ID '.$order->product_id;
                            if($order->varient_id){
                                $error_sku.=' with varient ID '.$order->varient_id;
                            }
                            $error_sku.=' cannot be parsed';

                            ?>
                            <table><tr><td>Order ID:</td><td><?php echo $order->order_id;?></td></tr>
                            <tr><td>Status: </td><td>Failed</td></tr>
                            <tr><td>Error Message: </td><td><?php echo $error_sku; ?></td></tr>
                            </table>
                            <?php 
                        }
					} 
            } ?>
        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->