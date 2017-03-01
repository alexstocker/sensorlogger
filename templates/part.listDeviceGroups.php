<table id="sensorDeviceGroupsTable" style="width: 100%;">
	<thead>
	<th id="id">#</th>
	<th id="deviceGroupName">Device Group</th>
	<th id="assigendDevices">Assigned devices</th>
	<th></th>
	</thead>
	<tbody>
<?php foreach($_['deviceGroups'] as $deviceGroup){ ?>
	<tr>
		<td><?php p($deviceGroup['id']); ?></td>
		<td><?php p($deviceGroup['device_group_name']); ?></td>
		<td></td>
		<td>Show devices</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
