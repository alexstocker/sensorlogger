<?php
script('sensorlogger', array(
	'script',
	'jquery.jqplot.min',
	'plugins/jqplot.dateAxisRenderer.min',
	'plugins/jqplot.canvasTextRenderer.min',
	'plugins/jqplot.canvasAxisTickRenderer.min',
	'plugins/jqplot.highlighter.min'
));
style('sensorlogger', 'style');
?>
<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('part.navigation')); ?>
		<?php print_unescaped($this->inc('part.settings')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('part.dashboard')); ?>
		</div>
	</div>
</div>
