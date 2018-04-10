<style type="text/css">
.content .row{
 margin: 0;
}
table{
 max-width: 40%;
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
table tr td label.lbl{ font-size:25px;}

label.error{display:block; color:red;}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>Roses Only.</h1>
	  <ol class="breadcrumb">
	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	    <li class="active">Dashboard</li>
	  </ol>
	</section>
<!-- Main content -->
	<section class="content">
		<h3>You will have to re-login with new password after changing password</h3>
	  	<div class="row">
			<form action="<?php echo base_url('pages/change_password'); ?>" method="POST" name="passwordchange" id="passwordchange">
				<table>
					<tr>
						<td colspan=2 align="center"><label class="lbl">Change Password</label></td>
					</tr>
					<tr>
						<td><label for="password">Password</label></td>
						<td><input type="password" name="password" size="45" id="password" required /></td>
					</tr>
					<tr>
						<td><label for="confirm_password">Confirm Password</label></td>
						<td><input type="password" name="confirm_password" size="45" id="confirm_password" required /></td></tr>
					<tr>
						<td></td>
						<td><input type="submit" name="Submit"></td>
					</tr>
				</table>
	 		 </form>
		</div><!-- /.row (main row) -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->