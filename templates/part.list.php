<div id="app-content">
	<div id="app-content-wrapper">
<table id="sensorLogsTable" style="width: 100%;">
	<thead>
	<th id="id">#</th>
	<th id="id">UUID</th>
	<th id="temperature">°C</th>
	<th id="humidity">% r.F.</th>
	<th id="created_at">Created</th>
	</thead>
	<tbody>
<?php foreach($_['logs'] as $log){ ?>
	<tr>
		<td><?php p($log['id']); ?></td>
		<td><?php p($log['device_uuid']); ?></td>
		<td><?php p($log['temperature']); ?></td>
		<td><?php p($log['humidity']); ?></td>
		<td><?php p($log['created_at']); ?></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
		</div>
	</div>
