<div id="content">          
    <div class="inner dashboard-section" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-12">
                <h1> Dashboard </h1>
            </div>
        </div>
         <div class="col-md-8 dash-board clearfix">
         <div class="col-sm-5 total-user">
         	<div class="t1"><h2><?php echo (!empty($total_users))?$total_users['cnt']:0; ?></h2>
            <p>Total User</p></div>
            <i class="icon-users dash-icon" aria-hidden="true"></i>
    
         </div>
          <div class="col-sm-5 user">
          	<div class="t1"><h2><?php echo (!empty($tot_sub_users))?$tot_sub_users['cnt']:0; ?></h2>
            <p>Subscribed Users</p></div>
             <i class="icon-users dash-icon" aria-hidden="true"></i>
         </div>
         
          <div class="col-sm-5 customer">
          	<div class="t1"><h2><?php echo ucfirst($recently_added['default_id']); ?></h2>
            <h4><?php echo date("D m Y", $recently_added['date_created']); ?></h4>
            <p>Recently Added Customer</p></div>
             <i class="icon-user dash-icon" aria-hidden="true"></i>
         </div>
          <div class="col-sm-5 sales">
          	<div class="t1"><h2>2,123</h2>
            <p>Last Month Sales</p></div>
             <i class="icon-usd dash-icon" aria-hidden="true"></i>
         </div>
         </div>
    </div>
</div>
<!--END PAGE CONTENT -->
        
       