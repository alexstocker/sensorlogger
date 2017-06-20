<table id="sensorDevicesTable" style="width: 100%;">
	<thead>
	<th id="headerId"><span class="th-header">#</span></th>
	<th id="headerUuid"><span class="th-header">UUID</span></th>
	<th id="headerName"><span class="th-header">Name</span></th>
	<th id="headerType"><span class="th-header">Type</span></th>
	<th id="headerGroup"><span class="th-header">Group</span></th>
	<th></th>
	</thead>
	<tbody id="deviceList">
<?php foreach($_['devices'] as $device){ ?>
	<tr data-id="<?php p($device->getId()); ?>" data-type="dir" class="deviceEdit">
		<td class="td-data"><?php p($device->getId()); ?></td>
		<td class="td-data"><?php p($device->getUuid()); ?></td>
		<td class="td-data"><?php p($device->getName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceTypeName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceGroupName()); ?></td>
		<td class="td-data"><button class="deviceChart" data-id="<?php p($device->getId()); ?>">Chart</button><button class="deviceListData" data-id="<?php p($device->getId()); ?>">Data</button></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['devices']) && empty($_['devices'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>No Devices registered</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
<?php } ?>

