<div class="row-fluid">
						<h2>Log History</h2>
</div>					
<table class="table table-striped table-bordered fancyTable" id="logs_table">
	<thead class="greenbg_title nowrap">
		<tr>
			<th>Description</th>
			<th>Created Time</th>
			<th>Created By</th>
		</tr>
	</thead>
	<tbody class="white_bg">
		<?php if(count($list)):?>
			<?php foreach ($list as $item) : ?>
		<tr class="txt_11">
			<td><?php echo $item['action'];?></td>
			<td><?php echo displayData($item['created_time'], 'datetime');?></td>
			<td><?php echo strcmp(trim($item['admin_user']), '') === 0?'HMS':$item['admin_user'];?></td>
		</tr>		
			<?php endforeach; ?>
		<?php endif;?>
		
	</tbody>
</table>
<?php /*if($count):?>    
<div class="span12">  
	<div class="pagination pagination-mini" id="logs_pagination">  
		<?php echo $pagination ?>
	</div>
</div>
<?php endif;*/ ?>             	

