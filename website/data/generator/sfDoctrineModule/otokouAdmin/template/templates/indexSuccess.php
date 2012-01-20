[?php use_helper('I18N', 'Date') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]

<div id="sf_admin_container">
    
   [?php slot('content_title'); ?]
    <h1>[?php echo <?php echo $this->getI18NString('list.title') ?> ?]</h1>
   [?php end_slot(); ?]

  [?php include_partial('<?php echo $this->getModuleName() ?>/flashes') ?]

  <?php if ($this->configuration->hasFilterForm()): ?>
  <div id="filters">
        [?php if ('show' === $filters_appearance) : ?]
            [?php include_partial('<?php echo $this->getModuleName() ?>/filters', array('form' => $filters, 'configuration' => $configuration)) ?]
        [?php endif; ?]
    </div>
  <?php endif; ?>
  
   <div id="sf_admin_actions_menu">
        <ul>
            [?php if ('show' === $filters_appearance) : ?]
                <li> [?php echo link_to('Hide filters', '@<?php echo $this->getUrlForAction('list') ?>?filters_appearance=hidden', array('id' => "filters_button")); ?]</li>
            [?php else : ?]
                <li> [?php echo link_to('Show filters', '@<?php echo $this->getUrlForAction('list') ?>?filters_appearance=show', array('id' => "filters_button")); ?]</li>
            [?php endif; ?]
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]
            <li>[?php include_partial('<?php echo $this->getModuleName() ?>/list_header', array('pager' => $pager)) ?]</li>
        </ul>
    </div>
  
  <div id="sf_admin_content">
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'batch')) ?]" method="post">
<?php endif; ?>
    [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?]
    <ul class="sf_admin_actions">
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper)) ?]
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]
    </ul>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    </form>
<?php endif; ?>
  </div>

  <div id="sf_admin_footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager)) ?]
  </div>
</div>
