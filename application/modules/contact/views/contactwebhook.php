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
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Jsonal</h1>
		<h2><?php if(isset($pagetitle)){ echo $pagetitle; }?></h2>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form name="webcontact" action="" method="POST">
            <?php if($webook_data){ ?>
                <table>
                    <tr>
                        <th>S NO.</th>
                        <th>Webhook URL</th>
                        <th>Remove</th>
                    </tr>
                    <?php  $count=1;
                        foreach ($webook_data as $dataz) { ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><input type="text" name="contactweb[]" value="<?php echo $dataz->webhook_url; ?>" size="105" /></td>
                                <td><a href="javascript:void(0);" class="remove">Remove</a></td>
                            </tr>
                        <?php } ?>
                </table>
               
            <?php } 
            else{ ?> 
            <table>
                <tr>
                    <th>S NO.</th>
                    <th>Webhook URL</th>
                    <th>Remove</th>
                </tr>
            </table>
            <?php } ?>
            <div class="addparent"><a href="javascript:void(0)" style="float:right" class="slot" data-id="contactus"><div class="addlink">Add Slot</div></a></div><br/>
             <input type="submit" name="submit">
            </form>

        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script type="text/javascript">
    jQuery('.row a.slot').on('click',function(){
        var newslot='<tr><td></td><td><input type="text" name="contactweb[]" value="" size="105"/></td><td><a href="javascript:void(0);" class="remove">Remove</a></td></tr>';
        jQuery('table').append(newslot);
    });

    jQuery(document.body).on('click', '.row a.remove' ,function(){
        jQuery(this).closest("tr").remove();
    });
</script>