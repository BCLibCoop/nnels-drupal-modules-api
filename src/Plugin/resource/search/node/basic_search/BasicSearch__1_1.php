<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\search\node\basic_search\BasicSearch__1_1
 */

namespace Drupal\nnels_api\Plugin\resource\search\node\basic_search;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful_search_api\Plugin\Resource\ResourceSearchBase;

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
 *     "searchIndex": "default_node_index",
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
      'uuid_link' => array(
        'property' => 'uuid',
        'process_callbacks' => array(
          array($this, 'buildLinks')
        )
      ),
      'nid_link' => array(
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
      'version_id' => array(
        'property' => 'vid',
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
      ),
      //Should look this up in process callback
//      'formats' => array(
//        'property' => 'field_file_format',
//        'sub_property' => 'value',
//      ),
      'body' => array(
        'property' => 'body',
        'sub_property' => LANGUAGE_NONE . '::0::value',
      ),
      'human_readable_path' => array(
        'property' => 'nid',
        'process_callbacks' => array(
          array($this, 'getItemPath')
        )
      )
    );
  }

  public static function buildLinks($id) {
    $options = array('absolute' => TRUE);
    #UUID or NID
    if ( strlen($id) > 30 ) $options['query'] = array('loadByFieldName' =>
          'uuid');
    $path = url("api/v1.0/repo_items/" . $id, $options);
    return $path;
  }

  public static function getItemPath($nid) {
    return drupal_lookup_path('alias', "node/" . $nid);
  }

}
