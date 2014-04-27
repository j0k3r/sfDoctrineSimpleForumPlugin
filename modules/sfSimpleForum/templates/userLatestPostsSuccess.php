<?php use_helper('Pagination') ?>
<?php $title = __('Messages by %user%', array('%user%' => get_partial('sfSimpleForum/author_name', array('author' => $user, 'sf_cache_key' => $username))), 'sfSimpleForum') ?>

<?php slot('title', $title.' - Forum '.sfConfig::get('app_config_title_short')) ?>

<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(__(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), null, 'sfSimpleForum'), 'sfSimpleForum/forumList'),
    $title
  )) ?>
<?php end_slot() ?>

<div id="middle-side" class="forum">

  <div class="breadcrumb">
    <?php include_slot('forum_navigation') ?>
  </div>

  <h1><?php echo $title ?></h1>

  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => true,
    'nb_topics'           => Doctrine_Core::getTable('sfSimpleForumTopic')->findByUserId($user->getId())->count(),
    'topic_rule'          => 'sfSimpleForum/userLatestTopics?username='.$username,
    'display_post_link'   => false,
    'nb_posts'            => $post_pager->getNbResults(),
    'post_rule'           => '',
    'feed_rule'           => 'sfSimpleForum/userLatestPostsFeed?username='.$username,
    'feed_title'          => $feed_title
  )) ?>

  <?php include_partial('sfSimpleForum/post_list', array('posts' => $post_pager->getResults(), 'include_topic' => true,'rankArray' => $rankArray)) ?>

  <div id="pager">
    <?php echo pager_navigation($post_pager, 'sfSimpleForum/userLatestPosts?username='.$username) ?>
  </div>

</div>