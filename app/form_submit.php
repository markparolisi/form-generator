<?php
require('form_init.php');
$curl = new CurlSend($_POST);
$curl->writeLog();
if (!isset($_REQUEST['error'])) {
?>
<!-- this message is a fallback for non-AJAX requests -->
    <meta http-equiv="refresh" content="5;url=<?php echo $_SERVER['HTTP_REFERER']; ?>">
    <h2>Thank you for your submission.</h2>
    <p>You will be redirected back in 5 seconds.</p>
    <div style="display: none">
    <?php echo $curl->sendToAPI(); ?>
</div>
<?php
}
