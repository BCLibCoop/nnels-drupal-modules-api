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
    $out = array();

    if ($data['field'] == 'field_dc_relation') {
      //Need special joins to pull out what we need from field collection
      $query = db_query(
        'SELECT n.nid
        FROM
            {node} n
        INNER JOIN {field_data_field_dc_relation} r ON
            r.entity_id = n.nid
        INNER JOIN field_data_field_dc_relation_qualifiers rqual ON
	        rqual.entity_id = r.field_dc_relation_value
        INNER JOIN {field_data_field_dc_relation_term_value} term ON
            r.field_dc_relation_value = term.entity_id
        WHERE
            r.deleted = :notdeleted
            AND n.type = :ctype
            AND term.field_dc_relation_term_value_tid = :tid
            AND rqual.field_dc_relation_qualifiers_value = :qualifier',
        array(
          ':notdeleted' => 0, ':ctype' => "repository_item", ':tid' =>
          $data['id'], ':qualifier' => 'IsPartOf')
      );
      //Create an EFQ-like result object for use downstream.
      $results['node'] = $query->fetchAllAssoc('nid');

    } else { //normal taxonomy term condition
      $query = new EntityFieldQuery();
      $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', array('repository_item'));
      $query->fieldCondition($data['field'], 'tid', $data['id'], '=');
      $results =  $query->execute();
    }

    foreach(array_keys($results['node']) as $instance => $nid) {

      $links = LocatorUtility::buildLinks($nid);
      $entity = entity_metadata_wrapper('node', $nid);
      $resource_metadata = [];

      if ($resources = $entity->field_file_resource->value()) {
        $resource_metadata = RepositoryItems__1_2::populateFC($resources);
        foreach($resource_metadata as $delta => $resource) {
          if ( isset( $resource['format_short'] ) )
            $resource_metadata[$delta] = $resource['format_short'];
        }
      }

      $out[] = [
        'nid' => $nid,
        'title' => $entity->title->value(),
        'author' => $entity->field_dc_creator->value(),
        'cover_art' => RepositoryItems__1_1::getCoverArt($nid),
        'format_short' => $resource_metadata,
        'self' => $links
      ];
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