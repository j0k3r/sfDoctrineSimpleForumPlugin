<?php use_helper('Date') ?>
<div class="forum_sidebar_block">
  <h2><?php echo __('Recent posts', null, 'sfSimpleForum') ?></h2>
  <ol>
  <?php foreach ($posts as $post): ?>
    <li>
      <?php echo link_to($post->getTitle(), 'sfSimpleForum/post?id='.$post->getId()) ?> <br />
      <?php echo __('%date% ago by %author%', array(
        //'%date%'   => distance_of_time_in_words($post->getCreatedAt('U')),
        '%date%'   => format_date($post->getCreatedAt('U')),
        '%author%' => link_to($post->getAuthorName(), 'sfSimpleForum/latestUserPosts?username='.$post->getAuthorName())
        ), 'sfSimpleForum') ?>
    </li>
  <?php endforeach; ?>
  </ol>
</div>