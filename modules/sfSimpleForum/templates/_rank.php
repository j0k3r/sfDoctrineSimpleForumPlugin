<?php
$rank = forum_rank($rankArray,$nb_posts);
if($rank):?>

<?php echo __('Rank',null,'sfSimpleForum'); ?> : <?php echo $rank['title']; ?> (<?php echo format_number_choice('[0]0 message|[1]1 message|(1,+Inf] %1% messages', array('%1%' => $rank['nb_posts']), $rank['nb_posts'], 'sfSimpleForum'); ?>)
<?php if($rank['image']): ?>
  <img src="/uploads/<?php echo sfConfig::get('app_sfSimpleForumPlugin_upload_dir',''); ?><?php echo $rank['image']; ?>" />
<?php endif; ?>

<?php endif; ?>
