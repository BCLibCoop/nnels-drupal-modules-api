<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\flagging\BookShelf__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\flagging;

use Drupal\restful\Plugin\resource\DataInterpreter\DataInterpreterInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldInterface;
use Drupal\restful\Plugin\resource\Resource;
use Drupal\restful\Plugin\resource\ResourceEntity;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\nnels_api\Plugin\resource\search\node\basic_search
\BasicSearch__1_1;

/**
 * Class BookShelf
 * @package Drupal\restful\Plugin\resource\entity\flagging
 *
 * @Resource(
 *   name = "bookshelf:1.0",
 *   resource = "bookshelf",
 *   label = "Bookshelf Items",
 *   description = "A user's listing of items to save for later.",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "flagging",
 *     "bundles": {
 *       "bookshelf"
 *     },
 *     "idField": "id",
 *   },
 *   formatter = "json_api_custom",
 *   majorVersion = 1,
 *   minorVersion = 0,
 * )
 */

class Bookshelf__1_0 extends ResourceEntity implements ResourceInterface {

  protected function publicFields() {
    $public_fields = parent::publicFields();
    unset($public_fields['self']);
    unset($public_fields['label']);

    $public_fields['id']['methods'] = array('GET', 'POST', 'PATCH', 'OPTIONS');

    $public_fields['id'] = array(
      'property' => 'flagging_id',
    );

    $public_fields['repositoryItem'] = array(
      'property' => 'entity_id',
      'process_callbacks' => array(
        array(
          $this,
          'loadNodeData',
        )
      ),
    );

    return $public_fields;
  }

  /**
   * {@inheritdoc}
   */
  protected function dataProviderClassName() {
    return '\Drupal\nnels_api\Plugin\DataProvider\DataProviderUserFlags';
  }

  /**
   * @param $entity_id
   * @return array
   */
  public function loadNodeData($entity_id) {
    $wrapped = entity_metadata_wrapper('node', $entity_id);
    $formats = array();

    foreach ($wrapped->field_file_resource->value() as $index => $instance)
    {
      $formats[] = entity_metadata_wrapper('field_collection_item', $instance)
        ->field_file_format->label();
    }
    return array(
      'title' => $wrapped->title_field->value(),
      'author' => $wrapped->field_dc_creator->value(),
      'formats' => $formats,
      'self' => \Drupal\nnels_api\Plugin\resource\search\node\basic_search
      \BasicSearch__1_1::buildLinks($entity_id),
    );
  }

  public function filterByUserFlagged($i) {
    $flags = flag_get_user_flags('node', NULL, $this->getAccount()->uid);
    if (! empty($flag['bookshelf']) ) return $flags['bookshelf'];
    else return array( 'message' => 'Your bookshelf is currently empty.');
  }
}