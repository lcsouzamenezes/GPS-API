 <!--PAGE CONTENT -->
<div id="content">
    <div class="inner" style="min-height:1200px;">
        <div class="row">
            <div class="col-lg-12">
                <h2>Plan Access</h2>
            </div>
        </div>
    <hr />
    
    <div class="pro-container table-responsive">
       <?php
            if(validation_errors()) {
                echo validation_errors();
            } 
        ?>
       <form name="planaccess" method="POST" >
       <table width="100%" border="1" cellspacing="0" cellpadding="0" class="price-table">
        <tr class="top-bg">
            <td  align="center" valign="middle"><strong class="white-color">Promo Codes</strong></td>
            <td  align="center" valign="middle"><strong class="yellow-color">Free Guest</strong></td>
            <td  align="center" valign="middle"><strong class="yellow-color">Free App</strong></td>
            <td  align="center" valign="middle"><strong class="green-color">Pro 10</strong></td>
            <td  align="center" valign="middle"><strong class="green-color">Pro 50</strong></td>
<?php /*
            <td  align="center" valign="middle"><strong class="sky-blue">Business200</strong></td>
            <td  align="center" valign="middle"><strong class="sky-blue">Business1000</strong></td>
*/ ?>
        </tr>
        <tr>
            <td  align="center" valign="middle"><h1 class="hmgps-title">HMGPS Plans</h1></td>
            <td colspan="2" align="center" valign="middle"><h1>Free Plans<img src="assets/img/i-icon.png" alt=""></h1></td>
            <td colspan="2" align="center" valign="middle"><h1 class="hmgps-title">Pro Plans <img src="assets/img/i-icon.png" alt=""></h1></td>
<?php /*
            <td colspan="2" align="center" valign="middle"><h1 class="business-title">Business Plans<img src="assets/img/i-icon.png" alt=""></h1></td>
*/ ?>
        </tr>
      
        <tr>
            <td align="center" valign="middle" bgcolor="#000000">&nbsp;</td>
            
            <td class="yellow" align="center" valign="middle" bgcolor="#000000">Free</td>
            
            <td class="yellow" align="center" valign="middle" bgcolor="#000000">Free</td>
            
            <td class="green bld" align="center" valign="middle" bgcolor="#000000">$5 One Time </td>
            
            <td class="green bld" align="center" valign="middle" bgcolor="#000000">$9 One Time </td>
         <?php /*   
            <td class="blue1 bld" align="center" valign="middle" bgcolor="#000000">$30 One Time </td>
            
            <td class="blue1 bld" align="center" valign="middle" bgcolor="#000000">$40 Monthly</td>
            */ ?>
        </tr>
   
        <?php foreach($fields as $fkey => $fvalue) {   ?>
        <tr>
            <td align="left" valign="middle"><?php echo $field_name[$fkey]; ?></td>
               <?php 
                  for($i=0;$i<count($planaccess[$fkey]); $i++) { 
                       $value   = (!isset($planaccess[$fkey][$i]))?"0":$planaccess[$fkey][$i];
                       $checked = (isset($planaccess[$fkey][$i]) && ($planaccess[$fkey][$i]==1))?'checked="checked"':"";
                ?>
                    <td align="center" valign="middle" bgcolor="#cdffcc">
                     <?php if(($planaccess[$fkey][$i] == 1 || $planaccess[$fkey][$i] == 0 ) && ($fkey != 'number_of_joined_user_allowed_on_one_map') && ($planaccess[$fkey][$i] != 'na')) { ?>
                        <p class="">
                            <input type="checkbox" name="<?php echo $fkey.$i;?>"  <?php echo $checked; ?>  />
                            <label for="test1"></label>
                        </p>
                        <?php }else if($fkey == 'number_of_joined_user_allowed_on_one_map'){ ?>
                            <input type="text" name="<?php echo $fkey.$i;?>" value="<?php echo $value; ?>" />
                        <?php }else { ?>
                         na <input type="hidden" name="<?php echo $fkey.$i;?>"  value="0"  />
                        <?php } ?>
                    </td>
                <?php } ?>
        </tr>
        <?php }  ?>  
        <tr>
            <td colspan="7"> <input type="submit" name="planaccess" class="btn btn-primary" value="SAVE" /></td>
        </tr>
       </form>
     </table> 
    </div>
  </div>
</div>
