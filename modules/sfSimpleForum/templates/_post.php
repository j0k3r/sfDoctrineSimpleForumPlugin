<?php use_helper('Date') ?>
<tr>
  <td class="post_author">
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_show_author_details', false)): ?>
      <?php echo include_partial('sfSimpleForum/author', array('author_name' => $post->getAuthorName())) ?>
    <?php else: ?>
      <?php echo link_to(get_partial('sfSimpleForum/author_name', array('author' => $post->getAuthorName())), 'sfSimpleForum/userLatestPosts?username='.$post->getAuthorName()) ?><br/>
    <?php endif; ?>
    <?php echo format_date($post->getCreatedAt('U')) ?>
  </td>
  <td class="post_message">
    <?php if ($include_topic): ?>
    <div class="post_details">
      <?php echo link_to($post->getsfSimpleForumForum()->getName(), 'sfSimpleForum/forum?forum_name='.$post->getsfSimpleForumForum()->getSlug()) ?>
     &raquo;
      <?php echo link_to($post->getTitle(), 'sfSimpleForum/post?id='.$post->getId()) ?>
    </div>
    <?php endif ?>
    <a name="post<?php echo $post->getId() ?>"></a>
    <div class="post_content" id="post-<?php echo $post->getId() ?>">
      <?php echo nl2br($post->getContent()) ?> 
    </div>

    <?php if ($sf_user->hasCredential('moderator')): ?>
      <?php include_partial('sfSimpleForum/post_moderator_actions', array('post' => $post)) ?>
    <?php endif; ?>
  </td>
</tr>
<tr class="spacer"><td colspan="2"></td></tr>