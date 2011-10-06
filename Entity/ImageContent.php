<?php

namespace Ibrows\SimpleCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ibrows\SimpleCMSBundle\Entity\ImageContent
 * 
 * @ORM\Table(name="scms_imagecontent")
 * @ORM\Entity(repositoryClass="Ibrows\SimpleCMSBundle\Repository\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
 class ImageContent extends Content
{
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     * 
     * @Assert\File(maxSize="6000000")
     */
    protected $file;    
    

    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        if($this->getPath()){
            if(file_exists($this->getPath())){
                unlink($this->getAbsolutePath());
            }    
        }
        $this->file = $file;
        $this->path='';//path must be changed when file change
    }



    public function getWebPath()
    {
        return null === $this->getPath() ? null : $this->getPath();
    }

    protected function getUploadRootDir()
    {
        return dirname( $this->getAbsolutePath());
    }
    
    protected function getAbsolutePath()
    {
        // the absolute directory path where uploaded documents should be saved
        //rootpath

        return  $this->params->getParameter('kernel.root_dir').'/../web/'.$this->getPath();
    }    


    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return $this->params->getParameter('ibrows_simple_cms.upload_dir');

    }        
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        
        if (null !== $this->file) {            
            $this->setPath($this->getUploadDir().'/'.$this->name. time().'.'. $this->file->guessExtension());
            
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
        

        if (null === $this->file) {
            return;
        }
        //@todo kill the old
        $filename = str_replace(dirname($this->getAbsolutePath()), '', $this->getAbsolutePath());
        $this->file->move($this->getUploadRootDir(), $filename); 

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

  
    
    //return html
    public function toHTML(\Ibrows\SimpleCMSBundle\Helper\HtmlFilter $filter,array $args){
        
        $return ='';
        $arr = parent::mergeUserArgs($args, array('attr'=>array('class'=>'simplecms-imagecontent','alt'=>$this->getName(),'title'=>$this->getName())));
        foreach($arr['attr'] as $key => $val){
            $return .= "$key=\"$val\"";
        }
        
        $return = '<img src="/'.$this->getWebPath().'" '.$return.' ">';
        return $return;
            ;
    }
}