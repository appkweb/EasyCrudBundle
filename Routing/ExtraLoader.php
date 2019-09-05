<?php
/*
 * This file is part of the Appkweb package.
 *
 * (c) Valentin REGNIER <vregnier@appkweb.com>
 *
 * Contributors :
 * - REGNIER Valentin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Appkweb\Bundle\EasyCrudBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class ExtraLoader extends Loader
{
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        $resource = '@EasyCrudBundle/Resources/config/main_routing.yaml';
        $type = 'yaml';

        $importedRoutes = $this->import($resource, $type);
        $collection->addCollection($importedRoutes);

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'advanced_extra';
    }
}