<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\field_collection
 * \collectionSet
 * \CollectionSet__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\field_collection
\collectionSet;
use Drupal\restful\Plugin\resource\ResourceEntity;

/**
 * Class CollectionSet__1_0
 * @package Drupal\nnels_api\Plugin\resource\entity\field_collection
 * \collectionSet
 *
 * @Resource(
 *   name = "collectionSet:1.0",
 *   resource = "collections",
 *   label = "collection_set",
 *   description = "Expose the DC Relation field collection associated with
 *   repository item",
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "field_collection_item",
 *     "bundles": {
 *       "field_dc_relation"
 *     },
 *   },
 *   formatter = "json_api",
 *   majorVersion = 1,
 *   minorVersion = 0
 * )
 */

class CollectionSet__1_0 extends ResourceEntity {

  protected function publicFields() {
    $public_fields = parent::publicFields();

    $public_fields['entity_id']['methods'] = array('GET');

    $public_fields['qualifier'] = array(
      'property' => 'field_dc_relation_qualifiers'
    );
    $public_fields['value'] = array(
      'property' => 'field_dc_relation_term_value'
    );

    return $public_fields;
  }
}