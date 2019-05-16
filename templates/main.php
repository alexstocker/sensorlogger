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
		<div class="content-wrapper">
			<div class="sidebarInfoContainer">
				<div class="sidbarInfoView">
					<div class="title"></div>
					<div class="body">
						<div class="tpl_bodyDetails"></div>
					</div>
				</div>
			</div>
			<div class="sidebarTabsContainer">
				<!--
				<ul class="tabHeaders">
					<li class="tabHeader selected" data-tabid="shareTabView" data-tabindex="0">
						<a href="#">Share</a>
					</li>
					<li class="tabHeader" data-tabid="activityTabView" data-tabindex="1">
						<a href="#">Activities</a>
					</li>
					<li class="tabHeader" data-tabid="notificationsTabView" data-tabindex="1">
						<a href="#">Notifications</a>
					</li>
				</ul>
				<div id="tabsContainer">
					<div id="shareTabView">
						<div class="dialogContainer"></div>
					</div>
				</div>
				-->
			</div>
			<div class="sidebarFooterContainer footer">
				<a id="save-btn" class="icon-save" style="display:none;">
					<span class="icon icon-save" ></span>
				</a>
                <a id="wipeout-btn" class="icon-wipeout" style="display:none;">
                    <span class="has-tooltip icon icon-wipeout"></span>
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
