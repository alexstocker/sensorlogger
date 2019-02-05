<div class="content-wrapper">
<?php $device = $_['device'] ?>
<h3>Chart for Device <b><?php p(htmlentities($device->getName())); ?></b><small><a href="#" id="zoom_reset" class="reset">
            <span class="icon icon-reset"></span>
        </a></small></h3>
<div id="chart" data-id="<?php p($device->getId()); ?>"></div>
</div>