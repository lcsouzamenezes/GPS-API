<?php

    if(count($participants)) {
        foreach($participants as $pkey => $pvalue) {
      ?>            
            
      <span><?php echo $pvalue['default_id'];?></span>    
      <br />  
       <?php  }
    }
?>