<?php

function myConsultantTotalSales() {
    //get loged user's key
    $key = get_current_user_key();

    //Total Users on My left leg
    $leftLegUsers = totalLeftLegUsers($key);

    //paid users on my left leg
    $rightLegUsers = totalRightLegUsers($key);

    $totalConsultant = $leftLegUsers + $rightLegUsers;

    //show total users on left leg
    $totalUsersSales = totalSales($key);
    //_e("<pre>");print_r($totalUsersSales);exit;
    ?>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load('visualization', '1', {packages: ['table']});
    </script>
    <script type="text/javascript">
        var visualization;
        var data;
        var options = {'showRowNumber': true};
        function drawVisualization() {
            // Create and populate the data table.
            var dataAsJson =
                    {cols: [
                            {id: 'A', label: '<?php echo _e("User Name", "binary-mlm-pro"); ?>', type: 'string'},
                            {id: 'B', label: '<?php echo _e("User Key", "binary-mlm-pro"); ?> ', type: 'string'},
                            {id: 'C', label: '<?php echo _e("Sponsor", "binary-mlm-pro"); ?>', type: 'string'},
                            {id: 'D', label: '<?php echo _e("Placement", "binary-mlm-pro"); ?>', type: 'string'},
                            {id: 'E', label: '<?php echo _e("Status", "binary-mlm-pro"); ?>', type: 'string'}],
                        rows: [
    <?php
    foreach ($totalUsersSales as $details) {
        foreach ($details as $row) :
            ?>
                                    {c: [{v: '<?php echo $row['username'] ?>'}, {v: '<?php echo $row['user_key'] ?>'}, {v: '<?php echo $row['sponsor_key'] ?>'}, {v: '<?php echo $row['leg'] ?>'}, {v: '<?php echo $row['payment_status'] ?>'}]},
            <?php
        endforeach;
    }
    ?>
                        ]};
            data = new google.visualization.DataTable(dataAsJson);
            // Set paging configuration options
            // Note: these options are changed by the UI controls in the example.
            options['page'] = 'enable';
            options['pageSize'] = 10;
            options['pagingSymbols'] = {prev: 'prev', next: 'next'};
            options['pagingButtonsConfiguration'] = 'auto';
            //options['allowHtml'] = true;
            //data.sort({column:1, desc: false});
            // Create and draw the visualization.
            visualization = new google.visualization.Table(document.getElementById('table'));
            draw();
        }
        function draw() {
            visualization.draw(data, options);
        }
        google.setOnLoadCallback(drawVisualization);
        // sets the number of pages according to the user selection.
        function setNumberOfPages(value) {
            if (value) {
                options['pageSize'] = parseInt(value, 10);
                options['page'] = 'enable';
            } else {
                options['pageSize'] = null;
                options['page'] = null;
            }
            draw();
        }
        // Sets custom paging symbols "Prev"/"Next"
        function setCustomPagingButtons(toSet) {
            options['pagingSymbols'] = toSet ? {next: 'next', prev: 'prev'} : null;
            draw();
        }
        function setPagingButtonsConfiguration(value) {
            options['pagingButtonsConfiguration'] = value;
            draw();
        }
    </script>
    <!--va-matter-->
    <div class="va-matter">
        <!--va-matterbox-->
        <div class="va-matterbox">
            <!--va-headname-->
            <div class="va-headname"><?php _e('My Consultants Details', 'binary-mlm-pro'); ?></div>
            <!--/va-headname-->
            <div class="va-admin-leg-details">
                <!--va-admin-mid-->
                <div class="paging">
                    <form action="">
                        <div class="left-side">
                            <?php _e('Display Number of Rows', 'binary-mlm-pro'); ?> : &nbsp; 
                        </div>
                        <div class="right-side">
                            <select style="font-size: 12px" onchange="setNumberOfPages(this.value)">
                                <option value="5">5</option>
                                <option selected="selected" value="10">10</option>
                                <option value="20">20</option>
                                <option  value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="">All</option>
                            </select>
                        </div>	
                    </form>
                    <div class="right-members">
                        <?php _e('Consultants', 'binary-mlm-pro'); ?>: &nbsp; <?php _e('Left', 'binary-mlm-pro'); ?>: <strong><?php echo $leftLegUsers; ?></strong>&nbsp;<?php _e('Right', 'binary-mlm-pro'); ?>: <strong><?php echo $rightLegUsers; ?></strong>
                        &nbsp; <?php _e('Total', 'binary-mlm-pro'); ?>: <strong><?php echo $totalConsultant; ?></strong>
                    </div>
                    <div class="va-clear"></div>
                </div>
                <div id="table"></div>
                <div class="va-clear"></div>
            </div>		
        </div>
    </div>	
    <?php
}
?>