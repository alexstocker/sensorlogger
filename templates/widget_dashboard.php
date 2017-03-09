<?php $widgets = $_['widgets']; ?>
<?php if($widgets) { ?>

	<?php foreach ($widgets as $widget) { ?>

		<div id="column<?php p($widget->device_id); ?>" class="column">
			<div id="dashboard-widget-<?php p($widget->device_id); ?>" class="widget dashboard-widget dragbox">
				<h2 class="widget-header ui-widget-header"><?php p($widget->getName()); ?>
					<a href="#" class="delete opIcons"><span class="icon icon-delete-white"></span></a>
					<a href="#" class="maxmin opIcons"> </a>
				</h2>

				<div class="section <?php p($widget->getType()); ?>">

					<?php if($widget->getType() == 'last') { ?>

						<?php if(is_array($widget->getLog()->getData()) && !empty($widget->getLog()->getData())) { ?>
							<?php foreach ($widget->getLog()->getData() as $dataLog) { ?>
								<h3><?php p($dataLog->getValue()); ?> <?php p($dataLog->getShort()); ?></h3>
								<small><?php p($dataLog->getDescription()); ?></small>
							<?php } ?>
						<?php } else { ?>
							<h3><?php p($widget->getLog()->getTemperature()); ?>°C</h3>
							<h3><?php p($widget->getLog()->getHumidity()); ?>% r.F.</h3>
						<?php } ?>
						<h3><?php p($widget->getLog()->getCreatedAt()); ?> UTC</h3>

					<?php } ?>

					<?php if($widget->getType() == 'list') { ?>

						<table style="width: 100%;">
						<?php foreach ($widget->getLog() as $dataLog) { ?>
							<tr ><td colspan="3" class="right"><small><?php p($dataLog->getCreatedAt()); ?></small></td></tr>
							<?php foreach ($dataLog->getData() as $log) { ?>
							<tr>
								<td><?php p($log->getType()); ?></td>
								<td class="right"><?php p($log->getValue()); ?></td>
								<td class="center"> <?php p($log->getShort()); ?></td>
							</tr>
							<?php } ?>

							<tr ><td colspan="3" >--</td></tr>

						<?php } ?>
						</table>

					<?php } ?>

					<?php if($widget->getType() == 'chart') { ?>
						<?php //var_dump($widget); ?>
						<div id="widget-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>"></div>
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
		<h2>No dashboard widget configured</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
	</div>
	</div>
<?php } ?>