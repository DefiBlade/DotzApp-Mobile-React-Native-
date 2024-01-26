<?php $pager->setSurroundCount(2) ?>
<ul class="pagination mb-sm-0">
    <?php if ($pager->hasPreviousPage()) : ?>
    <li class="page-item">
        <a href="<?= $pager->getPreviousPage() ?>" class="page-link"><i class="mdi mdi-chevron-left"></i></a>
    </li>
    <?php endif ?>

    <?php foreach ($pager->links() as $link) : ?>
        <li <?= $link['active'] ? 'class="page-item active"' : 'class="page-item"' ?>>
            <a href="<?= $link['uri'] ?>" class="page-link">
                <?= $link['title'] ?>
            </a>
        </li>
    <?php endforeach ?>
    
    <?php if ($pager->hasNextPage()) : ?>
    <li class="page-item">
        <a href="<?= $pager->getNextPage() ?>" class="page-link"><i class="mdi mdi-chevron-right"></i></a>
    </li>
    <?php endif ?>
</ul>