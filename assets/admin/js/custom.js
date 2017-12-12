function requirepassword()
{
    alert(123); 
}

function send_notification()
{
    //alert(123);
   var notify_user_id = $("#notify_user_id").val();
   var message        = $("#message").val();
  /// alert(message);
    
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/send_notification/",
          data:{notify_user_id:notify_user_id,message:message},
          dataType:'json',
          success: function(res){
            
          },
          error: function(e) {
         	console.log(e.message);
                
             }
        });
}

function generate_promo()
{
    $.ajax({
          type: "POST",
          url: base_url+"admin/promo/promo_code_generation",
         // data: {},
          success: function(res){
           $("#promo_code").val(res);
          },
          error: function(e) {
         	console.log(e.message);    
          }
       });
}

function assign_user(id){
   
    $("#tab4primary #assign_user_id").val(id);
    
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/promos/"+id,
          data:{user_id:id},
          dataType:'json',
          success: function(res){
            var outpt = res.output;
            //$(".modal-body #promos").html(outpt);
            $("#tab4primary").html(outpt);
          },
          error: function(e) {
         	console.log(e.message);   
           }
        });
}

function assign_promo()
{
    var user_id  = $("#assign_user_id").val();
    var promo_id = $("#promo_code").val();
    
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/assign_promo",
          data:{user_id:user_id,promo_id:promo_id},
          dataType:'json',
          success: function(res){
            if(res.output){
                //$("#uiModal .modal-body #promos").html(res.output);
                $("#tab4primary").html(res.output);
            }
            else
            {
                $("#tab4primary #msg").html(res.msg);
            }
          },
          error: function(e) {
            	console.log(e.message);
          }
        });
}

function get_participants_lists(group_id)
{
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/group_participant_lists",
          data:{group_id:group_id},
          dataType:'json',
          success: function(res){
            $("#participant_lists .modal-body #group_participants_lists").html(res.output); 
          },
          error: function(e) {
            	console.log(e.message);
          }
        });
}


function DeleteCheckedRow(e,cls,url,divid='')
{ 
    var rec_count = $(document).find('.'+cls+':checked').length, ids = '';
    
  if(rec_count <= 0 ) {
    alert("Please check atleast 1 record to delete!"); return false;
  }
   
  var conf = confirm("Are you sure want to delete "+ rec_count + " Record(s)?");
  
  if(! conf ) return false;
  
     $(document).find("."+cls+":checked").each(function(){ 
        ids += (ids)?','+$(this).val():$(this).val();
  });
  
   url = base_url+url;
  // before_ajax(e);
     
   $.ajax({
          type: "POST",
          url: url,
          data: {id:ids},
          success: function(res){ 
            alert(rec_count+' record(s) deleted successfully');
            window.location.reload();
            //after_ajax(e);
          },
          error: function(e) {
        	//called when there is an error
        	console.log(e.message);
           // after_ajax(e);
          }
   });
}

function notification(id){
    $("#notify_user_id").val(id);
}


function res_password(user_id)
{
    var pass = "", cpass = "";
    pass  = $("#password").val();
    cpass = $("#confirm_password").val();
    
    if(pass == '') {
        alert("Password should not be empty!");
        return false;
    }
    else if(cpass == '')
    {
        alert("Confrim Password should not be empty!");
        return false;
    }
    else if(pass != cpass)
    {
        alert("Password couldn't match!");
        return false;
    }
    
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/reset_password/"+user_id,
          data:{password:pass,confirm_password:cpass},
          dataType:'json',
          success: function(res){
            if(res.status == 'success') {
                $(".pass_success").html("Password Successfully reset.");
                location.reload();
            }
          },
          error: function(e) {
            	console.log(e.message);
          }
        });   
}

function send_mail(uid)
{
     var ename = "", esub = "", emess='', eto='';
    ename  = $("#email_name").val();
    esub   = $("#email_subject").val();
    emess  = $("#email_message").val();
    eto    = $("#email_to").val();
    
    if(ename == '') {
        alert("Name should not be empty!");
        return false;
    }
    else if(esub == '')
    {
        alert("Subject should not be empty!");
        return false;
    }
    else if(emess == '')
    {
        alert("Message should not be empty!");
        return false;
    }
    
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/admin_send_mail/"+uid,
          data:{name:ename,subject:esub,message:emess,email:eto},
          dataType:'json',
          success: function(res){
            if(res.status == 'success') {
                $(".mail_send_status").html("Mail Send Successfully!");
                //location.reload();
            }
          },
          error: function(e) {
            	console.log(e.message);
          }
        }); 
}

function delete_group_user(user_id,group_id,usertype)
{
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/delete_group_user",
          data:{user_id:user_id,group_id:group_id,usertype:usertype},
          dataType:'json',
          success: function(res){
            if(res.status == 'success') {
               alert("Deleted Successfully");
               location.reload();
            }
          },
          error: function(e) {
            	console.log(e.message);
          }
        });
}