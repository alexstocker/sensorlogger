<div class="content-wrapper">
<table id="sensorDeviceTypesTable" style="width: 100%;">
	<thead>
		<th></th>
		<th id="id"><span class="th-header">#</span></th>
		<th id="deviceTypeName"><span class="th-header">Type Name</span></th>
		<th id="name"><span class="th-header">Assigned devices</span></th>
		<th><span class="th-header"></span></th>
	</thead>
	<tbody>
<?php foreach($_['deviceTypes'] as $deviceType){ ?>
	<tr data-id="<?php p($deviceType['id']); ?>" data-type="dir" class="deviceTypeEdit">
        <td class="td-data">
            <a href="#" class="devicetype-delete" title="<?php p($l->t('Delete Device Type')); ?>">
				<span class="icon icon-delete"
                      data-id="<?php p($deviceType['id']); ?>">
				</span>
            </a>
        </td>
		<td class="td-data"><?php p($deviceType['id']); ?></td>
		<td class="td-data"><?php p($deviceType['device_type_name']); ?></td>
		<td class="td-data"><?php p($deviceType['uuid']); ?></td>
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
</div>
