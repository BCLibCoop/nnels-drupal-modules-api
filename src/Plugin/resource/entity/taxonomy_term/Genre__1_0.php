<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Genre__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\taxonomy_term;

use Drupal\restful\Plugin\resource\DataInterpreter\DataInterpreterInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldInterface;
use Drupal\restful\Plugin\resource\ResourceEntity;
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
 *   minorVersion = 0
 * )
 */
class Genre__1_0 extends ResourceEntity implements ResourceInterface {
  /**
   * Overrides ResourceEntity::checkEntityAccess().
   *
   * Allow access to create "Genre" resource for privileged users, as
   * we can't use entity_access() since entity_metadata_taxonomy_access()
   * denies it for a non-admin user.
   */
  protected function checkEntityAccess($op, $entity_type, $entity) {
    $account = $this->getAccount();

    return user_access('view published content ', $account);
  }

  protected function publicFields() {
    $public_fields = parent::publicFields();
    unset($public_fields['self']);

    return $public_fields;
  }
}