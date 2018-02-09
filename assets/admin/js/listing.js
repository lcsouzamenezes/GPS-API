	
	(function ( $ ) {
		
		var listing_form_data = {};
		
		var progress_bar_flag = false;
		
		var grids = {};
		grids.notes_table = {pagination:'notes_pagination', search_option:false,search_bar:false,sidebar:false};		
		grids.logs_table = {pagination:'logs_pagination', search_option:false,search_bar:false,sidebar:false};
		
		if(namespace == "create_sales_order_index"){
			$("input[name='search_text']").keyup(function() {
		        var textbox = $(this);
		        if (textbox.val().length > 2 ) {
		          $("#simple_search_button").trigger('click');
		          return false;
		        }  
	  		});
		}	
		$.fn.zoomgrid = function( options ) { 
			
			//merge options with the default
			options = $.extend($.fn.zoomgrid.defaults, options);
			
			//assign the current data_table id
			options.data_tbl = $(this).attr('id');
			
			//initiate grid
			$.fn.init_grid(options);
			
			//initiate sidebar if flag is true
			if(options.sidebar == true)
				$.fn.init_sidebar(options);
			
			//initiate search_bar if flag is true
			if(options.search_bar == true)
				$.fn.init_search_bar(options);
			
			
			//set flag as 'false' for both search_bar and sidebar.It will avoid duplicate initiations(morethan once)
			//If these will be initialized more than once, More Ajax call will happen. 
			$.fn.zoomgrid.defaults.search_bar = false;
			$.fn.zoomgrid.defaults.sidebar    = false;

			
		};
						
		// Establish our default settings				
		$.fn.zoomgrid.defaults = {
				pagination:'pagination',
				search_option:true,
				search_bar:false,
				sidebar:false
				};
		
	
		$.fn.init_grid = function( options ) { 
			
			$("#"+options.data_tbl).find("thead").find('a').each(function(index,elm){ 
				tmp = "javascript:$.fn.display_grid('"+$(this).attr('href')+"', '"+options.data_tbl+"');";
				$(this).attr({'href':'javascript:void(0);', 'onclick': tmp}).css({'cursor':'pointer'});
				
			});
			
			$("#"+options.data_tbl).parent().find("#"+options.pagination).find('a').each(function(){
				tmp = "javascript:$.fn.display_grid('"+$(this).attr('href')+"', '"+options.data_tbl+"');";
				$(this).attr({'href':'javascript:void(0);', 'onclick': tmp}).css({'cursor':'pointer'});				
			});
			
			//alert($("#"+options.data_tbl).parent().find("#"+options.pagination).html());
			/*$("#"+options.data_tbl).parent().find("#"+options.pagination).find('a').bind('click', function(){
				$.fn.display_grid($(this).attr('href'), options.data_tbl);
				return false;
			});*/
			
		};
		
		$.fn.init_search_bar = function(options) {
			
			var target_url = base_url+current_controller+'/'+current_method;
			if(options.bae_url)
				target_url = options.bae_url;
			if(options.namespace)
				namespace = options.namespace;
			
			$("#simple_search_button").bind('click', function(){
				listing_form_data = $("#simple_search_form").serialize();
				$.fn.display_grid(target_url, 'data_table');
			});
			
			/*$("input[name='search_text']").enterKey(function () {
				$("#simple_search_button").trigger('click');
				return false;
			});*/
			
			$.fn.get_advance_search_form();
			
			$("select[name='per_page_options']").bind('change', function(){
				$.post(base_url+current_controller+'/set_records_per_page/'+namespace,{per_page:$(this).val()},function(){
					$.fn.display_grid(target_url, 'data_table');
				}, 'json');
			});
			
		};
		
		$.fn.init_sidebar = function( options ) {
			
			$("#slide_panel_right h2 a").click(function(e){
				e.preventDefault();
				var div = $("#slide_panel_right");
				console.log(div.css("right"));
				if (div.css("right") === "-195px") {
					$.post(base_url+current_controller+'/get_fields_sidebar/'+namespace,{},function(data){
						$("#grid_columns").html(data.fields_sidebar);
						$("#slide_panel_right").animate({
							right: "0px"
						}); 
						$('input[name^="list_"]').bind('change', function(){
							listing_form_data = {};
							listing_form_data.action = this.checked?'add_field':'remove_field';
							listing_form_data.field  = $(this).val();
							$.fn.display_grid(base_url+'/'+current_controller+'/'+current_method, 'data_table');
							
						});
					}, 'json');
				} else {
					$("#slide_panel_right").animate({
						right: "-195px"
					});
				}
			});
						
		};
						
		$.fn.display_grid = function( url, data_tbl ) {
			
			if(data_tbl == 'notes_table')
				opts =  grids.notes_table;
			else if(data_tbl == 'logs_table')
				opts =  grids.logs_table;
			else
				opts = $.fn.zoomgrid.defaults;
			
			progress_bar_flag = true;
			
			$.fn.init_progress_bar();
			
			$.post(url,listing_form_data,function(rdata){
				
				$("#"+data_tbl).parent().html(rdata.listing);
				
				$( "#"+data_tbl ).zoomgrid(opts);
				
				if(opts.search_option == true)
				{
					$.fn.get_advance_search_form();	
					
					$('a').tooltip();
					
					
				}
				
				//inti_tooltip_on_grid(data_tbl);

				if(namespace == 'vendors_price_list' || namespace == 'user_price_list')
				{
					init_inline_edit();
				}
				
			}, 'json');
		};
						
		
						
		$.fn.clear_simple_search = function(){
			listing_form_data = {};
			//$("select[name='search_type']").selectpicker('deselectAll');
			$("input[name='search_text']").val('');
			$("#simple_search_button").trigger('click');
		};
						
		$.fn.get_advance_search_form = function(){
			if($('.advancesearch').length)
			{
				$.post(base_url+current_controller+'/get_advance_filter_form/'+namespace,{},function(data){
					
					$("#popOverBox").html(data.advance_filter_form);
					//reconstruct the advance serch form
					$('#advancesearch').remove();
					tmp = '<section id="advancesearch" class="pull-left"><div class="btn-group">';
					tmp += '<button class="btn pad_4"><i class="icon-cog"></i></button>';
					tmp += '<a href="#" class="btn" data-toggle="popover" data-placement="top">Advanced Search <span class="caret"></span></a>';
					tmp += '</div></section>';
					tmp += '<button type="button" class="btn btn-link" onclick="$.fn.clear_advance_search();" data-placement="top" data-toggle="tooltip" data-original-title="clear advanced search">Clear Filter</button>';
					$('.advancesearch').html(tmp);
					$('button').tooltip();
					$('#advancesearch').popover( {
					      placement : 'bottom', //placement of the popover. also can use top, bottom, left or right
					      title : 'Advanced - Refine search <button class="close" onclick="$(\'#advancesearch\').popover(\'hide\')">&times;</button> ', //this is the top title bar of the popover. add some basic css
					      html: 'true', //needed to show html of course
					      content : $("#popOverBox").html(),//this is the content of the html box. add the image here or anything you want really.
					      callback: function() { 
					    	  init_datepicker();
					      } 
					}).click(function (e) {
				        e.preventDefault();
					});
					//if($("#cur_page").val() == '0')
						//inti_tooltip_on_grid('data_table');
				}, 'json');
			}
		};
						
		$.fn.submit_advance_search_form = function(){
			listing_form_data = {};
			listing_form_data = $("#advance_search_form").serialize();
			$.fn.display_grid(base_url+current_controller+'/'+current_method, 'data_table');
		};
			
		$.fn.clear_advance_search = function(){
			listing_form_data = {};
			listing_form_data.clear_advance_search = 'yes';
			$.fn.display_grid(base_url+current_controller+'/'+current_method, 'data_table');
		};

		$.fn.custom_search = function(field, txt, obj){
			
			listing_form_data = {};
			//clear both simple and advanced search
			listing_form_data.clear_advance_search = 'yes';
			listing_form_data.clear_simple_search = 'yes';
			listing_form_data.custom_search = 'yes';
			listing_form_data[field] = txt;
			if(namespace == 'shipment_index')
			{
				listing_form_data['back_order'] = 'all';
				listing_form_data['pre_order']  = 'all';
			}
			if(obj)
			{
				listing_form_data[obj.key] = obj.val;
			}
			
			$.fn.display_grid(base_url+current_controller+'/'+current_method, 'data_table');
		};
		
		//initiate progress bar.It will show progress bar based on the ajax-states.
		$.fn.init_progress_bar = function(){
									
			$(document).ajaxStart(function() {
				if(progress_bar_flag)
				{
					html = '<div id="listing_progress_bar" class="progress progress-success progress-striped" style="left: 0px; right: 0px; margin: 0px auto; width: 30%; z-index: 9999; position: fixed; top: 60%;">';
					html += '<div style="width: 100%;" class="bar"></div>';
					html += '</div>';
					$('body').append(html);
					$("#listing_progress_bar div").css('width', '50%');
				}
				
			});
			
			$(document).ajaxSend(function() {
				if(progress_bar_flag)
					$("#listing_progress_bar div").css('width', '75%');
				
			});
			
			$(document).ajaxSuccess(function(event, xhr, options) {
				if(progress_bar_flag)
				{
                    
					$("#listing_progress_bar div").css('width', '100%').animate({
					opacity: 0.25
					}, 500, function() {
						$("#listing_progress_bar").remove();
						progress_bar_flag = false;
					});	
				}
                
                if($('.drag_drop').length) {
                    //drag_drop();
                }												
			});
			
		};
								
	}( jQuery ));
	
	
