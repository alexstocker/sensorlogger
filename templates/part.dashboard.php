<div class="section">
	<?php $log = $_['log']; ?>
		<h2><?php p($log->getTemperature()); ?>°C</h2>
		<h3><?php p($log->getHumidity()); ?>% r.F.</h3>
		<h3><?php p($log->getCreatedAt()); ?> UTC</h3>
</div>
