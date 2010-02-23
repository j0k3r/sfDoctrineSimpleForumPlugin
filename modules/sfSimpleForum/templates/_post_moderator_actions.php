
<ul class="post_actions">
  <li><?php echo link_to(__('Delete', null, 'sfSimpleForum'), 'sfSimpleForum/deletePost?id='.$post->getId(), array('confirm' =>__('Are you sure you want to delete this post?', null, 'sfSimpleForum'))) ?></li>
</ul>
