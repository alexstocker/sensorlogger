<div id="app-content">
	<div id="app-content-wrapper">
		<h3>Chart for Device #<?php p($_['device']['uuid']); ?></h3>
		<?php if(is_array($_['logs']) && empty($_['logs'])) { ?>
			<div id="emptycontent" class="">
				<div class="icon-info"></div>
				<h2>No Data</h2>
				<p>Sensorendaten werden hier angezeigt</p>
			</div>
		<?php } ?>
		<div id="chart" data-id="<?php p($_['device']['id']); ?>"></div>
	</div>
</div>