<?php use_stylesheets_for_form($filters) ?>
<?php use_javascripts_for_form($filters) ?>

<div class="graphs_filters">
<?php if ($filters->hasGlobalErrors()): ?>
    <?php echo $filters->renderGlobalErrors() ?>
<?php endif; ?>

<?php echo $filters->renderFormTag(url_for('@graph_filter')) ?>
<table>
    <?php echo $filters ?>
    <tr>
        <td colspan="2">
            <?php echo link_to('Reset', '@graph_index', array(), array('query_string' => '_reset', 'method' => 'post')) ?>
            <input type="submit" value="Filter" />
        </td>
    </tr>
</table>
</form>

</div>
