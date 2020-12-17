<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\search\node\basic_search\BasicSearch__1_2
 */

namespace Drupal\nnels_api\Plugin\resource\search\node\basic_search;
use Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Genre__1_0;
use Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Subject__1_0;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful_search_api\Plugin\Resource\ResourceSearchBase;
use Drupal\nnels_api\Plugin\resource\search\node\basic_search
\BasicSearch__1_1;

/**
 * Class BasicSearch__1_2
 * @package Drupal\nnels_api\Plugin\resource\search\node\basic_search
 *
 * @Resource(
 *   name = "basicSearch:1.2",
 *   resource = "basicSearch",
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
 *   minorVersion = 2,
 *   formatter = "json"
 * )
 */
class BasicSearch__1_2 extends BasicSearch__1_1 {

  /**
   * Overrides Resource::publicFields().
   */
  public function publicFields() {
    $public_fields = parent::publicFields();

    /* Inherits relevance, field_file_resource, human_readable_path

    /* @todo check file_resource

    /* Override in this version */
    unset($public_fields['field_file_resource']);
    unset($public_fields['field_genre']);

    $public_fields['abstract'] =
      array(
        'property' => 'body',
        'sub_property' => LANGUAGE_NONE . '::0::value',
        'process_callbacks' => array(
          'strip_tags',
          'trim'
        ),
      );

    $public_fields['author'] =
      array(
        'property' => 'field_dc_creator',
        'sub_property' => LANGUAGE_NONE . '::0::value',
      );

    $public_fields['genre'] =
      array(
        'property' => 'field_genre',
        'sub_property' => LANGUAGE_NONE . '::0::tid',
        'process_callbacks' => array(
          array(
            $this, "getGenres"
          ),
        ),
      );
    $public_fields['subjects'] =
      array(
        'property' => 'field_subject',
        'sub_property' => LANGUAGE_NONE . '::0::tid',
        'process_callbacks' => array(
          array(
            $this, "getSubjects"
          ),
        ),
      );

    $public_fields['path_alias'] = $public_fields['human_readable_path'];
    unset($public_fields['human_readable_path']);

    return $public_fields;
  }

  public function getGenres($tid) {
    return Genre__1_0
      ::getGenres($tid);
  }

  public function getSubjects($tid) {
    return Subject__1_0
      ::getSubjects($tid);
  }
}
