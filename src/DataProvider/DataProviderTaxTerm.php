<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\DataProvider\DataProviderNodeExtra
 */

namespace Drupal\nnels_api\Plugin\DataProvider;

use Drupal\restful\Exception\UnprocessableEntityException;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderEntity;
use Drupal\restful\Exception\BadRequestException;

/**
 * Class DataProviderTaxTerm
 * @package Drupal\nnels_api\Plugin\DataProvider
 *
 */
class DataProviderTaxTerm extends DataProviderEntity {

  /**
   * Return default 10 items per page
   */
  public function getRange() {
    return 10;
  }

  /**
   * Parses the request object to get the pagination options.
   *
   * @return array
   *   A numeric array with the offset and length options.
   *
   * @throws BadRequestException
   * @throws UnprocessableEntityException
   */
  protected function parseRequestForListPagination() {
    $pager_input = $this->getRequest()->getPagerInput();

    $page = isset($pager_input['number']) ? $pager_input['number'] : 1;
    if (!ctype_digit((string) $page) || $page < 1) {
      throw new BadRequestException('"Page" property should be numeric and equal or higher than 1.');
    }

    $range = isset($pager_input['size']) ? (int) $pager_input['size'] : $this->getRange();
    $range = $range > $this->getRange() ? $this->getRange() : $range;
    if (!ctype_digit((string) $range) || $range < 1) {
      throw new BadRequestException('"Range" property should be numeric and equal or higher than 1.');
    }

    $url_params = empty($this->options['urlParams']) ? array() : $this->options['urlParams'];
    if (isset($url_params['range']) && !$url_params['range']) {
      throw new UnprocessableEntityException('Range parameters have been disabled in server configuration.');
    }

    $offset = ($page - 1) * $range;
    return array($offset, $range);
  }
}