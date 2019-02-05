<ul id="navigation-list">
	<?php $navItems = $_['navItems']; ?>
	<?php if(is_array($navItems) && !empty($navItems)) { ?>
		<?php foreach($navItems as $navItem) { ?>
			<li id="<?php p($navItem['id']); ?>"><a class="menuItem" href="<?php p($navItem['href']); ?>"><?php p($l->t($navItem['name'])); ?></a></li>
		<?php } ?>
	<?php } ?>
</ul>