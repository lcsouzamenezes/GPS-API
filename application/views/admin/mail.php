<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-12">
                <h1> INBOX </h1>
            </div>
        </div>
            <?php 
           //  echo "<pre>";
             // print_r($overview);
            //  print_r($message);
            foreach($overview as $okey=>$ovalue){
                //print_r($ovalue);
            ?>
            <div class="col-md-12">
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne<?php echo $okey; ?>"><span class="glyphicon glyphicon-file">
                            </span><?php echo $ovalue[0]->subject; ?></a>
                        </h4>
                    </div>
                    <div id="collapseOne<?php echo $okey; ?>" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $message[$okey]; ?>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <?php } ?>
    </div>
</div>	