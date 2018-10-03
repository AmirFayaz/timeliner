<section id="card-actions">
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <div class="card border-grey border-lighten-3 py-1 m-0">
                <div class="card-content">
                    <div class="card-body">
                        ویرایش پروژه

                        <button class="btn btn-sm btn-outline-success rounded inline-block
                                        text-bold col col-auto ml-0"
                                        style="margin-right:auto;">
                            <i class="fas fa-check text-bold"></i>
                            ذخیره تغییرات
                        </button>
                        <pre></pre>
                        <div class="form-group m-0 p-0">
                            <input type="text" placeholder="نام پروژه" 
                                class="project col col-sm-12 col-md-8 col-lg-6 mr-0 ml-0 mb-4" 
                                id="input_proj_name" 
                                value="<?php echo $proj['title'];?>"
                                >
                        </div>

                        <div class="form-group m-0 p-0">
                            <label for="select-params" class="col col-lg-auto col-md-auto col-sm-12 mb-2 text-small text-bold p-0">
                                پارامتر را انتخاب کنید :‌
                            </label>
                            <select class="form-control form-control-sm col col-lg-6 col-md-12 col-sm-12 inline-block mb-2 p-0" 
                                    id="select-params">
                                <?php
                                foreach($parameters as $param)
                                {
                                    ?>
                                        <option data-param-id="<?php echo $param['param_id'];?>"
                                                value="<?php echo $param['param_id'];?>"
                                                class="text-small text-bold">
                                            <?php echo $param['caption'].' ['.$param['unit'].']'; ?>
                                        </option>
                                    <?php
                                }
                                ?>
                            </select>

                            <button type="submit" onclick="form_addParamToProj(this)"
                                    data-proj-id="<?php echo $proj['proj_id'];?>"
                                    class="btn btn-sm btn-outline-primary rounded inline-block
                                            text-small text-bold
                                            col col-lg-auto col-md-6 col-sm-12 mb-2"
                                    >
                                <i class="fas fa-plus text-small text-bold"></i>
                                افزودن پارامتر
                            </button>
                        </div>
                        
                        <div id="param-list" class="row">
                            <?php 
                            if($relation)
                            {
                                foreach($relation as $rel)
                                {
                                    show_param_items_of_project($rel);
                                }
                            }
                            ?>
                        </div>


                        <!-- <div class="row" id="plot_">
                            <div id="myChart"></div>

                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


