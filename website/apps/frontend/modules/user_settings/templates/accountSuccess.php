<?php slot('leftcol') ?>
<?php include_partial('user_settings_menu'); ?>
<?php end_slot() ?>

<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="sf_admin_form">
<?php echo $form->renderFormTag(url_for('@user_settings_account')) ?>
  <table>
    <?php echo $form ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="Update" />
      </td>
    </tr>
  </table>
</form>
</div>