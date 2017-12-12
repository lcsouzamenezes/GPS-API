<div id="content">      
    <div class="inner" style="min-height: 700px;">
        <div class="row">
            <div class="col-lg-6">
                <h1> Promos </h1>
            </div>
            <div class="col-lg-6" style="margin-top: 20px; margin-bottom: 10px; left:220px;">
                <a class="btn btn-primary" href="<?php echo site_url();?>admin/promo/add">ADD</a>
                <a class="btn btn-primary" onclick="DeleteCheckedRow(this,'delete-promo','admin/promo/delete')">DELETE</a>
            </div>
        </div>
            <?php echo $grid;?>
    </div>
</div>	