<?php use_stylesheets_for_form($filters) ?>
<?php use_javascripts_for_form($filters) ?>


        
            <?php if ('show' === $filters_visibility) : ?>


        <div class="sf_admin_filter">
            <?php if ($filters->hasGlobalErrors()): ?>
                <?php echo $filters->renderGlobalErrors() ?>
            <?php endif; ?>

            <?php echo $filters->renderFormTag(url_for('@chart_filter')) ?>
            <table>
                <tbody>
                    <?php echo $filters ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?php echo link_to( 'Reset', 'chart_filter', array( ), array('query_string' => '_reset', 'method' => 'post')) ?>
                            <input type="submit" value="Filter" />
                        </td>
                    </tr>
                </tfoot>
            </table>
            </form>

        </div>


    <?php endif; ?>
</div>
