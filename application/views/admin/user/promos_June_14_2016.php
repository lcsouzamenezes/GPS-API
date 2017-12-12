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