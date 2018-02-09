$(document).ready(function() {              
    $('i.glyphicon-thumbs-up, i.glyphicon-thumbs-down').click(function(){    
        var $this = $(this),
        c = $this.data('count');    
        if (!c) c = 0;
        c++;
        $this.data('count',c);
        $('#'+this.id+'-bs3').html(c);
    });      
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    }); 
    
                                           
}); 

//$("video").prop('muted', true);

document.getElementById("myvideo1"); 
  function play_video_1() { myvideo1.play(); } 
  function pause_video_1() { myvideo1.pause(); } 

document.getElementById("myvideo2"); 
  function play_video_2() { myvideo2.play(); } 
  function pause_video_2() { myvideo2.pause(); } 

document.getElementById("myvideo3"); 
  function play_video_3() { myvideo3.play(); } 
  function pause_video_3() { myvideo3.pause(); } 

document.getElementById("myvideo4"); 
  function play_video_4() { myvideo4.play(); } 
  function pause_video_4() { myvideo4.pause(); } 



