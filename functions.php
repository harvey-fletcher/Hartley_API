<?php

    function query($params){

        if($params['verb'] == 'SELECT'){
            $query = "SELECT " . $params['columns'] . " FROM " . $params['table'];

            if(isset($params['conditions'])){
                if(sizeof($params['conditions']) > 0){
                    $query.= " WHERE ";

                    $condition_index = 0;

                    foreach($params['conditions'] as $condition){
                        $query.= $condition . " " .$params['conjunctives'][$condition_index] . " ";
                        $condition_index++;
                    }
                }
            }
        } else if ($params['verb'] == "INSERT"){
            $query = "INSERT INTO " . $params['table'] . " (";

            $columns = mysqli_query($GLOBALS['conn'], "DESCRIBE " . $params['table']);
            $colcount = 0;
            while($col = mysqli_fetch_array($columns, MYSQLI_ASSOC)){
                if($col['Field'] != "id" && $col['Field'] != "created"){
                    $query.= "`" . $col['Field'] . "`,";
                    $colcount++;
                }
            }

            $query = substr($query, 0, -1) . ') VALUES (';

            if(sizeof($params['values']) != $colcount){
                echo "Incorrect value count in your query (". sizeof($params['values']) ."). Values must match the number of columns. (" . $colcount . ")";
                die();
            }

            foreach($params['values'] as $value){
                $query.= "'" . $value . "',"; 
            }

            $query = substr($query, 0, -1) . ")";
        } else if($params['verb'] == "DELETE"){
            $query = "DELETE FROM " . $params['table'];

            if(sizeof($params['conditions']) > 0){
                $query.= " WHERE ";
                $condition_index = 0;

                foreach($params['conditions'] as $condition){
                    $query.= $condition . " " . $params['conjunctives'][$condition_index];
                    $condition_index++;
                }
            }
        } else if($params['verb'] == "UPDATE"){
            $query = "UPDATE " . $params['table'] . " SET ";

            $columns = array_keys($params['values']);

            $column_index = 0;

            foreach($params['values'] as $value){
                $query.= $columns[$column_index] . "='" . $value . "',";
                $column_index++;
            }

            $query = substr($query, 0, -1);

            $conjunctive_index = 0;

            if(isset($params['conditions'])){
                foreach($params['conditions'] as $condition){
                     $query.= " WHERE " . $condition . " " . $params[$conjunctive_index];
                     $conjunctive_index++;
                }
            }
        }

        $results = mysqli_query($GLOBALS['conn'], $query);

        $resultset = array();

        while($row = mysqli_fetch_array($results, MYSQLI_ASSOC)){
            array_push($resultset, $row);
        }

        $data = array(
                "results" => $resultset,
                "count" => mysqli_num_rows($results),
                "affected" => mysqli_affected_rows($GLOBALS['conn']),
                "inserted_id" => mysqli_insert_id($GLOBALS['conn']),
            );

        if($GLOBALS['debug']){
            $data['query'] = $query;
        };

        return $data;
    }
