<?php

namespace Lsw\GuzzleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Lsw\GuzzleBundle\DependencyInjection\LswGuzzleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Lsw\GuzzleBundle\DependencyInjection\Compiler\EnableSessionSupport;

/**
* Bundle for Guzzle API calling clients with debug toolbar integration
*
* @author Maurits van der Schee <m.vanderschee@leaseweb.com>
*/
class LswGuzzleBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
