<?php

/**
* @file
* Contains \Drupal\nnels_api\Plugin\resource\entity\node\repository_items\Repo_Items__1_0
*/

namespace Drupal\nnels_api\Plugin\resource\entity\node\repository_items;

use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Plugin\resource\ResourceNode;

/**
* Class RepoItems
* @package Drupal\nnels_api\Plugin\resource\entity\node\repository_items
*
* @Resource(
*   name = "repo_items:1.0",
*   resource = "repo_items",
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
*   minorVersion = 0,
*   renderCache = FALSE
* )
*/
class Repo_Items__1_0 extends ResourceNode implements ResourceInterface {

  // TODO: Document the use of the idField.
  /**
   * {@inheritdoc}
   */
  protected function publicFields() {
    $public_fields = parent::publicFields();


    //Rename field label
    //$public_fields['title'] = $public_fields['tochange'];

    //http methods allowed per field
    $public_fields['id']['methods'] = array('GET');


    //expose text field
    $public_fields['abstract'] = array(
      'property' => 'body',
      'sub_property' => 'value',
      'process_callbacks' => array(
        'strip_tags' //strip html
        //,'remove newlines' //@todo
      )
    );

    $field_keys = array(
      'creators' => 'field_dc_creator',
      'languages' => 'field_iso_language',
      'isbn' => 'field_isbn',
      //'relation' => 'field_dc_relation',
      'uuid' => 'uuid'
    );

    foreach ($field_keys as $label => $field) {
      $public_fields[$label] = array('property' => $field);
    }

    $public_fields['published_date'] = array(
      'property' => 'field_date',
      //'sub_property' => 'field_qualifier_date'
    );

//    $public_fields['file_resources'] = array(
//      'property' => 'field_file_resource',
//      //'class' => '\Drupal\restful\Plugin\resource\Field
//      //\ResourceFieldCollection',
//      //'class' => '\Drupal\nnels_api\Plugin\resource\entity\field_collection
//      //\fileResources\FileResources__1_0',
//      //'entityType' => 'field_collection_item',
//      //'wrapperMethod' => 'getIdentifier',
//      //'wrapperMethodOnEntity' => TRUE,
//      'resource' => array(
//        'name' => 'fileResources',
//        'majorVersion' => 1,
//        'minorVersion' => 0,
//      )
//    );

    $public_fields['file_resources'] = array(
      'property' => 'nid',
      'process_callbacks' => array(
        array($this, 'getFiles'))
    );
//    $public_fields['file_resources'] = array(
//      'property' => 'field_file_resource',
////      'process_callbacks' => array(
////        $this, 'getFiles'),
//      //'sub_property' => 'field_s3_path'
//    );

    unset($public_fields['self']);
    /*$to_unset = array(
      'published_date' =>array(
        'item_id',
        'revision_id',
        'field_name',
        'default_revision',
        'archived',
        'uuid',
        'rdf_mapping',
        'field_dc_date' => array(
          'und' => array('format')
        )

    unset($public_fields['published_date'][{$to_unset}]); */

    return $public_fields;
  }
  /*
   * hefty helper method - use statically from FileResources later
   */
  public static function getFiles($nid) {

    module_load_include('inc', 'cals_s3', 'cals_s3.NNELSStreamWrapper.class');
    $files = array();
    $loaded = node_load($nid);
    $entities = $loaded->field_file_resource;

    foreach ($entities['und'] as $entity) {
      $entity_id = $entity['value'];
      $fc_wrapped = entity_metadata_wrapper('field_collection_item',
        $entity_id);

      //Check user perm for "download restricted s3 item"
      //Otherwise they can only view format and general info about it

      $stream = new \Drupal\cals_s3\NNELSStreamWrapper;
      $stream->setUri($fc_wrapped->field_s3_path->value());
      $s3_path_signed = $stream->getExternalUrl();

      $multi_fields = array(
        'field_performer'
      );

      foreach($multi_fields as $field) {
        foreach ($fc_wrapped->$field->getIterator() as $delta =>
                 $wrapped) {
          $files["$entity_id"]['narrator'][] = array($delta => $wrapped->value
          ());
        }
      }

      $files["$entity_id"]['self'] = '/api/v1.0/fileResources/' . $entity['value'];
      $files["$entity_id"]['format'] = $fc_wrapped->field_file_format->label();
      $files["$entity_id"]['s3_path_signed'] = $s3_path_signed;
    }

    return $files;
  }
}