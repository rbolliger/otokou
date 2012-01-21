[?php use_helper('I18N', 'Date') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]

[?php include_partial('<?php echo $this->getModuleName() ?>/slots_new',array('to_slots' => $to_slots)) ?]

<div id="sf_admin_container">
     
  [?php slot('content_title'); ?]
    <h1>[?php echo <?php echo $this->getI18NString('new.title') ?> ?]</h1>
  [?php end_slot(); ?]

  [?php include_partial('<?php echo $this->getModuleName() ?>/flashes') ?]

  <div id="sf_admin_header">
    [?php include_partial('<?php echo $this->getModuleName() ?>/form_header', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration)) ?]
  </div>

  <div id="sf_admin_content">
    [?php include_partial('<?php echo $this->getModuleName() ?>/form', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?]
  </div>

  <div id="sf_admin_footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/form_footer', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration)) ?]
  </div>
</div>
