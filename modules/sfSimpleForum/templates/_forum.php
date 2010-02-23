<?php use_helper('Date', 'Text') ?>
<tr>
  <td class="forum_name">
    <?php echo link_to($forum->getName(), 'sfSimpleForum/forum?forum_name='.$forum->getSlug()) ?><br />
    <?php echo simple_format_text($forum->getDescription(), array('class' => 'forum_description')) ?>
  </td>
  <td class="forum_threads"><?php echo $forum->getNbTopics() ?></td>
  <td class="forum_posts"><?php echo $forum->getNbPosts() ?></td>
  <td class="forum_recent">
    <?php if ($forum->getLatestPostId()): ?>
      <?php $latest_post = $forum->getsfSimpleForumPost(); ?>
      <?php echo link_to($latest_post->getTitle(), 'sfSimpleForum/post?id='.$latest_post->getId()) ?><br />
      <?php echo __('%date% ago by %author%', array(
        //'%date%'   => distance_of_time_in_words(strtotime($latest_post->getCreatedAt())),
        '%date%'   => format_date($latest_post->getCreatedAt()),
        '%author%' => link_to(get_partial('sfSimpleForum/author_name', array('author' => $latest_post->getAuthorName(), 'sf_cache_key' => $latest_post->getAuthorName())), 'sfSimpleForum/userLatestPosts?username='.$latest_post->getAuthorName())
        ), 'sfSimpleForum') ?>
    <?php endif ;?>
  </td>
</tr>