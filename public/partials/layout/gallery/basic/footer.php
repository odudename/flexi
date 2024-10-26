</div>
</div>
<div style="clear: both; display: block; position: relative;"></div>
<?php
if (0 == $count) {
    echo '<div id="flexi_no_record" class="flexi_alert-box flexi_notice">' . __('No records', 'flexi') . '</div>';
    do_action("flexi_no_records", $search);
}

?>