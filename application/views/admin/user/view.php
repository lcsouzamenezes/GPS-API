<div id="content">    
<div class="inner" style="min-height: 700px;">
<div class="container">
    <div class="page-header">
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel with-nav-tabs panel-primary">
                <div class="panel-heading">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1primary" data-toggle="tab">View User</a></li>
                           <?php if($form_data['login_type'] !='website'){ ?> 
                            <li><a href="#tab7primary" data-toggle="tab">Mail Communication</a></li>
                            
                           <!--
 <li><a href="#tab2primary" data-toggle="tab">HMGPS Account</a></li>
-->
                            <!--
<li><a href="#tab3primary" data-toggle="tab">Contacts</a></li>
-->
                            <li><a href="#tab4primary" onclick="assign_user(<?php echo $this->uri->segment(4);?>)" data-toggle="tab">My Promos</a></li>
                            <!--
<li><a href="#tab5primary" data-toggle="tab">Payment</a></li>
-->
                            <li><a href="#tab6primary" data-toggle="tab">Reset Password</a></li>
                            <?php } ?>
                        </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab1primary">
                               <div class="col-lg-11 user-detailed" style="display:inline-block">
                               <?php //echo "<pre>"; print_r($channels['channels']); ?>
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
                                          <label>Current Promo Code</label><span>:</span>
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
                                            <input type="radio" type="" name="update_user_status" value="4" <?php echo ($form_data['user_status'] == 4)?'checked=checked':"";?> />Unflag
                                            <input type="radio" type="" name="update_user_status" value="5" <?php echo ($form_data['user_status'] == 5)?'checked=checked':"";?> />Block
                                            <input type="radio" type="" name="update_user_status" value="6" <?php echo ($form_data['user_status'] == 6)?'checked=checked':"";?> />Un Block
                                            <input type="submit" type="" name="user_status" class="btn btn-primary" style="margin-left: 20px; margin-bottom: 5px;" value="UPDATE" />
                                          </form>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="col-lg-12" id="user_channels">
                                        <h5><b>CHANNELS</b></h5>
                                        <table class="table user-list table-hover" style="height:10px !important;">
                                        <thead>
                                            <th>CHANNEL NAME</th>
                                            <th>CHANNEL TYPE</th>
                                            <th>MEMBER TYPE</th>
                                            <th>ACTION</th>
                                        </thead>
                                        <tbody> 
                                            <?php if(count($channels) > 0){
                                                 foreach($channels as $ckey => $cvalue){ 
                                                   $usertype = ($cvalue['guser']==$cvalue['uguser'])?'owner':'group_user'; 
                                              ?>
                                                 <tr>
                                                    <td><?php echo $cvalue['jkey'];?></td>
                                                    <td><?php echo $cvalue['type'];?></td>
                                                    <td><?php if($cvalue['guser'] == $cvalue['uguser']) {echo "Owner";}else{echo "Joined User";} ?></td>
                                                    <td><a title="Delete User From this Channel" onclick="delete_group_user(<?php echo $cvalue['uguser'];?>,<?php echo $cvalue['group_id'];?>,'<?php echo $usertype; ?>');"><img src="<?php echo site_url();?>assets/admin/images/delete.png" width="20" height="20" /></a>
                                                    <button title="Channel Participants Lists" class="btn btn-info btn-action" data-toggle="modal" data-target="#participant_lists" onclick="get_participants_lists(<?php echo $cvalue['group_id'];?>)"> 
                                                          <img src="<?php echo base_url();?>assets/admin/images/participants.png" class="img-responsive m-menu" alt="participants" /> 
                                                        </button>
                                                    </td>
                                                 </tr>  
                                            <?php } } else { ?>
                                                <tr>
                                                    <td colspan="3"> No Channels Found.</td>
                                                </tr>
                                            <?php } ?>
                                           
                                        </tbody>
                                        </table>
                                    </div>      
                        </div>
                        <div class="tab-pane fade" id="tab2primary">
                            HMGPS Account
                        </div>
                        <div class="tab-pane fade" id="tab7primary">
                           <div class="row">
                              <form name="reset_password" method="post">
                                    <input type="hidden" value="<?php echo $this->uri->segment(4);?>" id="email_user_id" name="email_user_id" />
                                    <span class="pass_success" style="color: #218c92; font-weight:bold;"></span>
                                    <br />
                                    <label>To</label>
                                    <input type="text" class="form-control" style="width: 200px !important;" id="email_to" name="email_to" value="<?php echo (!empty($form_data['email']))?$form_data['email']:""; ?>" readonly="readonly" />
                                    <br />
                                    <label>Name</label>
                                    <input type="text" class="form-control" id="email_name" style="width: 200px !important;" name="email_name" />
                                     <br />
                                    <label>Subject</label>
                                    <input type="text" class="form-control" id="email_subject" style="width: 200px !important;" name="email_subject" />
                                     <br />
                                     <label>Message</label>
                                     <br /> 
                                      <textarea class="form-control" id="email_message" style="width: 400px; height:150px;" name="email_message"></textarea>
                                    <br />
                                <input type="button" onclick="send_mail(<?php echo $this->uri->segment(4);?>);" name="email_send" class="btn btn-primary"  value="SAVE" />
                              </form>
                          </div>
                        </div>
                        
                        <div class="tab-pane fade" id="tab3primary">
                            Contacts
                        </div>
                        <div class="tab-pane fade" id="tab4primary">  
                            My Promos
                        </div>
                        <div class="tab-pane fade" id="tab5primary">
                            Payment
                        </div>
                         <div class="tab-pane fade" id="tab6primary">
                            <div class="row">
                              <form name="reset_password" method="post">
                                    <span class="pass_success" style="color: #218c92; font-weight:bold;"></span>
                                    <br />
                                    <label>Password</label>
                                    <input type="password" class="form-control" id="password" style="width: 200px !important;" name="password" />
                                     <br />
                                     <label>Confirm Password</label>
                                      <input type="password" class="form-control" id="confirm_password" style="width: 200px !important;" name="confirm_password" />
                                    <br />
                                <input type="button" onclick="res_password(<?php echo $this->uri->segment(4);?>);" name="user_update" class="btn btn-primary"  value="SAVE" />
                              </form>
                          </div>
                      </div>
                      
                </div>
            </div>
        </div>
	</div>
</div>
<br/>
</div>

<div class="modal fade" id="participant_lists" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="H3">Participants Lists</h4>
            </div>
            <div class="modal-body">
          
             <div class="clearfix" style="margin-top: 20px;"></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div id="group_participants_lists"></div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>
</div>

    
        <!--
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
                      <label>Current Promo Code</label><span>:</span>
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
                      <form name="update_user_status" method="post" action="<?php //echo base_url();?>admin/user/add/<?php echo $this->uri->segment(4);?>">
                        <input type="radio" name="update_user_status" style="margin-left: 15px;" value="1" <?php echo ($form_data['user_status'] == 1)?'checked=checked':"";?> />Active
                        <input type="radio" name="update_user_status" value="2" <?php //echo ($form_data['user_status'] == 2)?'checked=checked':"";?> />In Active
                        <input type="radio" type="" name="update_user_status" value="3" <?php //echo ($form_data['user_status'] == 3)?'checked=checked':"";?> />Flag
                        <input type="submit" type="" name="user_status" class="btn btn-primary" style="margin-left: 20px; margin-bottom: 5px;" value="UPDATE" />
                      </form>
                    </div>
                </div>
			</div>
		</div>
        <div class="clear"></div>
     
    </div>
</div>
-->	

