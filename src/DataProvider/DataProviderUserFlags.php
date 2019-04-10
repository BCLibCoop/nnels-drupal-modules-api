<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\DataProvider\DataProviderUserFlags
 */

namespace Drupal\nnels_api\Plugin\DataProvider;

use Drupal\restful\Plugin\resource\DataProvider\DataProviderEntity;
use Drupal\restful\Exception\BadRequestException;

/**
 * Class DataProviderUserFlags
 * @package Drupal\nnels_api\Plugin\DataProvider
 *
 */
class DataProviderUserFlags extends DataProviderEntity {

  public function create ($object) {
    parent::create($object);
  }

  /**
   * {@inheritdoc}
   */
  protected function queryForListFilter(\EntityFieldQuery $query) {
    $fid = 0;
    $path = explode('/', $this->getRequest()->getPath());
    $flags = flag_get_flags();

    if (array_key_exists($path[1], $flags)) $fid = $flags[$path[1]]->fid;

    $query
      ->propertyCondition('uid', $this->getAccount()->uid)
      ->propertyCondition('entity_type', 'node')
      ->propertyCondition('fid', $fid);

//    if (isset($request['check_flagged'])) {
//        // Check if the user already flagged the current entity.
//        if (empty($request['entity']) || empty($request['id'])) {
//            throw new BadRequestException('You did not provide entity type or ID.');
//        }
//        // We need to check if the user already flagged this an entity.
//        $query
//            ->propertyCondition('uid', $this->getUserId())
//            ->propertyCondition('entity_type', $request['entity'])
//            ->propertyCondition('entity_id', $request['id']);
//    }
    parent::queryForListFilter($query);
  }
}