<div class="content-wrapper">
<table id="sensorDataTypesTable" style="width: 100%;">
	<thead>
	<tr>
		<th scope="col"><span class="th-header"><?php p($l->t('Actions')); ?></span></th>
		<th scope="col"><span class="th-header">#</span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Description')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Data type')); ?></span></th>
		<th scope="col"><span class="th-header"><?php p($l->t('Unit')); ?></span></th>
	</tr>
	</thead>
	<tbody>
<?php foreach($_['dataTypes'] as $dataType){ ?>
	<tr data-id="<?php p($dataType['id']); ?>" data-type="dir" class="dataTypeEdit">
        <td class="td-data">
        </td>
		<td class="td-data"><?php p($dataType['id']); ?></td>
		<td class="td-data"><?php p($dataType['description']); ?></td>
		<td class="td-data"><?php p($dataType['type']); ?></td>
		<td class="td-data"><?php p($dataType['short']); ?></td>
	</tr>
<?php } ?>
	</tbody>
	<tfoot></tfoot>
</table>
<?php if(is_array($_['dataTypes']) && empty($_['dataTypes'])) { ?>
	<div id="emptycontent" class="">
		<div class="icon-info"></div>
		<h2><?php p($l->t('No data types registered')); ?></h2>
		<p>Read <a href="https://github.com/alexstocker/sensorlogger/wiki/Users#data-types"
				   title="SensorLogger Wiki" target="_blank">SensorLogger Wiki DataTypes</a></p>
	</div>
<?php } ?>
</div>
