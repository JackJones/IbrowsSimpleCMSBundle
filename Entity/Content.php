<?php

namespace Ibrows\SimpleCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ibrows\SimpleCMSBundle\Model\Content
 *
 * @ORM\MappedSuperclass 
 */
abstract class  Content implements ContentInterface
{
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $keyword
     *
     * @ORM\Column(name="keyword", type="string", length=255, unique=true)
     */
    protected $keyword;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $params
     *
     */
    protected $params;
    
    
    public function setParameters(\Symfony\Component\DependencyInjection\ContainerInterface $params) {
        $this->params = $params;
    }
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set keyword
     *
     * @param string $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    
    protected function mergeUserArgs(array $userargs,array $defaultargs){
        if(isset($userargs['attr'])){
            $attr = $userargs['attr'];  
            if(!is_array($attr)){
                throw \Exception('No array given');
            }
        }else{
            $attr = array();
        }
        if(isset($defaultargs['attr'])){
            $defaultattr = $defaultargs['attr'];  
            if(!is_array($defaultattr)){
                throw \Exception('No array given');
            }
        }else{
            $defaultattr = array();
        }        
        foreach($defaultattr as $key => $value){
            if(!isset( $attr[$key])){
                $attr[$key] = $defaultattr[$key] ;
            }else if ($key == 'class'){
                $attr[$key] .= ' '. $defaultattr[$key] ;
            }    
            
        }
        return array('attr'=>$attr);
        
    }
    
    /**
     * Get the HTML for frontend
     * @param \Ibrows\SimpleCMSBundle\Helper\HtmlFilter $filter
     * @param array $args
     * @return string 
     */
    public function toHTML(\Ibrows\SimpleCMSBundle\Helper\HtmlFilter $filter, array $args){


            
                
        
        
        return 'id'.$this->getId().'keyword'.$this->getKeyword();
    }    
    
}