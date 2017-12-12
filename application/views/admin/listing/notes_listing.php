<?php if(count($list)):?>
<div class="row-fluid notes_sec scroll_notes">
				
	<table class="table table-bordered" id="notes_table">
		<tbody class="white_bg">			
			<?php foreach ($list as $item) : ?>				
			<tr>
			<td class="pad_b_none">
				<small><?php echo displayData($item['created'], 'datetime');?> - <?php echo $item['admin_user'];?> wrote:</small>
				<p><?php echo $item['message'];?></p>
			</td>
			</tr>				
			<?php endforeach; ?>		
			
		</tbody>
	</table>

</div>
<?php else:?>
<div class="row-fluid notes_sec" id="notes_list" >
	No notes found.
</div>
<?php endif;?>

<?php /*if($count):?>    
<div class="span12">  
	<div class="pagination pagination-mini" id="notes_pagination">  
		<?php echo $pagination ?>
	</div>
</div>
<?php endif; */ ?>             	

