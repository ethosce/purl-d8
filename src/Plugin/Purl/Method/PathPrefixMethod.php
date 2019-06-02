<?php

namespace Drupal\purl\Plugin\Purl\Method;

use Drupal\purl\Annotation\PurlMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @PurlMethod(
 *     id="path_prefix"
 * )
 */
class PathPrefixMethod extends MethodAbstract implements MethodInterface, RequestAlteringInterface
{
    public function contains(Request $request, $modifier)
    {
        $uri = $request->getRequestUri();
        return $this->pathContains($modifier, $uri);
    }

    private function pathContains($modifier, $path)
    {
        return strpos($path, '/' . $modifier) === 0;
    }

    /**
     * Allow for altering the request when the RequestSubscriber event fires.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $identifier
     *
     * @return \Symfony\Component\HttpFoundation\Request|false
     * Return the request or FALSE if the request was not altered.
     *
     */
    public function alterRequest(Request $request, $identifier)
    {
        $uri = $request->server->get('REQUEST_URI');
        $newPath = substr($uri, strlen($identifier) + 1);
        $request->server->set('REQUEST_URI', $newPath);

        return $request;
    }

    public function enterContext($modifier, $path, array &$options)
    {
        return '/' . $modifier . $path;
    }

    public function exitContext($modifier, $path, array &$options)
    {
        if (!$this->pathContains($modifier, $path)) {
           return null;
        }

        return substr($path, 0, strlen($modifier) + 1);
    }
}
