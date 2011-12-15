<?php slot('sf_admin.current_header') ?>
<th class="sf_admin_text sf_admin_list_th_amount">
  <?php if ('amount' == $sort[0]): ?>
    <?php echo link_to(__('Amount', array(), 'messages'), '@charge', array('query_string' => 'sort=amount&sort_type='.($sort[1] == 'asc' ? 'desc' : 'asc'))) ?>
    <?php echo image_tag('sort_'.$sort[1].'.png', array('size' => '16x16', 'alt' => __($sort[1], array(), 'sf_admin'), 'title' => __($sort[1], array(), 'sf_admin'))) ?>
  <?php else: ?>
    <?php echo link_to(__('Amount', array(), 'messages'), '@charge', array('query_string' => 'sort=amount&sort_type=asc')) ?>
  <?php endif; ?>
</th>
<?php end_slot(); ?>
<?php include_slot('sf_admin.current_header') ?><?php slot('sf_admin.current_header') ?>
<th class="sf_admin_text sf_admin_list_th_quantity">
  <?php if ('quantity' == $sort[0]): ?>
    <?php echo link_to(__('Quantity', array(), 'messages'), '@charge', array('query_string' => 'sort=quantity&sort_type='.($sort[1] == 'asc' ? 'desc' : 'asc'))) ?>
    <?php echo image_tag('sort_'.$sort[1].'.png', array('size' => '16x16', 'alt' => __($sort[1], array(), 'sf_admin'), 'title' => __($sort[1], array(), 'sf_admin'))) ?>
  <?php else: ?>
    <?php echo link_to(__('Quantity', array(), 'messages'), '@charge', array('query_string' => 'sort=quantity&sort_type=asc')) ?>
  <?php endif; ?>
</th>
<?php end_slot(); ?>
<?php include_slot('sf_admin.current_header') ?><?php slot('sf_admin.current_header') ?>
<th class="sf_admin_date sf_admin_list_th_date">
  <?php if ('date' == $sort[0]): ?>
    <?php echo link_to(__('Date', array(), 'messages'), '@charge', array('query_string' => 'sort=date&sort_type='.($sort[1] == 'asc' ? 'desc' : 'asc'))) ?>
    <?php echo image_tag('sort_'.$sort[1].'.png', array('size' => '16x16', 'alt' => __($sort[1], array(), 'sf_admin'), 'title' => __($sort[1], array(), 'sf_admin'))) ?>
  <?php else: ?>
    <?php echo link_to(__('Date', array(), 'messages'), '@charge', array('query_string' => 'sort=date&sort_type=asc')) ?>
  <?php endif; ?>
</th>
<?php end_slot(); ?>
<?php include_slot('sf_admin.current_header') ?><?php slot('sf_admin.current_header') ?>
<th class="sf_admin_text sf_admin_list_th_kilometers">
  <?php if ('kilometers' == $sort[0]): ?>
    <?php echo link_to(__('Kilometers', array(), 'messages'), '@charge', array('query_string' => 'sort=kilometers&sort_type='.($sort[1] == 'asc' ? 'desc' : 'asc'))) ?>
    <?php echo image_tag('sort_'.$sort[1].'.png', array('size' => '16x16', 'alt' => __($sort[1], array(), 'sf_admin'), 'title' => __($sort[1], array(), 'sf_admin'))) ?>
  <?php else: ?>
    <?php echo link_to(__('Kilometers', array(), 'messages'), '@charge', array('query_string' => 'sort=kilometers&sort_type=asc')) ?>
  <?php endif; ?>
</th>
<?php end_slot(); ?>
<?php include_slot('sf_admin.current_header') ?><?php slot('sf_admin.current_header') ?>
<th class="sf_admin_text sf_admin_list_th_comment">
  <?php if ('comment' == $sort[0]): ?>
    <?php echo link_to(__('Comment', array(), 'messages'), '@charge', array('query_string' => 'sort=comment&sort_type='.($sort[1] == 'asc' ? 'desc' : 'asc'))) ?>
    <?php echo image_tag('sort_'.$sort[1].'.png', array('size' => '16x16', 'alt' => __($sort[1], array(), 'sf_admin'), 'title' => __($sort[1], array(), 'sf_admin'))) ?>
  <?php else: ?>
    <?php echo link_to(__('Comment', array(), 'messages'), '@charge', array('query_string' => 'sort=comment&sort_type=asc')) ?>
  <?php endif; ?>
</th>
<?php end_slot(); ?>
<?php include_slot('sf_admin.current_header') ?>