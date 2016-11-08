<?php


interface IMusicStore
{
    public function getProducts($url, $id_category, $weight, $name_suffix = null, $filters = array());

}