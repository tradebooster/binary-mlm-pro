<?php

function mlmProductPrice() {
    global $pagenow, $wpdb, $table_prefix;


    $error = '';
    $chk = 'error';


    $tabs = array(
        'add' => __('Add Product', 'binary-mlm-pro'),
        'view' => __('View Product', 'binary-mlm-pro')
    );

    $tabval = 'add';
    $tabfun = 'add_product_price';

    if (!empty($_GET['action'])) {
        if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'product_price' && $_GET['action'] == 'view')
            $current = 'view';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'product_price' && $_GET['action'] == 'add')
            $current = 'add';
    }
    else
        $current = $tabval;


    _e('<h2 class="nav-tab-wrapper">');
    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        _e("<a class='nav-tab$class' href='?page=admin-settings&tab=product_price&action=$tab'>$name</a>");
    }
    _e('</h2>');



    if (!empty($_GET['action'])) {
        if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'product_price' && $_GET['action'] == 'view')
            update_product_price();

        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'product_price' && $_GET['action'] == 'add')
            add_product_price();
    }
    else
        $tabfun();
}

function add_product_price() {
    global $wpdb, $table_prefix;
    $error = '';
    $chk = 'error';
    if (isset($_POST['add_price'])) {

        $product_name = $_POST['product_name'];


        $product_price = $_POST['product_price'];


        if ($product_name == '')
            $error .= "\n Please Specify Product Name.";

        if ($product_price == '')
            $error .= "\n Please Specify Product Price.";


        //if any error occoured
        if (!empty($error))
            $error = nl2br($error);
        else {
            $chk = '';
            $wpdb->query("insert into {$table_prefix}mlm_product_price (p_id,product_name,product_price) values ('','" . $product_name . "','" . $product_price . "')");
            $msg = _e("<span style='color:green;'>Your product price details has been successfully updated.</span>", 'binary-mlm-pro');
        }
    }
    ?>
    <div class="notibar msginfo">
        <a class="close"></a>	
        <p><?php _e('Use this section to create the various products that you would like to offer on your site. This will not add any e-commerce functionality on your site but would enable you to sell multiple products on your site without the need to install an ecommerce plugin. Each product will be mapped to an ePin. More details about the same is available under the ePins Tab.', 'unilevel-mlm-pro'); ?></p>
        <p><strong><?php _e('Product Name', 'unilevel-mlm-pro'); ?> -</strong> <?php _e(' This is the name of the product. ', 'unilevel-mlm-pro'); ?> </p>
        <p><strong><?php _e('Product Price', 'unilevel-mlm-pro'); ?> -</strong> <?php _e(' This is the price of the product. The % commission figures will be applied to this price. ', 'unilevel-mlm-pro'); ?></p>

    </div>
    <?php if ($error) : ?>
        <div class="notibar msgerror">
            <a class="close"></a>
            <p> <strong><?php _e('Please Correct the following Error', 'binary-mlm-pro'); ?> :</strong> <?php _e($error); ?></p>
        </div>
    <?php endif; ?>
    <?php if (!empty($msg)) : ?>
        <div class="notibar msgerror">
            <a class="close"></a>
            <p><?php _e($msg); ?></p>
        </div>
    <?php endif; ?>


    <form name="newproduct" action="" method="post">
        <TABLE id="dataTableheading" cellspacing="5" cellpadding="5"  border="0" width="450">
            <TR>
                <TD align="left" width="44%"> <strong><?php _e('Product Name', 'binary-mlm-pro'); ?></strong></TD>
                <TD align="left" width="44%"> <INPUT type="text" name="product_name" size="15" value="" class="text" required/> </TD></TR>

            <TR>
                <TD align="left" width="44%"> <strong><?php _e('Product Price', 'binary-mlm-pro'); ?></strong></TD>
                <TD align="left" width="44%"> <INPUT type="text" name="product_price" size="15" value="" class="text" required/> </TD>
            </TR>

            <TR>
                <TD>&nbsp;</TD>
                <TD align="left" width="44%">
                    <p class="submit">
                        <input type="submit" name="add_price" id="add_price" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' >
                    </p></TD></TR>
        </TABLE>
    </form>


    <?php
}

