
<header class="main-box-header clearfix">

<div class="clearfix manual-fields" style="margin:15px 0">
  
  <!-- -->
  <div class="row">
  <?php  if(isset($bulk_actions)):?>
  
  <?php if($this->uri->segment(2) == 'contact_form') {?>
    <div class="col-lg-3">
    <div class="col-xs-12 input-group">
      <span class="col-xs-9">
        <?php echo form_dropdown('action', $bulk_actions, '', 'id="action" class="form-control custom-input " aria-controls="table-example"');?>
      </span>
      <button class="col-xs-2 btn btn-success" type="button" onclick="bulk_action();">Go</button>
    </div> 
    </div> 
    <?php }?>
    <?php endif;?>
    <form method="post" id="simple_search_form">
    <div class="col-sm-5 col-lg-2 mB_10">
    <div class="col-xs-12 input-group">
      <?php echo form_dropdown('search_type', $simple_search_fields, $search_conditions['search_type'], 'class="form-control custom-input"');?>
      
    </div>
    </div>
    
    <div class="col-sm-6 col-lg-3 mB_10">
    <div class="row">
    <div class="col-xs-12 input-group">
      <div class="col-xs-12 input-group">     
        <input type="text" id="exmaplePrependButton" name="search_text" class="form-control" placeholder="Type to filter..">
        <span class="input-group-btn">
          <button type="button" class="btn btn-primary" id="simple_search_button" >Go!</button> &nbsp;&nbsp;
        </span>
        
      </div>

    </div>
    </div>
    </div>
    
    <div class="col-sm-2 col-lg-1 mB_10">
    <div class="col-xs-6 input-group">
      <span class="col-xs-10">
        
      </span>
     <button class="btn btn-primary btn-green" type="button" onclick="$.fn.clear_simple_search();" data-original-title="clear search" data-toggle="tooltip" data-placement="top" >Clear</button>
    </div>
    </div>
    </form>
   <!-- <div class="col-lg-2">
    <div class="col-xs-12 input-group">
      &nbsp;&nbsp;<!--<select name="table-example_length" aria-controls="table-example" class="form-control custom-input input-sm">
        <option value="">Advanced Search</option>
      </select>
    </div>
    </div>-->
    
    <div class="col-sm-8 col-md-3 mB_10 show-details">
    <div class="col-md-12 input-group">
      <span class="col-xs-3 col-md-3 mT_6">Show</span>
      <span class="col-xs-6 col-md-6">
        <?php echo form_dropdown('per_page_options', $per_page_options, $per_page, 'aria-controls="table-example" class="form-control custom-input perpage"');?>
      </span>
      <span class="col-xs-3 col-md-3 mT_6">
      <div class="row">
      Entries</div>
       </span>
      
    </div>
    </div>
    
    
    
    
    
  </div>
  <!-- -->
</div>
</header>





























