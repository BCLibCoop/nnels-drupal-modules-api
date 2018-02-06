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
//    $public_fields = array(
//        'type' => array(
//          'wrapper_method' => 'getBundle',
//          'wrapper_method_on_entity' => TRUE,
//        ),
//      ) + parent::publicFields();
    $public_fields = parent::publicFields();

//    $public_fields['type'] = array(
//      'wrapper_method' => 'getBundle',
//      'wrapper_method_on_entity' => TRUE
//    );
    $public_fields['id']['methods'] = array('GET');

    $public_fields['s3_path'] = array(
      'property' => 'field_s3_path'
    );
    $public_fields['format'] = array(
      'property' => 'field_file_format'
    );
    return $public_fields;
  }
}