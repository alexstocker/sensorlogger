<table id="sensorLogsTable" style="width: 100%;">
	<thead>
	<th id="id"><span class="th-header">#</span></th>
	<th id="id"><span class="th-header">UUID</span></th>
	<th id="temperature"><span class="th-header">°C</span></th>
	<th id="humidity"><span class="th-header">% r.F.</span></th>
	<th id="created_at"><span class="th-header">Created</span></th>
	</thead>
	<tbody id ="logList">
<?php foreach($_['logs'] as $log){ ?>
	<tr>
		<td class="td-data"><?php p($log['id']); ?></td>
		<td class="td-data"><?php p($log['device_uuid']); ?></td>
		<td class="td-data"><?php p($log['temperature']); ?></td>
		<td class="td-data"><?php p($log['humidity']); ?></td>
		<td class="td-data"><?php p($log['created_at']); ?></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['logs']) && empty($_['logs'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>No Data</h2>
		<p>Sensorendaten werden hier angezeigt</p>
	</div>
<?php } ?>
