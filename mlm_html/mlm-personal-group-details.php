<?php

function myPersonalGroupDetails($id = '') {
    $table_prefix = mlm_core_get_table_prefix();
    global $wpdb;
    //get loged user's key
    if ($id == '')
        $key = get_current_user_key();
    else
        $key = $wpdb->get_var("SELECT user_key FROM {$table_prefix}mlm_users WHERE user_id = $id");

    //Total Users on My left leg
    $personalLegUsers = totalMyPersonalSales($key);

    //paid users on my left leg
    $personalLegActiveUsers = activeUsersOnPersonalSales($key);

    //show total users on left leg
    $totalPersonalLegUsers = myTotalPersonalUsers($key);
    //_e("<pre>");print_r($totalPersonalLegUsers);exit;
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
                            {id: 'D', label: '<?php echo _e("Status", "binary-mlm-pro"); ?>', type: 'string'}],
                        rows: [
    <?php foreach ($totalPersonalLegUsers as $row) : ?>
                                {c: [{v: '<?php echo $row['username'] ?>'}, {v: '<?php echo $row['user_key'] ?>'}, {v: '<?php echo $row['payment_status'] ?>'}]},
    <?php endforeach; ?>
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
            <div class="va-headname"><?php _e('My Personal Group Details', 'binary-mlm-pro'); ?></div>
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
                        <?php _e('Total Users', 'binary-mlm-pro'); ?>: <strong><?php echo $personalLegUsers; ?></strong>&nbsp;<?php _e('Active Users', 'binary-mlm-pro'); ?>: <strong><?php echo $personalLegActiveUsers; ?></strong>
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