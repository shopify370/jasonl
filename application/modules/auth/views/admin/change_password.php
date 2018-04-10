<div class="col-lg-12">
	<div class="box">
	<header>
		<div class="icons"><i class="fa fa-edit"></i></div>
		<h5><?php echo lang('change_password_heading');?></h5>
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
			<form class="form-horizontal" method="POST" autocomplete="off">
		  <div class="form-group <?php echo form_error('old_password')?'has-error':''?>">
			  <label for="old_password" class="control-label col-lg-2"><?php echo lang('change_password_old_password_label');?></label>
			<div class="col-lg-7">
				<?php echo form_input($old_password);?>
				<?php echo form_error('old_password'); ?>
			</div>
		  </div>
		  <div class="form-group <?php echo form_error('new_password')?'has-error':''?>">
			  <label for="new_password" class="control-label col-lg-2"><?php echo lang('change_password_new_password_label');?></label>
			<div class="col-lg-7">
				<?php echo form_input($new_password);?>
				<?php echo form_error('new_password'); ?>
			</div>
		  </div>
		  <div class="form-group <?php echo form_error('new_password_confirm')?'has-error':''?>">
			  <label for="new_password_confirm" class="control-label col-lg-2"><?php echo lang('change_password_new_password_confirm_label');?></label>
			<div class="col-lg-7">
				<?php echo form_input($new_password_confirm);?>
				<?php echo form_error('new_password_confirm'); ?>
			</div>
		  </div>
		  <div class="form-group">
			  <div class="col-sm-offset-2 col-sm-8"><input type="submit" name="submit" class="btn btn-primary" value="Change Password" />
			  </div>
		  </div>
		</form>		
			
		</div>
</div>
</div>

<div id="infoMessage"><?php // echo $message;?></div>
