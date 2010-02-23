<?php use_helper('Pagination') ?>
<?php $title = __('Topics by %user%', array('%user%' => get_partial('sfSimpleForum/author_name', array('author' => $user, 'sf_cache_key' => $username))), 'sfSimpleForum') ?>

<?php slot('title', $title.' - Forum '.sfConfig::get('app_config_title_short')) ?>

<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), 'sfSimpleForum/forumList'),
    $title
  )) ?>
<?php end_slot() ?>

<div id="middle-side" class="forum">

  <div class="breadcrumb">
    <?php include_slot('forum_navigation') ?>
  </div>
  
  <h1><?php echo $title ?></h1>

  <?php if (sfConfig::get('app_sfSimpleForumPlugin_allow_new_topic_outside_forum', true)): ?>
  <ul class="forum_actions">
    <li><?php echo link_to(__('New topic', null, 'sfSimpleForum'), 'sfSimpleForum/createTopic') ?></li>
  </ul>    
  <?php endif; ?>
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => false,
    'nb_topics'           => $topics_pager->getNbResults(),
    'topic_rule'          => '',
    'display_post_link'   => true,
    'nb_posts'            => Doctrine::getTable('sfSimpleForumPost')->findByUserId($user->getId())->count(),
    'post_rule'           => 'sfSimpleForum/userLatestPosts?username='.$username,
    'feed_rule'           => 'sfSimpleForum/userLatestTopicsFeed?username='.$username,
    'feed_title'          => $feed_title
  )) ?>
    
  <?php include_partial('sfSimpleForum/topic_list', array('topics' => $topics_pager->getResults(), 'include_forum' => true)) ?>

  <div id="pager">
    <?php echo pager_navigation($topics_pager, 'sfSimpleForum/userLatestTopics?username='.$username) ?>
  </div>

</div>