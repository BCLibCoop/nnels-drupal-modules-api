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

/**
 * Class BookShelf
 * @package Drupal\restful\Plugin\resource\entity\flagging
 *
 * @Resource(
 *   name = "bookshelf:1.0",
 *   resource = "bookshelf",
 *   label = "Bookshelf",
 *   description = "Show the user's Bookshelf",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "flagging",
 *     "bundles": {
 *       "bookshelf"
 *     },
 *   },
 *   formatter = "json_api",
 *   majorVersion = 1,
 *   minorVersion = 0,
 * )
 */
class Bookshelf__1_0 extends ResourceEntity implements ResourceInterface {
  /**
   * Overrides ResourceEntity::checkEntityAccess().
   *
   * Allow access to create "Bookshelf" resource for privileged users.
   */
//  protected function checkEntityAccess($op, $entity_type, $entity) {
//    $account = $this->getAccount();
//    return user_access('flag bookshelf', $account);
//  }

  protected function publicFields() {
    $public_fields = parent::publicFields();
    unset($public_fields['self']);
    unset($public_fields['label']);

    //$public_fields['id']['methods'] = array('GET');

    $public_fields['id'] = array(
      'property' => 'entity_id',
//      'process_callbacks' => array(
//        array($this, 'loadFileResource')
//        ),
      'resource' => array(
        'name' => 'RepositoryItems',
        'majorVersion' => 1,
        'minorVersion' => 1,
      )
    );
//    $fields['bookshelf']['callback'] = array($this,
//     'filterByUserFlagged');

    return $public_fields;
  }

  /**
   * {@inheritdoc}
   */
  protected function dataProviderClassName() {
    return '\Drupal\nnels_api\Plugin\DataProvider\DataProviderUserFlags';
  }

  public function filterByUserFlagged($i) {
    $flags = flag_get_user_flags('node', NULL, $this->getAccount()->uid);
    if (! empty($flag['bookshelf']) ) return $flags['bookshelf'];
    else return array( 'message' => 'Your bookshelf is currently empty.');
  }
}