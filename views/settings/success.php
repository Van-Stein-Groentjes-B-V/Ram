<?php
?>
<div class='col-md-12 column'>
    <h4 class="alert alert-success"><?php echo _('Config update completed.'); ?></h4>
    <p>
        <?php echo _('The config is updated and will soon be working correctly.'); ?>
        <?php header("refresh:1; url=" . SITE_ROOT . "settings/admin"); ?>
    </p>
</div>