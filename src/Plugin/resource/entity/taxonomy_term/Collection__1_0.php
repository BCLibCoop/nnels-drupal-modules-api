<?php

namespace Drupal\nnels_api\Plugin\resource\entity\taxonomy_term;

use Drupal\restful\Plugin\resource\DataInterpreter\DataInterpreterInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldInterface;
use Drupal\restful\Plugin\resource\ResourceEntity;
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
class Collection__1_0 extends ResourceEntity implements ResourceInterface {
  /**
   * Overrides ResourceEntity::checkEntityAccess().
   *
   * Allow access to create "Collection" resource for privileged users, as
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

    $public_fields['path'] = array(
      'property' => 'tid',
      'process_callbacks' => array(
        array(
          $this,
          'getRelations',
        )
      )
    );

    return $public_fields;
  }

  public static function getRelations($tid) {
    $term = taxonomy_term_load($tid);
    $options = array('absolute' => TRUE);
    $version = str_replace('_', '.',explode("__", get_called_class())[1]);

    return array(
      array(
        'label' => $term->name,
        'tid' => $term->tid,
        'path' => url("api/v{$version}/relation/" . $term->tid, $options),
      )
    );
  }

}