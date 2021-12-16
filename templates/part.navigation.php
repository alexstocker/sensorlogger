<ul id="navigation-list">
	<?php $navItems = $_['navItems']; ?>
	<?php if(is_array($navItems) && !empty($navItems)) { ?>
		<?php foreach($navItems as $navItem) { ?>
			<li id="<?php p($navItem['id']); ?>">
				<a class="menuItem <?php p($navItem['active']?'active':'')?>"
				   href="<?php p($navItem['href']); ?>">
					<?php p($l->t($navItem['name'])); ?></a>
				<?php if($navItem['id'] === 'showDashboard' && $navItem['active']) { ?>
					<ul id="dashboard-actionbar">
						<li class="actions">
							<a href="#" class="button add-widget" data-original-title="" title="">
								<span class="icon icon-add"></span>
								<span class="hidden-visually">Neu</span>
							</a>
						</li>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
	<?php } ?>
</ul>