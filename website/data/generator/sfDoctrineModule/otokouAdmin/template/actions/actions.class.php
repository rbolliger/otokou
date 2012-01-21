[?php

require_once(dirname(__FILE__).'/../lib/Base<?php echo ucfirst($this->moduleName) ?>GeneratorConfiguration.class.php');
require_once(dirname(__FILE__).'/../lib/Base<?php echo ucfirst($this->moduleName) ?>GeneratorHelper.class.php');

/**
 * <?php echo $this->getModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 31002 2010-09-27 12:04:07Z Kris.Wallsmith $
 */
abstract class <?php echo $this->getGeneratedModuleName() ?>Actions extends <?php echo $this->getActionsBaseClass()."\n" ?>
{


  public function preExecute()
  {
    $this->dispatcher->connect('admin.pre_execute', array($this, 'addUserToConfig'));  
  
    $this->configuration = new <?php echo $this->getModuleName() ?>GeneratorConfiguration();

    if (!$this->getUser()->hasCredential($this->configuration->getCredentials($this->getActionName())))
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    $this->dispatcher->notify(new sfEvent($this, 'admin.pre_execute', array('configuration' => $this->configuration)));

    $this->helper = new <?php echo $this->getModuleName() ?>GeneratorHelper();

    parent::preExecute();
    
    $this->dispatcher->connect('admin.build_query', array($this, 'addUserFilter'));
    
    $this->to_slots = array();
  }
  
  
   public function addUserFilter($event, $query) {

        return $query->andWhere($query->getRootAlias() . '.user_id = ? ', $this->getUserIdFromRouteOrSession());
    }

    public function addUserToConfig(sfEvent $event) {
        $this->configuration->setUserId($this->getUserIdFromRouteOrSession());
    }
    
    
   protected function getUserIdFromRouteOrSession() {

        $username = $this->getUsernameFromRouteOrSession();

        if ($username == $this->getUser()->getGuardUser()->getUsername()) {
            $user = $this->getUser()->getGuardUser();
        } else {
            $user = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username);
        }

        $this->forward404Unless($user);

        return $user->getId();
    }
    
    
    public function executeMaxPerPage(sfRequest $request) {

        $form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);

        $isValid = $form->process($request);

        if ($isValid) {

            $this->redirect('@<?php echo $this->getUrlForAction('list') ?>?page=1');
        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();
        $this->filters_appearance = $this->getFiltersAppearance();

        $this->setTemplate('index');
        $this->pager->form = $form;

    }
    
    protected function getMaxPerPageOptions() {


        $def = $this->getUser()->getGuardUser()->getListMaxPerPage() ?
                $this->getUser()->getGuardUser()->getListMaxPerPage() :
                $this->configuration->getGeneratorMaxPerPage();


        $options = array(
            'max_per_page_name' => '<?php echo $this->getModuleName() ?>_list_max_per_page',
            'max_per_page_choices' => array(
                5,
                10,
                20,
                50,
                100,
                150,
                1000,
            ),
            'max_per_page_value' => $def,
        );

        return $options;
    }
    
protected function setFiltersAppearance($appearance) {
        $this->getUser()->setAttribute('charge.filters_appearance', $appearance, 'admin_module');
}

protected function getFiltersAppearance() {
        return $this->getUser()->getAttribute('charge.filters_appearance', 'hidden', 'admin_module');
}

    

<?php include dirname(__FILE__).'/../../parts/indexAction.php' ?>

<?php if ($this->configuration->hasFilterForm()): ?>
<?php include dirname(__FILE__).'/../../parts/filterAction.php' ?>
<?php endif; ?>

<?php include dirname(__FILE__).'/../../parts/newAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/createAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/editAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/updateAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/deleteAction.php' ?>

<?php if ($this->configuration->getValue('list.batch_actions')): ?>
<?php include dirname(__FILE__).'/../../parts/batchAction.php' ?>
<?php endif; ?>

<?php include dirname(__FILE__).'/../../parts/processFormAction.php' ?>

<?php if ($this->configuration->hasFilterForm()): ?>
<?php include dirname(__FILE__).'/../../parts/filtersAction.php' ?>
<?php endif; ?>

<?php include dirname(__FILE__).'/../../parts/paginationAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/sortingAction.php' ?>
}
