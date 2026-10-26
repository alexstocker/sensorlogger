<?php
script('sensorlogger', 'sensorlogger');
style('sensorlogger', 'style');
style('sensorlogger', 'sensorlogger');
?>
<div
	id="sensorlogger-app"
	data-navigation="<?php p(json_encode($_['navItems'])); ?>"
	data-initial-view="<?php p($_['initialView']); ?>"
	data-initial-id="<?php p($_['initialId'] ?? ''); ?>"
	data-active-navigation="<?php p($_['activeNavigation']); ?>">
</div>
