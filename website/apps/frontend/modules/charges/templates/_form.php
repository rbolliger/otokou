<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>


<?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

<?php echo form_tag_for($form, '@charges') ?>
<table>
    <tfoot>
        <tr>
            <td colspan="2">
                <?php echo $form->renderHiddenFields(false) ?>
                &nbsp;<a href="<?php echo url_for('charges') ?>">Back to list</a>
                <?php if (!$form->getObject()->isNew()): ?>
                    &nbsp;<?php echo link_to('Delete', 'charges_delete', $form->getObject(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
                <?php endif; ?>
                <input type="submit" value="Save" />
                <input type="submit" value="Save and Add" name="_save_and_add" />
                
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php echo $form['vehicle_id']->renderRow() ?>
        <?php echo $form['category_id']->renderRow() ?>
        <?php echo $form['date']->renderRow() ?>
        <?php echo $form['kilometers']->renderRow() ?>
        <?php echo $form['amount']->renderRow() ?>
        <?php echo $form['comment']->renderRow() ?>
        <?php echo $form['quantity']->renderRow() ?>


    </tbody>
</table>
</form>

