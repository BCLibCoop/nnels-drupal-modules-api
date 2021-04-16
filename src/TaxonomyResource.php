<?php


namespace Drupal\nnels_api;

use Drupal\nnels_api\Plugin\resource\entity\node\repository_items\RepositoryItems__1_1;
use Drupal\nnels_api\Plugin\resource\entity\node\repository_items\RepositoryItems__1_2;
use Drupal\restful\Plugin\resource\ResourceEntity;
use Drupal\restful\Plugin\resource\ResourceInterface;
use EntityFieldQuery;

/**
 * Class TaxonomyResource
 * @package Drupal\nnels_api
 */
class TaxonomyResource extends ResourceEntity implements ResourceInterface {

  /**
   * @return array|void
   */
  protected function publicFields(): array {
    return parent::publicFields();
  }

  /**
   * @param $data
   * @return array
   */
  public static function getTermResourcePath($data): array {
    $term = taxonomy_term_load($data['id']);
    $taxonomy = $data['name'];
    $options = array('absolute' => TRUE);
    $version = getHighestResourceMinorVersion($taxonomy);
    $path = url("api/v{$version}/{$taxonomy}/" . $term->tid, $options);
    if ($data['path_only'] === TRUE) {
      return array(
        array(
          'self' => $path,
        )
      );
    } else { //fleshed out
      return array(
        array(
          'tid' => $data['id'],
          'label' => $term->name,
          'self' => $path,
        )
      );
    }
  }

  public static function getTerm() {

  }
  /**
   * @param $data
   * @return array
   * @throws \EntityMetadataWrapperException
   */
  public static function getItemsWithTerm($data): array {
    $query = new EntityFieldQuery();
    $out = array();
    $results = $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', array('repository_item'))
      ->fieldCondition($data['field'], 'tid', $data['id'], '=')
      ->execute();

    foreach(array_keys($results['node']) as $instance => $nid) {

      $links = LocatorUtility::buildLinks($nid);
      $entity = entity_metadata_wrapper('node', $nid);
      $resource_metadata = [];

      if ($resources = $entity->field_file_resource->value()) {
        $resource_metadata = RepositoryItems__1_2::populateFC($resources);
      }

      $out[] = array(
        'nid' => $nid,
        'title' => $entity->title->value(),
        'author' => $entity->field_dc_creator->value(),
        'cover_art' => RepositoryItems__1_1::getCoverArt($nid),
        'file_resource_hint' => $resource_metadata,
        'self' => $links
      );
    }
    return $out;
  }

  /**
   * Overrides ResourceEntity::checkEntityAccess().
   * Allow access to create "Genre" resource for privileged users, as
   * we can't use entity_access() since entity_metadata_taxonomy_access()
   * denies it for a non-admin user.
   * @param $op
   * @param $entity_type
   * @param $entity
   * @return bool
   */
  protected function checkEntityAccess($op, $entity_type, $entity): bool {
    $account = $this->getAccount();
    return user_access('view published content ', $account);
  }
}