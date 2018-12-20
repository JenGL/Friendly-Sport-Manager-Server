<?php

return function ($err_core, $custom_msg = '') {
    switch ($err_core) {
        case 400:
            http_response_code(400);
            $arr = array('error' => 'Bad Format ' . $custom_msg);
            return json_encode($arr);
        case 401:
            http_response_code(401);
            $arr = array('error' => 'Not authorized ' . $custom_msg);
            return json_encode($arr);
        case 403:
            http_response_code(403);
            $arr = array('error' => 'Not authorized ' . $custom_msg);
            return json_encode($arr);
        case 404:
            http_response_code(404);
            $arr = array('error' => 'Match Not Found ' . $custom_msg);
            return json_encode($arr);
        default:
            http_response_code(500);
            $arr = array('error' => 'Something Went Really Wrong ' . $custom_msg);
            return json_encode($arr);
    }
};