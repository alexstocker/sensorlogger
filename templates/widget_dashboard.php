<?php $widgets = $_['widgets']; ?>
<?php if($widgets) { ?>

	<?php foreach ($widgets as $widget) { ?>
		<div id="column<?php p($widget->device_id); ?>" class="column">
	<div id="dashboard-widget-<?php p($widget->device_id); ?>" class="widget dashboard-widget dragbox">
		<h2 class="widget-header ui-widget-header"><?php p($widget->name); ?>
			<a href="#" class="delete opIcons"><span class="icon icon-delete-white"></span></a>
			<a href="#" class="maxmin opIcons"> </a>
		</h2>
		<div class="section">

		<?php foreach ($widget->log as $log) { ?>
			<?php if(is_array($log->getData()) && !empty($log->getData())) { ?>
				<?php foreach ($log->getData() as $dataLog) { ?>
					<h3><?php p($dataLog->getValue()); ?> <?php p($dataLog->getShort()); ?></h3>
					<small><?php p($dataLog->getDescription()); ?></small>
				<?php } ?>
			<?php } else { ?>
				<h3><?php p($log->getTemperature()); ?>°C</h3>
				<h3><?php p($log->getHumidity()); ?>% r.F.</h3>
			<?php } ?>

			<h3><?php p($log->getCreatedAt()); ?> UTC</h3>
		<?php } ?>
		</div>
	</div>
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="widget dashboard-widget">
		<div class="widget-header"></div>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>No dashboard data vailable</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
	</div>
	</div>
<?php } ?>