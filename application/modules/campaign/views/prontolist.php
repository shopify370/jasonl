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
        <?php 
            $url=$_SERVER['REQUEST_URI']; 
            if (strpos($url,'prontotoActiveErr') !== false) {
                $select_err='selected';
                $select_dta='';
            } else {
                $select_err='';
                $select_dta='selected';
            }
        ?>
        <h1>Jsonal</h1>
		<h2><?php echo 'Pronto to Active Campaign'; ?></h2>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <form name="filterfield" id="filterfield">
             <table style="width:6%; margin:20px;">
                <tr>
                    <td>
                        <select name="type" id="selecttype" style="width: 121px; height: 34px;" onchange="location = this.value;">
                            <option value="<?php echo base_url() . 'campaign/Activepage/prontotoActive'; ?>" <?php echo $select_dta; ?>>All</option>
                            <option value="<?php echo base_url() . 'campaign/Activepage/prontotoActiveErr'; ?>" <?php echo $select_err; ?>>Error</option>
                        </select>
                    </td>
            </table>
        </form>

        <div class="row">
            <?php if($pronto_data){
                $count=  ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
                if($count>0)
                $count=($count-1)*20;
                ?>
                <table>
                    <tr>
                        <th>S NO.</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
						<th>Phone</th>
						<th>PostCode</th>
                        <th>Status</th>
                        <th>Message</th>
                    </tr>
                    <?php  
                        foreach ($pronto_data as $dataz) { 
                            $error='success';
                            if(isset($dataz['id'])){ $error='errordata'; } ?>
                            <tr class="<?php echo $error; ?>">
                                <td><?php echo ++$count; ?></td>
                                <td><?php echo $dataz['first_name']; ?></td>
                                <td><?php echo $dataz['last_name']; ?></td>
                                <td><?php echo $dataz['email']; ?></td>
								<td><?php echo $dataz['phone']; ?></td>
								<td><?php echo $dataz['postcode']; ?></td>
                                <td><?php if(isset($dataz['status'])){ if($dataz['status']==1){ echo 'Success'; } else{ echo 'Failed'; } } ?></td>
                                <td><?php if(isset($dataz['error'])){ ?><?php echo $dataz['error']; ?> <?php } ?></td>
                            </tr>
                        <?php } ?>
                </table>
             <div class="pagelists"><?php echo $links; ?></div>              
            <?php } 
            else{
                echo 'No data Found';

            }?>

        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->