<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Collection__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\taxonomy_term;

use Drupal\nnels_api\TaxonomyResource;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class Collection
 * @package Drupal\restful\Plugin\resource\entity\taxonomy_term
 *
 * @Resource(
 *   name = "collection:1.0",
 *   resource = "collection",
 *   label = "Collection",
 *   description = "Export the relation taxonomy term.",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "taxonomy_term",
 *     "bundles": {
 *       "relations"
 *     },
 *   },
 *   majorVersion = 1,
 *   minorVersion = 0,
 *   formatter = "json"
 * )
 */
class Collection__1_0 extends TaxonomyResource implements ResourceInterface {

  protected function publicFields(): array {
    $public_fields = parent::publicFields();
    unset($public_fields['self']);

    $public_fields['path'] = array(
      'property' => 'tid',
      'process_callbacks' => array(
        array($this, 'taxonomyNameData'),
        array($this, 'getTermResourcePath'),
      )
    );

    $public_fields['items'] = array(
      'property' => 'tid',
      'process_callbacks' => array(
        array($this, 'taxonomyFieldData'),
        array($this, 'getItemsWithTerm'),
      )
    );

    return $public_fields;
  }

  public function taxonomyNameData($tid): array {
    return array('id' =>  $tid, 'name' => 'collection', 'path_only' => TRUE);
  }

  public function taxonomyFieldData($tid): array {
    return array('id' => $tid, 'field' => 'field_dc_relation');
  }
}