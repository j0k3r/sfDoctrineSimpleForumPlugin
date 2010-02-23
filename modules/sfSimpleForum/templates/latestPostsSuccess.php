<?php use_helper('Pagination') ?>
<?php $title = __('Latest messages', null, 'sfSimpleForum') ?>

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
    'display_topic_link'  => $nb_topics,
    'nb_topics'           => $nb_topics,
    'topic_rule'          => 'sfSimpleForum/latestTopics',
    'display_post_link'   => false,
    'nb_posts'            => $post_pager->getNbResults(),
    'post_rule'           => '',
    'feed_rule'           => 'sfSimpleForum/latestPostsFeed',
    'feed_title'          => $feed_title
  )) ?>
  
  <?php include_partial('sfSimpleForum/post_list', array('posts' => $post_pager->getResults(), 'include_topic' => true)) ?>
  
  <br/>
  
  <div id="pager">
    <?php echo pager_navigation($post_pager, 'sfSimpleForum/latestPosts') ?>
  </div>

</div>