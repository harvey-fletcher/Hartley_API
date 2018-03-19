<?php

    //Database connection
    include_once('database.php');

    //Standard error messages
    $endpoint_error = json_encode(array("status"=>400, "info"=>"Not an endpoint. Please consult the API docs."), JSON_FORCE_OBJECT);
    $too_few_params = json_encode(array("status"=>400, "info"=>"Not enough paramaters. Please consult the API docs."), JSON_FORCE_OBJECT);

    //These are the endpoints recognised by the API
    $endpoints = array(
            "/products/list",
            "/products/add",
            "/products/delete",
            "/products/update",
            "/orders/place",
            "/orders/complete",
            "/orders/list",
        );

    //Change this to true to show query in the API output
    $debug = false;

?>
