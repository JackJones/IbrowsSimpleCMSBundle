<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibrows\SimpleCMSBundle\Security;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Sonata\AdminBundle\Admin\AdminInterface;

class SecurityHandler {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Bundle\SecurityBundle\Security\FirewallMap
     */
    protected $map;
    
    /**
     *
     * @var array
     */
    protected $securityMap;  
    
    /**
     *
     * @var array
     */
    protected $globalRole;   
    
    /**
     *
     * @var SecurityContextInterface 
     */
    protected $securityContext;

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container,
            \Symfony\Bundle\SecurityBundle\Security\FirewallMap $map,
            SecurityContextInterface $securityContext,
            $globalRole, 
            array $securityMap ) {
        
        $this->container = $container;
        $this->map = $map;
        $this->securityMap = $securityMap;
        $this->globalRole = $globalRole;
        $this->securityContext = $securityContext;
    }

    public function isGranted($route = "ibrows_simple_cms_content", $parameters=array()) {
        
        
    
        
   
        try {
            if (false === $this->securityContext->isGranted($this->globalRole)) {
                return false;
            }
        }  catch (AuthenticationCredentialsNotFoundException $e){
            return true;
        }
        if(isset($parameters['type']) && key_exists($parameters['type'], $this->securityMap)){
            $roles = $this->securityMap[$parameters['type']];             
            foreach($roles as $roletype => $role){              
                if(stripos($route, $roletype) !== false || $roletype == 'general') {                    
                    if(false === $this->securityContext->isGranted($role)){
                        return false;
                    }
                    
                }
            }            
        }
        

        
        
        return true;
        
    }

    
    /*
    public function isGranted($route = "ibrows_simple_cms_content", $parameters=array()) {
        $url = $this->container->get('router')->generate($route, $parameters, true);


        if (stripos($this->container->get('request')->getUri(), $url) !== false) {
            return true;
        }

        $subRequest = $this->container->get('request')->duplicate();
        /* @var $subRequest \Symfony\Component\HttpFoundation\Request *


        $params = $subRequest->cookies->all();
        if (isset($params['subrequestscms']))
            throw new \Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException();

        $params['subrequestscms'] = 'true';
        $url = preg_replace('!(/app_[^\.]*\.php)!', '', $url);
        $subRequest = $subRequest->create($url, 'GET', $params, $params);

        try {
            $return = $this->container->get('http_kernel')->handle($subRequest);
        } catch (\Exception $e) {
            return false;
        }


        /* @var $return Symfony\Component\HttpFoundation\Response *
        if ($return->isSuccessful()) {
            return true;
        }
        return false;
    }
*/
}