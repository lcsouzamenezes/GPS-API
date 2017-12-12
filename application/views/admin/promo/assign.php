<div id="content">
      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-6">
                <h4> <?php echo (!empty($title))?$title:""; ?> </h4>
            </div>
            <div class="col-lg-6" style="margin-top: 20px; margin-bottom: 10px; left:220px;">
                <a class="btn btn-primary" href="<?php echo site_url();?>admin/promo/index">BACK</a></a>
                
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
             <div class="col-lg-6">
                <?php
                    if(validation_errors()) {
                        echo validation_errors();
                    } 
                ?>
                
                 <form role="form" name="assign_promo" method="post">
                    <div class="form-group">
                        <label class="control-label">Users</label>
                        <select multiple="multiple">
                            <option>Select</option>
                            <?php if(count($users) > 0) {
                                    foreach($users as $ukey => $uvalue){ ?>
                                    <option value="<?php echo $uvalue['id'];?>"><?php echo $uvalue['default_id'];?></option>
                               <?php }}else{ echo "No Users Found";} ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Promos</label>
                        <select name="promo_code">
                            <option>Select</option>
                             <?php if(count($promos) > 0) {
                                    foreach($promos as $pkey => $pvalue){ ?>
                                    <option value="<?php echo $pvalue['id'];?>"><?php echo $pvalue['code'];?></option>
                               <?php }}else{ echo "No Users Found";} ?>
                        </select>
                    </div>
                    <input class="btn btn-primary" name="assign_promo" type="submit" value="ASSIGN" />
                </form>
              </div>
            </div>
        </div>
    </div>
</div>	