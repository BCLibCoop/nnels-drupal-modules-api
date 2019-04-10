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
 * Class FileResources__1_1
 * @package Drupal\nnels_api\Plugin\resource\entity\field_collection
 * \fileResources
 *
 * @Resource(
 *   name = "fileResources:1.1",
 *   resource = "fileResources",
 *   label = "File Resources",
 *   description = "Expose the file resources attached to repository items.",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "field_collection_item",
 *     "bundles": {
 *       "field_file_resource"
 *     },
 *   },
 *   formatter = "json_api",
 *   renderCache = FALSE,
 *   majorVersion = 1,
 *   minorVersion = 1
 * )
 */
class FileResources__1_1 extends ResourceEntity {

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

    $stream = new \Drupal\amazons3\StreamWrapper;
    $uri = reset(
        $data_emw->getWrapper()->field_s3_file_upload->value()
      )['uri'];
    $stream->setUri($uri);
    $filesize_bytes = $stream->url_stat($stream->getUri(), 1)['size'];

    //Setter sets the value with a method
    //Process callback operates on set value
    $map = array(
      "s3_path_signed" => array(
        'setter' => 'getExternalUrl',
      ),
      "filesize" => array(
        'preload' => $filesize_bytes,
        'process' => 'getFilesize', //call formatSize in other method
      )
    );

    $output = array();

    foreach ($map as $mappable => $ops) {
      $setter = $ops['setter'];
      $check_callable = array($stream, $setter);
      if (is_callable($check_callable)) $output[$mappable] = $stream->$setter();
      else $output[$mappable] = $ops['preload'];
      if($ops['process']) $output[$mappable] =
        call_user_func(
          array($this, $ops['process']),
          $output[$mappable]
        );
  }

    return $output;
  }

  public function formatRunTime($runtime) {
    return date('H:i:s', strtotime($runtime));
  }

  public function getFilesize($bytes) {
    $decimals = 2;
    $size = array('B','kB','MB','GB','TB','PB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .
      $size[$factor];
  }
}