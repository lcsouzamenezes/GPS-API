<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-12">
                <h1> </h1>
            </div>
        </div>
        <div class="col-md-10 user-view">
        	<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">View User</h3>
				</div>
				<div class="panel-body">
                 <div class="col-lg-11 user-detailed" style="display:inline-block">
        		<?php //print_r($form_data); ?>
                 <div class="form-control">
                      <label>Default ID</label><span>:</span>
                      <?php echo (!empty($form_data['default_id']))?$form_data['default_id']:""; ?>
                 </div>
                 <div class="form-control">
                      <label>Phonenumber</label><span>:</span>
                      <?php echo (!empty($form_data['phonenumber']))?$form_data['phonenumber']:""; ?>
                 </div>
                 <?php if(!empty($form_data['email'])) { ?>
                  <div class="form-control">
                      <label>Email</label><span>:</span>
                      <?php echo (!empty($form_data['email']))?$form_data['email']:""; ?>
                 </div>
                 <?php } ?>
                 <div class="form-control">
                      <label>Display Name</label><span>:</span>
                      <?php echo (!empty($form_data['display_name']))?$form_data['display_name']:""; ?>
                 </div>
                 <div class="form-control">
                      <label>Created Date</label><span>:</span>
                      <?php echo date("Y-m-d", $form_data['date_created']); ?>
                 </div>
                 <div class="form-control">
                      <label>User Type</label><span>:</span>
                      <?php echo ucfirst($form_data['user_type']); ?>
                 </div>
                 <div class="form-control">
                      <label>Tracker</label><span>:</span>
                      <?php echo ($form_data['is_tracked']==1)?'On':"Off"; ?>
                 </div>
                 <?php if(!empty($form_data['promo'])) { ?>
                 <div class="form-control">
                      <label>Last Assigned Promo</label><span>:</span>
                      <?php echo $form_data['promo']; ?>
                 </div>
                 <?php } ?>
                 <div class="form-control">
                      <label>Location</label><span>:</span>
                      <a href="http://maps.google.com/maps?z=12&t=m&q=loc:<?php echo $form_data['position']['lat'];?>+<?php echo $form_data['position']['lon'];?>">View Location</a>
                 </div>
                 
                </div>
                <div class="col-lg-12">
                    <h5><b>UPDATE USER STATUS</b></h5>
                    <div class="row">
                      <form name="update_user_status" method="post" action="<?php echo base_url();?>admin/user/add/<?php echo $this->uri->segment(4);?>">
                        <input type="radio" name="update_user_status" style="margin-left: 15px;" value="1" <?php echo ($form_data['user_status'] == 1)?'checked=checked':"";?> />Active
                        <input type="radio" name="update_user_status" value="2" <?php echo ($form_data['user_status'] == 2)?'checked=checked':"";?> />In Active
                        <input type="radio" type="" name="update_user_status" value="3" <?php echo ($form_data['user_status'] == 3)?'checked=checked':"";?> />Flag
                        <input type="submit" type="" name="user_status" class="btn btn-primary" style="margin-left: 20px; margin-bottom: 5px;" value="UPDATE" />
                      </form>
                    </div>
                </div>
			</div>
		</div>
        <div class="clear"></div>
     
    </div>
</div>	

