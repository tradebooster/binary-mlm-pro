<?php

class BinaryTree {

    //$clients array contain the nodes
    public $clients = array();
    //$add_page_id variable take the wordpress pageID for the network registration
    public $add_page_id;
    //$view_page_id take the wordpress pageID where the netwok to open
    public $view_page_id;
    //$counter varibale take the how many level you want to shows the network
    public $counter = 3;

    //constructor
    function BinaryTree() {
        $this->add_page_id = get_post_id('mlm_registration_page');
        $this->view_page_id = get_post_id('mlm_network_page');
    }

    //getUsername() function take the key and return the username of the user's key
    function getUsername($key) {
        global $wpdb;
        $sql = "SELECT username 
				FROM {$wpdb->prefix}mlm_users
				WHERE user_key = '" . $key . "'
				AND banned = '0'";
        return $wpdb->get_var($sql);
        
    }

    function getuserkey($username) {
        global $wpdb;
        $sql = "SELECT user_key 
				FROM {$wpdb->prefix}mlm_users
				WHERE username = '" . $username . "'
				AND banned = '0'";
        return $wpdb->get_var($sql);
    }

    function getname($username) {
        global $wpdb;
        $sql = "SELECT user_id FROM {$wpdb->prefix}mlm_users WHERE username = '" . $username . "'";
        $user_id = $wpdb->get_var($sql);
        $user_info = get_userdata($user_id);
        if (!empty($user_info->first_name)) {
            $name = ucfirst(strtolower($user_info->first_name)) . " " . ucfirst(strtolower($user_info->last_name));
            return $name;
        }
    }

    function totalLeftLegUsers($key) {
        global $wpdb;
        return $num = $wpdb->get_var(" SELECT COUNT(*) AS num FROM {$wpdb->prefix}mlm_leftleg  WHERE pkey = '" . $key . "'");
    }

    function totalRightLegUsers($key) {
        global $wpdb;
        return $num = $wpdb->get_var(" SELECT COUNT(*) AS num FROM {$wpdb->prefix}mlm_rightleg WHERE pkey = '" . $key . "'");
    }

