function select_all(e){
    
        if($(e).is(":checked")) { // check select status
            $('.chk_sel').prop('checked',true);
        }else{
            $('.chk_sel').prop('checked',false);       
        }

}
$(function($) {
	setTimeout(function() {
		$('#content-wrapper > .row').css({
			opacity: 1
		});
	}, 200);
	
	$('#sidebar-nav,#nav-col-submenu').on('click', '.dropdown-toggle', function (e) {
		e.preventDefault();
		
		var $item = $(this).parent();

		if (!$item.hasClass('open')) {
			$item.parent().find('.open .submenu').slideUp('fast');
			$item.parent().find('.open').toggleClass('open');
		}
		
		$item.toggleClass('open');
		
		if ($item.hasClass('open')) {
			$item.children('.submenu').slideDown('fast');
		} 
		else {
			$item.children('.submenu').slideUp('fast');
		}
	});
	
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav .dropdown-toggle', function (e) {
		if ($( document ).width() >= 992) {
			var $item = $(this).parent();

			if ($('body').hasClass('fixed-leftmenu')) {
				var topPosition = $item.position().top;

				if ((topPosition + 4*$(this).outerHeight()) >= $(window).height()) {
					topPosition -= 6*$(this).outerHeight();
				}

				$('#nav-col-submenu').html($item.children('.submenu').clone());
				$('#nav-col-submenu > .submenu').css({'top' : topPosition});
			}

			$item.addClass('open');
			$item.children('.submenu').slideDown('fast');
		}
	});
	
	$('body').on('mouseleave', '#page-wrapper.nav-small #sidebar-nav > .nav-pills > li', function (e) {
		if ($( document ).width() >= 992) {
			var $item = $(this);
	
			if ($item.hasClass('open')) {
				$item.find('.open .submenu').slideUp('fast');
				$item.find('.open').removeClass('open');
				$item.children('.submenu').slideUp('fast');
			}
			
			$item.removeClass('open');
		}
	});
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav a:not(.dropdown-toggle)', function (e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	$('body').on('mouseleave', '#page-wrapper.nav-small #nav-col', function (e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	
	$('#make-small-nav').click(function (e) {
		$('#page-wrapper').toggleClass('nav-small');
	});
	
	$(window).smartresize(function(){
		if ($( document ).width() <= 991) {
			$('#page-wrapper').removeClass('nav-small');
		}
	});
	
	if ($( document ).width() <= 991) {
		$('#sidebar-nav').removeClass('navbar-collapse');
	}

	
	$('.mobile-search').click(function(e) {
		e.preventDefault();
		
		$('.mobile-search').addClass('active');
		$('.mobile-search form input.form-control').focus();
	});
	$(document).mouseup(function (e) {
		var container = $('.mobile-search');

		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.removeClass('active');
		}
	});
	
	//$('.fixed-leftmenu #col-left').nanoScroller({
//    	alwaysVisible: false,
//    	iOSNativeScrolling: false,
//    	preventPageScrolling: true,
//    	contentClass: 'col-left-nano-content'
//    });
	
	// build all tooltips from data-attributes
	$("[data-toggle='tooltip']").each(function (index, el) {
		$(el).tooltip({
			placement: $(this).data("placement") || 'top'
		});
	});
    
    
      
    
});

$.fn.removeClassPrefix = function(prefix) {
    this.each(function(i, el) {
        var classes = el.className.split(" ").filter(function(c) {
            return c.lastIndexOf(prefix, 0) !== 0;
        });
        el.className = classes.join(" ");
    });
    return this;
};

(function($,sr){
	// debouncing function from John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced () {
			var obj = this, args = arguments;
			function delayed () {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			};

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	}
	// smartresize 
	jQuery.fn[sr] = function(fn){	return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

function bulk_action(form_id, select_box_name, chk_box_name)
{
	select_box_name 	= select_box_name?select_box_name:'action';
	chk_box_name		= chk_box_name?chk_box_name:'op_select';
	
	var select_box = $("select[name='"+select_box_name+"']");
	
	if(!validate_bulk_options(select_box_name, chk_box_name))
		return false;
	
	switch(namespace)
	{
		case 'admin_contact_form_index':
        
			if (select_box.val() == 'delete') 
			{
		       $("#bulk_action").val('delete');
               var conf = confirm("Are you sure you want to delete "+$("input[name^='"+chk_box_name+"']:checked").length+" record?")
             if(!conf){
                return false;
             }
		       $("#"+namespace).submit();
		    }
       
			break;
  
    }

}


function validate_bulk_options(select_box_name, chk_box_name)
{
	select_box_name 	= select_box_name?select_box_name:'action';
	chk_box_name		= chk_box_name?chk_box_name:'op_select';
	
	var select_box = $("select[name='"+select_box_name+"']");
	
	if (select_box.val()== '') 
	{
        alert("Please select an action");
        select_box.focus();
        return false;
    }
	
	if($("input[name^='"+chk_box_name+"']:checked").length == 0)
	{
		alert("You must select at least one record.");
        return false;
	}
	
	return true;
}

function export_contacts(){
    window.location = base_url+"admin/contact_form/export";
}

function textCounter(field,cntfield,maxlimit) {
if (field.value.length > maxlimit) // if too long...trim it!
field.value = field.value.substring(0, maxlimit);
// otherwise, update 'characters left' counter
else
cntfield.value = maxlimit - field.value.length;
} 