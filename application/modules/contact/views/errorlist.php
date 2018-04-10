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
.addlink{
    padding: 10px;
    clear: both;
    margin: 15px;
    background-color: green;
    color: white;
}
.addparent{display:inline-block;}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
            <?php if($error_data){ ?>
                <table>
                    <tr>
                        <th>S NO.</th>
                        <th>Errors</th>
                        <th>Webhook URL</th>
                        <th>Form Type</th>
                    </tr>
                    <?php  $count=1;
                        foreach ($error_data as $dataz) { ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo $dataz->curl_error; ?></td>
                                <td><?php echo $dataz->weburl; ?></td>
                                <td>
                                	<?php 
	                                	if($dataz->type==1){
	                                		echo 'Get a Quote';
	                                	} 
	                                	elseif($dataz->type==2){
	                                		echo 'Add a Quote';
	                                	}
	                                	elseif($dataz->type==3){
	                                		echo 'Contact Us';
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