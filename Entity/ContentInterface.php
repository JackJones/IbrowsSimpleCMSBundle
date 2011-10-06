<?php

namespace Ibrows\SimpleCMSBundle\Entity;


/**
 * Ibrows\SimpleCMSBundle\Model\ContentInterface
 *
 */
interface  ContentInterface
{
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId();


    /**
     * Set keyword
     *
     * @param string $keyword
     */
    public function setKeyword($keyword);


    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword();
    
    
    
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $params
     */
    public function setParameters(\Symfony\Component\DependencyInjection\ContainerInterface $params);


    
    /**
     * Get the HTML for frontend
     * @param \Ibrows\SimpleCMSBundle\Helper\HtmlFilter $filter
     * @param array $args
     * @return string 
     */
    public function toHTML(\Ibrows\SimpleCMSBundle\Helper\HtmlFilter $filter, array $args);
            
            
}