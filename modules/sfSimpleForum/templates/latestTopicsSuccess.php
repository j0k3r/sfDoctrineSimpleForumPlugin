<?php use_helper('Pagination') ?>
<?php $title = __('Latest topics', null, 'sfSimpleForum') ?>

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
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => false,
    'nb_topics'           => $topics_pager->getNbResults(),
    'topic_rule'          => '',
    'display_post_link'   => true,
    'nb_posts'            => Doctrine::getTable('sfSimpleForumPost')->findAll()->count(),
    'post_rule'           => 'sfSimpleForum/latestPosts',
    'feed_rule'           => 'sfSimpleForum/latestTopicsFeed',
    'feed_title'          => $feed_title
  )) ?>
    
  <?php include_partial('sfSimpleForum/topic_list', array('topics' => $topics_pager->getResults(), 'include_forum' => true)) ?>
  
  <br/>
  
  <div id="pager">
    <?php echo pager_navigation($topics_pager, 'sfSimpleForum/latestTopics') ?>
  </div>

</div>