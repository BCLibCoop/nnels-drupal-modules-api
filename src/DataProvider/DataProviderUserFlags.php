<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\DataProvider\DataProviderUserFlags
 */

namespace Drupal\nnels_api\Plugin\DataProvider;

use Drupal\restful\Exception\ForbiddenException;
use Drupal\restful\Exception\UnprocessableEntityException;
use Drupal\restful\Http\RequestInterface;
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
    $this->validateBody($object);
    $entity_info = $this->getEntityInfo();
    $bundle_key = $entity_info['entity keys']['bundle'];
    // TODO: figure out how to derive the bundle when posting to a resource with
    // multiple bundles.
    $bundle = reset($this->bundles);
    $values = $bundle_key ? array($bundle_key => $bundle) : array();

    $entity = entity_create($this->entityType, $values);

    if ($this->checkEntityAccess('create', 'flag ' . $bundle, $entity) ===
      FALSE) {
      // User does not have access to create entity.
      throw new ForbiddenException('You do not have access to create a new resource.');
    }

    $create_values = $object + $values;
    $flagging= flagging_create($create_values);
    try {
      flagging_save($flagging);
    } catch (\Exception $e) {
        return _restful_build_http_api_error($e->getMessage(), restful()->getResponse());
    }
    if (! isset($flagging->flagging_id) ) {
      $flags = flag_get_user_flags('node', NULL, $this->getAccount()->uid);
      if (in_array($create_values["entity_id"], array_keys($flags[$bundle]))) {
        $response = restful()->getResponse();
        $output = _restful_build_http_api_error(new UnprocessableEntityException, $response);
        $output['title'] = "Entity already flagged for {$bundle}.";
        $response->setStatusCode(422);
        $response->setContent(drupal_json_encode($output));
        $response->send();
        return;
      }
      else {
        $wrapper = entity_metadata_wrapper($this->entityType, $flagging);
      }
    }


    // The access calls use the request method. Fake the view to be a GET.
    $old_request = $this->getRequest();
    $this->getRequest()->setMethod(RequestInterface::METHOD_GET);
    $output = array($this->view($wrapper->getIdentifier()));
    // Put the original request back to a POST.
    $this->request = $old_request;

    return $output;
  }

  public function remove($identifier) {
    parent::remove($identifier);
  }

  /**
   * Check access to CRUD an entity.
   *
   * @param string $op
   *   The operation. Allowed values are "create", "update" and "delete".
   * @param string $entity_type
   *   The entity type.
   * @param object $entity
   *   The entity object.
   *
   * @return bool
   *   TRUE or FALSE based on the access. If no access is known about the entity
   *   return NULL.
   */
  protected function checkEntityAccess($op, $entity_type, $entity) {
    $account = $this->getAccount();
    return user_access('flag bookshelf', $account);
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
  }
}