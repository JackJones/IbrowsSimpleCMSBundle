<?php

namespace Ibrows\SimpleCMSBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\templating\Helper\CoreAssetsHelper;
use Symfony\Component\Routing\RouterInterface;
use Ibrows\SimpleCMSBundle\Security\SecurityHandler;

class ResponseListener {

    private $assetHelper;
    private $router;
    private $includeLibs;
    private $securityHandler;
    private $wysiwygconfig;

    public function __construct(CoreAssetsHelper $assetHelper, SecurityHandler $securityHandler, RouterInterface $router, $includeLibs = true,array $wysiwygconfig = array()) {
        $this->assetHelper = $assetHelper;
        $this->router = $router;
        $this->includeLibs = $includeLibs;
        $this->securityHandler = $securityHandler;
        $this->wysiwygconfig = $wysiwygconfig;
    }

    public function onKernelResponse(FilterResponseEvent $event) {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }


        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }


        if (!$this->securityHandler->isGranted('ibrows_simple_cms_content')) {
            return;
        }
        $this->inject($response);

    }

    /**
     *
     * @param Response $response A Response instance
     */
    protected function inject(Response $response) {
        $content = $response->getContent();
        $pos = strripos($content, '</head>');
        if ($pos === false) {
            return false;
        }


        $scripts = '';
        $needed = array(
            'jquery-1.6' => 'js/jquery-1.6.4.min.js',
            'jquery-ui-1.8' => 'js/jquery-ui-1.8.16.custom.min.js',
            'jquery-ui.css' => 'themes/darkness/jquery-ui.css',
            'jquery.form' => 'js/jquery.form-2.8.5.js',
            'jquery.tinymce' => 'js/tiny_mce/jquery.tinymce.js',
        );

        if ($this->includeLibs === true) {
            foreach ($needed as $key => $value) {
                if (strripos($content, $key) === false) {
                    $url = $this->assetHelper->getUrl('bundles/ibrowssimplecms/' . $value);
                    if (stripos($value, '.css')) {
                        $scripts .= ' <link rel="stylesheet" type="text/css" media="screen" href="' . $url . '" /> ';
                    } else {
                        $scripts .= '<script type="text/javascript" src="' . $url . '"></script>' . "\n";
                    }
                }
            }
        }
    
        $this->wysiwygconfig['script_url'] = $this->assetHelper->getUrl('bundles/ibrowssimplecms/' . 'js/tiny_mce/tiny_mce.js');
         $confscript= <<<HTML
<script type="text/javascript">
    var simple_cms_wysiwyg_config = %s;
</script>
HTML;
            
        $scripts .= sprintf($confscript, json_encode( $this->wysiwygconfig ));
        $url = $this->assetHelper->getUrl('bundles/ibrowssimplecms/js/simplecms.js');
        $scripts .= '<script type="text/javascript" src="' . $url . '"></script>' . "\n";
        $url = $this->assetHelper->getUrl('bundles/ibrowssimplecms/css/simplecms.css');
        $scripts .= ' <link rel="stylesheet" type="text/css" media="screen" href="' . $url . '" /> ';
        $content = substr($content, 0, $pos) . $scripts . substr($content, $pos);
        $response->setContent($content); 
        return true;
    }

}
