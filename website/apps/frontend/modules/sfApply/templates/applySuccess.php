<?php use_helper('I18N') ?>
<?php
  // Override the login slot so that we don't get a login prompt on the
  // apply page, which is just odd-looking. 0.6
?>
<?php slot('sf_apply_login') ?>
<?php end_slot() ?>

<?php slot('content_title') ?>
<h2><?php echo __("Apply for an Account") ?></h2>
<?php end_slot() ?>

<div id="sf_admin_container">
<div class="sf_admin_filter">

    
    
<form method="POST" action="<?php echo url_for('sfApply/apply') ?>"
  name="sf_apply_apply_form" id="sf_apply_apply_form"> 
 <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>
 <table cellspacing="0">
     <tfoot>
     <tr>
      <td colspan="2" class="sf_apply_submit_row">
        <input type="submit" value="<?php echo __("Create My Account") ?>"/>
        <?php echo __("or") ?> 
        <?php echo link_to(__("Cancel"), sfConfig::get('app_sfApplyPlugin_after', '@homepage')) ?>
      </td>
    </tr>
    </tfoot>
    <?php echo $form ?>
    
  </table>
    
    

</form>
</div>
    </div>
