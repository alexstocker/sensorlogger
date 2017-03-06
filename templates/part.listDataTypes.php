<table id="sensorDeviceTypesTable" style="width: 100%;">
	<thead>
	<th id="id"><span class="th-header">#</span></th>
	<th id="descprition"><span class="th-header">Description</span></th>
	<th id="type"><span class="th-header">Data Type</span></th>
	<th id="short"><span class="th-header">Unit</span></th>
	<th><span class="th-header">Assigned Devices</span></th>
	<th><span class="th-header"></th>
	</thead>
	<tbody>
<?php foreach($_['dataTypes'] as $dataType){ ?>
	<tr>
		<td class="td-data"><?php p($dataType['id']); ?></td>
		<td class="td-data"><?php p($dataType['description']); ?></td>
		<td class="td-data"><?php p($dataType['type']); ?></td>
		<td class="td-data"><?php p($dataType['short']); ?></td>
		<td class="td-data"></td>
		<td class="td-data">Show devices</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>