<?php

namespace App\Http\Services;

use App\Models\SmsLog;
use Exception;
use mrmuminov\eskizuz\Eskiz;
use mrmuminov\eskizuz\types\sms\SmsSingleSmsType;

class SmsService
{
    protected Eskiz $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new Eskiz(config('services.eskiz.email'), config('services.eskiz.password'));
        $this->client->requestAuthLogin();
    }

    public function send($phone, $message): void
    {
        $phone = self::sanitizePhone($phone);
        $requestData = new SmsSingleSmsType(
            from: config('services.eskiz.sender'),
            message: $message,
            mobile_phone: $phone,
            user_sms_id: rand(999, 9999),
            callback_url: config('services.eskiz.callback_url')
        );
        $res = $this->client->requestSmsSend($requestData);
        SmsLog::create([
            'phone' => $phone,
            'data' => json_encode($res->getResponse()),
            'action' => SmsLog::ACTION_SEND,
        ]);
    }

    public static function sendSms($phone, $message): void
    {
        (new self())->send($phone, $message);
    }

    public static function sanitizePhone($phone): array|string|null
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
