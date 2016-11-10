<?php


interface IMusicStore
{
    function getProducts($url);

    function getProductPage(&$product);
}