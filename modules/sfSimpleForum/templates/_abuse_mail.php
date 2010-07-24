<?php use_helper('I18N', 'Url') ?>
<?php echo __(<<<EOM
An abuse was reported for topic %1%.

Please, check the topic and delete it if necessary.

%2%

EOM
, array("%1%" => $topic->getTitle(),
  "%2%" => url_for("@forum_topic?id=" . $topic->getId() . "&stripped_title=" . $topic->getSlug(), true))) ?>
