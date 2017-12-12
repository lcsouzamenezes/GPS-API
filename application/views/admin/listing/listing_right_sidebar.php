<?php foreach ($fields as $field => $values):?>
	<label class="checkbox">
		<input type="checkbox" name="<?php echo 'list_'.$field;?>" id="<?php echo 'list_'.$field;?>" value="<?php echo $field;?>" <?php echo ($values['default_view'] == '1')?('checked'):('');?> /><?php echo $values['name'];?>
	</label>
 <?php endforeach;?>
 