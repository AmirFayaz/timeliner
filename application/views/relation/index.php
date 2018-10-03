<?php
// pr($rel);

?>

<pre></pre>
<a href="<?php echo base_url('project/edit/'.$rel['proj_id']);?>">
    <i class="fas fa-arrow-right"></i>
    بازگشت
</a>
<pre></pre>
<h4>
    <?php echo 'پارامتر '.$rel['caption'].' مربوط به پروژه '.$rel['title'];?>
</h4>
<pre></pre>

<div class="row m-0 p-0">
    <div class="btn col col-md-4 col-sm-12 border border-light rounded p-1 text-light bg-danger"
        onclick="null;" style="cursor:pointer; text-align: center">
        <strong>
        پیش بینی
        </strong>
    </div>
    <div class="btn col col-md-4 col-sm-12 border border-light rounded p-1 text-light bg-primary"
        onclick="null;" style="cursor:pointer; text-align: center">
        <strong>
        داده های عملی
        </strong>
    </div>
    <div class="btn col col-md-4 col-sm-12 border border-light rounded p-1 text-light bg-success"
        onclick="null;" style="cursor:pointer; text-align: center">
        <strong>
        گزارش
        </strong>
    </div>
</div>

<pre></pre>

<div class="row m-0 p-0">
    <div class="col col-12">
        <div class="col col-lg-auto col-sm-12 inline-block">
            در تاریخ 
        </div>
        <div class="col col-lg-auto col-sm-12 inline-block">
        <?php show_pds('pds_data_expect' , NULL , NULL , FALSE); ?>
        </div>
        <div class="col col-lg-auto col-sm-12 inline-block">
            <?php echo $rel['caption']; ?>
        </div>
        <div class="col col-lg-auto col-sm-12 inline-block">
            <input class="form-control" type="text">
        </div>
        <div class="col col-lg-auto col-sm-auto inline-block">
            <?php echo $rel['unit'].' خواهد شد ';?>
        </div>
        <div class="col col-lg-auto col-sm-auto inline-block">
            <button class="btn btn-outline-dark" type="text">
            ثبت
            </button>
        </div>
    </div>
</div>
