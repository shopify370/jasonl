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
.pagelists a, .pagelists strong {
    padding: 6px 12px;
    margin: 7px 1px;
    background-color: darkgray;
	color:black;
}

.pagelists{    
	padding: 20px;
    margin-top: 18px;
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
                    <?php  
					$count=  ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
          if($count>0)
          $count=($count-1)*20;
                        foreach ($orders as $single_order){ ?>  
                            <tr>
                                <td><?php echo ++$count; ?></td>
                                <td>
								<?php if($single_order->type==1){ ?>
									<a href="<?php echo base_url('pages/orderdetail').'/'.$single_order->insertId;?>"> 
									<?php } else{ ?>
									<a href="<?php echo base_url('pages/prontodetail').'/'.$single_order->insertId;?>">
									<?php }  echo $single_order->order_id; ?>
								</a>
								</td>
                                <td><?php if($single_order->status==0){ echo 'Failed'; } else { echo 'Success';} ?></td>
                                <td><?php echo $single_order->message; ?></td>
                            </tr>
                        <?php } ?>
                </table>
            <?php } ?>
			 <div class="pagelists"><?php echo $links; ?></div>
        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->