<?php

namespace WCHub;

use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest as SearchRequest;

/**
 * Common traits for using the HubSpot CRM API
 */
trait HubObject
{
    /**
     * HubSpot API CLient
     *
     * @var HubSpot\Factory
     */
    public $client;

    /**
     * Hubspot SimplePublicObject Response
     *
     * @var HubSpot\Client\Crm\Contacts\Model\CollectionResponseWithTotalSimplePublicObjectForwardPaging
     */
    public $response = null;

    /**
     * Hubspot PublicSearchRequestObject params
     */
    public $filter_groups = [];
    public $sorts = null;
    public $query = null;
    public $properties = null;
    public $limit = null;
    public $after = null;

    /**
     * Create an instance of this class
     */
    public static function factory($client)
    {
        $class_name = __CLASS__;
        return new $class_name($client);
    }

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function where($name, $operator, $value = null)
    {
        $filter = $this->filter_object($name, $operator, $value);
        $this->filter_groups[]['filters'] = [$filter];

        return $this;
    }

    public function and_where($name, $operator, $value)
    {
        $filter = $this->filter_object($name, $operator, $value);
        $last_group = count($this->filter_groups) ? array_key_last($this->filter_groups) : 0;
        $this->filter_groups[$last_group]['filters'][] = $filter;

        return $this;
    }

    public function filter_object($name, $operator, $value)
    {
        $filter = [
            'propertyName' => $name,
            'operator' => $operator,
        ];

        if ($value !== null) {
            $valuePropName = is_array($value) ? 'values' : 'value';
            $filter[$valuePropName] = $value;
        }

        return $filter;
    }

    public function search_request()
    {
        $request = new SearchRequest();
        $request->setFilterGroups($this->filter_groups);
        $request->setQuery($this->query);
        $request->setLimit($this->limit);
        $request->setProperties($this->properties);
        $request->setAfter($this->after);
        $request->setSorts($this->sorts);

        return $request;

    }

    public function create($params)
    {
        return $this->basicApi()->create([
            'properties' => $params
        ]);
    }

    public function update($id, $params)
    {
        return $this->basicApi()->update($id, [
            'properties' => $params
        ]);

    }

    /**
     * Searches for a hubspot object with $matching field value
     * and either updates or creates a new object with params.
     *
     * @param array $params // Paramerters of object
     * @param string $matching // Field name to match on
     * @return void
     */
    public function createOrUpdate($params, $matching = 'hs_object_id' )
    {

        $object_id = false;

        if ( isset($params[$matching]) ){
            $this->where($matching, 'EQ', $params[$matching]);
            $object = $this->first();

            $properties = $object ? $object->getProperties() : [];

            $object_id = isset($properties['hs_object_id'])
                ? $properties['hs_object_id']
                : false;
        }

        return $object_id
            ? $this->update($object_id, $params)
            : $this->create($params);
    }

    public function search()
    {
        $this->response = $this->searchApi()
            ->doSearch($this->search_request());    
    }

    public function first()
    {
        $this->search();

        if (!$this->response) {
            return null;
        }

        $results = $this->response->getResults();
        return $results ? $results[0] : null;
    }

    public function get()
    {
        $this->search();
        return $this->response ? $this->response->getResults() : null;
    }

    public function getById($id, $properties = [])
    {
        $object = $this->basicApi()->getById($id, [implode(',', $properties)], null, null, false);
        return $object ? $object->getProperties() : [];
    }

}