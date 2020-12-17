<?php

/**
* @file
* Contains \Drupal\nnels_api\Plugin\resource\entity\node\repository_items
 * \RepositoryItems__1_1
*/

namespace Drupal\nnels_api\Plugin\resource\entity\node\repository_items;

use Drupal\cals_s3\NNELSStreamWrapper;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Plugin\resource\ResourceNode;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderEntity;

/**
* Class RepositoryItems
* @package Drupal\nnels_api\Plugin\resource\entity\node\repository_items
*
* @Resource(
*   name = "repositoryItems:1.2",
*   resource = "repositoryItems",
*   label = "Repository Items",
*   description = "Expose the repository_item content type.",
*   authenticationOptional = TRUE,
*   dataProvider = {
*     "entityType": "node",
*     "bundles": {
*       "repository_item"
*     },
*   },
*   formatter = "json_api",
*   majorVersion = 1,
*   minorVersion = 2,
* )
*/
class RepositoryItems__1_2 extends RepositoryItems__1_1 {

  /**
   * {@inheritdoc}
   */
  protected function publicFields() {
    $public_fields = parent::publicFields();

    $public_fields['path_alias'] = $public_fields['human_readable_path'];
    unset($public_fields['human_readable_path']);

    $public_fields['file_resource_hint'] = array(
      'property' => 'field_file_resource',
      'process_callbacks' => array(array($this, 'populateFC')),
    );

    return $public_fields;
  }

  protected function dataProviderClassName() {
    return '\Drupal\nnels_api\Plugin\DataProvider\DataProviderNodeExtra';
  }

  public function populateFC($fieldCollections) {
    $output = array();

    foreach ($fieldCollections as $instance) {
      $entity = entity_metadata_wrapper('field_collection_item', $instance);
      //@todo handle this in dataProvider for FileResources
      //Only Availability == Produced (1) files should be attached.
      if ($entity->field_availability_status->value() == 1) {

        $output[] = array(
          'type' => 'fileResources',
          'format_long' => $entity->field_file_format->label(),
          'format_short' => str_replace(' ', '', strtolower($entity->field_file_format->label())),
          'id' => $entity->item_id->value(),
        );
      }
    }
    return $output;
  }
}