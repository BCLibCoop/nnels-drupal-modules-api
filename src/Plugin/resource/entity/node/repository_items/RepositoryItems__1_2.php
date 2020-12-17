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

    //@todo

    return $public_fields;
  }

  protected function dataProviderClassName() {
    return '\Drupal\nnels_api\Plugin\DataProvider\DataProviderNodeExtra';
  }
}