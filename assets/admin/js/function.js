var triggered_element = '';
var elm_old_text; // The text value of element that started  ajax_process


//application-wide common functions initialization goes here.
$(function() {

 //drag_drop();  
});
$(document).ready(function() {
	
	$('.drag_drop tr').hover(function() {
          $(this.cells[0]).addClass('showDragHandle');
   }, function() {
         $(this.cells[0]).removeClass('showDragHandle');
   });
});


/*The functions "before_ajax" is to avoid double-click on anchors or buttons.
 * It should be called before each ajax process start.
 * And also it replaces the original text or value by the argument 'content' passed by called function.
 */ 

function before_ajax(elm, content)
{
	if(!$(elm).length)
		return false;

	content=content?content:'Processing...';
	
	if($(elm).attr("disabled")=="disabled")
    {
		return false;
    }
	else
	{
		$(elm).attr("disabled","disabled");
		
		if(content)
		{
			var w1 = $(elm).width();
			
			if($(elm).is("a"))
			{
			 elm_old_text = $(elm).html();
             
			 if($(elm).hasClass("add_loader_class")){
			     $(elm).addClass($(elm).attr("data-add_loader_class"));
			 }
             else
             {
				$(elm).text(content);
             }
			
			}
			else if($(elm).is("button"))
			{
				elm_old_text = $(elm).text();
				$(elm).text(content);
			}
			else if($(elm).attr("type") == 'checkbox' && $(elm).parent().hasClass('add-on') && $(elm).parent().next().hasClass('add-on'))
			{
				elm_old_text = $(elm).parent().next().html();
				$(elm).parent().next().html(content);
			}
			
			var w2 = $(elm).width();
			
			if(w2<w1)
				$(elm).width(w1);
			
		}
			
		return true;
	}
	
}

/*The functions "after_ajax" should be called after ajax-process end.
 * It enables the button or anchor elemnts and resets thier original text.
 */ 

function after_ajax(elm,data)
{
    
	if($(elm).length)
	{
		if($(elm).is("a,span"))
		{
		    if($(elm).hasClass("add_loader_class")){
			     $(elm).removeClass($(elm).attr("data-add_loader_class"));
	        }
        
            $(elm).removeAttr("disabled");
            $(elm).html(elm_old_text);
			
		}
		else if($(elm).is("button"))
		{
			$(elm).removeAttr("disabled");
			$(elm).text(elm_old_text);
		}
		else if($(elm).attr("type") == 'checkbox' && $(elm).parent().hasClass('add-on') && $(elm).parent().next().hasClass('add-on'))
		{
			$(elm).removeAttr("disabled");
			$(elm).parent().next().html(elm_old_text);
		}
	}
	
    if(data.access_status && data.access_status == 'denied')
    {
    	if(data.access_message && data.access_message != '')
    		alert(data.access_message);
    	else
    		alert("Access denied.");
        
        return false;
        
    }
    else if(data.session_status && data.session_status == 'destroyed')
    {
        alert("Oops!! Your session has expierd.");
        location.href = base_url+'login';
        return false;
        
    }
    
	return true;	
	
}



/*Ajax save popup form for both add/edit.
 *action : form submit url.
 *div_id :the div which has submit form. Note: "div should contain only one form"
 *save_type:@string 'add' or 'edit'
 *call_back_fn: the function should be called after success.(optional)
 *elm: object of clicking tag.
 */
