<?php
$user_id = get_loggedin_user('user_id');

?>

<pre></pre>
<div class="row">
<div class="col col-lg-7 col-sm-12 inline-block mt-0 mb-auto">
    
    <div class="shadow-box row p-3" style="min-height:180px;">
        <h5>افزودن پروژه جدید</h5>
        <form action="" id="form_createProject">
            <input type="text" class="form-control col col-lg-6 col-md-12 mb-1" id="input_projectTitle" placeholder="نام پروژه">
            <button type="submit" class="btn btn-sm btn-outline-primary p-1">
            افزودن پروژه جدید
            </button>
        </form>
    </div>

    <?php
    if(isset($projects))
    {
        ?>
        <pre></pre>
        <div class="shadow-box p-3" style="height:420px">
            <h5>لیست پروژه ها</h5>
            <table class="table table-sm table-hover">
                <tr class="bg-primary text-light">
                    <th>عنوان</th>
                    <th>تاریخ ایجاد پرونده</th>
                    <th>ابزار</th>
                </tr>
                <?php
                foreach($projects as $project)
                {
                    ?>
                    <tr class="text-medium">
                        <td><?php echo $project['title']; ?></td>
                        <td><?php echo persian_date($project['created_at'] , TRUE); ?></td>
                        <td>
                            <a href="<?php echo base_url().'project/edit/'.$project['proj_id'];?>"
                                class="btn btn-sm btn-outline-info">
                            ویرایش
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <pre></pre>
        <?php
    }
    ?>
</div>


<div class="col col-lg-5 col-sm-12 inline-block mt-0 mb-auto">
    
    <div class="shadow-box row p-3" style="min-height:180px;">
        <h5>افزودن پارامتر جدید</h5>
        <form action="" id="form_createParameter" class="col col-12">
            <input type="text" class="form-control col col-lg-auto col-md-12 p-1 m-1 block" id="input_parameterCaption" placeholder="نام پارامتر">
            <input type="text" class="form-control col col-lg-auto col-md-12 p-1 m-1 block" id="input_parameterUnit" placeholder="واحد پارامتر">
            <button type="submit" class="btn btn-sm btn-outline-danger p-1">
            افزودن پارامتر جدید
            </button>
        </form>
    </div>

    <?php
    if(isset($parameters))
    {
        ?>
        <pre></pre>
        <div class="shadow-box p-3" style="height:420px">
            <h5>لیست پارامتر ها</h5>
            <table class="table table-sm table-hover">
                <tr class="bg-danger text-light">
                    <th>پارامتر</th>
                    <th>واحد</th>
                </tr>
                <?php
                foreach($parameters as $parameter)
                {
                    ?>
                    <tr>
                        <td><?php echo $parameter['caption']; ?></td>
                        <td><?php echo $parameter['unit']; ?></td>
                        <!-- <td>
                            <a href="<?php echo base_url().'parameter/edit/'.$parameter['proj_id'];?>"
                                class="btn btn-sm btn-outline-info">
                            ویرایش
                            </a>
                        </td> -->
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <pre></pre>
        <?php
    }
    ?>
</div>
</div>