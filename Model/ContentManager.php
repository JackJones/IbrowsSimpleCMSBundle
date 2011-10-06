<?php


namespace Ibrows\SimpleCMSBundle\Model;

use Ibrows\SimpleCMSBundle\Entity\ContentInterface;


class ContentManager
{
    protected $em;
    protected $items;





    public function __construct(\Doctrine\ORM\EntityManager $em, $entitiesToManage)
    {
        $this->em = $em;        
        

        $this->items = array();
        foreach($entitiesToManage as $key => $val){
            $this->addEntityType($key,$val['class'],$val['type'],$val['repository'],$val['label']);
        }
                
        //$em->getRepository($class);
        
       // $metadata = $em->getClassMetadata($class);
       // $this->class = $metadata->name;
    }

    public function addEntityType($id,$class,$formtype=null,$repository=null,$label=null){
        if(!$id || !$class || $class instanceof \Ibrows\SimpleCMSBundle\Entity\ContentInterface){
            return false;
        }
        if($label == null){
            $label = $id;
        }
        if($repository == null){            
            $repository = $this->em->getRepository($class);            
        }else{
            $repository .= 'Repository';
            $repository = new $repository($this->em, $this->em->getClassMetadata($class));
        }
        $this->items[$id] = new ContentManagerItem($class, $repository, $label,$formtype);
        
        return true;
    }
    
    public function getClass($type='text'){                
        return $this->items[$type]->getClass() ;
    }

    /**
     *
     * @param string $type
     * @return ContentInterface 
     */
    public function getEntity($type='text'){                
        $class = $this->getClass($type);
        return new $class() ;
    }    
    
    public function getRepository($type='text'){                
        return $this->items[$type]->getRepository() ;
    }    

    public function getFormType($type='text'){                
        $formtype = $this->items[$type]->getFormType();
        return new $formtype();
    }        
    
    public function getTypes(){
        return array_keys($this->items);
    }
    
    public function getContentModelItems(){
        return $this->items;
    }    
    /**
     *
     * @param string $type
     * @param string $key
     * @return ContentInterface 
     */
    public function create($type='text',$key=null)
    {        
        $class = $this->getClass($type);        
        $obj = new $class();
        $obj->setKeyword($key);
        return $obj;
    }

    /**
     *
     * @param string $type
     * @param string $key
     * @return ContentInterface|null 
     */
    public function find($type='text',$key)
    {                  
        return $this->getRepository($type)->findOneBy(array('keyword'=>$key));
    }

    /**
     * @param string $type
     * @param string $group
     * @param string $key 
     * @return ContentInterface|null 
     */
    public function findAll( $type='text', $key) 
    {
        $repo = $this->getRepository($type);
        /* @var $repo \Ibrows\SimpleCMSBundle\Repository\ContentRepository */
        $qb = $repo->createQueryBuilder('scmsc');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb->where('scmsc.keyword LIKE ?1');
        $qb->orderBy('scmsc.keyword','ASC');
        $qb->setParameter(1, addcslashes( "___{$key}___%",'_') );
        $results = $qb->getQuery()->execute();        
        return $results;
    }
    
    public function getNewGroupKey($key,$all=null){
        if(!$all){
            $groupkey[1]= $key;  
            $groupkey[2] = 1;
        }else{
            end($all);
            $last = current($all);
            $groupkey= $this->splitGroupKey($last->getKeyword());    
            $groupkey[2] = intval($groupkey[2])+1;
        }

        return $this->generateGroupKey($groupkey[1],$groupkey[2]);
    }
    
    
    public function splitGroupKey($groupkey){
        $matches = array();
        preg_match('!___(.*)___(.*)___!u', $groupkey,$matches);
        if(sizeof($matches) == 3){
            unset($matches[0]);
            return $matches;
        }
        return false;
    }
    
    private function generateGroupKey($group,$key){
        $key = sprintf('%010s',  intval($key));
        return "___{$group}___{$key}___";
    }
    

            
    
}
