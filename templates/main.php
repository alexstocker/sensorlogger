<?php

script('sensorlogger', array(
	'script',
	'jquery.poshytip.min',
	'jquery-editable-poshytip.min',
	'jquery.jqplot.min',
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

\OC_Util::addVendorScript('select2/select2');
\OC_Util::addVendorStyle('select2/select2');
\OCP\Util::addScript('select2-toggleselect');
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
	<div id="app-sidebar" data-type="" data-id="" ng-class="" style="display: none;">
		<div class="content-wrapper">
			<div ng-controller="DetailsController" ng-click="" class="handler ng-scope">
				<div ng-show="" ng-class="" class="disabled">
					<div class="title" ng-class="{'editing':route.parameter=='name'}">
						<span class="handler editable"></span>
						</div>
					<div class="body" watch-top="" ng-style="{top:divTop}" style="top: 30px;">
						<div class="tpl_bodyDetails"></div>
					</div>
					<div class="footer">
						<a id="save-btn" class="icon-save" style="display:none;">
							<span class="icon icon-save" ></span>
						</a>
						<a class="icon-delete handler" style="display:none;">
							<span class="icon icon-delete"></span>
						</a>
						<a class="icon-close handler close-all">
							<span class="icon icon-close"></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
