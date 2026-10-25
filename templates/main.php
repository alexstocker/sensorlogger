<?php
script('sensorlogger', [
	'app',
	'script',
	'jquery.poshytip.min',
	'jquery-editable-poshytip.min',
	'jquery.jqplot.min',
	'plugins/jqplot.cursor.min',
	'plugins/jqplot.dateAxisRenderer.min',
	'plugins/jqplot.canvasTextRenderer.min',
	'plugins/jqplot.canvasAxisTickRenderer.min',
	'plugins/jqplot.highlighter.min',
	'plugins/jqplot.enhancedLegendRenderer.min',
]);
style('sensorlogger', 'style');
style('sensorlogger', 'jquery-editable');

?>

	<div id="app-navigation" role="navigation" aria-label="<?php p($l->t('SensorLogger')); ?>">
		<?php print_unescaped($this->inc('part.navigation')); ?>
		<?php print_unescaped($this->inc('part.settings')); ?>
	</div>

	<div id="app-content">
		<main id="app-content-wrapper">
			<?php print_unescaped($this->inc('part.'.$_['part'])); ?>
		</main>
	</div>
	<aside id="app-sidebar" aria-label="<?php p($l->t('Details')); ?>" aria-hidden="true" style="display: none;">
		<div class="content-wrapper">
			<div class="detailDeviceInfoContainer">
				<div class="mainDeviceInfoView">

					<h2 class="title"></h2>
					<div class="body">
						<div class="tpl_bodyDetails"></div>
					</div>


				</div>
				<div class="systemTagsInfoView"></div>
			</div>
			<div class="footer" role="group" aria-label="<?php p($l->t('Actions')); ?>">
				<button type="button" id="save-btn" class="icon-save" aria-label="<?php p($l->t('Save')); ?>" style="display:none;">
					<span class="icon icon-save" aria-hidden="true"></span>
				</button>
				<button type="button" id="wipeout-btn" class="icon-wipeout" aria-label="<?php p($l->t('Wipe out device')); ?>" style="display:none;">
					<span class="has-tooltip icon icon-wipeout" aria-hidden="true"></span>
				</button>
				<button type="button" class="icon-delete handler" aria-label="<?php p($l->t('Delete')); ?>" style="display:none;">
					<span class="icon icon-delete" aria-hidden="true"></span>
				</button>
				<button type="button" class="icon-close handler close-all" aria-label="<?php p($l->t('Close')); ?>">
					<span class="icon icon-close" aria-hidden="true"></span>
				</button>
			</div>

		</div>
	</aside>
