<?php use_helper('I18N', 'Date') ?>
<?php include_partial('charge/assets') ?>


<?php slot('leftcol'); ?>
<?php include_partial('charge/sum_amount', array('amounts' => $sumAmount)); ?>


<?php end_slot(); ?>

<div id="sf_admin_container">
    <h1><?php echo __('List of registered charges', array(), 'messages') ?></h1>

    <?php include_partial('charge/flashes') ?>


    <div id="sf_admin_bar">
        <a href="#" id="button"> &gt; Filter results</a>
        <div id="filters">
            <?php include_partial('charge/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
        </div>
    </div>


    <div id="sf_admin_header">
        <?php include_partial('charge/list_header', array('pager' => $pager)) ?>
    </div>

    <div id="sf_admin_content">
        <ul class="sf_admin_actions">
            <?php include_partial('charge/list_batch_actions', array('helper' => $helper)) ?>
            <?php include_partial('charge/list_actions', array('helper' => $helper)) ?>
        </ul>
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
