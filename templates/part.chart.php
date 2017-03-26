<div id="app-content">
	<div id="app-content-wrapper">
		<?php $device = $_['device'] ?>
		<h3>Chart for Device #<?php p($device->getUuid()); ?></h3>
		<div id="chart" data-id="<?php p($device->getId()); ?>"></div>
	</div>
</div>