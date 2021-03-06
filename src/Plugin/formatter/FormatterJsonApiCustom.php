<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\formatter\FormatterJsonApiCustom
 */

namespace Drupal\nnels_api\Plugin\formatter;

use Drupal\restful\Plugin\formatter\FormatterJsonApi;

/**
 * Class FormatterJsonApiCustom
 * @package Drupal\nnels_api\Plugin\formatter
 *
 * @Formatter(
 *   id = "json_api_custom",
 *   label = "JSON API Custom",
 *   description = "Output in using the JSON API format with some tweaks for
 *   NNELS API."
 * )
 */
class FormatterJsonApiCustom extends FormatterJsonApi {

  /**
   * Content Type
   *
   * @var string
   */
  protected $contentType = 'application/vnd.api+json; charset=utf-8';

  /**
   * Add HATEOAS links to list of item.
   *
   * @param array $data
   *   The data array after initial massaging.
   * @param ResourceInterface $resource
   *   The resource to use.
   * @param string $path
   *   The resource path.
   */
  protected function addHateoas(array &$data, ResourceInterface $resource = NULL, $path = NULL) {
   parent::addHateoas($data, $resource, $path);

   //modify paged links to use same keys as query parameters
    $resource = $this->getResource();
    $request = $resource->getRequest();
    $input = $request->getParsedInput();
    $page = $input['page'];

    if ($page > 1) {
      $query = $input;
      $query['page']['number'] = $page - 1;
      $data['previous'] = array(
        'title' => 'Previous',
        'href' => $resource->versionedUrl('', array('query' => $query), TRUE),
      );
    }

    // We know that there are more pages if the total count is bigger than the
    // number of items of the current request plus the number of items in
    // previous pages.
    $items_per_page = $this->calculateItemsPerPage($resource);
    $previous_items = ($page - 1) * $items_per_page;
    if (isset($data['count']) && $data['count'] > count($data['data']) + $previous_items) {
      $query = $input;
      $query['page']['number'] = $page + 1;
      $data['next'] = array(
        'title' => 'Next',
        'href' => $resource->versionedUrl('', array('query' => $query), TRUE),
      );
    }

    $self_link = $data["links"]["self"];
    preg_match('/(page%5B(number|size)%5D)=(\d+)/', $self_link, $output);
    if ($output[1]) {
      $internal = ['page%5Bnumber%5D', 'page%5Bsize%5D'];
      $external = ['page', 'range'];
      $data["links"]["self"] = str_replace($internal, $external, $self_link);
    }
  }

}
