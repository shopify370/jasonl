<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>FAAA</title>
        <link rel="shortcut icon" href="<?php echo base_url() ?>assets/favicon.png">
        <link href="<?php echo base_url() ?>assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url() ?>assets/css/main.min.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url() ?>assets/css/style.app.css" rel="stylesheet" type="text/css" media="screen" />
        <script> var base_url = '<?php echo base_url() ?>'</script>		
        <script src="<?php echo base_url() ?>assets/js/jquery.js"></script>		
        <script src="<?php echo base_url() ?>assets/lib/jquery.form.js"></script>		
        <script src="<?php echo base_url() ?>assets/js/api_scripts.js"></script>		
    </head>
    <body class='bg-egg_shell'>
        <div id='sidebar' class='inner'>
            <h3>Functions</h3>
            <div class="mainNav">
                <ul class="nav list-unstyled">
                    <?php foreach($links as $val): ?>
                    <li> <a href="<?php echo $val['link']?>" class="load-form"><?php echo $val['title']?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div id='content' >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            
                            <div class="form-inline"  >	
                                <div class="form-group">
                                    <label class="sr-only" for="auth_user_id" >* Authentication Key:</label>
                                    <input class="form-control" type="text" id="auth_user_id" name="authentication_key" value=""  placeholder="Authentication Key"/>
                                </div>
<!--                                <div class="form-group">
                                    <label  class="sr-only" for="auth_hash_code" >* Hash Code:</label>
                                    <input class="form-control" type="text" id="auth_hash_code" name="hash_code" value="" placeholder="hash Code" />
                                </div>
                                <div class="form-group">
                                    <label  class="sr-only" for="auth_device_id" >* Device Id:</label>
                                    <input class="form-control" type="text" id="auth_device_id" name="device_id" value="" placeholder="Device Id" />
                                </div>
                                <div class="form-group">
                                    <label  class="sr-only" for="auth_device_type" >* Device Type:</label>
                                    <input class="form-control" type="text" id="auth_device_type" name="device_type" value="" placeholder="Device Type" />1: iphone, 2: android
                                </div>-->
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div id="form-view">
                            <h3>Forms</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <h3>Request</h3>
                            <pre id="request"></pre>
                        </div>
                        <div>
                            <h3>Response</h3>
                            <pre id="results"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
