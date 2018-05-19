<div class="content-wrapper">
<?php $device = $_['device'] ?>
<h3>Chart for Device #<?php p($device->getUuid()); ?><small><a href="#" id="zoom_reset" class="reset">
            <span class="icon icon-reset"></span>
        </a></small></h3>
<div id="chart" data-id="<?php p($device->getId()); ?>"></div>
</div>