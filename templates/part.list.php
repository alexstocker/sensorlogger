<div class="content-wrapper">
<table id="sensorLogsTable" style="width: 100%;">
	<thead>
	<th></th>
	<th id="id"><span class="th-header">#</span></th>
	<th id="id"><span class="th-header">UUID</span></th>
	<th id="temperature"><span class="th-header">°C</span></th>
	<th id="humidity"><span class="th-header">% r.F.</span></th>
	<th id="created_at"><span class="th-header">Created</span></th>
	</thead>
	<tbody id ="logList">
<?php foreach($_['logs'] as $log){ ?>
	<?php $valueString = ''; ?>

	<tr data-id="log-row<?php p($log->getId()); ?>" data-type="dir" class="logEdit">
		<td class="td-data">
			<a href="#" class="log-delete" title="<?php p($l->t('Delete Record')); ?>">
				<span class="icon icon-delete"
					  data-id="<?php p($log->getId()); ?>">
				</span>
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
		<h2>No Sensor data</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Users#list"
				   title="SensorLogger Wiki Log List" target="_blank">SensorLogger Wiki Log List</a></p>
	</div>
<?php } ?>
