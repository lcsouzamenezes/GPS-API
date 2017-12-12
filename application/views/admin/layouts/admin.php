<!DOCTYPE html>
<html>
	<head>
	    
		<?php include_title(); ?>
        <?php include_metas(); ?>
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <?php include_links(); ?>
        <?php include_stylesheets(); ?>
        <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css'>
        <?php include_raws() ?>
        
        <script>
			//declare global JS variables here
			var base_url = '<?php echo base_url();?>';
			var current_controller = '<?php echo $this->uri->segment(1, 'admin').'/'.$this->uri->segment(2, 'index');?>';
			var current_method = '<?php echo $this->uri->segment(3, 'index');?>';
			var namespace = '<?php echo $this->namespace;?>';
			var previous_url = '<?php echo $this->previous_url;?>';
            var FCPATH  = '<?php echo FCPATH; ?>';
		</script>
        
        
	</head>


	<?php if( !is_logged_in() ): ?>
		<?php //$this->load->view('admin/_partials/header', $this->data); ?>
			
		<?php echo $content; ?>

		<?php //$this->load->view('admin/_partials/footer'); ?>

		<?php //$this->load->view('admin/_partials/config_tools'); ?>
		
	<?php else: ?>
	<body class="padTop53 admin_side" >
		<div id="wrap">
			<?php $this->load->view('admin/_partials/header', $this->data); ?>
			
					<?php $this->load->view('admin/_partials/left_menu', $this->data); ?>
						<?php echo $content; ?>
                    <?php //$this->load->view('admin/_partials/right_info', $this->data); ?>    
                    	</div>
					<?php $this->load->view('admin/_partials/footer'); ?>
				
			<?php //$this->load->view('admin/_partials/config_tools'); ?>
	
	<?php endif; ?>
	
		<!-- javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->

		<?php include_javascripts(); ?>
		
		<?php 
		
			if(is_array($this->init_scripts))
			{
				foreach ($this->init_scripts as $file)
					$this->load->view($file, $this->data);
			}
		?>
	</body>
</html>