    //addLeftLeg() function build the Left leg registration node of the network
    function addLeftLeg($key, $add_page_id) {
        $username = '(' . $this->totalLeftLegUsers($key) . ') ' . $this->getUsername($key) . ' (' . $this->totalRightLegUsers($key) . ')' . '<br/>(' . $this->getname($this->getUsername($key)) . ')';
        $str = "[{v:'" . $key . "ADD', 
				f:'<a href=\"?page_id=" . $add_page_id . "&k=" . $key . "&l=0\">" . __('ADD', 'binary-mlm-pro') . "</a><br><span class=\"leg\">" . __('Left leg', 'binary-mlm-pro') . "</span>'}, 
				'" . $username . "', 
				''],";
        return $str;
    }

    //addRightLeg() function build the Right leg registration node of the network
    function addRightLeg($key, $add_page_id) {
        $username = '(' . $this->totalLeftLegUsers($key) . ') ' . $this->getUsername($key) . ' (' . $this->totalRightLegUsers($key) . ')' . '<br/>(' . $this->getname($this->getUsername($key)) . ')';
        $str = "[{v:'" . $key . "ADD2', 
				f:'<a href=\"?page_id=" . $add_page_id . "&k=" . $key . "&l=1\">" . __('ADD', 'binary-mlm-pro') . "</a><br><span class=\"leg\">" . __('Right leg', 'binary-mlm-pro') . " </span>'}, 
				'" . $username . "', 
				''],";
        return $str;
    }

    //checkPaymentStatus() function check the node user is paid or not
    function checkPaymentStatus($key, $payment, $view_page_id) {
        $paymentStr = '<br><a href="?page_id=' . $view_page_id . '&k=' . $key . '">' . __('View', 'binary-mlm-pro') . '</a>';
        if ($payment == 1) {
            $paymentStr.='<br><span class=\"paid\">' . __('PAID', 'binary-mlm-pro') . '</span>';
        }
        return $paymentStr;
    }

    //buildRootNetwork() function take the key and build the root node of the network
    function buildRootNetwork($key) {
        $level = array();
        $username = '(' . $this->totalLeftLegUsers($key) . ') ' . $this->getUsername($key) . ' (' . $this->totalRightLegUsers($key) . ')' . '<br/>(' . $this->getname($this->getUsername($key)) . ')';
        $myclients[] = "[{v:'" . $username . "', f:'" . $username . "<br><span class=\"owner\">" . __('Owner', 'binary-mlm-pro') . "</span>'}, '', 'The owner'],";
        $this->clients[] = $myclients;
        $level[] = $key;
        // splite over rule
        return $level;
    }

    //buildLevelByLevelNetwork() function build the 1st and more level network
    function buildLevelByLevelNetwork($key, $add_page_id, $view_page_id, $counter, $level) {
        global $wpdb;
        $level1 = array();

        for ($i = 0; $i < $counter; $i++) {
            $myclients = array();
            if (!empty($level[$i]) && $level[$i] != 'add' && $level[$i] != '') {

                $sql = "SELECT username, payment_status, user_key, leg 
						FROM {$wpdb->prefix}mlm_users 
						WHERE parent_key = '" . $level[$i] . "' 
						AND banned='0' 
						ORDER BY leg DESC ";
                $sql = mysql_query($sql);
                $num = mysql_num_rows($sql);
                // no child case
                if (!$num) {
                    $myclients[] = $this->addLeftLeg($level[$i], $add_page_id);
                    $myclients[] = $this->addRightLeg($level[$i], $add_page_id);
                    $level1[] = 'add';
                    $level1[] = 'add';
                }
                //if child exist
                else if ($num > 0) {
                    $username = '(' . $this->totalLeftLegUsers($level[$i]) . ') ' . $this->getUsername($level[$i]) . ' (' . $this->totalRightLegUsers($level[$i]) . ')' . '<br/>(' . $this->getname($this->getUsername($level[$i])) . ')';
                    while ($row = mysql_fetch_array($sql)) {
                        //check user paid or not
                        $payment = $this->checkPaymentStatus($row['user_key'], $row['payment_status'], $view_page_id);
                        //if only one child exist
                        if ($num == 1) {
                            //if right leg child exist
                            if ($row['leg'] == 1) {
                                $name = '(' . $this->totalLeftLegUsers($this->getuserkey($row['username'])) . ') ' . $this->getUsername($this->getuserkey($row['username'])) . ' (' . $this->totalRightLegUsers($this->getuserkey($row['username'])) . ')' . '<br/>(' . $this->getname($row['username']) . ')';
                                $myclients[] = $this->addLeftLeg($level[$i], $add_page_id);
                                $myclients[] = "[{v:'" . $name . "',f:'" . $name . $payment . "'}, '" . $username . "', ''],";
                                $level1[] = 'add';
                                $level1[] = $row['user_key'];
                            }
                            else {  // if left leg child exist
                                $name = '(' . $this->totalLeftLegUsers($this->getuserkey($row['username'])) . ') ' . $this->getUsername($this->getuserkey($row['username'])) . ' (' . $this->totalRightLegUsers($this->getuserkey($row['username'])) . ')' . '<br/>(' . $this->getname($row['username']) . ')';
                                $myclients[] = "[{v:'" . $name . "',f:'" . $name . $payment . "'}, '" . $username . "', ''],";
                                $myclients[] = $this->addRightLeg($level[$i], $add_page_id);
                                $level1[] = $row['user_key'];
                                $level1[] = 'add';
                            }
                        }
                        else {  //both child exist left and right leg
                            $name = '(' . $this->totalLeftLegUsers($this->getuserkey($row['username'])) . ') ' . $this->getUsername($this->getuserkey($row['username'])) . ' (' . $this->totalRightLegUsers($this->getuserkey($row['username'])) . ')' . '<br/>(' . $this->getname($row['username']) . ')';
                            $myclients[] = "[{v:'" . $name . "',f:'" . $name . $payment . "'}, '" . $username . "', ''],";
                            $level1[] = $row['user_key'];
                        }
                    } //end while loop
                }
                $this->clients[] = $myclients; //print_r($myclients); 
            } // end most outer if statement
        } //end for loop 
        return $level1;
    }

    function network() {
        global $current_user;
        get_currentuserinfo();
        $username2 = $current_user->user_login;

        global $wpdb;
        $res12 = mysql_fetch_array(mysql_query("SELECT * FROM {$wpdb->prefix}mlm_users WHERE username = '" . $username2 . "'"));
        if (!empty($_POST['search'])) {
            $username = $_POST['username'];
            $select = mysql_query("SELECT user_key FROM {$wpdb->prefix}mlm_users WHERE username = '" . $username . "'");
            $num = mysql_num_rows($select);
            if ($num) {
                $result = mysql_fetch_array($select);
                $left = mysql_query("SELECT pkey FROM {$wpdb->prefix}mlm_leftleg 
									 WHERE ukey = '" . $result['user_key'] . "' AND pkey = '" . $res12['user_key'] . "'");
                $leftnum = mysql_num_rows($left);
                if ($leftnum)
                    $key = $result['user_key'];
                else {
                    $right = mysql_query("SELECT pkey FROM {$wpdb->prefix}mlm_rightleg 
										  WHERE ukey = '" . $result['user_key'] . "' AND pkey = '" . $res12['user_key'] . "'");
                    $rightnum = mysql_num_rows($right);
                    if ($rightnum)
                        $key = $result['user_key'];
                    else {
                        ?>
                        <p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;"><?php _e("You can't authorized to access searched user's genealogy.", "binary-mlm-pro"); ?></p>
                        <div class="button-wrap" style="height:14px; margin-left:20px;">
                            <div class="red button">
                                <a href="javascript:history.back(1)"><?php _e('Back', 'binary-mlm-pro'); ?></a>
                            </div>
                        </div>
                        <?php
                        exit;
                    }
                }
            }
            else {
                ?>
                <p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;"><?php _e('You have searched a wrong username.', 'binary-mlm-pro'); ?></p>
                <div class="button-wrap" style="height:14px; margin-left:20px;">
                    <div class="red button">
                        <a href="javascript:history.back(1)"><?php _e('Back', 'binary-mlm-pro'); ?></a>
                    </div>
                </div>
                <?php
                exit;
            }
        }
        else {
            if (!empty($_GET['k'])) {
                if ($_GET['k'] != '')
                    $key = $_GET['k'];
            }
            else {
                $key = get_current_user_key();
            }
        }
        /*
          $browser = getBrowser();
          if($browser=="error")
          {?>
          <p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;">For best results use only <a href="http://www.apple.com/safari/download/" target="_blank">Safari</a> / <a href="https://www.google.com/chrome" target="_blank">Chrome</a>.</p>
          <?php
          exit;
          } */


        /*         * ********************************************* Root node ***************************************** */
        $level = $this->buildRootNetwork($key);
        /*         * ********************************************* First level ***************************************** */
        $level = $this->buildLevelByLevelNetwork($key, $this->add_page_id, $this->view_page_id, 1, $level);
        /*         * ********************************************* 2 and more level's ***************************************** */
        if ($this->counter >= 2) {
            $j = 1;
            for ($i = 2; $i <= $this->counter; $i++) {
                $j = $j * 2;
                $level = $this->buildLevelByLevelNetwork($key, $this->add_page_id, $this->view_page_id, $j, $level);
            }
        }
        if (!checkKey($key)) {
            ?>
            <p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;"><?php _e('There has been an error while generating this tree. <br>Please contact the system admin at', 'binary-mlm-pro'); ?> <?php echo get_option('admin_email') ?> <?php _e('to report this problem.', 'binary-mlm-pro'); ?></p>
            <?php
            return true;
        }
        return $this->clients;
    }

//end function
}

// end class

function viewBinaryNetwork() {
    global $wpdb;
    $obj = new BinaryTree();
    global $current_user;
    get_currentuserinfo();

    $username1 = $current_user->user_login;
    $var = $obj->network();


    global $wpdb;
    $res = mysql_fetch_array(mysql_query("SELECT user_key FROM {$wpdb->prefix}mlm_users WHERE username = '" . $username1 . "'"));
    ?>
    <style type="text/css">
        span.owner
        {
            color:#339966; 
            font-style:italic;
        }
        span.paid
        {
            color: #669966!important; 
            /*background-color:#770000; */
            font-style:normal;
        }
        span.leg
        {
            color:red; 
            font-style:italic;
        }
    </style>
    <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
        google.load('visualization', '1', {packages: ['orgchart']});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');
            data.addRows([<?php
    for ($i = 0; $i < count($var); $i++) {
        for ($j = 0; $j < count($var[$i]); $j++) {
            _e($var[$i][$j]);
        }
    }
    ?> ['', null, '']]);
            var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
            chart.draw(data, {allowHtml: true});
        }
    </script>
    <script type="text/javascript" language="javascript">
        function searchUser()
        {
            var user = document.getElementById("username").value;
            if (user == "")
            {
                alert("Please enter username then searched.");
                document.getElementById("username").focus();
                return false;
            }
        }
    </script>
    <table border="0" cellspacing="0" cellpadding="0" >
        <tr>
            <td align="center">	
                <form action="<?php bloginfo('url', 'binary-mlm-pro'); ?>/?page_id=<?php echo $obj->view_page_id ?>&k=<?php _e($res['user_key']); ?>" method="post">
                    <input type="submit" value="<?php echo __('YOU', 'binary-mlm-pro'); ?>">
                </form>
            </td>
            <td align="center">
                <form name="usersearch" id="usersearch" action="" method="post" onSubmit="return searchUser();">
                    <input type="text" name="username" id="username"> <input type="submit" name="search" value="<?php echo __('Search', 'binary-mlm-pro'); ?>">
                </form>
            </td>
        </tr>               
    </table>
    <div style="margin:0 auto;padding:0px;clear:both; width:100%!important;" align="center">
        <div id='chart_div'></div>
    </div>
    <?php
}
?>