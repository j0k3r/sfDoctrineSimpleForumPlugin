<?php if (sfConfig::get('app_sfSimpleForum_include_breadcrumb', true)): ?>
<?php slot('forum_navigation') ?>
<?php if ($forum): ?>
  <?php echo forum_breadcrumb(array(
    array(__(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), null, 'sfSimpleForum'), 'sfSimpleForum/forumList'),
    array($forum->getName(), 'sfSimpleForum/forum?forum_name='.$forum->getSlug()),
    __('New topic', null, 'sfSimpleForum')
  )) ?>
<?php else: ?>
  <?php echo forum_breadcrumb(array(
    array(__(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), null, 'sfSimpleForum'), 'sfSimpleForum/forumList'),
    __('New topic', null, 'sfSimpleForum')
  )) ?>
<?php endif; ?>
<?php end_slot() ?>
<?php endif; ?>

<div id="middle-side" class="forum">

  <div class="breadcrumb">
    <?php include_slot('forum_navigation') ?>
  </div>

  <div id="news">
    <h1><?php echo __('Create a new topic', null, 'sfSimpleForum') ?></h1>
    <br/>
    <div class="leftcol" style="width:auto;">
      <p><?php echo __('You are about to create a new topic in the forum %forum_name%', array('%forum_name%' => '"<strong>'.$forum->get('name').'</strong>"'), 'sfSimpleForum') ?></p>
      <?php if(isset($error)): ?>

      <div class="error"><?php echo $error ?></div>

      <?php endif ?>

      <dl class="topic">
        <dd>
          <form action="<?php echo url_for('sfSimpleForum/createTopic?forum_name=' . $forum->get('slug')) ?>" method="post">
            <?php if(sfConfig::get('sf_csrf_secret')): echo $form['_csrf_token']->render(); endif; ?>
            <?php echo $form['forum_id']->render() ?>
            <p>
              <label for="forum_topic_title"><?php echo __('Title', null, 'sfSimpleForum') ?> <span class="required">*</span></label>
              <?php echo $form['title']->renderError() ?>
              <?php echo $form['title']->render(array('class' => 'field', 'style' => 'width: 370px')) ?>
            </p>
            <p>
              <label for="forum_topic_content"><?php echo __('Message', null, 'sfSimpleForum') ?> <span class="required">*</span></label>
              <?php echo $form['content']->renderError() ?>
              <?php echo $form['content']->render(array('class' => 'field', 'style' => 'width: 370px')) ?>
            </p>
            <?php if($sf_user->hasCredential('moderator')): ?>
            <p>
              <label for="forum_topic_is_sticked"><?php echo __('Sticked', null, 'sfSimpleForum') ?></label>
              <?php echo $form['is_sticked']->renderError() ?>
              <?php echo $form['is_sticked']->render(array('class' => 'field')) ?>
            </p>
            <p>
              <label for="forum_topic_is_locked"><?php echo __('Locked', null, 'sfSimpleForum') ?></label>
              <?php echo $form['is_locked']->renderError() ?>
              <?php echo $form['is_locked']->render(array('class' => 'field')) ?>
            </p>
            <?php endif ?>
            <p>
              <span class="button">
                <button value="Validate" type="submit"><?php echo __('Validate', null, 'sfSimpleForum') ?></button>
              </span>
            </p>
          </form>
        </dd>
      </dl>
    </div>
  </div>
</div>