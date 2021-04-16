<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Subject__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\taxonomy_term;

use Drupal\nnels_api\TaxonomyResource;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class Subject
 * @package Drupal\restful\Plugin\resource\entity\taxonomy_term
 *
 * @Resource(
 *   name = "subject:1.0",
 *   resource = "subject",
 *   label = "Subject",
 *   description = "Export the subject taxonomy term.",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "taxonomy_term",
 *     "bundles": {
 *       "subject"
 *     },
 *   },
 *   majorVersion = 1,
 *   minorVersion = 0,
 *   formatter = "json_api_custom"
 * )
 */
class Subject__1_0 extends TaxonomyResource implements ResourceInterface {

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
    return array('id' =>  $tid, 'name' => 'subject', 'path_only' => TRUE);
  }

  public static function taxonomyFieldData($tid): array {
    return array('id' => $tid, 'field' => 'field_subject');
  }
}