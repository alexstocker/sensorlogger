<table id="sensorDeviceTypesTable" style="width: 100%;">
	<thead>
	<th id="id">#</th>
	<th id="descprition">Description</th>
	<th id="type">Data Type</th>
	<th id="short">Unit</th>
	<th>Assigned Devices</th>
	<th></th>
	</thead>
	<tbody>
<?php foreach($_['dataTypes'] as $dataType){ ?>
	<tr>
		<td><?php p($dataType['id']); ?></td>
		<td><?php p($dataType['description']); ?></td>
		<td><?php p($dataType['type']); ?></td>
		<td><?php p($dataType['short']); ?></td>
		<td></td>
		<td>Show devices</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>