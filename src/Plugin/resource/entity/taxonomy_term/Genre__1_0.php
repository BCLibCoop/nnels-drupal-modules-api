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
use EntityFieldQuery;

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

    $public_fields['path'] = array(
      'property' => 'tid',
      'process_callbacks' => array(
        array(
          $this,
          'getGenres',
        )
      )
    );

    $public_fields['items'] = array(
      'property' => 'tid',
      'process_callbacks' => array(
        array(
          $this,
          'getItemsWithTerm',
        )
      )
    );

    return $public_fields;
  }

  public static function getGenres($tid): array {
    $term = taxonomy_term_load($tid);
    $options = array('absolute' => TRUE);
    $version = str_replace('_', '.',explode("__", get_called_class())[1]);

    return array(
      array(
      'label' => $term->name,
      'tid' => $term->tid,
      'path' => url("api/v{$version}/genre/" . $term->tid, $options),
      )
    );
  }

  public static function getItemsWithTerm($tid): array {
    $query = new EntityFieldQuery();
    $out = array();
    $results = $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', array('repository_item'))
      ->fieldCondition('field_genre', 'tid', $tid, '=')
      ->execute();
    //->getResourceManager()
    foreach(array_keys($results['node']) as $nid) {
//      $out[]['items'] = url("api/v1.2/repositoryItems/" .
//        $nid, array('absolute' => TRUE));
      $out[] = array('nid' => json_encode($nid));
    }
    return $out;
  }
}