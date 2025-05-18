<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;


#[AsTwigComponent]
final class NavBar
{

    private Package $package;

    public array $links;

    public function __construct()
    {
        $this->package = new Package(new EmptyVersionStrategy());

        $this->links = [
            [
                'name' => 'TCL',
                'link' => '/tcl',
                'icon' => $this->package->getUrl('/static/logos/tcl-sytral.svg'),
            ],
            [
                'name' => 'Velov',
                'link' => '/velov',
                'icon' => $this->package->getUrl('/static/logos/velov.svg'),
            ],
            [
                'name' => 'News',
                'link' => '/news',
                'icon' => $this->package->getUrl('/static/logos/newspaper.svg'),
            ],
            [
                'name' => 'Radio',
                'link' => '/radio',
                'icon' => $this->package->getUrl('/static/logos/radio.svg'),
            ],
            // [
            //     'name' => 'Meteo',
            //     'link' => '/meteo',
            //     'icon' => $this->package->getUrl('/static/logos/meteo.svg'),
            // ],
            [
                'name' => 'Wifi',
                'link' => '/livebox',
                'icon' => $this->package->getUrl('/static/logos/wifi.svg'),
            ]
        ];
    }
}
