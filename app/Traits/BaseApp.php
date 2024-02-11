<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

trait BaseApp
{

    /**
     * Generate a response general and standard with the description of the error code
     *
     * @param array $fields List of fields with the response of the API 
     *                      - [error_code]: error code generated to the end of the process in the functions.
     *                      - [data]: List of informatión obtained for the response.
     *                      - [function]: name of the function from which to call this function.
     *                      - [class]: name of the function from which to call this class.
     * @param array $dynamic_fields List of dynamic fields for the case where a response message contains variable data
     * @return void
     */
    public function setCustomizeResponse($fields)
    {
        $error_code = $fields['error_code'];

        $status = 500;
        if (substr($error_code, -3) >= 200 && substr($error_code, -3) < 300) {
            $status = 200;
        } else    if (substr($error_code, -3) >= 400 && substr($error_code, -3) < 500) {
            $status = 400;
        }

        $response = array("code" => $error_code, 'status' => $status, "data" => $fields['data']);
        $this->saveLog($fields['function'], $fields['class'], $response);
        return response()->json($response, $status);
    }



    /**
     * Insert log call apis in the server
     *
     * @param string $function
     * @param string $class
     * @param string $response
     * @return void
     */
    public function saveLog($function, $class, $response)
    {
        try {
            $request = Request();
            $status = $response[0]['status'];
            $response_time = round((microtime(true) - $_SERVER['REQUEST_TIME']), 3);
            $ip = $this->getIp();

            $request_all = str_replace('"', "'", stripslashes(json_encode($request->all())));
            $response = str_replace('"', "'", stripslashes(json_encode($response)));

            Log::channel("server")->debug($function . "    |   " . $class . "  |   " . $request->url() . " |   " . $request->method() . "  |   " . $request_all . "    |   " . $response . "    |   " . $ip . "   |   " . $status . "  |   " . $response_time);
        } catch (Exception $e) {
            $data = $this->getException($e);
            Log::channel("error_server")->alert(json_encode($data));
        }
    }

    /**
     * Get Ip
     *
     * @return string
     */
    function getIp()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }


    /**
     * Get Exception
     *
     * @param exception $exception Exception result of try catch
     * @return array
     */
    function getException($exception)
    {
        return array("message" => $exception->getMessage(), "file" => $exception->getFile(), "line" => $exception->getLine());
    }

    /**
     * Get Execution time for a process
     *
     * @param mixed $start
     * @return integer
     */
    public function getExecutionTime($start)
    {
        return $start->diffInMilliseconds(Carbon::now()) / 1000;
    }
}
