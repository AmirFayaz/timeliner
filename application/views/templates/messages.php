<?php

if(isset($showMessage)){
    foreach ($showMessage as $message){
        $type = $message['type'];
        $text = $message['text'];
    ?>
    <div class="alert alert-<?php echo $type; ?> alert-dismissible show" role="alert">
        <!-- <strong><?php echo $type; ?></strong><?php echo '  '.$text; ?> -->
        <strong><?php echo $text; ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
    }
}

?>
    
<div id="errorBox">
</div>
<div class="container">
    <!--<div class="row">-->
        
