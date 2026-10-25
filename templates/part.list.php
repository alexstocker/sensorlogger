<div class="content-wrapper">
<table id="sensorLogsTable" style="width: 100%;">
	<thead>
	<tr>
		<th scope="col"><span class="th-header"><?php p($l->t('Actions')); ?></span></th>
		<th scope="col"><span class="th-header">#</span></th>
		<th scope="col"><span class="th-header">UUID</span></th>
		<th scope="col"><span class="th-header">°C</span></th>
		<th scope="col"><span class="th-header">% r.F.</span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Created')); ?></span></th>
	</tr>
	</thead>
	<tbody id="logList">
<?php foreach($_['logs'] as $log){ ?>
	<?php $valueString = ''; ?>

	<tr data-id="log-row<?php p($log->getId()); ?>" data-type="dir" class="logEdit">
		<td class="td-data">
			<a href="#" class="log-delete" title="<?php p($l->t('Delete Record')); ?>">
				<span class="icon icon-delete"
					  aria-hidden="true"
					  data-id="<?php p($log->getId()); ?>">
				</span>
				<span class="hidden-visually"><?php p($l->t('Delete Record')); ?></span>
			</a>
		</td>
		<td class="td-data"><?php p($log->getId()); ?></td>
		<td class="td-data"><?php p($log->getDeviceUuid()); ?></td>
		<?php if(sizeof($log->getData()) > 0) { ?>
			<?php foreach($log->getData() as $lg) { ?>
				<?php $valueString .= $lg->getValue().' '; ?>
			<?php } ?>
			<td class="td-data" colspan="2">Multi Value Sensor <small>(<?php p($valueString); ?>)</small></td>
		<?php } else { ?>
		<td class="td-data"><?php p($log->getTemperature()); ?></td>
		<td class="td-data"><?php p($log->getHumidity()); ?></td>
		<?php } ?>
		<td class="td-data"><?php p($log->getCreatedAt()); ?></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['logs']) && empty($_['logs'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2><?php p($l->t('No sensor data')); ?></h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Users#list"
				   title="SensorLogger Wiki Log List" target="_blank">SensorLogger Wiki Log List</a></p>
	</div>
<?php } ?>
