<?php

function show_param_items_of_project($data)
{
    ?>
    <div class="alert alert-dismissible alert-info fade show p-1 m-1" role="alert">

        <button type="button" class="close p-0 mr-2 ml-2 delete" 
                data-delete-obj="data"
                data-delete-type="releation"
                data-delete-id="<?php echo $data['data_id'];?>"
                onclick="delete_object(this)"
                style="position: relative" 
                data-dismiss="alert" aria-label="Close">
            <span class="p-0 m-0 delete" aria-hidden="true">&times;</span>
        </button>

        <strong class="m-auto p-1  text-dark text-bold">
            <?php echo $data['caption']; ?>
        </strong>
        <!-- <i class="far fa-hand-point-left"></i> -->
        <small class="m-auto p-1  text-dark">
            <?php echo ' [ '.$data['unit'].' ] '; ?>
        </small>      
        <a href="<?php echo base_url('relation/index/'.$data['data_id']);?>" class="p-0 m-2" data-row-number=""
            data-param-id="<?php echo $data['param_id'];?>" >
            <i class="fas fa-share-square"></i>
        </a>
    </div>
    <?php
}