function update_product_price() {
    global $wpdb, $table_prefix;

    

    if (isset($_POST['update'])) {
        $wpdb->query("update {$table_prefix}mlm_product_price set product_name='" . $_POST['product_name'] . "',product_price='" . $_POST['product_price'] . "' where p_id='" . $_POST['p_id'] . "'");
    }
    else if (isset($_POST['delete'])) {

        $wpdb->query("delete from {$table_prefix}mlm_product_price where p_id='" . $_POST['p_id'] . "'");
    }
    $results = $wpdb->get_results("select * from {$table_prefix}mlm_product_price where p_id!='1' order by p_id", ARRAY_A);
    $num = $wpdb->num_rows;
    if ($num > 0) {
        ?>
        <TABLE id="dataTableheading" cellspacing="5" cellpadding="5"  border="0" width="450">
            <TR>
                <TD align="left" width="34%"> <strong><?php _e('Product Name', 'binary-mlm-pro'); ?></strong></TD>

                <TD align="left" width="24%"> <strong><?php _e('Product Price', 'binary-mlm-pro'); ?></strong></TD>
                <TD>&nbsp;</TD>
            </TR>
            <?php
            foreach ($results as $detail) {
                echo '<TR>
<form name="" action="" method="post">
<TD align="left" width="34%"> <INPUT type="text" name="product_name" size="15" value="' . $detail['product_name'] . '"/> </TD>
<TD align="left" width="24%"> <INPUT type="text" name="product_price" size="8" value="' . $detail['product_price'] . '"/></TD>
<TD align="left" width="42%"> 
<input type="hidden" class="f" name="p_id" value="' . $detail['p_id'] . '"/>
<input type="submit" class="f" name="update" value="update">
<input type="submit" class="f" name="delete" value="delete"></TD>
</form>
</TR>';
            }
            ?>
        </TABLE>

        <?php
    }
    else {
        echo "<strong>You have not Set the any product. Please add product first.</strong>";
    }
}

