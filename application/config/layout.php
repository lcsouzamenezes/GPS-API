<?php
 
//Config for Admin End
                                         
$config['layout']['admin']['js_dir']   = base_url('assets/admin/');
$config['layout']['admin']['css_dir']  = base_url('assets/admin/');
$config['layout']['admin']['img_dir']  = base_url('assets/admin/images');
$config['layout']['admin']['template'] = 'admin/layouts/admin';
$config['layout']['admin']['title']    = 'HeresMYGPS.com';
$config['layout']['admin']['javascripts'] = array('plugins/jquery-2.0.3.min','plugins/bootstrap/js/bootstrap'); 
$config['layout']['admin']['stylesheets'] = array('plugins/bootstrap/css/bootstrap');
$config['layout']['admin']['description'] = '';
$config['layout']['admin']['keywords']    = '';
$config['layout']['admin']['http_metas'] = array(
    'Content-Type' => 'text/html; charset=utf-8',
	'viewport'     => 'width=device-width, initial-scale=1.0',
    'author' => 'World Health Organization',
    'X-UA-Compatible' => 'IE=edge,chrome=1'
);


// Config for FrontEnd

$config['layout']['frontend']['js_dir']   = base_url('assets/frontend/js');
$config['layout']['frontend']['css_dir']  = base_url('assets/frontend/css');
$config['layout']['frontend']['img_dir']  = base_url('assets/frontend/images');
$config['layout']['frontend']['template'] = 'frontend/layouts/frontend';
$config['layout']['frontend']['title']    = 'HeresMYGPS.com';

$config['layout']['frontend']['javascripts'] = array("jquery-2.1.1.min","jquery.min","jquery.elevateZoom","jquery.migrate","modernizrr","jquery.fitvids","nivo-lightbox.min","jquery.isotope.min","jquery.appear","count-to","jquery.textillate","jquery.lettering","jquery.easypiechart.min","jquery.nicescroll.min","jquery.parallax","mediaelement-and-player","script","owl.carousel","owl.carousel.min","smk-accordion","common","tools","officespace");
 
$config['layout']['frontend']['stylesheets'] = array("bootstrap","bootstrap.min", "bootstrap-theme","datepicker","bootstrap-theme.min","font-awesome.min","font-awesome","custom","animate","responsive","sasha","style","colors/orange");

$config['layout']['frontend']['description'] = '';
$config['layout']['frontend']['keywords']    = '';

$config['layout']['frontend']['http_metas'] = array(
    'Content-Type' => 'text/html; charset=utf-8',
	'viewport'     => 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0',
    'X-UA-Compatible' => 'IE=edge,chrome=1'
);




?>
