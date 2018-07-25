<?php

class FiltersData
{
    static private $instance;
    static private $filtersData = [];

    static public function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setFiltersData($key, $data) {
        self::$filtersData[$key] = $data;
    }

    public function getFiltersData($key) {
        return self::$filtersData[$key];
    }

    public function isSetKey($key) {
        if (isset(self::$filtersData[$key]) && self::$filtersData[$key] != '') return true;
        else return false;
    }

    public function deleteFiltersData() {
        self::$filtersData = [];
    }
}