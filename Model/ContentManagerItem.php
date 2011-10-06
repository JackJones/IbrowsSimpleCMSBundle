<?php


namespace Ibrows\SimpleCMSBundle\Model;




class ContentManagerItem
{
    protected $label;
    protected $repository;
    protected $class;
    protected $formtype;

    public function __construct($class,$repository,$label,$formtype)
    {
        $this->setClass($class);
        $this->setRepository($repository);
        $this->setLabel($label);
        $this->setFormtype($formtype);
    }

    public function getFormtype() {
        return $this->formtype;
    }

    public function setFormtype($formtype) {
        $this->formtype = $formtype;
    }

        
    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

    public function getRepository() {
        return $this->repository;
    }

    public function setRepository($repository) {
        $this->repository = $repository;
    }

    public function getClass() {
        return $this->class;
    }

    public function setClass($class) {
        $this->class = $class;
    }


    
}
