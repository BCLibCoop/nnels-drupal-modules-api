<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Genre__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\taxonomy_term;

use Drupal\nnels_api\TaxonomyResource;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class Genre
 * @package Drupal\restful\Plugin\resource\entity\taxonomy_term
 *
 * @Resource(
 *   name = "genre:1.0",
 *   resource = "genre",
 *   label = "Genre",
 *   description = "Export the genre taxonomy term.",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "taxonomy_term",
 *     "bundles": {
 *       "genre"
 *     },
 *   },
 *   majorVersion = 1,
 *   minorVersion = 0,
 *   formatter = "json"
 * )
 */
class Genre__1_0 extends TaxonomyResource implements ResourceInterface {

  /**
   * @return array
   */
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

  public static function taxonomyNameData($tid): array {
    return array('id' =>  $tid, 'name' => 'genre', 'path_only' => TRUE);
  }

  public static function taxonomyFieldData($tid): array {
    return array('id' => $tid, 'field' => 'field_genre');
  }
}