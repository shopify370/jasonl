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
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
		<form action="<?php echo base_url('credential/adminDetail'); ?>" name="admindetail" method="POST">
			<table>
				<tr>
					<td><label for="username">Username: </label></td>
					<td><input type="text" name="username" id="username" required value="<?php if($credential['username']){ echo $credential['username']; } ?>"></td>
				</tr>
				<tr>
					<td><label for="password">Password: </label></td>
					<td><input type="text" name="password" id="password" required value="<?php if($credential['password']){ echo $credential['password']; } ?>"></td>
				</tr>
				<tr>
					<td><label for="loginurl">Base URL: </label></td>
					<td><input type="text" name="loginurl" id="loginurl" required value="<?php if($credential['loginurl']){ echo $credential['loginurl']; } ?>" size="80"></td>
				</tr>
				<tr>
					<td><label for="email">Notification Email: </label></td>
					<td><input type="text" name="email" id="email" required value="<?php if($credential['email']){ echo $credential['email']; } ?>" size="90"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" id="submit"></td>
				</tr>
			</table>
			</form>
        </div><!-- /.row (main row) -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->