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
                ?>
                
                 <form role="form" name="promo" method="post">
                    <div class="form-group">
                        <label class="control-label">Select Plan</label>
                        <select name="plan_id">
                             <option value="select">Select Plan</option>
                            <?php if(count($plans)) { foreach($plans as $pkey=>$pvalue){ if($pvalue['planname']!='Free'){ ?>
                                
             <option value="<?php echo $pvalue['id'];?>" <?php if(isset($form_data['plan_id']) && !empty($form_data['plan_id']) && ($form_data['plan_id']==$pvalue['id'])){echo 'selected="selected"';} ?>><?php echo ucfirst($pvalue['planname']); ?></option>
                           <?php     
                            }}} ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Promo Code</label>
                        <input type="text" name="code" id="promo_code" class="form-control" value="<?php echo set_value('code',(isset($form_data['code']) && !empty($form_data['code']))?$form_data['code']:""); ?>" />
                        <input type="button"  onclick="generate_promo();" class="btn btn-primary" value="GENERATE PROMO" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Purchased From</label>
                        <input type="text" name="purchased_from" class="form-control"  value="<?php echo (isset($form_data['purchased_from']) && !empty($form_data['purchased_from']))?$form_data['purchased_from']:""; ?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Expire Date</label>
                        <input type="text" name="expire" class="form-control" id="expire" placeholder="YYYY-MM-DD" value="<?php echo set_value('expire',(isset($form_data['expire']) && !empty($form_data['expire']))?$form_data['expire']:""); ?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Participant Count</label>
                        <input type="text" name="participant_count" class="form-control" value="<?php echo set_value('participant_count',(isset($form_data['participant_count']) && !empty($form_data['participant_count']))?$form_data['participant_count']:""); ?>"  />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Number Of Time to Use</label>
                        <input type="text" name="number_of_time_to_use" class="form-control" value="<?php echo set_value('number_of_time_to_use',(isset($form_data['number_of_time_to_use']) && !empty($form_data['number_of_time_to_use']))?$form_data['number_of_time_to_use']:""); ?>"  />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Number of Time Per User</label>
                        <input type="text" name="number_of_user_to_use" class="form-control" value="<?php echo set_value('number_of_user_to_use',(isset($form_data['number_of_user_to_use']) && !empty($form_data['number_of_user_to_use']))?$form_data['number_of_user_to_use']:""); ?>" />
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