<div id="app-navigation">
<ul>
	<li id="showDashboard"><a class="menuItem" href="#showDashboard">Dashboard</a></li>
	<li id="showList"><a class="menuItem" href="#showList">List</a></li>
	<li id="deviceList"><a class="menuItem" href="#deviceList">Devices</a></li>
	<li id="deviceTypeList"><a class="menuItem" href="#deviceTypeList">Device Types</a></li>
	<li id="deviceGroupList"><a class="menuItem" href="#deviceGroupList">Device Groups</a></li>
	<li id="dataTypeList"><a class="menuItem" href="#deviceTypeList">Data Types</a></li>
</ul>
	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button" data-apps-slide-toggle="#app-settings-content">
				<?php p($l->t('Settings'));?>
			</button>
		</div>
		<div id="app-settings-content">
			<label for="apikey"><?php p($l->t('API Key'));?></label>
			<input id="apikey" type="text" readonly="readonly" value="<?php p($_['config']['apiKey']);?>" />
			<label for="sharedSecret"><?php p($l->t('Shared secret'));?></label>
			<input id="sharedsecret" type="text" readonly="readonly" value="<?php p($_['config']['sharedSecret']);?>" />
			<em><?php print_unescaped($l->t('Use this address to <a href="%s" target="_blank" rel="noreferrer">access your Files via WebDAV</a>', array(link_to_docs('user-webdav'))));?></em>
		</div>
	</div>
</div>