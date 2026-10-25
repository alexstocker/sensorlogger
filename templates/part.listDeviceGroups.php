<div class="content-wrapper">
<table id="sensorDeviceGroupsTable" style="width: 100%;">
	<thead>
	<tr>
		<th scope="col"><span class="th-header"><?php p($l->t('Actions')); ?></span></th>
		<th scope="col"><span class="th-header">#</span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Device group')); ?></span></th>
	</tr>
	</thead>
	<tbody>
<?php foreach($_['deviceGroups'] as $deviceGroup){ ?>
	<tr data-id="<?php p($deviceGroup['id']); ?>" data-type="dir" class="deviceGroupEdit">
		<td class="td-data">
			<a href="#" class="devicegroup-delete" title="<?php p($l->t('Delete Device Group')); ?>">
				<span class="icon icon-delete"
					  aria-hidden="true"
					  data-id="<?php p($deviceGroup['id']); ?>">
				</span>
				<span class="hidden-visually"><?php p($l->t('Delete Device Group')); ?></span>
			</a>
		</td>
		<td class="td-data"><?php p($deviceGroup['id']); ?></td>
		<td class="td-data"><?php p($deviceGroup['device_group_name']); ?></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['deviceGroups']) && empty($_['deviceGroups'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2><?php p($l->t('No device groups registered')); ?></h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Users#device-groups"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki DeviceGroups</a></p>
	</div>
<?php } ?>
</div>
