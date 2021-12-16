<?php $widgets = $_['widgets']; ?>
<?php if($widgets) { ?>
	<?php foreach ($widgets as $widgetIdx => $widget) { ?>
		<?php //var_dump($widget->getOptions()); ?>
		<div id="column-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>"
			 class="column"
			 data-gs-id="widget-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>"
			 data-gs-width="<?php p($widget->getOptions('size')['w']) ?>"
			 data-gs-height="<?php p($widget->getOptions('size')['h']) ?>"
			 data-gs-x="<?php p($widget->getOptions('position')['x']) ?>"
			 data-gs-y="<?php p($widget->getOptions('position')['y']) ?>"
			 data-gs-auto-position>
			<div id="dashboard-widget-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>"
                 class="widget dashboard-widget widget-type-<?php p($widget->getType()); ?> dragbox"
                 data-id="<?php p($widget->getDeviceId()); ?>"
                 data-widget-type="<?php p($widget->getType()); ?>">
				<h2 class="widget-header ui-widget-header">
                    <?php p($widget->getName()); ?> -
                    <small><?php p($l->t('Widget '.$widget->getDisplayName())); ?></small>
					<?php if($widget->getType() === 'chart') { ?>
						<a href="#" id="zoom_reset" title="<?php p($l->t('Reset Chart')); ?>" class="reset opIcons">
							<span class="icon icon-reset-white"></span>
						</a>
					<?php } ?>
					<a href="#" class="widget-delete opIcons" title="<?php p($l->t('Delete Widget')); ?>">
						<span class="icon icon-delete-white"
							  data-id="widget-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>">
						</span>
					</a>
				</h2>

				<div class="section <?php p($widget->getType()); ?>">
                    <?php //var_dump($widget); ?>
                    <?php //print_unescaped($this->inc('widgets/'.$widget->widgetTemplateName(), array('widget' => $widget))); ?>
					<?php if($widget->getType() === 'last') { ?>
						<?php if($widget->getLog()) { ?>
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
						<?php } else { ?>
							<div class="center">
								<div class="icon-info"></div>
									<span class="center">No data</span>
									<p>Read
										<a href="https://github.com/alexstocker/sensorlogger/wiki"
										   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a>
									</p>
							</div>
						<?php } ?>
					<?php } else if ($widget->getType() === 'list') { ?>
						<?php if($widget->getLog()) { ?>
							<table style="width: 100%;">
								<?php foreach ($widget->getLog() as $dataLog) { ?>
									<tr ><td colspan="3" class="right"><small><?php p($dataLog->getCreatedAt()); ?></small></td></tr>

									<?php if (empty($dataLog->getData())) { ?>

										<tr>
											<td>Temperature</td>
											<td class="right"><?php p($dataLog->getTemperature()); ?></td>
											<td class="center">°C</td>
										</tr>
										<tr>
											<td>Humidity</td>
											<td class="right"><?php p($dataLog->getHumidity()); ?></td>
											<td class="center">% r.F.</td>
										</tr>

									<?php } else { ?>

										<?php foreach ($dataLog->getData() as $log) { ?>
											<tr>
												<td><?php p($log->getType()); ?></td>
												<td class="right"><?php p($log->getValue()); ?></td>
												<td class="center"> <?php p($log->getShort()); ?></td>
											</tr>
										<?php } ?>

									<?php } ?>

									<tr><td colspan="3" >--</td></tr>
								<?php } ?>
							</table>
						<?php } else { ?>
							<div class="center">
								<div class="icon-info"></div>
								<span class="center">No data</span>
								<p>Read
									<a href="https://github.com/alexstocker/sensorlogger/wiki"
									   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a>
								</p>
							</div>
						<?php } ?>
					<?php  ?>
					<?php } else if($widget->getType() === 'chart') { ?>

						<?php //var_dump($widget); ?>
						<div class="widget widget-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>">
							<?php //var_dump($widget)//$device = $_['device'] ?>
							<div id="widget-plotArea-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>" class="chartcontainer" data-id="<?php p($widget->getDeviceId()); ?>"></div>
						</div>
						<div>

						</div>
                    <?php } else if($widget->getType() === 'realTimeChart') { ?>

                        <?php //var_dump($widget); ?>
                        <div class="widget widget-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>">
                            <?php //var_dump($widget)//$device = $_['device'] ?>
                            <div id="widget-realTimePlotArea-<?php p($widget->getType()); ?>-<?php p($widget->getDeviceId()); ?>" class="chartcontainer" data-id="<?php p($widget->getDeviceId()); ?>"></div>
                        </div>
                        <div>

                        </div>
                    <?php } else { ?>
                        <?php print_unescaped($this->inc('widgets/'.$widget->widgetTemplateName(), array('widget' => $widget))); ?>
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
				<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Users#widgets"
						   title="SensorLogger Wiki Dashboard Widgets" target="_blank">SensorLogger Wiki Dashboard Widgets</a></p>
		</div>
	</div>
<?php } ?>