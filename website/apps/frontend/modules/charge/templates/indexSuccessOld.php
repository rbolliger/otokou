<?php use_helper('I18N', 'Date') ?>
<?php include_partial('charge/assets') ?>


<?php slot('leftcol'); ?>
<?php include_partial('charge/sum_amount', array('amounts' => $sumAmount)); ?>
<?php end_slot(); ?>

<div id="sf_admin_container">
    <?php slot('content_title'); ?>
    <h1><?php echo __('List of registered charges', array(), 'messages') ?></h1>
    <?php end_slot(); ?>

    <?php include_partial('charge/flashes') ?>




    <div id="filters">
        <?php if ('show' === $filters_appearance) : ?>
            <?php include_partial('charge/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
        <?php endif; ?>
    </div>



    <div id="sf_admin_actions_menu">
        <ul>
            <?php if ('show' === $filters_appearance) : ?>
                <li> <?php echo link_to('Hide filters', '@charge?filters_appearance=hidden', array('id' => "filters_button")); ?></li>
            <?php else : ?>
                <li> <?php echo link_to('Show filters', '@charge?filters_appearance=show', array('id' => "filters_button")); ?></li>
            <?php endif; ?>
            <?php include_partial('charge/list_actions', array('helper' => $helper)) ?>
            <li><?php include_partial('charge/list_header', array('pager' => $pager)) ?></li>
        </ul>
    </div>

    <div id="sf_admin_content">
        <form action="<?php echo url_for('charge_collection', array('action' => 'batch')) ?>" method="post">
            <?php include_partial('charge/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
            <ul class="sf_admin_actions">
                <?php include_partial('charge/list_batch_actions', array('helper' => $helper)) ?>
                <?php include_partial('charge/list_actions', array('helper' => $helper)) ?>
            </ul>
        </form>
    </div>

    <div id="sf_admin_footer">
        <?php include_partial('charge/list_footer', array('pager' => $pager)) ?>
    </div>
</div>
