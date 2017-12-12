<?php if ($message = $this->service_message->render()) :?>
<?php echo $message;?>
<?php endif; ?>

<!-- button tool bar section start here -->

<?php echo $search_bar;?>



<div class="row">
	<div class="col-lg-12">
		<div class="main-box no-header clearfix">
			<div class="main-box-body clearfix">
				<div class="table-responsive">
				<form name="<?php echo $this->namespace;?>"
				id="<?php echo $this->namespace;?>"
				action="<?php echo site_url($this->uri->segment(1, 'admin').'/'.$this->uri->segment(2, 'index').'/bulk_actions');?>"
				method="post">
					<?php echo $listing;?>
				</div>
			</div>
		</div>
	</div>
</div>


<!--Advanced Search Popup content starts here-->
<div id="popOverBox" style="display: none;"></div>


