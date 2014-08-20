<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class PayoutReport_List_Table extends WP_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'id', //singular name of the listed records
            'plural' => 'id', //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));
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
            case 'id':
            case 'payoutdate':
            case 'View':


                // case 'product_name':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('Payout ID', 'binary-mlm-pro'),
            'payoutdate' => __('Payout Date', 'binary-mlm-pro'),
            'View' => __('Details', 'binary-mlm-pro'),
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
        $per_page = 5;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        //======== Search code
        $usersearch = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
        if (!empty($usersearch)) {
            extract($_REQUEST); //echo '<pre>';print_r($_REQUEST);echo '</pre>';
            $this->_column_headers = array($columns, $hidden, $sortable);
            $sql = "SELECT * FROM {$table_prefix}mlm_payout_master ORDER BY id DESC";
            $rs = $wpdb->get_results($sql,ARRAY_A);
            $i = 0;
            $listArr = array();
            $num = $wpdb->num_rows;
            if ($num > 0) {
                foreach ($rs as $row) {
                    $listArr[$i]['id'] = $row['id'];
                    $pdate = date_create($row['date']);
                    $listArr[$i]['payoutdate'] = date_format($pdate, $date_format);
                    $listArr[$i]['View'] = "<a href= '" . admin_url() . "admin.php?page=admin-reports&tab=payoutreports&id=" . $row['id'] . "'>View</a>";
                    $i++;
                }
            }
        }
        else {  //=search code end
            $this->_column_headers = array($columns, $hidden, $sortable);


            $sql = "SELECT * FROM {$table_prefix}mlm_payout_master ORDER BY id DESC";
            $rs = $wpdb->get_results($sql,ARRAY_A);
            $i = 0;
            $listArr = array();
            $num = $wpdb->num_rows;
            if ($num > 0) {
                foreach ($rs as $row) {
                    $listArr[$i]['id'] = $row['id'];
                    $pdate = date_create($row['date']);
                    $listArr[$i]['payoutdate'] = date_format($pdate, $date_format);
                    $listArr[$i]['View'] = "<a href= '" . admin_url() . "admin.php?page=admin-reports&tab=payoutreports&id=" . $row['id'] . "'>View</a>";
                    $i++;
                }
            }
        }
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