function save_form(action,div_id,save_type,elm,call_back_fn,popup){

    if(action == 'user/add'){
      $('#addBusiness').modal('hide');
    } 
     //remove previous service message
     $("#div_service_message").remove();
     
     call_back_fn = call_back_fn?call_back_fn:false;
     popup = popup?popup:false;
     
     var loader_content = (popup)?"Saving..":"";       
     
     if(!before_ajax(elm, loader_content))
      return false;
     
     if(div_id == 'user_bugreport_Form'){
        $("#bug_description").val(editor.getData());
     }

     post_data =  $("#"+div_id).find("form").serializeArray();
     ck_editors_values_get();
     
     //disbaled fields change to enable - to get values in post
     $("#"+div_id).find("form").find("input,select").removeAttr("disabled");
 
     var obj = {
          url:base_url+action,
          type: "POST",
          data: $("#"+div_id).find("form").serialize(),
          dataType:"json",
          error : function(data) {
           after_ajax(elm,data);
      }
     };
    
     if(call_back_fn){
        obj.success = function(data){
        call_back_fn(data,div_id,elm,action,post_data,popup);
      };
     }   
     else
      obj.success = function(data){
        if(data.status == "success"){
            
            if(action == 'sales_rep/add' && div_id=='repForm'){

              bootbox.alert(data.msg);
              $('#auto_fillrep').html(data.repdrop);
              $('#repForm').modal('hide');
            }
            else if(action.search("user/cust_price_add")!=-1 && div_id=='cust_add_new_price')
            {
              bootbox.alert(data.msg); 
              $("#custom_mytab a[href='#price_list']").trigger('click');             
              $('#cust_add_new_price').modal('hide');
            }
            else
            {
              //success message set.
              var alert_msg = data.msg;
              service_message(data.status,alert_msg);
            
              //to close popupshow
              $('#'+div_id).modal('hide');
              
              //grid refresh
              refresh_grid();
            } 

        }
        else if(data.status == "error")
        {

            //critical error
            if( data.error_msg ) {
                    bootbox.alert(data.error_msg);
                    return false;
            }

            //price list error
            if( data.price_error ) {
                bootbox.alert(data.price_error);
                $(elm).removeAttr("disabled"); 
                $(elm).text("Submit");
                return false;
            }
                
            //load validated form
            $("#"+div_id).find(".modal-body").html(data.form_view);
       
            form_utilities(div_id);

            if(action.search("user/cust_price_add")!=-1 && div_id=='cust_add_new_price')
                init_token_sku(data.sku); 
                   
        }
        
        if(! after_ajax(elm,data))
        return false;
        
      };
    
     $.ajax(obj);
}

/*Ajax form get into bootstrap popup or inline div.
 *action : form get url.
 *div_id :the div where the form should get loaded. 
 *title : header of popup form
 *elm: object of clicking tag.
 */
function get_form(action, div_id, title,  elm,  popup,  fn_to_call,call_fun_args){

     //remove previous service message
     $("#div_service_message").remove();
     
     popup = (popup == false)?false:true;
     call_fun_args = (call_fun_args)?call_fun_args:false;
     fn_to_call = (fn_to_call)?fn_to_call:false;
     
     var loader_content = (popup)?"Loading..":capitaliseFirstLetter(div_id.replace(/_/g,' '))+" loading..";
     
     if(fn_to_call == 'address_add_success')
        loader_content = 'Processing';

     if(!before_ajax(elm, loader_content))
      return false;

     $.ajax( {
          url:base_url+action,
          type: "POST",
          data: {},
          dataType:"json",
          success : function(data){
                
               if(! after_ajax(elm,data))
                    return false;
            
             //critical error
             if(data.status == "error" && data.error_msg) {
                    alert(data.error_msg);
                    return false;
                }
            
                if(data.form_view) 
                {
                    if(!popup){
                        
                       $("#"+div_id).html(data.form_view);
                        
                    }else
                    {
                        $("#"+div_id).find("#myModalLabel").html(title);
                        $("#"+div_id).find(".modal-body").html(data.form_view);
                        
                        if( (namespace == 'product_view' && div_id == 'addVendorForm') || div_id == 'user_bugreport_Form')
                        {
                          $("#"+div_id).css('width', '800px').modal({});
                        }

                        $("#previousUrl").val(previous_url);
                        init_modal();
                        $('#'+div_id).modal();
                        if(action == 'user/add')
                         {
                            changeChannel(11);
                         } 

                         if(action.search("user/cust_price_add")!=-1 && div_id=='cust_add_new_price')
                           init_token_sku(data.sku);
                    }
                      
                      
                    form_utilities(div_id);
                }
                
                if(data.description)
                  cke_description_old_text = data.description;

                if(data.features)
                  cke_features_old_text = data.features;

               if(fn_to_call){
                
                if(call_fun_args){
                  fn_to_call.apply(this, call_fun_args);
                }
                else
                {
                    fn_to_call();
                }
                    
               }
               
         
            },
          error : function(data) {
           after_ajax(elm,data);
      }
     });
}

/*service message set.
 *$err_type : success/error/warning.
 *$message :message wanted to display @string. 
 */
