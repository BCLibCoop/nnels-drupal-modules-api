<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\formatter\FormatterJsonApiCustom
 */

namespace Drupal\nnels_api\Plugin\formatter;

use Drupal\restful\Exception\BadRequestException;
use Drupal\restful\Plugin\formatter\FormatterJsonApi;
use Drupal\restful\Plugin\resource\DataInterpreter\DataInterpreterInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldBase;
use Drupal\restful\Plugin\resource\Field\ResourceFieldInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldResourceInterface;

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

  public function prepare(array $data) {
    // If we're returning an error then set the content type to
    // 'application/problem+json; charset=utf-8'.
    if (!empty($data['status']) && floor($data['status'] / 100) != 2) {
      $this->contentType = 'application/problem+json; charset=utf-8';
      return $data;
    }

    $extracted = $this->extractFieldValues($data);
    $included = array();
    $output = array('data' => $this->renormalize($extracted, $included));
    if ( $this->resource->getEntityType() == 'flagging' ) {
        foreach($output['data'] as $key => $value) {
          $output['data'][$key]['type'] .= "_item";
        }
    }

    $output = $this->populateIncludes($output, $included);

    if ($resource = $this->getResource()) {
      $request = $resource->getRequest();
      $data_provider = $resource->getDataProvider();
      $is_list_request = $request->isListRequest($resource->getPath());
      if ($is_list_request) {
        // Get the total number of items for the current request without
        // pagination.
        $output['meta']['count'] = $data_provider->count();
        // If there are items that were taken out during access checks,
        // report them as denied in the metadata.
        if (variable_get('restful_show_access_denied', FALSE) && ($inaccessible_records = $data_provider->getMetadata()->get('inaccessible_records'))) {
          $output['meta']['denied'] = empty($output['meta']['denied']) ? $inaccessible_records : $output['meta']['denied'] + $inaccessible_records;
        }
      }
      else {
        // For non-list requests do not return an array of one item.
        $output['data'] = reset($output['data']);
      }
      if (method_exists($resource, 'additionalHateoas')) {
        $output = array_merge($output, $resource->additionalHateoas($output));
      }

      // Add HATEOAS to the output.
      $this->addHateoas($output);
    }

    return $output;
  }

}
