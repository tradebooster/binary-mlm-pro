<?php

function mlm_ePins_reports() {
    global $wpdb, $table_prefix;
    require_once('admin-mlm-epins-reports-list-table.php');
    $objEpinList = new EpinReports_List_Table();
    $objEpinList->prepare_items();

    extract($_REQUEST);
    if (isset($page)) {
        $url = 'page=' . $page;
    }

    if (isset($epin_status)) {
        $url.='&epin_status=' . $epin_status;
    }
    else {
        $url.='';
    }
    $epin_status1 = isset($epin_status) ? $epin_status : '';

    $epin_value1 = isset($epin_value) ? $epin_value : '';
    ?>

    <div class='wrap'>

        <div id="icon-users" class="icon32"></div><h1><?php _e('ePin Report', 'binary-mlm-pro'); ?></h1></div>	
    <div class="notibar msginfo" style="margin:10px;">
        <a class="close"></a>
        <p><?php _e('The report below lists all the ePins that have been generated on your site. Filter the results to see just the Used Pins or Unused Pins. To revert back to the default listing click the Reset Button.', 'binary-mlm-pro'); ?></p>
        <p><?php _e('The <strong>Search</strong> and <strong>Export to CSV</strong> funtions will work on the currently active Recordset i.e. if you are currently on the Used ePin Filter then performing a search or Exporting to CSV will apply only to the Used ePins.', 'binary-mlm-pro'); ?></p>
    </div>
    <?php
    $mlm_settings = get_option('wp_mlm_general_settings');

    if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1') {
        ?>
        <div style="margin-left:10px;float:left;text-decoration:none;">
            <a class="button" style="text-decoration: none;" href="<?php echo admin_url() . "admin.php?page=" . $_REQUEST['page'] . "&tab=epinreports" ?>"><?php _e('Reset', 'binary-mlm-pro'); ?></a>
        </div>	

        <div style="margin-right:10px;float:right">			
            <form action="" method="get" style="float:right;">
                <input type="hidden" name="page" value="admin-reports" />
                <input type="hidden" name="tab" value="epinreports" />
                <input type="hidden" name="epin_status" value="<?php echo $epin_status1 ?>" />
                <input type="hidden" name="epin_value" value="<?php echo $epin_value1 ?>" />
                <input type="text" name="search"/><input type="submit" value="<?php _e('Search', 'binary-mlm-pro'); ?>" class="button"/>
            </form>
        </div>

        <form id="project-filter" method="GET" action="">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <input type="hidden" name="tab" value="epinreports" />

            <?php
            $objEpinList->display();
            ?>
        </form>	

        <?php
        if (!isset($epin_status) && !isset($search) && !isset($epin_value)) {
            $sql = "SELECT * FROM {$table_prefix}mlm_epins ORDER BY id ASC";
        }
        else {

            if (isset($epin_status) && $epin_status != '' && isset($epin_value) && $epin_value != '' && !empty($search)) {
                $amt = $epin_value;
                $where = " WHERE status='" . $epin_status . "' AND  p_id='" . $amt . "' AND epin_no like '%" . trim($search) . "%'";
            }
            else if (isset($epin_status) && $epin_status != '' && isset($epin_value) && $epin_value != '') {
                $amt = $epin_value;
                $where = " WHERE status='" . $epin_status . "' AND  p_id='" . $amt . "'";
            }
            else if (isset($epin_value) && $epin_value != '' && !empty($search)) {
                $amt = $epin_value;
                $where = " WHERE  p_id='" . $amt . "' AND epin_no like '%" . trim($search) . "%'";
            }
            else if (isset($epin_status) && $epin_status != '' && !empty($search)) {

                $where = " WHERE  status='" . $epin_status . "' AND epin_no like '%" . trim($search) . "%'";
            }
            else if (isset($epin_status) && $epin_status != '') {
                $where = " WHERE  status='" . $epin_status . "' ";
            }
            else if (isset($epin_value) && $epin_value != '') {
                $amt = $epin_value;
                $where = " WHERE  p_id='" . $amt . "' ";
            }
            else if (!empty($search)) {
                $where = " WHERE  epin_no like '%" . trim($search) . "%' ";
            }

            /*
              $status=(isset($epin_status) && $epin_status!='')? " status='$epin_status'":"";
              $usersearch =!empty($search)? " epin_no like '%".trim($search)."%'" : '';
              if(isset($epin_value) && $epin_value!='')
              {
              $amt=10000*$epin_value;
              $price="epin_price='".$amt."'";
              }
              else
              {
              $price="";
              }

              $where=isset($status) || !empty($usersearch) ? ' WHERE' : '';
              $and=isset($status) && !empty($usersearch) ? ' AND' : '';
              echo $status;
              if($and=='')
              {
              $epinPrice=' AND '.$price;
              }
              else
              {
              if($status=='')
              {
              $epinPrice=$price;
              }
              else
              {
              $epinPrice=' AND '.$price;
              }
              } */
//$status=" WHERE status=$epin_status";
            //$status $and $usersearch $epinPrice
            $sql = "SELECT * FROM {$table_prefix}mlm_epins  $where  ORDER BY id ASC";
        }
//echo $sql;
        $rs = $wpdb->get_results($sql, ARRAY_A);
        $i = 0;
        $listArr = array();
        $listArr[-1]['epin'] = __('Pin No.', 'binary-mlm-pro');
        $listArr[-1]['epinprice'] = __('Pin Price', 'binary-mlm-pro');
        $listArr[-1]['username'] = __('User Name', 'binary-mlm-pro');
        $listArr[-1]['firstname'] = __('First Name', 'binary-mlm-pro');
        $listArr[-1]['lastname'] = __('Last Name', 'binary-mlm-pro');
        $listArr[-1]['type'] = __('Type', 'binary-mlm-pro');
        $listArr[-1]['genarated_on'] = __('Generated On', 'binary-mlm-pro');
        $listArr[-1]['date_used'] = __('Used Date', 'binary-mlm-pro');
        $num = $wpdb->num_rows;
        if ($num > 0) {
            foreach ($rs as $row) {
                $user_id = getuseruidbykey($row['user_key']);
                $firstname = get_user_meta($user_id, 'first_name', true);
                $lastname = get_user_meta($user_id, 'last_name', true);
                $genaral_date = get_option('links_updated_date_format');

                if ($row['date_used'] == '0000-00-00 00:00:00') {
                    $used_date = '';
                }
                else {
                    $used_date = date("$genaral_date", strtotime($row['date_used']));
                }

                if ($row['date_generated'] == '0000-00-00 00:00:00') {
                    $genarated_on = '';
                }
                else {
                    $genarated_on = date("$genaral_date", strtotime($row['date_generated']));
                }
                $price = $wpdb->get_var("select product_price from {$table_prefix}mlm_product_price where p_id='" . $row['p_id'] . "'");
                $type = $row['point_status'] == '1' ? 'Regular' : 'Free';
                $listArr[$i]['epin'] = $row['epin_no'];
                $listArr[$i]['epinprice'] = $price;
                $listArr[$i]['username'] = getusernamebykey($row['user_key']);
                $listArr[$i]['firstname'] = $firstname;
                $listArr[$i]['lastname'] = $lastname;
                $listArr[$i]['type'] = $type;
                $listArr[$i]['genarated_on'] = $genarated_on;
                $listArr[$i]['date_used'] = $used_date;
                $i++;
            }
        }

        $value = serialize($listArr);
        ?>
        <form method="post" action="<?php echo plugins_url() ?>/binary-mlm-pro/mlm_html/export.php">
            <input type="hidden" name ="listarray" value='<?php echo $value ?>' />
            <input type="hidden" name ="filename" value='epin-report' />
            <input type="submit" name="export_csv" value="<?php _e('Export to CSV', 'binary-mlm-pro'); ?>" class="button-primary"/></form>
        <?php
    }
    else {
        ?><div style="padding: 20px;width: 84%;margin: 0 auto;">
            <?php _e('It seems you have not activated the ePin functionality under Settings -> General. ePin Report is accessible only with that setting set to Yes.', 'binary-mlm-pro'); ?>
            <br>
            <?php _e('Click', 'binary-mlm-pro'); ?>&nbsp;<a href="<?php echo admin_url() . "admin.php?page=admin-settings" ?>"><?php _e('<strong>Here</strong>', 'binary-mlm-pro'); ?></a>&nbsp;
            <?php _e(' to go to Settings -> General and activate the ePin functionality.', 'binary-mlm-pro'); ?>
        </div>
        <?php
    }
}
?>