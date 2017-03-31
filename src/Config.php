<?php

namespace Datastore;

use GDS\Schema;
use GDS\Store;

class Config {


    /**
     * GDS Store instance
     *
     * @var \GDS\Store|null
     */
    private $obj_store = NULL;

    /**
     * Update the cache from Datastore
     *
     * @return array
     */
    public function getConfigString($defaultValue="")
    {
        $obj_store = $this->getStore();
        $configObj = $obj_store->query("SELECT * FROM DBConfig")->fetchOne();
        if (empty($configObj)) {
          $this->createRecord($defaultValue);
          $configObj = $obj_store->query("SELECT * FROM DBConfig")->fetchOne();
        }
        return $configObj->configString;
    }
    
    public function setConfigString($strConfig)
    {
        $obj_store = $this->getStore();
        $configObj = $obj_store->query("SELECT * FROM DBConfig")->fetchOne();
        $configObj->configString = $strConfig;
        $obj_store->upsert($configObj);
    }

    /**
     * Insert the entity (plus limit the data to the same values as the form)
     *
     * @param $str_name
     * @param $str_message
     */
    public function createRecord($strConfig)
    {
        $obj_store = $this->getStore();
        $obj_store->upsert($obj_store->createEntity([
            'created' => date('Y-m-d H:i:s'),
            'configString' => $strConfig
        ]));
    }

    /**
     * Configure and return a Store
     *
     * @return \GDS\Store
     */
    private function getStore()
    {
/*
        if(NULL === $this->obj_store) {
            $obj_google_client = \GDS\Gateway::createGoogleClient(GDS_APP_ID, GDS_ACCOUNT, GDS_KEY_FILE);
            $obj_gateway = new \GDS\Gateway($obj_google_client, GDS_APP_ID);
            $this->obj_store = new \GDS\Store($obj_gateway, $this->makeSchema());
        }
*/
        if(NULL === $this->obj_store) {
            $this->obj_store = new \GDS\Store($this->makeSchema());
        }
        return $this->obj_store;
    }

    /**
     * Build a schema for Guest book entries
     *
     * the posted datetime as an indexed field
     *
     * @return \GDS\Schema
     */
    private function makeSchema()
    {
        return (new \GDS\Schema('DBConfig'))
            ->addDatetime('created')
            ->addString('configString', FALSE);
    }
    
}