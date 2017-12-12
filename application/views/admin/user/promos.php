<span id="msg" style="font-size:18px; color:#218c92;font-weight:bold;"></span>
<table class="table user-list table-hover" style="height:10px !important;">
<thead>
    <th>Promo Code</th>
    <th>Assign Date</th>
    <th>Expire Date</th>
</thead>
<tbody> 
    <?php if(count($assignedpromos) > 0){
         foreach($assignedpromos as $apkey => $apvalue){ ?>
         <tr>
            <td><?php echo $apvalue['code'];?></td>
            <td><?php echo $apvalue['assign_date'];?></td>
            <td><?php echo $apvalue['expire_date'];?></td>
         </tr>
            
    <?php } } else { ?>
        <tr>
            <td colspan="3"> No Promos Found.</td>
        </tr>
    <?php } ?>
   
</tbody>
</table>

<div class="clearfix" style="margin-top: 20px;"></div>
<form role="form" name="assignpromo" action="" method="POST">
<input type="hidden" name="assign_user_id" value="<?php echo $this->uri->segment(4);?>" id="assign_user_id"  />
<div class="row">
    <div class="col-lg-12">
     
        <div class="form-group">
                <label>Promo Codes</label>
                <select name="promo_code" id="promo_code">
                    <option>Assign Promo</option>
                     <?php if(count($promos) > 0) {
                            foreach($promos as $pkey => $pvalue){ ?>
                            <option value="<?php echo $pvalue['id'];?>"><?php echo $pvalue['code'];?></option>
                     <?php }}else{ echo "No Promos Found";} ?>
                </select>
            </div>
        
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="assign_promo();">ASSIGN</button>
</div>
</form>