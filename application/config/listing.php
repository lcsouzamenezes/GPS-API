<?php
/*
 * view - the path to the listing view that you want to display the data in
 * 
 * base_url - the url on which that pagination occurs. This may have to be modified in the 
 * 			controller if the url is like /product/edit/12
 * 
 * per_page - results per page
 * 
 * order_fields - These are the fields by which you want to allow sorting on. They must match
 * 				the field names in the table exactly. Can prefix with table name if needed
 * 				(EX: products.id)
 * 
 * OPTIONAL
 * 
 * default_order - One of the order fields above
 * 
 * uri_segment - this will have to be increased if you are paginating on a page like 
 * 				/product/edit/12
 * 				otherwise the pagingation will start on page 12 in this case 
 * 
 * 
 */
 



$config['admin_user_index'] = array(
	"view"		=> 	'admin/listing/listing',
	"init_scripts" => 'admin/listing/init_scripts',
	"advance_search_view" => 'admin/user/filter',
	"base_url"	=> 	'/admin/user/index/',
	"per_page"	=>	"20",
	"fields"	=> array(
							'default_id'=>array('name'=>'Default ID', 'data_type' => 'username', 'sortable' => TRUE, 'default_view'=>1),
                            'date_created' =>array('name'=>'Date Of Creation', 'data_type' => 'date_create', 'sortable' => TRUE, 'default_view'=>1),
							//'phonenumber'=>array('name'=>'Phone', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
							//'display_name'=>array('name'=>'Display Name', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                           // 'email'=>array('name'=>'Email', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'login_type'=>array('name'=>'User Type', 'data_type' => 'login_type', 'sortable' => TRUE, 'default_view'=>1),
                            'email' => array('name'=>'Current Promo Code', 'data_type' => 'curr_promo', 'sortable' => TRUE, 'default_view'=>1), 
                            'display_name' => array('name'=> "#CHID's", 'data_type' => 'channels_ct', 'sortable' => FALSE, 'default_view'=>1),
                            'user_status'=>array('name'=>'Status', 'data_type' => 'userstatus', 'sortable' => TRUE, 'default_view'=>1),
                            'last_updated'=>array('name'=>'Updated time State', 'data_type' => 'last_updated', 'sortable' => TRUE, 'default_view'=>1),
                            'last_updated'=>array('name'=>'Blocks', 'data_type' => 'blocks', 'sortable' => TRUE, 'default_view'=>1),
                            'password_protect'=>array('name'=>'Flags', 'data_type' => 'flags', 'sortable' => TRUE, 'default_view'=>1)
						),
	"default_order"	=> "id",
	"default_direction" => "DESC"
);


$config['admin_plan_index'] = array(
	"view"		=> 	'admin/listing/listing',
	"init_scripts" => 'admin/listing/init_scripts',
	"advance_search_view" => 'admin/plan/filter',
	"base_url"	=> 	'/admin/plan/index/',
	"per_page"	=>	"20",
	"fields"	=> array(
							'planname'=>array('name'=>'Plan Name', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
							'validity'=>array('name'=>'Validity', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1)
						),
	"default_order"	=> "id",
	"default_direction" => "DESC"
);

$config['admin_plantype_index'] = array(
	"view"		=> 	'admin/listing/listing',
	"init_scripts" => 'admin/listing/init_scripts',
	"advance_search_view" => 'admin/plantype/filter',
	"base_url"	=> 	'/admin/plantype/index/',
	"per_page"	=>	"20",
	"fields"	=> array(
							'name'=>array('name'=>'Plan Type', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
							'plan_id'=>array('name'=>'Plan Name', 'data_type' => 'planname', 'sortable' => TRUE, 'default_view'=>1),
                            'cost'=>array('name'=>'Cost', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'type'=>array('name'=>'Type', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1)
						),
	"default_order"	=> "id",
	"default_direction" => "DESC"
);

$config['admin_promo_index'] = array(
	"view"		=> 	'admin/listing/listing',
	"init_scripts" => 'admin/listing/init_scripts',
	"advance_search_view" => 'admin/promo/filter',
	"base_url"	=> 	'/admin/promo/index/',
	"per_page"	=>	"20",
	"fields"	=> array(
							'code'=>array('name'=>'Promo Code', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
							'expire'=>array('name'=>'Expire', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'purchased_from'=>array('name'=>'Purchased From', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1)
						),
	"default_order"	=> "id",
	"default_direction" => "DESC"
);

$config['admin_profile_channels'] = array(
	"view"		=> 	'admin/listing/listing',
	"init_scripts" => 'admin/listing/init_scripts',
	"advance_search_view" => 'admin/profile/filter',
	"base_url"	=> 	'/admin/profile/channels/',
	"per_page"	=>	"20",
	"fields"	=> array(
                            'display_name'=>array('name'=>'Display Name', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
							'name'=>array('name'=>'Group Name', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
							'join_key'=>array('name'=>'Joinkey', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'allow_deny'=>array('name'=>'Allow Deny', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'password_protect'=>array('name'=>'Password Protection', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'satiation_id'=>array('name'=>'Station Id', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'profile_image'=>array('name'=>'Profile Image', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'type'=>array('name'=>'Type', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
                            'location_type'=>array('name'=>'Location Type', 'data_type' => 'string', 'sortable' => TRUE, 'default_view'=>1),
						),
	"default_order"	=> "id",
	"default_direction" => "asc"
);