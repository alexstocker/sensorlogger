<div class="content-wrapper">
<table id="sensorDevicesTable" style="width: 100%;">
	<thead>
    <th></th>
	<th id="headerId"><span class="th-header">#</span></th>
	<th id="headerUuid"><span class="th-header">UUID</span></th>
	<th id="headerName"><span class="th-header">Name</span></th>
	<th id="headerType"><span class="th-header">Type</span></th>
	<th id="headerGroup"><span class="th-header">Group</span></th>
	</thead>
	<tbody id="deviceList">
<?php foreach($_['devices'] as $device){ ?>
	<tr data-id="<?php p($device->getId()); ?>" data-type="dir" class="deviceEdit">
        <td class="td-data">
            <a href="#" class="deviceChart device-datalist" data-id="<?php p($device->getId()); ?>" title="<?php p($l->t('Device Chart Data')); ?>">
                <span class="icon icon-datachart"></span>
            </a>
            <a href="#" class="deviceListData device-datalist" data-id="<?php p($device->getId()); ?>" title="<?php p($l->t('Device List Data')); ?>">
                <span class="icon icon-datalist"></span>
            </a>
            <a href="#" class="device-delete" title="<?php p($l->t('Delete Device')); ?>">
				<span class="icon icon-delete"
                      data-id="<?php p($device->getId()); ?>">
				</span>
            </a>
        </td>
		<td class="td-data"><?php p($device->getId()); ?></td>
		<td class="td-data"><?php p($device->getUuid()); ?></td>
		<td class="td-data"><?php p($device->getName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceTypeName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceGroupName()); ?></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['devices']) && empty($_['devices'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2>No Devices registered</h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Configuration#devices"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a></p>
	</div>
<?php } ?>
</div>
