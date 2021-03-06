<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\DataProvider\DataProviderNodeExtra
 */

namespace Drupal\nnels_api\Plugin\DataProvider;

use Drupal\restful\Plugin\resource\DataProvider\DataProviderNode;
use Drupal\restful\Exception\BadRequestException;

/**
 * Class DataProviderNodeExtra
 * @package Drupal\nnels_api\Plugin\DataProvider
 *
 */
class DataProviderNodeExtra extends DataProviderNode {

  /**
  * {@inheritdoc}
  * Converts query string filter to entity condition
  */
  protected function queryForListFilter(\EntityFieldQuery $query) {

    foreach ($this->parseRequestForListFilter() as $filter) {
        // Map path_alias to the node id.
        if ($filter['public_field'] == 'path_alias') {
          $node_path = explode('/', drupal_get_normal_path($filter['value'][0]));
          $nid = $node_path[1];
          $query->entityCondition('entity_id', $nid, $filter['operator'][0]);
          // Unset the filter to prevent problems when we call parent function.
          $this->getRequest()->setParsedInput(array());
        }
      }
    parent::queryForListFilter($query);
  }

}