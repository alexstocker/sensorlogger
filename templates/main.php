<?php
script('sensorlogger', array(
	'script',
    'app',
	'jquery.poshytip.min',
	'jquery-editable-poshytip.min',
	'jquery.jqplot.min',
	'plugins/jqplot.cursor.min',
	'plugins/jqplot.dateAxisRenderer.min',
	'plugins/jqplot.canvasTextRenderer.min',
	'plugins/jqplot.canvasAxisTickRenderer.min',
	'plugins/jqplot.highlighter.min',
	'plugins/jqplot.enhancedLegendRenderer.min'
));
style('sensorlogger', 'style');
style('sensorlogger', 'jquery-editable');

# TODO [GH11] Rebuild JS
# Rewrite JS and split it into smaller files

//\OC_Util::addScript('sensorlogger','app');
//\OC_Util::addScript('sensorlogger','navigation');
//\OC_Util::addScript('sensorlogger','deviceactions');
vendor_style('select2/select2');
vendor_script('select2/select2');


?>
<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('part.navigation')); ?>
		<?php print_unescaped($this->inc('part.settings')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('part.'.$_['part'])); ?>
		</div>
	</div>
	<div id="app-sidebar" data-type="" data-id="" ng-class="" style="display: none;">

	</div>
</div>
