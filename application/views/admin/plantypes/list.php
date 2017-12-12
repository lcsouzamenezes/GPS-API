<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-8">
                <h1> Plan Types </h1>
            </div>
            <div class="col-lg-4" style="margin-top: 20px; margin-bottom: 10px;">
                <a class="btn btn-primary" href="<?php echo site_url();?>admin/plantype/add">ADD</a>
                <a class="btn btn-primary" onclick="DeleteCheckedRow(this,'delete-plantype','admin/plantype/delete')">DELETE</a>
            </div>
        </div>
            <?php echo $grid;?>
    </div>
</div>	