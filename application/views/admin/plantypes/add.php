<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-6" style="margin-top: 20px; font-weight: bold;">
                
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
                
                 <form role="form" name="plantype" method="post">
                    <div class="form-group">
                        <label class="control-label">Select Plans</label>
                        <br />
                        <select name="plan_id" class="form-control">
                            <?php  if(count($form_data['plans'])) {
                                    foreach($form_data['plans'] as $pkey => $pvalue){
                              ?>          
                                <option <?php echo set_select('plan_id',$pvalue['id'],(($form_data['plan_id'] == $pvalue['id'])?true:false));?> value="<?php echo $pvalue['id'];?>"><?php echo $pvalue['planname'];?></option>  
                             <?php }} ?>
                        </select>
                    </div>
                   
                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo set_value('name',(isset($form_data['name']) && !empty($form_data['name']))?$form_data['name']:""); ?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Cost</label>
                        <br />
                        <input type="text" name="cost" class="form-control" value="<?php echo set_value('cost',(isset($form_data['cost']) && !empty($form_data['cost']))?$form_data['cost']:""); ?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Type</label>
                        <input type="text" name="type" class="form-control" value="<?php echo set_value('type',(isset($form_data['type']) && !empty($form_data['type']))?$form_data['type']:""); ?>"  />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <br />
                        <textarea name="description" class="form-control"><?php echo (isset($form_data['description']) && !empty($form_data['description']))?$form_data['description']:""; ?></textarea>
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
  