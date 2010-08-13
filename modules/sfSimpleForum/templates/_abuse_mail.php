<?php use_helper('I18N', 'Url') ?>
<?php echo __('An abuse was reported for topic "%1%".<br />Please, check the topic and delete it if necessary.<br /><a href="%2%">%2%</a>',
  array(
    "%1%" => $topic->getTitle(),
    "%2%" => url_for("@forum_topic?id=" . $topic->getId() . "&stripped_title=" . $topic->getSlug(), true)
  ),
  "sfSimpleForum"
) ?>
