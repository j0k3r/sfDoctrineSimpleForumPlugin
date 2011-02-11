<?php use_helper('Date') ?>
<tr>
  <td align="center" width="1%">
    <?php echo ($topic->getViewForUser($sf_user->getGuardUser()->getId())) ? image_tag(sfConfig::get('app_sfDoctrineSimpleForumPlugin_image_read'), array('style' => 'vertical-align: middle')) : image_tag(sfConfig::get('app_sfDoctrineSimpleForumPlugin_image_not_read'), array('style' => 'vertical-align: middle')) ?>
  </td>
  <td class="thread_name">
    
    <?php if ($topic->getIsSticked()): ?>
      <?php echo image_tag('/sfDoctrineSimpleForumPlugin/images/note.png', array(
        'style' => 'vertical-align: middle',
        'alt'   => __('Sticked topic', null, 'sfSimpleForum'),
        'title' => __('Sticked topic', null, 'sfSimpleForum')
      )) ?>
    <?php endif; ?>
    
    <?php if ($topic->getIsLocked()): ?>
      <?php echo image_tag('/sfDoctrineSimpleForumPlugin/images/lock.png', array(
        'style' => 'vertical-align: middle',
        'alt'   => __('Locked topic', null, 'sfSimpleForum'),
        'title' => __('Locked topic', null, 'sfSimpleForum')
      )) ?>
    <?php endif; ?>
    
    <?php if (!$topic->getIsLocked() && !$topic->getIsSticked()): ?>
      <?php $image = $topic->getNbReplies() ? 'comments' : 'comment'  ?>
      <?php echo image_tag('/sfDoctrineSimpleForumPlugin/images/'.$image.'.png', array(
        'style' => 'vertical-align: middle',
      )) ?>
    <?php endif; ?>
    
    <?php echo link_to(
      $topic->getTitle(),
      '@forum_topic?id='.$topic->getId().'&stripped_title='.$topic->getSlug(),
      array('class' => $topic->getIsNew() ? 'new' : '')) ?>
    
    <?php if ($user_is_moderator): ?>
      - <span id="title-<?php echo $topic->getId() ?>"><?php echo $topic->getTitle() ?></span>
    <?php endif ?>
    
    <?php $pages = ceil(($topic->getNbPosts()) / sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)) ?>
    <?php if ($pages > 1): ?>
      <?php echo link_to(
        '(last page)',
        'sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getSlug().'&page='.$pages
      ) ?>
    <?php endif; ?>
    
    <?php if ($include_forum): ?>
      in <?php echo link_to(
        $topic->getsfSimpleForumForum()->getName(),
        'sfSimpleForum/forum?forum_name='.$topic->getsfSimpleForumForum()->getSlug()
      ) ?>
    <?php endif; ?>
    
    <?php include_partial('sfSimpleForum/topic_moderator_actions', array('topic' => $topic, 'user_is_moderator' => $user_is_moderator)) ?>
    
  </td>
  <td class="thread_replies"><?php echo $topic->getNbReplies() ?></td>

  <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
  <td class="thread_views"><?php echo $topic->getNbViews() ?></td>
  <?php endif; ?>

  <?php if (sfConfig::get('app_sfSimpleForumPlugin_display_recommandations', true)): ?>
  <td class="thread_recommandations"><?php echo $topic->getNbRecommandations() ?></td>
  <?php endif; ?>


  <td class="thread_recent">
    <?php //$message_link = $topic->getNbReplies() ? __('Last reply', null, 'sfSimpleForum') : __('Posted', null, 'sfSimpleForum') ?>
    <?php $latest_post = $topic->getsfSimpleForumPost() ?>
    <?php echo __('%date% ago by %author%', array(
      //'%date%'   => distance_of_time_in_words(strtotime($latest_post->getCreatedAt())),
      '%date%'   => format_date($latest_post->getCreatedAt()),
      '%author%' => link_to(get_partial('sfSimpleForum/author_name', array('author' => $latest_post->getAuthorName(), 'sf_cache_key' => $latest_post->getAuthorName())), 'sfSimpleForum/userLatestPosts?username='.$latest_post->getAuthorName())
      ), 'sfSimpleForum') ?>

    <?php if ($topic->getNbReplies()): ?>
      <?php echo link_to(image_tag('/sfDoctrineSimpleForumPlugin/images/icon_latest_reply.gif'), 'sfSimpleForum/post?id='.$topic->getLatestPostId()) ?>
    <?php endif; ?>

  </td>

</tr>
