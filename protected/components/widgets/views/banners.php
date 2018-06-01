<div class="banner">
    <?php $this->entity; ?>
    <ul>
    <?php foreach($list as $banner) : ?>
        <li>
            <a href="<?=$banner['url']; ?>"><img src="<?=$banner['image']; ?>" /></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>