<html>
<head>
    <link media="all" href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet" />
   <link media="all" href="<?php echo base_url();?>assets/admin/css/login.css" type="text/css" rel="stylesheet" />
    <link media="all" href="<?php echo base_url();?>assets/admin/plugins/magic/magic.css" type="text/css" rel="stylesheet" />
    <link media="all" href="<?php echo base_url();?>assets/admin/plugins/bootstrap/css/style.css" type="text/css" rel="stylesheet" />
    
</head>
<body>
<script src="<?php echo base_url();?>assets/admin/plugins/jquery-2.0.3.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/bootstrap/js/bootstrap"></script>
<script src="http://mymaplist.com/js/vendor/TweenLite.min.js"></script>
<!-- This is a very simple parallax effect achieved by simple CSS 3 multiple backgrounds, made by http://twitter.com/msurguy -->
<div class="container">
   <div class="admin-login">
        <div class="text-center">
            <img src="<?php echo site_url();?>assets/admin/images/logo-new.png" id="" alt=" Logo" height="62" width="150" />
        </div>
        

    <div class="row">
       <div class="col-md-6 col-md-offset-4" style="margin-top: 50px;">
          <span style="color:black; font-szie:14px; font-weight:bold;"><?php echo $return_message; ?></span>
        </div>
    </div>
 <?php if(empty($return_message)) { ?>
    <div class="row">
       <div class="col-md-12">
          <span style="color:#fff; font-size:12px; font-weight:bold; text-align:center;"><?php echo validation_errors(); ?></span>
        </div>
    </div>
    <div class="row vertical-offset-100">
        <div class="tab-content">
    		<div id="login" class="tab-pane active">
			        <form name="change_password" class="form-signin" id="change_password" method="POST" action="">
                    <input name="user_id" id="user_id" value="<?php echo $user_id; ?>" type="hidden" />
                  
			    	  	<div class="control-group">
                            <div class="controls">
                                <input name="new_password" id="new_password" placeholder="New Password" class="form-control" type="password" value="<?php echo set_value('new_password'); ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                           
                            <div class="controls">
                                <input name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="form-control" type="password" value="<?php echo set_value('confirm_password'); ?>" />
                            </div>
                        </div>    
   		               <input class="btn text-muted text-center" type="submit" name="submit" value="SAVE" />
			    	
			      	</form>
			  </div>
		
	</div>
 <?php } ?>
</div>
</div>
</div>
</body>
</html>