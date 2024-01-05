<div id="controls">
    <div class="actions creatable">
        <a href="#" class="button new" data-original-title="" title="">
            <span class="icon icon-add"></span> Widget
            <span class="hidden-visually">Neu</span>
        </a>
    </div>
</div>

<?php $navItems = $_['navItems']; ?>
<ul id="navigation-list">
	<?php if(is_array($navItems) && !empty($navItems)) { ?>
		<?php foreach($navItems as $navItem) { ?>
			<li id="<?php p($navItem['id']); ?>"><a class="menuItem" href="<?php p($navItem['href']); ?>"><?php p($l->t($navItem['name'])); ?></a></li>
		<?php } ?>
	<?php } ?>
</ul>