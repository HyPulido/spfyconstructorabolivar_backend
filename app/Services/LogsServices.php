<?php

namespace App\Services;

use App\Traits\BaseApp;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait LogsServices
{

    // use BaseApp;

    /**
     * Save Log provider foreach api extern call
     *
     * @param Model $provider Mode of provider
     * @param string $api Name API use
     * @param string $url Endpoint API
     * @param string $method Method Type use
     * @param string $response
     * @param integer $status Status response to API
     * @param integer $execution_time Time wait for response of request
     * @return void
     */
    public function saveLogService($provider, $api, $url, $method, $response, $status, $execution_time)
    {
        try {
            $response = is_array($response) ? json_encode($response) : $response;
            $log = $provider;
            $log .= "\t|\t" . $api;
            $log .= "\t|\t" . $url;
            $log .= "\t|\t" . $method;
            $log .= "\t|\t" . $response;
            $log .= "\t|\t" . $status;
            $log .= "\t|\t" . $execution_time;
            Log::channel("provider")->debug($log);
        } catch (Exception $e) {
            $data = $this->getException($e);
            Log::channel("error_provider")->alert(json_encode($data));
        }
    }


    public function processService($api, $request, $response, $start)
    {
        $url = $request->getUri()->__toString();
        $provider = explode("/", $url)[2];
        $method = $request->getMethod();
        $content = $response->getBody()->getContents();
        $execution_time = $this->getExecutionTime($start);
        $status = $response->getStatusCode();
        $this->saveLogService($provider, $api, $url, $method, $content, $status, $execution_time);
        return $content;
    }
}
