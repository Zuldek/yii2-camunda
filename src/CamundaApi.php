<?php
/**
 * @link https://github.com/borodulin/yii2-camunda
 * @copyright Copyright (c) 2018 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-camunda/blob/master/LICENSE
 */
namespace borodulin\camunda;

use JsonSerializable;
use yii\base\Component;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Request;

/**
 * @property Request $request
 *
 * Class CamundaApi
 * @package common\components\camunda
 */
class CamundaApi extends Component
{
    public $apiUrl = 'http://localhost:8080/engine-rest';

    public $username;

    public $password;

    /**
     * Prepare request callback function.
     * function ($this){ return new Request(); };
     * @var callable
     */
    public $prepareRequest;

    /**
     * @var Request
     */
    private $_request;

    public function init()
    {
        parent::init();
        $this->apiUrl = rtrim($this->apiUrl, '/');
    }

    public function getRequest()
    {
        if ($this->_request === null) {
            if (is_callable($this->prepareRequest)) {
                $this->_request = call_user_func($this->prepareRequest, $this);
            } else {
                $this->_request = (new Client([
                    'baseUrl' => $this->apiUrl,
                ]))->createRequest();

                if ($this->username) {
                    $this->_request->addHeaders([
                        'Authorization' => 'Basic ' . base64_encode("$this->username:$this->password")
                    ]);
                }
            }
        }
        return $this->_request;
    }

    /**
     *
     * @param string|array $json
     * @return CamundaApi
     */
    public function postJson($json)
    {
        $json = is_string($json) ? $json : Json::encode($json);
        $this->getRequest()
            ->addHeaders(['content-type' => 'application/json'])
            ->setMethod('POST')
            ->setContent($json);
        return $this;
    }

    /**
     * @return $this
     */
    public function methodDelete()
    {
        $this->getRequest()->setMethod('DELETE');
        return $this;
    }

    /**
     * @return $this
     */
    public function methodPut()
    {
        $this->getRequest()->setMethod('PUT');
        return $this;
    }

    public function methodGet()
    {
        $this->getRequest()->setMethod('GET');
        return $this;
    }

    public function methodOptions()
    {
        $this->getRequest()->setMethod('OPTIONS');
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setPostData($data = [])
    {
        $this->getRequest()->setMethod('POST')->addData($data);
        return $this;
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function execute($endpoint, $params = [])
    {
        if ($params instanceof JsonSerializable) {
            $params = $params->jsonSerialize();
        }
        $params[0] = $endpoint;

        $request = $this->getRequest();
        $this->_request = null;

        $request->setUrl($params);

        $response = $request->send();

        if ($response->isOk) {
            if ($response->getFormat() === Client::FORMAT_JSON) {
                return Json::decode($response->content);
            }
            return $response->content;
        }

        throw new Exception(json_decode($response->content, true) ?: $response->content);
    }
}
