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
            ]
        ];
    }
}
