<div class="content-wrapper">
<table id="sensorDevicesTable" style="width: 100%;">
	<thead>
	<tr>
		<th scope="col"><span class="th-header"><?php p($l->t('Actions')); ?></span></th>
		<th scope="col"><span class="th-header">#</span></th>
		<th scope="col"><span class="th-header">UUID</span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Name')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Type')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Group')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Actions')); ?></span></th>
	</tr>
	</thead>
	<tbody id="deviceList">
<?php foreach($_['devices'] as $device){ ?>
	<tr data-id="<?php p($device->getId()); ?>" data-type="dir" class="deviceEdit">
        <td class="td-data">
            <a href="#"
               class="deviceChart device-datalist"
               data-id="<?php p($device->getId()); ?>"
               aria-label="<?php p($l->t('Show Device Chart for %s', [$device->getUuid()])); ?>">
                <span class="has-tooltip icon icon-datachart"
                      aria-hidden="true"
                      data-original-title="<?php p($l->t('Show Device Chart for %s',[$device->getUuid()])); ?>"></span>
            </a>
            <a href="#"
               class="deviceListData device-datalist"
               data-id="<?php p($device->getId()); ?>"
               aria-label="<?php p($l->t('Show Device Logs for %s', [$device->getUuid()])); ?>">
                <span class="has-tooltip icon icon-datalist"
                      aria-hidden="true"
                      data-original-title="<?php p($l->t('Show Device Logs for %s',[$device->getUuid()])); ?>"></span>
            </a>
            <span class="deviceactions">
                <a class="has-tooltip action action-share permanent"
                   href="#"
                   data-action="Share"
                   data-original-title="<?php p($l->t('Share Device %s',[$device->getUuid()])); ?>"
                   title="">
                    <span class="icon icon-share" aria-hidden="true"></span>
                    <span class="hidden-visually">Share</span></a>
                <a class="has-tooltip action action-menu permanent"
                   href="#"
                   data-action="menu"
                   data-original-title="<?php p($l->t('Edit Device %s',[$device->getUuid()])); ?>"
                   title="">
                    <span class="icon icon-more" aria-hidden="true"></span>
                    <span class="hidden-visually">Actions</span></a>
            </span>
        </td>
		<td class="td-data"><?php p($device->getId()); ?></td>
		<td class="td-data"><?php p($device->getUuid()); ?></td>
		<td class="td-data"><?php p($device->getName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceTypeName()); ?></td>
		<td class="td-data"><?php p($device->getDeviceGroupName()); ?></td>
        <td>
        </td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['devices']) && empty($_['devices'])) { ?>
    <div id="emptycontent" class="">
        <div class="icon-info"></div>
        <h2><?php p($l->t('No devices registered')); ?></h2>
        <p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Users#devices"
                   title="SensorLogger Wiki Log List" target="_blank">SensorLogger Wiki Devices</a></p>
    </div>
<?php } ?>
</div>
