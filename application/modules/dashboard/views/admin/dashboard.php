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
            <?php if($orders){ ?>
                <table>
                    <tr>
                        <th>S NO.</th>
                        <th>Order ID</th>
                        <th>Status</th>  
                        <th>Message</th>  
                    </tr>
                    <?php  $count=1;
                        foreach ($orders as $order) { ?>  
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><a href="<?php echo base_url('orders/orderdetail').'/'.$order->id;?>"><?php echo $order->order_id; ?></a></td>
                                <td><?php if($order->status==0){ echo 'Failed'; } else { echo 'Success';} ?></td>
                                <td>
                                    <?php 
                                        if($order->status==1){ 
                                            echo 'Sku of order '.$order->order_id.' is Parsed Successfully'; 
                                        }
                                        else{ 
                                            $error_sku='Sku: '.$order->sku.' of order '.$order->order_id.' with product ID '.$order->product_id;
                                            if($order->varient_id){
                                                $error_sku.=' with varient ID '.$order->varient_id;
                                            }
                                            $error_sku.=' cannot be parsed';
                                            echo $error_sku;
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                </table>
            <?php } ?>
        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->