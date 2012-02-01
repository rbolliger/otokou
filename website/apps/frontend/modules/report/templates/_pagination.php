<?php $nb_links = sfConfig::get('app_list_nb_links'); ?>

<div class="sf_admin_pagination">
    
 <?php if ($pager->getPage() > 1) : ?>
        <a href="<?php echo url_for($url) ?>?page=<?php echo $pager->getPreviousPage() ?>">
            &lt;&lt; Previous 
        </a>  
        <span class="separator">|</span>
 <?php endif; ?>   
 
 <?php if ($pager->getPage() > 1 + floor($nb_links / 2)) : ?>
        <a href="<?php echo url_for($url) ?>?page=1">
            1 
        </a> 
    <?php endif; ?>

    <?php if ($pager->getPage() > 2 + floor($nb_links / 2)) : ?>
        <span class="dots"> ...  </span>
    <?php endif; ?>


    <?php foreach ($pager->getLinks($nb_links) as $page): ?>
        <?php if ($page == $pager->getPage()): ?>
            <span class="thepage"><?php echo $page ?></span> 
        <?php else: ?>
            <a href="<?php echo url_for($url) ?>?page=<?php echo $page ?>"><?php echo $page ?></a>
        <?php endif; ?>

    <?php endforeach; ?>

    <?php if ($pager->getPage() < $pager->getLastPage() - floor($nb_links / 2) - 1) : ?>
        <span class="dots"> ...  </span>
    <?php endif; ?>

    <?php if ($pager->getPage() < $pager->getLastPage() - floor($nb_links / 2)) : ?>
        <a href="<?php echo url_for($url) ?>?page=<?php echo $pager->getLastPage() ?>">
            <?php echo $pager->getLastPage() ?>
        </a>
    <?php endif; ?>

    <?php if ($pager->getPage() < $pager->getLastPage()) : ?>
        <span class="separator">|</span>
        <a href="<?php echo url_for($url) ?>?page=<?php echo $pager->getNextPage() ?>">
            Next &gt;&gt;
        </a>
    <?php endif; ?>
 
</div>