function service_message(err_type,message,div_id){
    
        div_id = (div_id)?div_id:false; 	
        
        var str  ='<div id="div_service_message" class="alert alert-'+err_type+'">';
            str +='<button type="button" class="close" data-dismiss="alert">&times;</button>';
		    str +='<strong>'+capitaliseFirstLetter(err_type)+':&nbsp;</strong>';
		    str += message;
	        str +='</div>';
            
            if(div_id){
                 $("#"+div_id).html(str);
            }
            else
            {
                $(".breadcrumb_bg").after(str);
                scroll_to("div_service_message");
            }
            
}


/*to capitalize first letter.
*/
function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/* reset form 
*/
function reset_form(e){
 
    var form_id = $(e).parents("form").attr("id");
    var form_name = $(e).parents("form").attr("name");


    if(form_id){
         document.getElementById(form_id).reset();
    }
    else if(form_name){
        var t = document.getElementsByName(form_name);
        t[0].reset();
    }    
   
   // To display fancy select box.
  	$('select').not(".boot_select_false").selectpicker('render');
  
   if( typeof editor_old_text != 'undefined' && $("#note-body").length && editor )
   {
      editor.setData(editor_old_text.content);
   }

   if( $("#cke_description").length && $("#cke_features").length )
   {
      editors['description'].setData(cke_description_old_text);
      editors['features'].setData(cke_features_old_text);   
   }
}



function serialize_array_to_object(value){
    new_obj = {};
    $.each(value, function(i, obj) { new_obj[obj.name] = obj.value });
    return new_obj
}

function form_utilities(div_id){
       // To display fancy select box.
    	$('select').not(".boot_select_false").selectpicker();
    	
    	//initiate datepickers with basic features.
    	init_datepicker();
        
        //ck editor render for textarea which has the class name "ck_editor"
        ck_editors_render(div_id);
        
    	//enable tooltip on button and anchor elements by default.
        $('a,button').tooltip();
        
}

function scroll_to(jump_id){
    //page scroll
    if(jump_id !=""){
       $(window).scrollTop($('#'+jump_id).offset().top); 
    }
}

function drawColumnChart(data, params, div_id) 
{
      var obj = new google.visualization.ColumnChart(document.getElementById(div_id));
    // Create and draw the visualization.
    obj.draw(data,params);
}


//add hidden input in to given to_div_id.
function add_hidden_field(name,to_div_id,valu,type){
     
      type = type?type:false;
      
      if(type && type == "textarea") {
         $('<textarea></textarea>').attr({
        style: 'display:none;',
        id: name,
        name: name
        }).appendTo('#'+to_div_id).text(valu);

  }
  else
  {
    $('<input>').attr({
    type: 'hidden',
    id: name,
    name: name,
    value:valu
    }).appendTo('#'+to_div_id);
  }
    

}


