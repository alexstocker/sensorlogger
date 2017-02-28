<div id="app-content">
	<div id="app-content-wrapper">
<?php foreach($_['logs'] as $log){ ?>
<h1><?php p($log['temperature']); ?>°C</h1>
<h4><?php p($log['humidity']); ?>% r.F.</h4>
<h6><?php p($log['created_at']); ?> UTC</h6>
<?php } ?>
		</div>
	</div>
