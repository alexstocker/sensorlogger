<div id="controls">
    <div class="actions creatable">
        <button type="button" class="button new">
            <span class="icon icon-add" aria-hidden="true"></span>
            <?php p($l->t('Widget')); ?>
        </button>
    </div>
</div>

<?php $navItems = $_['navItems']; ?>
<ul id="navigation-list">
	<?php if(is_array($navItems) && !empty($navItems)) { ?>
		<?php foreach($navItems as $navItem) { ?>
			<li id="<?php p($navItem['id']); ?>"<?php if (isset($_['activeNavigation']) && $_['activeNavigation'] === $navItem['id']) { ?> class="active"<?php } ?>><a class="menuItem<?php if (isset($_['activeNavigation']) && $_['activeNavigation'] === $navItem['id']) { ?> active<?php } ?>" href="<?php p($navItem['href']); ?>"><?php p($l->t($navItem['name'])); ?></a></li>
		<?php } ?>
	<?php } ?>
</ul>