//js serialize as like php serialize
function js_serialize(mixed_value) {
  //   example 1: serialize(['Kevin', 'van', 'Zonneveld']);
  //   returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
  //   example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
  //   returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'

  var val, key, okey,
    ktype = '',
    vals = '',
    count = 0,
    _utf8Size = function(str) {
      var size = 0,
        i = 0,
        l = str.length,
        code = '';
      for (i = 0; i < l; i++) {
        code = str.charCodeAt(i);
        if (code < 0x0080) {
          size += 1;
        } else if (code < 0x0800) {
          size += 2;
        } else {
          size += 3;
        }
      }
      return size;
    };
  _getType = function(inp) {
    var match, key, cons, types, type = typeof inp;

    if (type === 'object' && !inp) {
      return 'null';
    }
    if (type === 'object') {
      if (!inp.constructor) {
        return 'object';
      }
      cons = inp.constructor.toString();
      match = cons.match(/(\w+)\(/);
      if (match) {
        cons = match[1].toLowerCase();
      }
      types = ['boolean', 'number', 'string', 'array'];
      for (key in types) {
        if (cons == types[key]) {
          type = types[key];
          break;
        }
      }
    }
    return type;
  };
  type = _getType(mixed_value);

  switch (type) {
    case 'function':
      val = '';
      break;
    case 'boolean':
      val = 'b:' + (mixed_value ? '1' : '0');
      break;
    case 'number':
      val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;
      break;
    case 'string':
      val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';
      break;
    case 'array':
    case 'object':
      val = 'a';
      /*
        if (type === 'object') {
          var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
          if (objname == undefined) {
            return;
          }
          objname[1] = this.serialize(objname[1]);
          val = 'O' + objname[1].substring(1, objname[1].length - 1);
        }
        */

      for (key in mixed_value) {
        if (mixed_value.hasOwnProperty(key)) {
          ktype = _getType(mixed_value[key]);
          if (ktype === 'function') {
            continue;
          }

          okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
          vals += this.js_serialize(okey) + this.js_serialize(mixed_value[key]);
          count++;
        }
      }
      val += ':' + count + ':{' + vals + '}';
      break;
    case 'undefined':
      // Fall-through
    default:
      // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
      val = 'N';
      break;
  }
  if (type !== 'object' && type !== 'array') {
    val += ';';
  }
  return val;
}

/**
 * function drag_drop() {
 *    
 *     $('body').find('.drag_drop tbody').sortable();
 * }
 */
function save_order(pagename='',lightbox_id = '',slideshow_id = '') {
    
          //alert(slideshow_id);
           var ids = '', link = '', table_name='', page='', perpage='',url='',data='';
           
           if(pagename!=''){
               page    = pagename;
            }
            else
            {
               page  = $(".page_name").val(); 
            }
            perpage = $(".perpage").val();
          

           if(page == 'menu'){
             var selector = 'drag_drop tbody#menu tr';
           }            
           else
           {
             var selector = 'drag_drop tbody tr';                                
           }  
                       
           
          if(page=='offer') {
            table_name = 'cub_special_offer';
          }
          else if(page == 'menu')
          {
            table_name = 'cub_menu';
          }
          else if(page == 'social') 
          {
            table_name = 'cub_search_nav_bar';
          }
          else if(page == 'service')
          {
            table_name = 'cub_service';
          }
          else if(page == 'client')
          {
            table_name = 'cub_happy_clients';
          }
          else if(page == 'paper')
          {
            table_name = 'cub_white_paper';
          }
          else if(page == 'portfolio')
          {
            table_name = 'cub_portfolio';
          } 
          else if(page == 'team')
          { 
            table_name = 'cub_team';
          }     
          else if(page == 'ao2')
          {
            table_name = 'cub_fabric_finish_headers';
          }
          else if(page == 'symmerty')
          {
            table_name = 'cub_symmetry_finish_headers';
          } 
          else if(page == 'calibrate')
          { 
            table_name = 'cub_officespacecalc';
          } 
          else if(page == 'landing')
          { 
            table_name = 'cub_landing';
          }  
           else if(page == 'servicelanding')
          {
            table_name = 'cub_service_landing';
          }
           else if(page == 'slider')
          {
            table_name = 'cub_slider';
          }
           else if(page == 'page')
          {
            table_name = 'cub_new_page';
          }
          else if(page == 'meta')
          {
            table_name = 'cub_metas';
          }
          else if(page == 'htaccess')
          {
            table_name = 'cub_htaccess_actualurl';
          }
          else if(page == 'lightbox'){
            table_name = 'cub_light_box';
          }
          else if(page == 'lightboxdata'){
            table_name = 'cub_light_box_data';
          }
           else if(page == 'slideimage-list'){
            table_name = 'cub_slideshow_images';
          }
          else if(page =='productcategory') {
            table_name = 'cub_product_category';
          }
          
          
           $("."+selector).each(function(){
		  	ids += (ids == '')?this.id:(','+this.id);
		  });
          if(page != 'lightboxdata') {
            link = 'admin/offer/save_order';
          }
          else
          {
            link = 'admin/lightboxdata/save_order';
          }
          
		  url  = base_url+link;
          data = {ids:ids,perpage:perpage,table:table_name,lightboxid:lightbox_id,slideshow_id:slideshow_id};
          
		
			$.post(url,data,function(rdata){
				 if(rdata=='success'){
				    alert("Re-ordered Successfully");
				    if(page == 'social') {
						$("#tabs ul li").eq(1).find('a').trigger('click');
					}
					else
					{ 
					   $.fn.clear_advance_search();
                       location.reload(); 
				    }    
				 }	
                else
                 {
                    alert("Error Occurred in Re-order!");
                 }	
            });
            
}

function clear_image(file) {
  
  if(document.getElementById(file).value!='') {
    
    document.getElementById(file).value='';
  }
  
  else {
    alert('Please Select a File');
  }
}






