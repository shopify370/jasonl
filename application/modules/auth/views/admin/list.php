<div class="col-lg-12">
<div class="box">
	<header>
		<div class="icons"><i class="fa fa-table"></i></div>
		<h5><?php echo $title?></h5>
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
		<table class="table table-bordered table-condensed table-hover table-striped dataTable" id="company_tbl">
		  <thead>
			<tr>
				<th style="width:30px;" >#</th>
				<th>Name  </th>
				<th>Email </th>
				<th>Club Name</th>
				<th>Status </th>
				<th>Action </th>
			</tr>
		  </thead>

		<tbody role="alert" aria-live="polite" aria-relevant="all">
                <?php if ($users): ?>
                    <?php foreach ($users as $key => $val): ?>
					
					<?Php
					//get users group
					$user_groups = $this->ion_auth->get_users_groups($val->id)->result();
					?>
					
                        <tr class="odd">
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo ucwords("{$val->first_name} {$val->last_name}"); ?></td>
                            <td><?php echo $val->email; ?></td>
                            <td><?php echo $val->club_name; ?></td>
                            <td class="center"><input type="checkbox"  <?php echo $val->active ? "checked" : ''; ?> class="ajax-toggle switch" data-toggle-href="<?php echo base_url('admin/auth/toggle_status/'); ?>" data-id="<?php echo $val->id; ?>" data-size="mini" data-on-color="success" data-off-color="danger" ></td>
                            <td class="center">
                                <a href="<?php echo base_url('admin/auth/edit/' . $val->id); ?>" class="btn btn-default btn-xs btn-round" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i> </a>
<!--                                <a href="<?php // echo base_url('admin/auth/delete/' . $val->id); ?>" class="btn btn-default btn-xs btn-round" onclick='if (!confirm("Are you sure to delete?"))
                                            return false;' data-toggle="tooltip" title="Delete"><i class="fa fa-times"></i> </a>-->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
		</tbody>
		</table>
		
		<a href="<?php echo base_url('admin/auth/add/'); ?>" class="btn btn-primary btn-sm btn-flat" data-original-title="" title="">Add a new user</a>
		
	</div>
</div>    
</div>