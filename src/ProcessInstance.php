<?php
/**
 * @link https://github.com/borodulin/yii2-camunda
 * @copyright Copyright (c) 2018 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-camunda/blob/master/LICENSE
 */
namespace borodulin\camunda;

use yii\helpers\ArrayHelper;

/**
 * Class ProcessInstance
 * @package borodulin\camunda
 */
class ProcessInstance extends Module
{
    /**
     * Deletes a running process instance.
     * @param $id
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function delete($id)
    {
        return $this->getApi()
            ->methodDelete()
            ->execute("process-instance/$id");
    }

    /**
     * Retrieves an Activity Instance (Tree) for a given process instance.
     * @param $id
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getActivityInstance($id)
    {
        return $this->getApi()
            ->execute("process-instance/$id/activity-instances");
    }


    /**
     * Query for the number of process instances that fulfill given parameters.
     * Takes the same parameters as the get instances method.
     * @param array $params
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getListCount($params = [])
    {
        $result = $this->getApi()
            ->execute("process-instance/count", $params);
        return ArrayHelper::getValue($result, 'count');
    }

    /**
     * Query for process instances that fulfill given parameters. Parameters may be static as well as dynamic
     * runtime properties of process instances. The size of the result set can be retrieved by using
     * the get instances count method.
     * @param array $params
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getList($params = [])
    {
        return $this->getApi()
            ->execute("process-instance", $params);
    }

    /**
     * Retrieves a single process instance according to the ProcessInstance interface in the engine.
     * @param $id
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getInstance($id)
    {
        return $this->getApi()
            ->execute("process-instance/$id");
    }

    /**
     * Submits a list of modification instructions to change a process instance's execution state.
     * A modification instruction is one of the following:
     *  Starting execution before an activity
     *  Starting execution after an activity on its single outgoing sequence flow
     *  Starting execution on a specific sequence flow
     * Cancelling an activity instance, transition instance, or all instances (activity or transition) for an activity
     * Instructions are executed immediately and in the order they are provided in this request's body.
     * Variables can be provided with every starting instruction.
     * The exact semantics of modification can be read about in the user guide.
     * @param $id
     * @param $params
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function modify($id, $params)
    {
        return $this->getApi()
            ->postJson($params)
            ->execute("process-instance/$id/modification");
    }

    /**
     * Query for the number of process instances that fulfill the given parameters. This method takes the same
     * message body as the POST query and therefore it is slightly more powerful than the GET query count.
     * @param $params
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getListCountPost($params)
    {
        $result = $this->getApi()
            ->postJson($params)
            ->execute("process-instance/count");
        return ArrayHelper::getValue($result, 'count');
    }

    /**
     * Query for process instances that fulfill given parameters through a JSON object. This method is slightly
     * more powerful than the GET query because it allows filtering by multiple process variables
     * of types String, Number or Boolean.
     * @param $params
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getListPost($params)
    {
        return $this->getApi()
            ->postJson($params)
            ->execute("process-instance");
    }

    /**
     * Activate or suspend a given process instance.
     * @param $id
     * @param $suspended
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function suspendById($id, $suspended)
    {
        return $this->getApi()->postJson([
            'suspended' => $suspended
        ])
            ->methodPut()
            ->execute("process-instance/$id/suspended");
    }

    /**
     * Activate or suspend process instances with the given process definition id.
     * @param $processDefinitionId
     * @param $suspended
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function suspendByDefinitionId($processDefinitionId, $suspended)
    {
        return $this->getApi()->postJson([
            'processDefinitionId' => $processDefinitionId,
            'suspended' => $suspended
        ])
            ->methodPut()
            ->execute("process-instance/suspended");
    }

    /**
     * Activate or suspend process instances with the given process definition key.
     * @param $processDefinitionKey
     * @param $suspended
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function suspendByDefinitionKey($processDefinitionKey, $suspended)
    {
        return $this->getApi()->postJson([
            'processDefinitionKey' => $processDefinitionKey,
            'suspended' => $suspended
        ])
            ->methodPut()
            ->execute("process-instance/suspended");
    }

    /**
     * Deletes a variable of a given process instance.
     * @param $id
     * @param $varName
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function deleteVariable($id, $varName)
    {
        return $this->getApi()
            ->methodDelete()
            ->execute("process-instance/{$id}/variables/{$varName}");
    }

    /**
     * Sets the serialized value for a binary variable or the binary value for a file variable.
     * @param $id
     * @param $varName
     * @param $data
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setVariableBinary($id, $varName, $data)
    {
        return $this->getApi()->postJson([
            'data' => $data
        ])
            ->execute("process-instance/{$id}/variables/{$varName}/data");
    }

    /**
     * Retrieves a variable of a given process instance.
     * @param $id
     * @param $varName
     * @param bool $deserializeValue
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getVariable($id, $varName, $deserializeValue = false)
    {
        return $this->getApi()
            ->execute("process-instance/{$id}/variables/{$varName}", [
                'deserializeValue' => $deserializeValue ? 'true' : 'false',
            ]);
    }

    /**
     * Retrieves all variables of a given process instance.
     * @param $id
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getVariables($id, $deserializeValues = false)
    {
        return $this->getApi()
            ->execute("process-instance/$id/variables", [
                'deserializeValues' => $deserializeValues ? 'true' : 'false',
            ]);
    }

    /**
     * Updates or deletes the variables of a process instance. Updates precede deletions.
     * So, if a variable is updated AND deleted, the deletion overrides the update.
     * @param $id
     * @param $modifications
     * @param array $deletions
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function modifyVariables($id, $modifications, $deletions = [])
    {
        return $this->getApi()->postJson([
            'modifications' => $this->translateVariables($modifications),
            'deletions' => $deletions
        ])
            ->execute("process-instance/{$id}/variables");
    }

    /**
     * Sets a variable of a given process instance.
     * @param $id
     * @param $varName
     * @param $value
     * @param null $type
     * @param null $valueInfo
     * @return mixed
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function updateVariable($id, $varName, $value, $type = null, $valueInfo = null)
    {
        $type = is_null($type) ? gettype($value) : $type;
        return $this->getApi()->postJson([
            'value' => $value,
            'type' => $type,
            'valueInfo' => $valueInfo
        ])
            ->methodPut()
            ->execute("process-instance/$id/variables/$varName");
    }



}