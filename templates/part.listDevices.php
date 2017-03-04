<table id="sensorDevicesTable" style="width: 100%;">
	<thead>
	<th id="headerId">#</th>
	<th id="headerUuid">UUID</th>
	<th id="headerName">Name</th>
	<th id="headerType">Type</th>
	<th id="headerGroup">Group</th>
	<th></th>
	</thead>
	<tbody id="deviceList">
<?php foreach($_['devices'] as $device){ ?>
	<tr data-id="<?php p($device['id']); ?>" data-type="dir" class="deviceEdit">
		<td><?php p($device['id']); ?></td>
		<td><?php p($device['uuid']); ?></td>
		<td><?php p($device['name']); ?></td>
		<td><?php p($device['type']); ?></td>
		<td><?php p($device['group']); ?></td>
		<td><button class="deviceChart" data-id="<?php p($device['id']); ?>">Chart</button><button class="deviceListData" data-id="<?php p($device['id']); ?>">Data</button></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>

