<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\search\node\basic_search\BasicSearch__1_1
 */

namespace Drupal\nnels_api\Plugin\resource\search\node\basic_search;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful_search_api\Plugin\Resource\ResourceSearchBase;
use Drupal\nnels_api\Plugin\resource\entity\node\repository_items\RepositoryItems__1_1;

/**
 * Class BasicSearch__1_1
 * @package Drupal\nnels_api\Plugin\resource\search\node\basic_search
 *
 * @Resource(
 *   name = "basic_search:1.1",
 *   resource = "basic_search",
 *   label = "Basic Search",
 *   description = "Provides basic info doing Search API searches.",
 *   dataProvider = {
 *     "searchIndex": "solr_repository_search",
 *     "idField": "entity_id"
 *   },
 *   renderCache = {
 *     "render": FALSE,
 *   },
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   majorVersion = 1,
 *   minorVersion = 1,
 *   formatter = "json"
 * )
 */
class BasicSearch__1_1 extends ResourceSearchBase implements ResourceInterface {

  /**
   * Overrides Resource::publicFields().
   */
  public function publicFields() {

    return array(
      'self' => array(
        'property' => 'nid',
        'process_callbacks' => array(
          array($this, 'buildLinks')
        )
      ),
      'entity_id' => array(
        'property' => 'search_api_id',
        'process_callbacks' => array(
          'intVal',
        ),
      ),
      'relevance' => array(
        'property' => 'search_api_relevance',
      ),
      'title' => array(
        'property' => 'title',
      ),
      'author' => array(
        'property' => 'field_dc_creator',
        //Remove format
      ),
      'abstract' => array(
        'property' => 'body',
        'sub_property' => LANGUAGE_NONE . '::0::value',
      ),
      'field_genre' => array(
        'property' => 'field_genre',
      ),
      'field_file_resource' => array(
            'property' => 'field_file_resource',
            'process_callbacks' => array(
              array($this, 'loadFileResource')
            ),
            //'class' => '\Drupal\restful\Plugin\resource\Field
        //\ResourceFieldCollection',
            //'class' => '\Drupal\nnels_api\Plugin\resource\entity
        //\field_collection\fileResources\FileResources__1_0',
            //'entityType' => 'field_collection_item',
            //'wrapperMethod' => 'getIdentifier',
            //'wrapperMethodOnEntity' => TRUE,
            'resource' => array(
              'name' => 'fileResources',
              'majorVersion' => 1,
              'minorVersion' => 0,
            )
      ),
      'human_readable_path' => array(
        'property' => 'nid',
        'process_callbacks' => array(
          array($this, 'getItemPath')
        )
      ),
    );
  }

  public static function buildLinks($nid) {
    //@todo move this to a generic helper class eventually
    $uuid = entity_get_uuid_by_id('node', array($nid) );
    $options = array('absolute' => TRUE);
    $options['query'] = array('loadByFieldName' =>
      'uuid');
    $version = getHighestResourceMinorVersion('repositoryItems');
    $uuid_path = url("api/v{$version}/repositoryItems/" . $uuid[$nid], $options);

    unset($options['query']);
    $nid_path = url("api/v{$version}/repositoryItems/" . $nid, $options);

    return array(
      'nid_link' => $nid_path,
      'uuid_link' => $uuid_path,
    );
  }

  public static function getItemPath($nid) {
    return drupal_lookup_path('alias', 'node/' . $nid);
  }

  public static function loadFileResource($entity_ids) {
    global $language ;
    $lang = $language->language;

    $version = '1.0'; //what is way to get resource version?
    if ($lang == 'en') $lang = 'und';
    foreach ($entity_ids[$lang] as $entity_id) {
//      $ent = entity_metadata_wrapper('field_collection_item', $entity_id['value']);
//      $format = $ent->field_file_format->label();
//      $avail_status = $ent->field_availability_status->label();
//      $path = url("api/v1.0/fileResources/". $entity_id, array('absolute' =>
//        TRUE));
//      $size = $ent->
//      $performers[] = $ent->field_performer->value();
    }

}
}
