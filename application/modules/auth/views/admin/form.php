<div class="col-lg-12">
	<div class="box">
	<header>
		<div class="icons"><i class="fa fa-edit"></i></div>
		<h5><?php echo $edit ? 'Edit' : 'Add'; ?> User</h5>
		<div class="toolbar">
		  <nav style="padding: 8px;">
			<a class="btn btn-default btn-xs collapse-box" href="javascript:;">
			  <i class="fa fa-minus"></i>
			</a> 
			<a class="btn btn-default btn-xs full-box" href="javascript:;">
			  <i class="fa fa-expand"></i>
			</a> 
		  </nav>
		</div>
	</header>
	<div class="body">
		<form class="form-horizontal" method="POST" enctype="multipart/form-data">
                    
          <?php echo form_fieldset('Details') ?>

		  <div class="form-group <?php echo form_error('club_type')?'has-error':''?>">
			<label for="club_type" class="control-label col-lg-3">Select Club Type *</label>
			<div class="col-lg-4">
                            <select id="user_type" name="club_type" class="form-control"<?php if($edit):?>disabled<?php endif; ?>>
					
					<?php foreach($club_types as $clubs): ?>
					<option <?php if($edit)if($clubs->club_id == $user_detail->club_id)echo 'selected'; ?> value="<?php echo $clubs->club_id;?>"><?php echo $clubs->club_name; ?></option>
					<?php endforeach; ?>
				
				</select>
				
				<?php echo form_error('club_type'); ?>
			</div>
		  </div>
		  
		  <div class="form-group <?php echo form_error('first_name')?'has-error':''?>">
			<label for="first_name" class="control-label col-lg-3">First Name *</label>
			<div class="col-lg-5">
				<input type="text" id="first_name" placeholder="First Name" name="first_name" class="form-control" value="<?php echo set_value("first_name", $edit ? $user_detail->first_name : ''); ?>" >
				<?php echo form_error('first_name'); ?>
			</div>
		  </div>
		  <div class="form-group <?php echo form_error('last_name')?'has-error':''?>">
			<label for="last_name" class="control-label col-lg-3">Last Name *</label>
			<div class="col-lg-5">
				<input type="text" id="last_name" placeholder="Last Name" name="last_name" class="form-control" value="<?php echo set_value("last_name", $edit ? $user_detail->last_name : ''); ?>" >
				<?php echo form_error('last_name'); ?>
			</div>
		  </div>
		  <div class="form-group <?php echo form_error('email')?'has-error':''?>">
			<label for="email" class="control-label col-lg-3">Email *</label>
			<div class="col-lg-5">
				<input type="text" id="email" placeholder="Email" name="email" class="form-control" value="<?php echo set_value("email", $edit ? $user_detail->email : ''); ?>" >
				<?php echo form_error('email'); ?>
			</div>
		  </div>
		  <div class="form-group">
			<label for="active" class="control-label col-lg-3">Active *</label>
			<div class="col-lg-7">
				<input type="checkbox" name="active" class="switch switch-small"  value="1" <?php echo set_checkbox('active', '1', ($edit) ? ($user_detail->active ? TRUE : FALSE) : TRUE); ?> data-on-color="success" data-off-color="danger" >
				<?php echo form_error('active'); ?>
			</div>
		  </div>
                        <?php echo form_fieldset_close()?>
                    <?php echo form_fieldset($edit ? 'Change Password' : 'Set Password') ?>
                            
		  <div class="form-group <?php echo form_error('password')?'has-error':''?>">
			<label for="password" class="control-label col-lg-3">Password</label>
			<div class="col-lg-5">
				<input type="password" id="password" placeholder="Password" name="password" class="form-control" value="" >
				<?php echo form_error('password'); ?>
			</div>
		  </div>
		  <div class="form-group <?php echo form_error('conf_pass')?'has-error':''?>">
			<label for="conf_pass" class="control-label col-lg-3">Confirm Password</label>
			<div class="col-lg-5">
				<input type="password" id="conf_pass" placeholder="Confirm Password" name="conf_pass" class="form-control" value="" >
				<?php echo form_error('conf_pass'); ?>
			</div>
		  </div>
                        <?php echo form_fieldset_close()?>
		  <div class="form-group">
			  <div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" class="btn btn-primary" value="Submit" />
				  <?php echo anchor('admin/auth/club', 'Cancel', 'class="btn btn-warning"'); ?>
			  </div>
		  </div>
		</form>		
	</div>
</div>
</div>