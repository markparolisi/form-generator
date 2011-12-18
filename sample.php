<?php
require('form_init.php');
$ini = (empty($_POST['ini-dd'])) ? 'default' : $_POST['ini-dd'];

function get_files($d) {
    foreach (array_diff(scandir($d), array('.', '..')) as $f) {
        if (is_file($d . '/' . $f))
            $f = str_replace(".ini", "", $f);
        $files[] = $f;
    }
    unset($files[0]);
    return $files;
}

$files = get_files(INI_DIR);
?>
<!html>
    <head>
        <title>Sample Form</title>
        <?php
        $form = new Form_Generator($ini);
        $form->printFormJS();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){

                ckgFormInit('#<?php echo $ini; ?>');

                extra_callback = function(){

                }

                extra_error = function(){

                }

            });
        </script>
        <style type="text/css">
            body {text-align:center; color: #4C4C4C; background: #efefef;}
            .clear {clear: both;}
            .form-wrap {width: 300px; margin: 20px auto; padding: 10px 30px 20px 30px; background: #fff; border: 5px solid #bbb; -moz-box-shadow: 5px 5px 10px #ddd;}
            .form-wrap h2 {font-size: 32px; text-align: center; text-shadow: #eee -2px -1px;  }
            #ini-select {text-align:center; padding: 0px 30px;}
            #ini-select h2 {margin: .2em;}
            #ini-chooser select, #ini-chooser input{ float: left; margin: 0 3px 10px 3px;}
            #sample {text-align:left; }
            label {display: block; clear: both;}
            label.header_label { font-size: 16px; font-weight: bold;  }
            label.radio_label {float: left; display: inline; margin-right: 5px; width: 150px;}
            label.checkbox_label {float: left; display: inline; margin-right: 5px; width: 150px;}
            input, textarea, select, body {font-family: arial, helvetica, sans; font-size: 16px;}
            input {border: 1px solid #eee; padding: 4px 2px; margin: 5px 0;}
            textarea {border: 1px solid #eee; width: 300px; padding: 4px 2px; margin: 2px auto; }
            select {border: 1px solid #eee; padding: 4px 2px; margin: 5px 0; display: block;}
            input.default_radio { float: left;}
            input.default_text_field {width: 170px; display: block;}
            input.default_text_field:first-child{margin-right: 7px; width: 123px;}
            input.default_checkbox{float: left; margin: 0 5px 0 0;}
            input.default_submit, input.default_reset {background: #aaa; color: #fff; font-size: 16px;  border: 3px solid #bbb; margin: 10px 25px 0 0; cursor:pointer;}
            fieldset{border: 0; margin: 0; padding: 0;}
            #qq_label {display: inline;}
            .invalid {color: red; font-weight: bold;}
            #zipcode {width: 100px; display: block;}
            .hidden { display: none;}
            p.errors {font-size: 18px; color: red;}
        </style>
    </head>
    <body>
        <div id="ini-select" class="form-wrap">
            <h2>INI Select</h2>
            <form method="POST" id="ini-chooser">
                <select id="ini-dd" name="ini-dd">
                    <?php
                    foreach ($files as $file) {
                        echo "<option value='$file'>$file</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="ini-submit" id="ini-submit" class="default_submit" value="refresh" />
            </form>
            <div class="clear"></div>
        </div>
        <h2>You are using the <?php echo $ini; ?> INI</h2>
        <div id="sample" class="form-wrap">
            <h2>Sample Form</h2>
            <?php
            echo $form->renderForm();
            ?>
        </div>
        <?php
        if ($form->errors):
            echo '<p class="errors">' . implode(', ', $form->errors) . '</p>';
        endif;
        ?>
        <div class="hidden">
            <div id="default_thankyou">
                Thank you
            </div>
        </div>
    </body>
</html>
