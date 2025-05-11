<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use chillerlan\QRCode\QRCode;

final class LiveboxController extends AbstractController
{
    private string $liveboxUrl;
    private string $sessionId;
    private string $contextId;
    private QRCode $qrcode;

    public function __construct(private HttpClientInterface $client, private LoggerInterface $logger)
    {
        $this->liveboxUrl = $_ENV['LIVEBOX_URL'];
        $this->qrcode = new QRCode();
    }

    #[Route('/livebox', name: 'app_livebox')]
    public function index(): Response
    {
        $this->connectToLivebox();

        $mibsResponse = $this->client->request('POST', $this->liveboxUrl, [
            'headers' => $this->getHeaders('getMIBs'),
            'json' => [
                'service' => 'NeMo.Intf.lan',
                'method' => 'getMIBs',
                'parameters' => [
                    'mibs' => 'wlanvap',
                ]
            ],
        ]);

        if ($mibsResponse->getStatusCode() !== 200) {
            throw new \Exception('Couldn\'t get wifi details');
        }

        $data = $mibsResponse->toArray();
        $ssid = $data['status']['wlanvap']['vap2g0priv0']['SSID'];
        $passphrase = $data['status']['wlanvap']['vap2g0priv0']['Security']['KeyPassPhrase'];

        $wifiDetails = [
            'ssid' => $ssid,
            'password' => $passphrase,
        ];

        return $this->render('livebox/index.html.twig', [
            'qrcode' => $this->qrcode->render($this->getQrCodeWifiData($ssid, $passphrase)),
            'data' => $wifiDetails
        ]);
    }

    private function getHeaders(string $method)
    {
        if ($method == 'createContext') {
            return [
                'Authorization' => 'X-Sah-Login',
                'Content-Type' => 'application/x-sah-ws-4-call+json',
            ];
        } else {
            return [
                'Content-type' => 'application/x-sah-ws-4-call+json',
                'Authorization' => 'X-Sah ' . $this->contextId,
                'X-Context' => $this->contextId,
                'Cookie' => '2f7de9a4/sessid=' . $this->sessionId . '; sah/contextId=' . $this->contextId
            ];
        }
    }

    private function connectToLivebox()
    {
        $response = $this->client->request('POST', $this->liveboxUrl, [
            'headers' => $this->getHeaders('createContext'),
            'json' => [
                'service' => 'sah.Device.Information',
                'method' => 'createContext',
                'parameters' => [
                    'applicationName' => 'webgui',
                    'username' => $_ENV['LIVEBOX_USERNAME'],
                    'password' => $_ENV['LIVEBOX_PASSWORD'],
                ]
            ]
        ]);

        if ($response->getStatusCode() !== 200) return false;

        $content = $response->toArray();
        $headers = $response->getHeaders();

        preg_match('/sessid=(.+?);/', $headers['set-cookie'][0], $matches);

        $this->sessionId = $matches[1];
        $this->contextId = $content['data']['contextID'];
    }

    private function getQrCodeWifiData(string $ssid, string $passphrase)
    {
        return 'WIFI:T:WPA;S:' . $ssid . ';P:' . $passphrase . ';;';
    }
}
