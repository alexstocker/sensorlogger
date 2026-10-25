<table id="sensorDevicesTable" style="width: 100%;">
	<thead>
	<tr>
		<th scope="col"><span class="th-header">#</span></th>
		<th scope="col"><span class="th-header">UUID</span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Name')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Type')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Group')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Actions')); ?></span></th>
	</tr>
	</thead>
	<tbody id="deviceList">
<?php foreach($_['devices'] as $device){ ?>
	<tr data-id="<?php p($device->getId()); ?>" data-type="dir" class="deviceEdit">
		<td class="td-data"><?php p($device->getId()); ?></td>
		<td class="td-data"><?php p($device->getUuid()); ?></td>
		<td class="td-data"><?php p($device->getName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceTypeName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceGroupName()); ?></td>
		<td class="td-data">
			<button type="button" class="deviceChart" data-id="<?php p($device->getId()); ?>"><?php p($l->t('Chart')); ?></button>
			<button type="button" class="deviceListData" data-id="<?php p($device->getId()); ?>"><?php p($l->t('Data')); ?></button>
		</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['devices']) && empty($_['devices'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>not implemented yet</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
<?php } ?>
