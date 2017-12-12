<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-12">
                <h1> Channel lists </h1>
            </div>
         </div>
         <div class="row">
            <div class="col-lg-12">
                <table>
                <tr>
                    <td>Profile Image</td>
                    <td>Join Key</td>
                    <td>Description</td>
                    <td>Allow Deny</td>
                    <td>Password Protection</td>
                    <td>Type</td>
                </tr>
                <?php //print_r($channel['channels']);
                      if(count($channel['channels'])>0){
                       foreach($channel['channels'] as $ckey => $cvalue){   ?>
                    <tr>
                     <td><img src="<?php echo $cvalue->profile_image; ?>" width="50" height="50" /></td>
                     <td><?php echo $cvalue->join_key; ?></td>
                     <td><?php echo $cvalue->group_description; ?></td>
                     <td><?php echo $cvalue->allow_deny; ?></td>
                     <td><?php echo $cvalue->password_protect; ?></td>
                     <td><?php echo $cvalue->type; ?></td>
                    </tr>
               <?php }} ?>
            </table>
            </div>
         </div>   
     </div>
</div>            