<section id="card-actions">
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <div class="card border-grey border-lighten-3 py-1 m-0">
                <div class="card-content">
                    <div class="card-body">
                        پروژه جدید

                        <div class="row">
                            <input type="text" placeholder="نام پروژه" 
                                class="project"
                                id="input_proj_name" >
                        </div>

                        <?php 
                        for($i=0;$i<3;$i++)
                        {
                            ?>
                            <div class="row" data-row-number="<?php echo $i; ?>">

                                <input type="text" class="param" placeholder="نام پارامتر قابل ارزیابی" 
                                    id="input_caption_<?php echo $i; ?>" >
                                <input type="text" class="param" placeholder="واحد شمارش یا اندازه گیری" 
                                    id="input_unit_<?php echo $i; ?>" >

                            </div>
                            <div class="row" id="plot_<?php echo $i; ?>" >

                            </div>

                            <?php
                        }
                        ?>
                        <div id="myChart"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


