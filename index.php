<?php

    //import all the settings and all the commonly used functions
    include_once('settings.php');
    include_once('functions.php');

    //Break up the requested URL into parameters
    $parameters = array_filter(explode("/",urldecode($_SERVER['REQUEST_URI'])), 'strlen');
    array_shift($parameters);

    //Check that the endpoint that the client has requested is valid
    $requested = "/" . implode("/", array_slice($parameters, 0, 2));
    if(in_array($requested, $endpoints) == false){
        echo $GLOBALS['endpoint_error'];
        die();
    }

    //Execute the requested function
    $parameters[0]($parameters);

    //Function for products
    function products($parameters){
        if($parameters[1] == "list"){
            $params = array(
                    "verb" => "SELECT",
                    "columns" => "*",
                    "table" => "products"
                );
        } else if($parameters[1] == "add"){
            if(sizeof($parameters) < 4){
                echo $GLOBALS['too_few_params'];
                die();
            }

            $params = array(
                    "verb" => "INSERT",
                    "table" => "products",
                    "values" => array(
                            $parameters[2],
                            0,
                            $parameters[3],
                        ),
                );
        } else if($parameters[1] == "delete"){
            if(sizeof($parameters) != 3){
                echo $GLOBALS['too_few_params'];
                die();
            }

            $params = array(
                    "verb" => "DELETE",
                    "table" => "products",
                    "conditions" => array(
                        "id=" . $parameters[2],
                    ),
                    "conjunctives" => array(
                        "",
                    ),
                );
        } else if($parameters[1] == "update"){
            $params = array(
                    "verb" => "UPDATE",
                    "table" => "products",
                    "values" => array(
                        $parameters[2] => $parameters[3],
                    ),
                    "conditions" => array(
                        $parameters[4] . "=" . $parameters[5],
                    ),
                    "conjunctives" => array(),
               );
        } else {
            echo $GLOBALS['endpoint_error'];
        }

        display(query($params));
    }

    function orders($parameters){
        if($parameters[1] == "place"){
             if(sizeof($parameters) != 4){
                 echo $GLOBALS['too_few_params'];
                 die();
             }

             $params = array(
                     "verb" => "INSERT",
                     "table" => "orders",
                     "values" => array(
                         json_encode(explode(",", $parameters[3])),
                         $parameters[2],
                         0,
                     ),
                 );

              display(query($params));
        } else if($parameters[1] == "complete"){
             if(sizeof($parameters) != 3){
                 echo $GLOBALS['too_few_params'];
                 die();
             }

             $params = array(
                     "verb" => "UPDATE",
                     "table" => "orders",
                     "values" => array(
                             "completed" => 1,
                         ),
                     "conditions" => array(
                             "id=" . $parameters[2],
                         ),
                     "conjunctives" => array(),
                 );

             display(query($params));
        } else if($parameters[1] == "list"){
             $params = array(
                     "verb" => "SELECT",
                     "table" => "orders",
                     "columns" => "*",
                     "conditions" => array(
                         "completed=0",
                     ),
                     "conjunctives" => array(),
                 );

             $orders = query($params);

             $order_index = 0;

             foreach($orders['results'] as $order){
                 $list = "(";

                 foreach(json_decode($order['product_list']) as $product){
                     $list.= $product . ',';
                 }

                 $list =  substr($list, 0, -1) . ")";

                 $params = array(
                         "verb" => "SELECT",
                         "table" => "products",
                         "columns" => "product_name",
                         "conditions" => array(
                             "id IN " . $list,
                         ),
                         "conjunctives" => array(),
                 );

                 $order['product_list'] = json_encode(query($params)['results']);
                 $orders['results'][$order_index] = $order;
                 $order_index++;
             }

             display($orders);
        } else {
            echo $GLOBALS['endpoint_error'];
        }
    }

    function display($output){
        echo json_encode($output);
    }
