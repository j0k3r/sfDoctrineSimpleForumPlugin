<?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
  <?php slot('auto_discovery_link_tag') ?>
    <?php echo auto_discovery_link_tag('rss', $feed_rule, array('title' => $feed_title)) ?>
  <?php end_slot() ?>
<?php endif; ?>

<div class="forum_figures">

  <?php echo link_to_if(
    $display_topic_link,
    format_number_choice('[0]No topic yet|[1]One topic|(1,+Inf]%topics% topics', array('%topics%' => $nb_topics), $nb_topics, 'sfSimpleForum'),
    $topic_rule
    ) ?>, 

  <?php echo link_to_if(
    $display_post_link,
    format_number_choice('[0]No message|[1]One message|(1,+Inf]%posts% messages', array('%posts%' => $nb_posts), $nb_posts, 'sfSimpleForum'),
    $post_rule
   ) ?>

  <?php if(sfConfig::get('app_sfSimpleForumPlugin_use_feeds', true)): ?>
    <?php echo link_to(image_tag('/sfDoctrineSimpleForumPlugin/images/feed-icon.png', 'align=top'), $feed_rule, 'title='.$feed_title) ?>
  <?php endif; ?>  

</div>