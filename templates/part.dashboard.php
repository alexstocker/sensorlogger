<div class="section">
	<?php $log = $_['log']; ?>
	<?php if($log) { ?>
		<h2><?php p($log->getTemperature()); ?>°C</h2>
		<h3><?php p($log->getHumidity()); ?>% r.F.</h3>
		<h3><?php p($log->getCreatedAt()); ?> UTC</h3>
	<?php } else { ?>
		<div id="emptycontent" class="">
			<div class="icon-info"></div>
			<h2>No dashboard data vailable</h2>
			<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
		</div>
	<?php } ?>
</div>
