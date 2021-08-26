<?php

/**
* @file
* Contains \Drupal\nnels_api\Plugin\resource\entity\node\repository_items
 * \RepositoryItems__1_1
*/

namespace Drupal\nnels_api\Plugin\resource\entity\node\repository_items;

use Drupal\cals_s3\NNELSStreamWrapper;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Plugin\resource\ResourceNode;

/**
* Class RepositoryItems
* @package Drupal\nnels_api\Plugin\resource\entity\node\repository_items
*
* @Resource(
*   name = "repositoryItems:1.1",
*   resource = "repositoryItems",
*   label = "Repository Items",
*   description = "Expose the repository_item content type.",
*   authenticationOptional = TRUE,
*   dataProvider = {
*     "entityType": "node",
*     "bundles": {
*       "repository_item"
*     },
*   },
*   formatter = "json_api",
*   majorVersion = 1,
*   minorVersion = 1,
* )
*/
class RepositoryItems__1_1 extends Repo_Items__1_0 {

  // TODO: Document the use of the idField.
  /**
   * {@inheritdoc}
   */
  protected function publicFields() {
    $public_fields = parent::publicFields();

    $public_fields['title'] = $public_fields['label'];
    unset($public_fields['label']);

    $public_fields['abstract'] = array(
      'property' => 'body',
      'sub_property' => 'value',
      'process_callbacks' => array(
        'strip_tags'
        //,'remove newlines' //@todo
      )
    );

    $field_keys = array(
      'creators' => 'field_dc_creator',
      'languages' => 'field_iso_language',
      'isbn' => 'field_isbn',
      'collections' => 'field_dc_relation',
      'uuid' => 'uuid',
    );

    foreach ($field_keys as $label => $field) {
      $public_fields[$label] = array('property' => $field);
    }

    $public_fields['collections']['process_callbacks'] = array(
      array( $this, 'formatRelation')
    );

    $public_fields['published_date'] = array(
      'property' => 'field_date',
      'process_callbacks' => array(
        array( $this, 'formatDCdate')
      )
    );

    $public_fields['genre'] = array(
      'property' => 'field_genre',
      'wrapper_method' => 'label',
      'resource' => array(
        'name' => 'genre',
        'majorVersion' => 1,
        'minorVersion' => 0,
      ),
    );

    $public_fields['subject'] = array(
      'property' => 'field_subject',
      'wrapper_method' => 'label',
      'resource' => array(
        'name' => 'subject',
        'majorVersion' => 1,
        'minorVersion' => 0,
      ),
    );

    $public_fields['file'] = array(
      'property' => 'field_file_resource',
      'class' => '\Drupal\restful\Plugin\resource\Field\ResourceFieldEntityReference',
      'entityType' => 'field_collection_item',
      'resource' => array(
        'name' => 'fileResources',
        'majorVersion' => '1',
        'minorVersion' => '1',
      )
    );

    $public_fields['cover_art'] = array(
      'property' => 'nid',
      'process_callbacks' => array(array($this, 'getCoverArt'))
    );

    # @todo the relationship fields suck

    unset($public_fields['self']);

    $public_fields['human_readable_path'] = array(
      'property' => 'nid',
      'process_callbacks' => array(
        array($this, 'getItemPath')
      )
    );

    return $public_fields;
  }

  /**
   * Override queryCount in DataProvider which is wrong for multiple IDs
   * @param $output
   * @return mixed
   */
  public function additionalHateoas($output) {
    if (isset($output['meta'])) {
      $data_elements_count = count($output['data']);
      $output['meta']['count'] = $data_elements_count < $output['meta']['count'] ?
        $data_elements_count : $output['meta']['count'];
    }
    return $output;
  }

  /**
   * @param $field
   * @return array
   */
  public static function formatRelation($field) {
    $output = array();
    foreach ($field as $instance) {

      $entity = entity_metadata_wrapper('field_collection_item', $instance);
      if ($entity->field_dc_relation_qualifiers->value() == 'IsPartOf') {

        $term_object = $entity->field_dc_relation_term_value->value(); //object

        $output[] = array( //ensure these are instances
          //'item_id' => $entity->item_id->value(),
          'term_id' => $term_object->tid,
          'vocabulary_id' => $term_object->vid,
          'label' => $term_object->name,
          'name' => $term_object->machine_name
        );
      }
    }
    return $output;
  }

  /**
   * @param $field
   * @return array
   */
  public static function formatDCdate($field) {
    $output = array();

    foreach ($field as $instance) {
      $entity = entity_metadata_wrapper('field_collection_item', $instance);

      $output[] = array(
        'qualifier' => $entity->field_qualifier_date->value(),
        'dc_date' => $entity->field_dc_date->value()
      );
    }
    return $output;
  }

  public static function getItemPath($nid) {
    return drupal_lookup_path('alias', "node/" . $nid);
  }

  /**
   * @param $nid
   * @return array
   */
  public static function getCoverArt($nid) {
    module_load_include('inc', 'nnels_content_cafe', 'nnels_content_cafe');
    $cover_info =
      nnels_content_cafe_select_multiple_content_cafe_jacket_rows_by_nids
      (array($nid));

    $uri = NULL;
    $covers = array();

    if ($cover_info) {
      $jacket = array_shift($cover_info[$nid]);
      $uri = file_load($jacket->fid)->uri;

      module_load_include('inc', 'cals_s3', 'cals_s3.NNELSStreamWrapper.class');
      $stream = new NNELSStreamWrapper();
      $stream->setUri($uri);

      $covers = array(
        'results' => array(
          'image_style' => '50x75',
          'path' => $stream->getExternalUrl()
        ),
        'single' => array(
          'image_style' => '200x300',
          'path' => $stream->getExternalUrl()
        ),
      );
    }
    return array(
      $covers,
    );
  }
}