function mlmProductPriceOLD() {
    global $wpdb, $table_prefix;


    $error = '';
    $chk = 'error';

    $mlm_general_settings = get_option('wp_mlm_general_settings');

    if (isset($_POST['mlm_product_price_settings'])) {

        $product_name = count(array_filter($_POST['product_name']));


        $product_price = count(array_filter($_POST['product_price']));


        if ($product_name == 0)
            $error .= "\n Please Specify Name. of Product.";

        if ($product_price == 0)
            $error .= "\n Please Specify Product Price.";


        //if any error occoured
        if (!empty($error))
            $error = nl2br($error);
        else {
            $chk = '';

            update_option('wp_mlm_product_price_settings', $_POST);
            $url = get_bloginfo('url') . "/wp-admin/admin.php?page=admin-settings&tab=product_price";
            _e("<script>window.location='$url'</script>");
            $msg = _e("<span style='color:green;'>Your product price details has been successfully updated.</span>", 'binary-mlm-pro');
        }
    }

    if ($chk != '') {
        $mlm_settings = get_option('wp_mlm_product_price_settings'); //print_r($mlm_settings);
        ?>

        <div class='wrap1'>
            <h2><?php _e('Product Price', 'binary-mlm-pro'); ?>  </h2>


            <?php if ($error) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p> <strong><?php _e('Please Correct the following Error', 'binary-mlm-pro'); ?> :</strong> <?php _e($error); ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($msg)) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p><?php _e($msg); ?></p>
                </div>
            <?php endif; ?>



            <?php
            if (empty($mlm_settings)) {
                ?>
                <form name="admin_product_settings" method="post" action="">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
                        <tr>

                            <td>
                                <INPUT type="button" value="<?php _e('Add Row', 'binary-mlm-pro') ?>" onclick="addRow('dataTable')" class='button-primary' />
                                <INPUT type="button" value="<?php _e('Delete Row', 'binary-mlm-pro') ?>" onclick="deleteRow('dataTable')" class='button-primary' />
                                <div class="toggle-visibility" id="admin-mlm-bonus-slab"><?php _e('Add or remove bonus slab.', 'binary-mlm-pro'); ?></div>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                    </table>

                    <TABLE id="dataTableheading" cellspacing="5" cellpadding="5"  border="0" width="450">
                        <TR>

                            <TD align="left" width="12%"><strong><?php _e('Select', 'binary-mlm-pro'); ?></strong></TD>

                            <TD align="left" width="44%"> <strong><?php _e('Product Name', 'binary-mlm-pro'); ?></strong></TD>

                            <TD align="left" width="44%"> <strong><?php _e('Product Price', 'binary-mlm-pro'); ?></strong></TD>
                        </TR>
                    </TABLE>
                    <br\>
                    <TABLE id="dataTable"  cellspacing="0" cellpadding="0" border="0" width="450">
                        <TR>

                            <TD align="left" width="12%"><INPUT type="checkbox" name="chk[]"/>
                                <INPUT type="hidden" name="p_id[]" />
                            </TD>

                            <TD align="left" width="44%"> <INPUT type="text" name="product_name[]" size="15" /> </TD>

                            <TD align="left" width="44%"> <INPUT type="text" name="product_price[]" size="15" /> </TD>

                        </TR>
                    </TABLE>

                    <table border="0" width="100%"><tr><td>
                                <p class="submit">
                                    <input type="submit" name="mlm_product_price_settings" id="mlm_product_price_settings" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
                                </p>
                            </td><tr></table>

                </form>
            </div>
            <script language="JavaScript">
                populateArrays();
            </script>
            <?php
        }
        else if (!empty($mlm_settings)) {
            ?>

            <form name="admin_product_settings" method="post" action="">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
                    <tr>

                        <td>
                            <INPUT type="button" value="<?php _e('Add Row', 'binary-mlm-pro') ?>" onclick="addRow('dataTable')" class='button-primary' />
                            <INPUT type="button" value="<?php _e('Delete Row', 'binary-mlm-pro') ?>" onclick="deleteRow('dataTable')" class='button-primary' />
                            <div class="toggle-visibility" id="admin-mlm-bonus-slab"><?php _e('Add or remove bonus slab.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                </table>

                <TABLE id="dataTableheading" cellspacing="5" cellpadding="5"  border="0" width="450">
                    <TR>

                        <TD align="left" width="12%"><strong><?php _e('Select', 'binary-mlm-pro'); ?></strong></TD>

                        <TD align="left" width="44%"> <strong><?php _e('Product Name', 'binary-mlm-pro'); ?></strong></TD>

                        <TD align="left" width="44%"> <strong><?php _e('Product Price', 'binary-mlm-pro'); ?></strong></TD>
                    </TR>
                </TABLE>
                <br\>
                <TABLE id="dataTable"  cellspacing="0" cellpadding="0" border="0" width="450">

                    <?php
                    $i = 0;
                    while ($i < count($mlm_settings['product_price'])) {
                        ?>
                        <TR>
                            <TD align="left" width="12%"><INPUT type="checkbox" name="chk[]"/>
                                <INPUT type="hidden" name="p_id[]" value="<?php echo $mlm_settings['p_id'][$i]; ?>"/>
                            </TD>
                            <TD align="left" width="44%"> <INPUT type="text" name="product_name[]" size="15" value="<?php echo $mlm_settings['product_name'][$i] ?>"/> </TD>
                            <TD align="left" width="44%"> <INPUT type="text" name="product_price[]" size="15" value="<?php echo $mlm_settings['product_price'][$i] ?>"/> </TD>
                        </TR>    	
                        <?php
                        $i++;
                    }
                    ?>

                </TABLE>

                <table border="0" width="100%"><tr><td>
                            <p class="submit">
                                <input type="submit" name="mlm_product_price_settings" id="mlm_product_price_settings" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
                            </p>
                        </td><tr></table>

            </form>
            </div>
            <script language="JavaScript">
                populateArrays();
            </script>			

            <?php
        }
    } // end if statement
    else
        _e($msg);

    echo '</div>';
}

//end mlmBonus function
?>