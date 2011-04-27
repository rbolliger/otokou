<?php
slot('leftcol');
include_component('report', 'vehiclesMenu');
end_slot();
?>

<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h1>Create a new custom report</h1>

<div class="report_form">
    <?php if ($form->hasGlobalErrors()): ?>
    <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php echo $form->renderFormTag(url_for('@report_create')) ?>
        <table><tbody>
            <?php echo $form ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Create" />
                </td>
            </tr>
        </tfoot>
    </table>
</form>

</div>
