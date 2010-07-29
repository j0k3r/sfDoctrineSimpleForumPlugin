<?php

/**
 * PluginsfSimpleForumRank form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginsfSimpleForumRankForm extends BasesfSimpleForumRankForm
{
  public function setup()
  {
    parent::setup();

    $this->widgetSchema['image'] = new sfWidgetFormInputFileEditable(array(
      'file_src' => '/uploads/' . sfConfig::get('app_sfSimpleForumPlugin_upload_dir','') .$this->getObject()->getImage(),
      'is_image' => true,
      'edit_mode' => !$this->isNew(),
      'with_delete' => true,
    ));
 
    $this->validatorSchema['image'] = new sfValidatorFile(array(
    	'required' => false,
    	'path' => sfConfig::get('sf_upload_dir') . '/' . sfConfig::get('app_sfSimpleForumPlugin_upload_dir',''),
        'mime_types' => 'web_images'
    ));
 
    $this->validatorSchema['image_delete'] = new sfValidatorBoolean();

  } 

}
