<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private  $api_key = '812e57746347a2ca76617f0c231877da';
    private $api_key_secret = '08e4deb5d7b100f1cec22b16170e1723' ;

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "staiifi57@gmail.com",
                        'Name' => "La boutique Dz"
                    ],
                    'To' => [
                        [
                            'Email' => "$to_email",
                            'Name' => "$to_name"
                        ]
                    ],
                    'TemplateID' => 2267473,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                      'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}