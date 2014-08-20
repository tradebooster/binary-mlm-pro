<?php

function po_file_editor() { ?>

    <div class="wrap">
        <?php //screen_icon();  ?>
        <h2><?php ?></h2>
        <div class="fileedit-sub">
            <div class="alignleft">
                <?php
                require(MLM_PLUGIN_DIR . '/mlm_html/php-mo.php');
                $locale = apply_filters('plugin_locale', get_locale(), 'binary-mlm-pro');
                ?>
                <h3><?php _e("Binary-MLM-Pro : Language file (binary-mlm-pro-$locale.po)"); ?></h3>
            </div>
            <br class="clear" />
        </div>
        <?php

        function Read() {
            $locale = apply_filters('plugin_locale', get_locale(), 'binary-mlm-pro');
            $file = MLM_PLUGIN_DIR . "/languages/binary-mlm-pro-$locale.po";
            if (file_exists($file)) {
                $fp = fopen($file, "r");
                while (!feof($fp)) {
                    $data = fgets($fp, filesize($file));
                    _e("$data");
                }
                fclose($fp);
            }
            else {
                $file = MLM_PLUGIN_DIR . "/languages/binary-mlm-pro-en_EN.po";
                $fp = fopen($file, "r");
                while (!feof($fp)) {
                    $data = fgets($fp, filesize($file));
                    _e("$data");
                }
                fclose($fp);
            }
        }

        ;

        function Write() {
            $locale = apply_filters('plugin_locale', get_locale(), 'binary-mlm-pro');
            $file = MLM_PLUGIN_DIR . "/languages/binary-mlm-pro-$locale.po";
            $fp = fopen($file, "w+") or die("can't open file");
            $data = stripslashes($_POST['newcontent']);
            $fff = fwrite($fp, $data);
            fclose($fp);
            phpmo_convert(MLM_PLUGIN_DIR . "/languages/binary-mlm-pro-$locale.po", MLM_PLUGIN_DIR . "/languages/binary-mlm-pro-$locale.mo");
        }

        ;
        ?>

        <?php
        if (isset($_POST["submit"])) {
            Write();
        };
        ?> 
        <form action="<?php admin_url() ?>admin.php?page=mlm-po-file-editor.php" method="post">
            <textarea cols="200" rows="28" name="newcontent" id="newcontent" aria-describedby="newcontent-description"><?php Read(); ?></textarea><br>
            <input type="submit" name="submit" value="Update text"></form>
        <?php
        if (isset($_POST["submit"])) {
            _e('Text updated');
        };
        ?>      
    <?php } ?>
