      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN</li>
          	 <!--   <li><a href="<?php echo base_url('pages');?>">Orders</a></li> 
              <li><a href="<?php echo base_url('pages/pronto');?>">Pronto Response</a></li>-->
    			  <li class="treeview">
                  <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Pronto Integraton</span> <i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
				    <li class="<?php echo ($this->uri->segment(1) == 'credential') ? 'active':''; ?>"><a href="<?php echo base_url('credential');?>"><i class="fa fa-circle-o"></i>Settings</a></li>
                      <li class="<?php echo ($this->uri->segment(2) == 'orderList') ? 'active':''; ?>"><a href="<?php echo base_url('pages/orderList'); ?>"><i class="fa fa-circle-o"></i>Orders</a></li>
    				   <li class="<?php echo ($this->uri->segment(3) == 'getorder') ? 'active':''; ?>"><a href="<?php echo base_url('getapi/manual/getorder'); ?>"><i class="fa fa-circle-o"></i>Push Order Manually</a></li>
                  </ul>
            </li>
			     <li><a href="<?php echo base_url('dashboard/catchapp');?>"><i class="fa fa-dashboard"></i>Catch Integeration</a></li>
				 <li class="treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Webhook URL</span> <i class="fa fa-angle-left pull-right"></i>
                  </a>
                    <ul class="treeview-menu">
                  <li><a href="<?php echo base_url('contact/Webhookpage/contactwebhook');?>"><i class="fa fa-circle-o"></i>Contact Us</a></li>
                  <li><a href="<?php echo base_url('contact/Webhookpage/getquotewebhook');?>"><i class="fa fa-circle-o"></i>Get a Quote</a></li>
                  <li><a href="<?php echo base_url('contact/Webhookpage/addquotewebhook');?>"><i class="fa fa-circle-o"></i>Add a Quote</a></li>
                  <li><a href="<?php echo base_url('contact/Webhookpage/product_contactwebhook');?>"><i class="fa fa-circle-o"></i>Contact Product</a></li>
                </ul>
            </li>
			<li class="treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Webhook Error</span> <i class="fa fa-angle-left pull-right"></i>
                  </a>
                    <ul class="treeview-menu">
                  <li><a href="<?php echo base_url('contact/Webhookpage/errorloging');?>"><i class="fa fa-circle-o"></i>Error Listing</a></li>
                </ul>
            </li>
			
			<li class="treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>AC Pronto Data Flow</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo base_url('campaign/activepage/credentials');?>">Settings</a></li>
					<li><a href="<?php echo base_url('campaign/activepage/activeData');?>">Active Campaign</a></li>
					<li><a href="<?php echo base_url('campaign/activepage/prontotoActive');?>">Pronto to Campaign</a></li>
					<li><a href="<?php echo base_url('campaign/activepage/prontoacwebhook');?>">Pronto to Campaign Webhook</a></li>
				</ul>
			</li>
			<li class="treeview"><a href="<?php echo base_url('pages/createuser'); ?>"><span><i class="fa fa-dashboard"></i> Create users</span></a></li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>