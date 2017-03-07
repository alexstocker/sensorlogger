<table id="sensorDeviceTypesTable" style="width: 100%;">
	<thead>
		<th id="id"><span class="th-header">#</span></th>
		<th id="deviceTypeName"><span class="th-header">Type Name</span></th>
		<th id="name"><span class="th-header">Assigned devices</span></th>
		<th><span class="th-header"></span></th>
	</thead>
	<tbody>
<?php foreach($_['deviceTypes'] as $deviceType){ ?>
	<tr>
		<td class="td-data"><?php p($deviceType['id']); ?></td>
		<td class="td-data"><?php p($deviceType['device_type_name']); ?></td>
		<td class="td-data"></td>
		<td class="td-data">Show devices</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['deviceTypes']) && empty($_['deviceTypes'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>No Device types registered</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
<?php } ?>