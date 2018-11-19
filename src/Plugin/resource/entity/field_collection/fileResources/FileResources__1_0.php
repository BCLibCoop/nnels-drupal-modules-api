<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\field_collection
 * \fileResources
 * \FileResources__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\field_collection
\fileResources;


use Drupal\restful\Plugin\resource\Field\ResourceFieldEntityReference;
use Drupal\restful\Plugin\resource\ResourceEntity;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class FileResources__1_0
 * @package Drupal\nnels_api\Plugin\resource\entity\field_collection
 * \fileResources
 *
 * @Resource(
 *   name = "fileResources:1.0",
 *   resource = "fileResources",
 *   label = "File Resources",
 *   description = "Expose the file resources attached to repository items.",
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "field_collection_item",
 *     "bundles": {
 *       "field_file_resource"
 *     },
 *   },
 *   formatter = "json_api",
 *   renderCache = FALSE,
 *   majorVersion = 1,
 *   minorVersion = 0
 * )
 */
class FileResources__1_0 extends ResourceEntity {

  protected function publicFields() {

    $public_fields = parent::publicFields();

    $public_fields['id']['methods'] = array('GET');
    $public_fields['s3_info']['callback'] =
      array($this, 'getS3Info');

    $public_fields['running_time'] = array(
      'property' => 'field_running_time',
      'process_callbacks' => array(
        array($this, 'formatRunTime')
      )
    );
    $public_fields['format'] = array(
      'property' => 'field_file_format',
      'wrapper_method' => 'label',
    );
    $public_fields['availability'] = array(
      'property' => 'field_availability_status',
      'wrapper_method' => 'label',
    );
    $public_fields['public_note'] = array(
      'property' => 'field_description',
    );
    $public_fields['narration'] = array(
      'property' => 'field_performer',
    );

    # Unset
    $unset = array('label', 'self');
    foreach ($unset as $field) {
      unset($public_fields[$field]);
    }

    return $public_fields;
  }

  public function getS3Info($data_emw) {
    module_load_include('inc', 'cals_s3', 'cals_s3.NNELSStreamWrapper.class');

    $stream = new \Drupal\cals_s3\NNELSStreamWrapper;
    $stream->setUri($data_emw->getWrapper()->field_s3_path->value());

    $map = array(
      "s3_path_signed" => array(
        'setter' => 'getExternalUrl',
      ),
      "filesize" => array(
        'setter' => 'get_filesize',
        'process' => 'format_size',
      )
    );

    $output = array();

    foreach ($map as $field_name => $funcs) {
      $setter = $funcs['setter'];
      $output[$field_name] = $stream->$setter();
      if($funcs['process']) $output[$field_name] =
        call_user_func($funcs['process'], $output[$field_name]);
  }

    return $output;
  }

  public function formatRunTime($runtime) {
    return date('H:i:s', strtotime($runtime));
  }
}