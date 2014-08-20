<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EpinReports_List_Table extends WP_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'id', //singular name of the listed records
            'plural' => 'id', //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));
    }

    function get_bulk_actions() {
        $actions = array(
                // I don't need to delete post in my case
                //'delete'    => 'Delete'
        );


        $this->epin_dropdown();
        submit_button(__('Filter', 'binary-mlm-pro'), 'secondary', false, false, array('id' => 'post-query-submit'));

        return $actions;
    }

    function epin_dropdown() {
        global $wpdb, $table_prefix;
        extract($_REQUEST);

        if (isset($epin_status)) {
            $epin_status = $epin_status;
        }
        else {
            $epin_status = '';
        }
        ?>
        <select name="epin_status">

            <option value="" <?php echo ($epin_status == '' ? 'selected="selected"' : ''); ?>><?php _e('Choose Option', 'binary-mlm-pro') ?></option>

            <option value="1" <?php echo ($epin_status == '1' ? 'selected="selected"' : ''); ?>><?php _e('Used ePin', 'binary-mlm-pro') ?></option>

            <option value="0" <?php echo ($epin_status == '0' ? 'selected="selected"' : ''); ?>><?php _e('UnUsed ePin', 'binary-mlm-pro') ?></option>
        </select>

        <?php
        if (isset($epin_value)) {
            $epin_value = $epin_value;
        }
        else {
            $epin_value = '';
        }
        ?>
        <?php $pro_price_settings = $wpdb->get_results("select * from {$table_prefix}mlm_product_price"); ?>

        <select name="epin_value">
            <option value="">select ePin Price</option>
            <?php foreach ($pro_price_settings as $pricedetail) { ?>       
                <option value="<?php echo $pricedetail->p_id ?>" <?php echo ($epin_value == $pricedetail->p_id ? 'selected="selected"' : ''); ?>><?php echo $pricedetail->product_name ?></option>
            <?php } ?>
        </select>
        <?php
    }

    function extra_tablenav($which) {
        if ($which == "top") {
            // GENERATE FILTER FOR TOP
        }
        if ($which == "bottom") {
            // FILTER FOR BOTTOM
        }
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'epin':
            case 'epinprice':
            case 'username':
            case 'firstname':
            case 'lastname':
            case 'type':
            case 'genarated_on':
            case 'date_used':

                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'epin' => __('Pin No.', 'binary-mlm-pro'),
            'epinprice' => __('Pin Price', 'binary-mlm-pro'),
            'username' => __('Username', 'binary-mlm-pro'),
            'firstname' => __('First Name', 'binary-mlm-pro'),
            'lastname' => __('Last Name', 'binary-mlm-pro'),
            'type' => __('Type', 'binary-mlm-pro'),
            'genarated_on' => __('Generated On', 'binary-mlm-pro'),
            'date_used' => __('Used on', 'binary-mlm-pro'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
        );
        return $sortable_columns;
    }

    function prepare_items() {
        global $wpdb;
        global $table_prefix;
        global $date_format;
        $per_page = 30;



        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        //======== Search code
        $usersearch = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
        extract($_REQUEST);

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

            $sql = "SELECT * FROM {$table_prefix}mlm_epins  $where  ORDER BY id ASC";
        }
        $this->_column_headers = array($columns, $hidden, $sortable);



        $rs = $wpdb->get_results($sql, ARRAY_A);
        $i = 0;
        //$ID = 1;

        $listArr = array();
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
        /* if(!empty($usersearch)){ 

          extract($_REQUEST);
          $epin_status=isset($epin_status) &&!empty($epin_status)? " AND status='$epin_status'":"";
          $this->_column_headers = array($columns, $hidden, $sortable);


          if(isset($epin_value) && ($epin_value!=''))
          {

          $amount=10000*$epin_value;
          $epin_status.=" AND epin_price=$amount";


          }


          $sql = "SELECT * FROM {$table_prefix}mlm_epins
          where epin_no like '%$usersearch%' $epin_status";

          $rs = $wpdb->get_results($sql);
          $listArr = array();
          $num = $wpdb->num_rows;
          if($num>0){
          $i=0;
          foreach( $rs as $row,ARRAY_A){
          $user_id=empty($row['user_key'])?'':getuseruidbykey($row['user_key']);
          $firstname=empty($user_id)?'':get_user_meta($user_id, 'first_name', true);
          $lastname=empty($user_id)?'':get_user_meta($user_id, 'last_name', true);
          $genaral_date=get_option('links_updated_date_format');

          if($row['date_used']=='0000-00-00 00:00:00')
          {
          $used_date='';
          }
          else
          {
          $used_date=date("$genaral_date",strtotime($row['date_used']));
          }

          if($row['date_generated']=='0000-00-00 00:00:00')
          {
          $genarated_on='';
          }
          else
          {
          $genarated_on=date("$genaral_date",strtotime($row['date_generated']));
          }
          $type=$row['point_status']=='1'?'Regular':'Free';
          $listArr[$i]['epin'] = $row['epin_no'];
          $listArr[$i]['epinprice'] = $row['epin_price'];
          $listArr[$i]['username'] = getusernamebykey($row['user_key']);
          $listArr[$i]['firstname'] = $firstname;
          $listArr[$i]['lastname'] = $lastname;
          $listArr[$i]['type'] = $type;
          $listArr[$i]['genarated_on'] = $genarated_on;
          $listArr[$i]['date_used'] = $used_date;
          $i++;
          }
          }}
          else { 	//=search code end
          $this->_column_headers = array($columns, $hidden, $sortable);

          extract($_REQUEST);

          if(isset($epin_status) && !empty($epin_status))
          {      if($epin_status==1 || $epin_status==0)
          $status=" WHERE status=$epin_status";
          }
          else
          {
          $status="";
          }

          if(isset($epin_value) && ($epin_value!=''))
          {
          if($status!='')
          {
          $amount=10000*$epin_value;

          $status.= " AND epin_price=$amount";

          }
          else {
          $amount=10000*$epin_value;
          $status=" WHERE epin_price=$amount";
          }


          }


          $sql = "SELECT * FROM {$table_prefix}mlm_epins  $status ORDER BY id ASC";

          $rs = $wpdb->get_results($sql,ARRAY_A);
          $i = 0;
          //$ID = 1;

          $listArr = array();
         * $num = $wpdb->num_rows;
          if($num>0){

          foreach($rs = $row)
          {
          $user_id=getuseruidbykey($row['user_key']);
          $firstname=get_user_meta($user_id, 'first_name', true);
          $lastname=get_user_meta($user_id, 'last_name', true);
          $genaral_date=get_option('links_updated_date_format');

          if($row['date_used']=='0000-00-00 00:00:00')
          {
          $used_date='';
          }
          else
          {
          $used_date=date("$genaral_date",strtotime($row['date_used']));
          }

          if($row['date_generated']=='0000-00-00 00:00:00')
          {
          $genarated_on='';
          }
          else
          {
          $genarated_on=date("$genaral_date",strtotime($row['date_generated']));
          }

          $type=$row['point_status']=='1'?'Regular':'Free';
          $listArr[$i]['epin'] = $row['epin_no'];
          $listArr[$i]['epinprice'] = $row['epin_price'];
          $listArr[$i]['username'] = getusernamebykey($row['user_key']);
          $listArr[$i]['firstname'] = $firstname;
          $listArr[$i]['lastname'] = $lastname;
          $listArr[$i]['type'] = $type;
          $listArr[$i]['genarated_on'] = $genarated_on;
          $listArr[$i]['date_used'] = $used_date;
          $i++;

          }
          }
          } */
        $data = $listArr;

        /* function usort_reorder($a,$b){
          $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id';
          $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'DESC';
          $result = strcmp($a[$orderby], $b[$orderby]);
          return ($order==='ASC') ? $result : -$result;
          }
          usort($data, 'usort_reorder');
         */

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

}
?>