<?php use_helper('Pagination') ?>

<?php slot('title', $topic->getTitle().' - '.$topic->getsfSimpleForumForum()->getName().' - Forum '.sfConfig::get('app_config_title_short')) ?>

<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
    array($topic->getsfSimpleForumForum()->getName(), 'sfSimpleForum/forum?forum_name='.$topic->getsfSimpleForumForum()->getSlug()),
    $topic->getTitle()
  )) ?>
<?php end_slot() ?>

<?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
<?php slot('auto_discovery_link_tag') ?>
  <?php echo auto_discovery_link_tag('rss', 'sfSimpleForum/topicFeed?id='.$topic->getId().'&stripped_title='.$topic->getSlug(), array('title' => $feed_title)) ?>
<?php end_slot() ?>
<?php endif; ?>

<div id="middle-side" class="forum">

  <div class="breadcrumb">
    <?php include_slot('forum_navigation') ?>
  </div>

  <h1><?php echo $topic->getTitle() ?></h1>

  <ul class="forum_actions">
    <?php if ($sf_user->hasCredential('moderator')): ?>
      <li><?php echo link_to(
        '<span>'.($topic->getIsSticked() ? __('Unstick', null, 'sfSimpleForum') : __('Stick', null, 'sfSimpleForum')).'</span>',
        'sfSimpleForum/toggleStick?id='.$topic->getId(),
        array('class' => 'button')
      ) ?></li>
      <li><?php echo link_to(
        '<span>'.($topic->getIsLocked() ? __('Unlock', null, 'sfSimpleForum') : __('Lock', null, 'sfSimpleForum')).'</span>',
        'sfSimpleForum/toggleLock?id='.$topic->getId(),
        array('class' => 'button')
      ) ?></li>
    <?php endif ?>
    <?php if ($sf_user->isAuthenticated()): ?>
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_display_recommandations', true) && !$topic->isRecommanded($sf_user->getGuardUser())): ?>
         <li><?php echo link_to(
           '<span>'. __('Recommand', null, 'sfSimpleForum').'</span>',
          '@forum_recommand?id='.$topic->getId(),
          array('class' => 'button')
        ) ?></li>
      <?php endif; ?>

      <?php if (sfConfig::get('app_sfSimpleForumPlugin_display_abuse', true) && !$topic->isAbuseReported($sf_user->getGuardUser())): ?>
         <li><?php echo link_to(
           '<span>'. __('Report abuse', null, 'sfSimpleForum').'</span>',
          '@forum_report_abuse?id='.$topic->getId(),
          array(
            'class' => 'button',
            'confirm' => __('Are you sure you want to report this topic?',null,'sfSimpleForum'))
        ) ?></li>
      <?php endif; ?>

    <?php endif; ?>

  </ul>

  <div class="forum_figures">
    <?php echo format_number_choice('[0]No message|[1]1 message, no reply|(1,+Inf]%posts% messages', array('%posts%' => $post_pager->getNbResults()), $post_pager->getNbResults(), 'sfSimpleForum') ?>
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
    - <?php echo format_number_choice('[0,1]1 view|(1,+Inf]%views% views', array('%views%' => $topic->getNbViews()), $topic->getNbViews(), 'sfSimpleForum') ?>
    <?php endif; ?>
    <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
      <?php echo link_to(image_tag('/sfDoctrineSimpleForumPlugin/images/feed-icon.png', 'align=top'), 'sfSimpleForum/topicFeed?id='.$topic->getId().'&stripped_title='.$topic->getSlug(), 'title='.$feed_title) ?>
    <?php endif; ?>
  </div>

  <?php include_partial('sfSimpleForum/post_list', array('posts' => $post_pager->getResults(), 'rankArray' => $rankArray, 'include_topic' => false)) ?>

  <div id="pager">
    <?php echo pager_navigation($post_pager, 'sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getSlug()) ?>
  </div>

  <?php if (!$topic->getIsLocked() && $sf_user->isAuthenticated()): ?>

    <form action="<?php echo url_for('sfSimpleForum/topic?id='.$topic->getId().'&stripped_title='.$topic->getSlug()) ?>" method="post">
      <fieldset>
        <legend><?php echo __('Post a reply', null, 'sfSimpleForum') ?></legend>
        <?php echo $form['_csrf_token']->render() ?>
        <?php echo $form['id']->render() ?>
        <?php echo $form['topic_id']->render() ?>
        <br/>
        <label for="forum_post_content">Message</label>
        <?php echo $form['content']->renderError() ?>
        <?php echo $form['content']->render(array('class' => 'field')) ?>
        <p style="margin: 10px 0 0 142px;">
          <span class="button">
            <button value="Valider" type="submit">Valider</button>
          </span>
        </p>
      </fieldset>
    </form>

  <?php elseif (!$topic->getIsLocked() && !$sf_user->isAuthenticated()): ?>

    <ul class="forum_actions">
        <li><?php echo link_to(
          __('Post a reply', null, 'sfSimpleForum'),
          sfConfig::get('sf_login_module').'/'.sfConfig::get('sf_login_action')
        ) ?></li>
    </ul>

  <?php elseif ($topic->getIsLocked() && $sf_user->isAuthenticated()): ?>

    <?php echo __('This topic was locked by a forum moderator. No reply can be added.', null, 'sfSimpleForum') ?>

  <?php endif; ?>
</div>
