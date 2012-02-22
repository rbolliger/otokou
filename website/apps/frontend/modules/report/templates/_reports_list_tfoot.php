<tfoot>
    <tr>
        <th colspan="6">
            <?php if ($pager->haveToPaginate()): ?>
                <?php include_partial('report/pagination', array('pager' => $pager, 'url' => $url)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
                <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
        </th>
    </tr>
</tfoot>
