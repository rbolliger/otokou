  public function getPagerClass()
  {
    return '<?php echo isset($this->config['list']['pager_class']) ? $this->config['list']['pager_class'] : 'sfDoctrinePager' ?>';
<?php unset($this->config['list']['pager_class']) ?>
  }

  
  public function getPagerMaxPerPage() 
{
$user = sfContext::getInstance()->getUser();
$value = $user->getAttribute('<?php echo $this->getModuleName() ?>_list_max_per_page', $user->getGuardUser()->getListMaxPerPage());

if ($value) {
    return $value; 
} else {
    return <?php echo isset($this->config['list']['max_per_page']) ? (integer) $this->config['list']['max_per_page'] : 20 ?>;
<?php unset($this->config['list']['max_per_page']) ?>
}

}


