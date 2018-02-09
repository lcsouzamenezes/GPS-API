$(function(){
       
    
});

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
    $("#assign_user_id").val(id);
    
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/promos",
          data:{user_id:id},
          dataType:'json',
          success: function(res){
            var outpt = res.output;
            $(".modal-body #promos").html(outpt);
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
                $("#uiModal .modal-body #promos").html(res.output);
            }
            else
            {
                $("#uiModal #msg").html(res.msg);
            }
          },
          error: function(e) {
            	console.log(e.message);
          }
        });
}

function get_participants_lists(user_id)
{
    $.ajax({
          type: "POST",
          url: base_url+"admin/user/group_participant_lists",
          data:{user_id:user_id},
          dataType:'json',
          success: function(res){
            //alert(res);
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
   before_ajax(e);
     
   $.ajax({
          type: "POST",
          url: url,
          data: {id:ids},
          success: function(res){ 
            alert(rec_count+' record(s) deleted successfully');
            window.location.reload();
            after_ajax(e);
          },
          error: function(e) {
        	//called when there is an error
        	console.log(e.message);
            after_ajax(e);
          }
   });
}

function notification(id){
    $("#notify_user_id").val(id);
}
