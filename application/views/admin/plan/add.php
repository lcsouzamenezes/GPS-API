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
                
                 <form role="form" name="plan" method="post">
                    <div class="form-group">
                        <label class="control-label">Plan Name</label>
                        <input type="text" name="planname" id="planname" class="form-control"  value="<?php echo set_value('planname',(isset($form_data['planname']) && !empty($form_data['planname']))?$form_data['planname']:""); ?>" />
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">Validity</label>
                        <input type="text" name="validity" class="form-control" value="<?php echo set_value('validity',(isset($form_data['validity']) && !empty($form_data['validity']))?$form_data['validity']:""); ?>"  />
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
