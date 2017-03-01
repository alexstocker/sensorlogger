<table id="sensorDeviceTypesTable" style="width: 100%;">
	<thead>
	<th id="id">#</th>
	<th id="deviceTypeName">Type Name</th>
	<th id="name">Assigned devices</th>
	<th></th>
	</thead>
	<tbody>
<?php foreach($_['deviceTypes'] as $deviceType){ ?>
	<tr>
		<td><?php p($deviceType['id']); ?></td>
		<td><?php p($deviceType['device_typ_name']); ?></td>
		<td></td>
		<td>Show devices</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>