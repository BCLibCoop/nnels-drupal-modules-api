<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\DataProvider\DataProviderUserFlags
 */

namespace Drupal\nnels_api\Plugin\DataProvider;

use Drupal\restful\Plugin\resource\DataProvider\DataProviderEntity;
use Drupal\restful\Plugin\resource\DataInterpreter\DataInterpreterEMW;
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

  public function remove($identifier) {
    parent::remove($identifier);
  }

  public function getIndexIds() {
    $result = $this
      ->getQueryForList()
      ->execute();

    if (empty($result[$this->entityType])) {
      return array();
    }

    $entity_ids = array_keys($result[$this->entityType]);
    if (empty($this->options['idField'])) {
      return $entity_ids;
    }

    // Get the list of IDs.
    //FIX This
    $resource_field = $this->fieldDefinitions->get($this->options['idField']);
    $ids = array();
    foreach ($entity_ids as $entity_id) {
      $interpreter = new DataInterpreterEMW($this->getAccount(), new \EntityDrupalWrapper($this->entityType, $entity_id));
      $ids[] = $resource_field->value($interpreter);
    }

    return $ids;
  }

  protected function getColumnFromProperty($property_name) {
    $info = flag_entity_info();
    //@todo ensure $property_name == flagging
    return $info["flagging"]["entity keys"]["id"];
  }

  /**
   * {@inheritdoc}
   */
  protected function queryForListFilter(\EntityFieldQuery $query) {
    //deprecated
    $fid = 0;
    $path = explode('/', $this->getRequest()->getPath());
    $flags = flag_get_flags();

    //Get the fid from the URL path, no longer necessary with above.
    if (array_key_exists($path[1], $flags)) $fid = $flags[$path[1]]->fid;

    $query
      ->propertyCondition('uid', $this->getAccount()->uid)
      ->propertyCondition('entity_type', 'node')
      ->propertyCondition('fid', $fid)
      ->propertyOrderBy('timestamp', 'DESC');

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
    //parent::queryForListFilter($query);
  }
}