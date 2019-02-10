<div class="content-wrapper">
<table id="sensorDeviceGroupsTable" style="width: 100%;">
	<thead>
	<th></th>
	<th id="id"><span class="th-header">#</span></th>
	<th id="deviceGroupName"><span class="th-header">Device Group</span></th>
	<th id="assigendDevices"><span class="th-header">Assigned devices</span></th>
	<th><span class="th-header"></span></th>
	</thead>
	<tbody>
<?php foreach($_['deviceGroups'] as $deviceGroup){ ?>
	<tr data-id="<?php p($deviceGroup['id']); ?>" data-type="dir" class="deviceGroupEdit">
		<td class="td-data">
			<a href="#" class="devicegroup-delete" title="<?php p($l->t('Delete Device Group')); ?>">
				<span class="icon icon-delete"
					  data-id="<?php p($deviceGroup['id']); ?>">
				</span>
			</a>
		</td>
		<td class="td-data"><?php p($deviceGroup['id']); ?></td>
		<td class="td-data"><?php p($deviceGroup['device_group_name']); ?></td>
		<td class="td-data"></td>
		<td class="td-data">Show devices</td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['deviceGroups']) && empty($_['deviceGroups'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>No Device groups registered</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
<?php } ?>
</div>
