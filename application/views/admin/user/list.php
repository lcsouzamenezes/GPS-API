<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-12">
                <h1> Users </h1>
            </div>
            
        </div>
        <div class="row">
         <div class="col-md-12">
                <a onclick="return DeleteCheckedRow(this,'delete-user','admin/user/user_delete');" class="btn btn-primary">
                   <i class="fa fa-minus-circle fa-lg"></i> Delete
                </a>
            </div>
            
        </div>
            <?php echo $grid;?>
    </div>
</div>	

<div class="col-lg-12">
<div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="H3">PROMOS</h4>
            </div>
            <span id="msg" style="color:red; font-weight:bold; margin-left:100px !important; margin-top: 500px !important;"></span>
            <div class="modal-body">
            <div class="row">
                <div class="col-lg-12" id="promos">
                    
                </div>
            </div>
            <div class="clearfix" style="margin-top: 20px;"></div>
            <form role="form" name="assignpromo" action="" method="POST">
            <input type="hidden" name="assign_user_id" id="assign_user_id"  />
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Promo Codes</label>
                        <select name="promo_code" id="promo_code">
                            <option>Assign Promo</option>
                             <?php if(count($promos) > 0) {
                                    foreach($promos as $pkey => $pvalue){ ?>
                                    <option value="<?php echo $pvalue['id'];?>"><?php echo $pvalue['code'];?></option>
                             <?php }}else{ echo "No Users Found";} ?>
                        </select>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="assign_promo();">ASSIGN</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="pushnotification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="H3">Notification</h4>
            </div>
            <div class="modal-body">
          
            <div class="clearfix" style="margin-top: 20px;"></div>
            <form role="form" name="pushnotification" method="POST">
                <input type="hidden" name="notify_user_id" id="notify_user_id"  />
                <div class="row">
                    <div class="col-lg-12">
                        <!--
<div class="form-group">
                           <label>Title</label>
                           <br />
                           <input name="title" id="title" class="form-control" />     
                        </div>
-->
                        <div class="form-group">
                           <label>Messgage</label>
                           <br />
                           <textarea name="message" id="message" class="form-control"> </textarea>    
                        </div>
                       <!--
 <div class="form-group">
                           <label>Description</label>
                           <br />
                            <textarea name="description" id="description" class="form-control"></textarea>     
                        </div>
-->
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input  type="button" name="notification_submit" onclick="send_notification();" class="btn btn-primary"  value="SEND" />
                </div>
            </form>
        </div>
    </div>
</div>
