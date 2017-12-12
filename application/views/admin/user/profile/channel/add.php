<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-6">
            </div>
        </div>
        <div class="col-md-10 user-view">
        	<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"> <?php echo (!empty($title))?$title:""; ?></h3>
				</div>
				<div class="panel-body">
            <div class="row">
             <div class="col-lg-6">
                <?php
                    if(validation_errors()) {
                        echo validation_errors();
                    } 
                    
                    //print_r($form_data);
                ?>
                 <form role="form" name="channel" method="post">
                    <div class="form-group">
                        <label class="control-label">Channel ID</label>
                        <input readonly="readonly" type="text" name="join_key" class="form-control"  value="<?php echo (isset($form_data['join_key']) && !empty($form_data['join_key']))?$form_data['join_key']:""; ?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Map Available</label>
                        <input type="radio" name="map_avail"  value="permanent" <?php echo set_radio('map_avail', 'permanent', ($form_data['map_avail']=='permanent')?TRUE:""); ?>  />Permanent
                        <input type="radio" name="map_avail"  value="temporary" <?php echo set_radio('map_avail', 'temporary', ($form_data['map_avail']=='temporary')?TRUE:""); ?>  />Temporary
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label">Map Type</label>
                        <input type="radio" name="type"  value="public"  <?php echo set_radio('type', 'public', ($form_data['type']=='public')?TRUE:""); ?>  />Public
                        <input type="radio" name="type"  value="private" <?php echo set_radio('type', 'private', ($form_data['type']=='private')?TRUE:""); ?>  />Private
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label">Location Type</label>                                                                      
                        <input type="radio" name="location_type"  value="mobile" <?php echo set_radio('location_type', 'mobile', ($form_data['location_type']=='mobile')?TRUE:""); ?>  />Mobile
                        <input type="radio" name="location_type"  value="static" <?php echo set_radio('location_type', 'static', ($form_data['location_type']=='static')?TRUE:""); ?>  />Static 
                    </div>
                    <div class="form-group">
                        <input type="radio" name="allow_deny"  value="1"  <?php echo set_radio('allow_deny', '1', ($form_data['allow_deny']=='1')?TRUE:""); ?>  />Allow Deny
                    </div>
                    <div class="form-group">                                                                     
                        <input type="radio" name="allow_deny"  value="0" <?php echo set_radio('allow_deny', '0', ($form_data['allow_deny']=='0')?TRUE:""); ?>  />No Map Protection
                    </div>
                    <div class="form-group"> 
                        <label class="control-label">Require Password</label>                                                                     
                        <input type="radio" name="require_password" id="requirepassword" onclick="requirepassword();"  value="1"  />
                        <input type="text" style="display:none;" name="password" value="<?php echo set_value('password',(isset($form_data['password']))?$form_data['password']:""); ?>" />
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="SAVE"  />
                    </div>
                </form>
              </div>
            </div>
      	</div>
		</div>
        <div class="clear"></div>
     
    </div>
</div>	