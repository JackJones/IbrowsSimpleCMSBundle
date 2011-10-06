<?php

namespace Ibrows\SimpleCMSBundle\Extension;

class TwigExtension extends \Twig_Extension implements \Ibrows\SimpleCMSBundle\Helper\HtmlFilter {

    /**
     *
     * @var \Ibrows\SimpleCMSBundle\Model\ContentManager 
     */
    private $manager;

    /**
     * @var \Ibrows\SimpleCMSBundle\Security\SecurityHandler
     */
    private $handler;

    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment) {
        $this->env = $environment;
    }

    public function __construct(\Ibrows\SimpleCMSBundle\Model\ContentManager $manager) {
        $this->manager = $manager;
    }

    public function setSecurityHandler(\Ibrows\SimpleCMSBundle\Security\SecurityHandler $handler) {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
            'scms' => new \Twig_Filter_Method($this, 'content', array('is_safe' => array('html'))),
            'scms_collection' => new \Twig_Filter_Method($this, 'contentCollection', array('is_safe' => array('html'))),
            'scmsc' => new \Twig_Filter_Method($this, 'contentCollection', array('is_safe' => array('html'))),
        );
    }

    public function getFunctions() {
        return array(
            'scms' => new \Twig_Function_Method($this, 'content', array('is_safe' => array('html'))),
            'scms_collection' => new \Twig_Function_Method($this, 'contentCollection', array('is_safe' => array('html'))),
            'scmsc' => new \Twig_Function_Method($this, 'contentCollection', array('is_safe' => array('html'))),
        );
    }

    public function contentCollection($key, $type, array $arguments = array(), $default='') {
        $debugmessage = '';

        if ($this->env->isDebug()) {
            $debugmessage .= "<!--debug IbrowsSimpleCMS Collection\n";
            $debugmessage .= "type=$type \n";
            $debugmessage .= "key=$key \n";
            $debugmessage .= "default=$default \n";
            $debugmessage .= "arguments=" . print_r($arguments, true) . " \n";
            $debugmessage .= '-->';

            if ($default == '') {
                $default = "$key-$type";
            }
        }

        $objs = $this->manager->findAll($type, $key);
        $out = '';
        $grant = $this->handler->isGranted('ibrows_simple_cms_content');
        $addkey = $this->manager->getNewGroupKey($key,$objs);
        if ($objs) {
            foreach ($objs as $objkey => $content) {
                /* @var $content \Ibrows\SimpleCMSBundle\Entity\ContentInterface */
                $outobj = $debugmessage . $content->toHTML($this, $arguments);
                if($grant && $this->handler->isGranted('ibrows_simple_cms_content_edit_key', array('key'=> $content->getKeyword(),'type'=>$type )) ){
                    $outobj =$this->wrapOutputForEdit($outobj, $content->getKeyword(), $type, $arguments, $default);
                }
                $out .= $outobj;
            }
        } else if (!$grant){
          $out = $default;
        }
        
        if(!$grant){
            return $out;
        }
        $class = '';
        if(isset($arguments['inline']) && $arguments['inline'] == true ){
            $class = 'inline';
        }        
        //addlink
        if($this->handler->isGranted('ibrows_simple_cms_content_create', array('type'=>$type )) ){
            $editpath = $this->env->getExtension('routing')->getPath('ibrows_simple_cms_content_edit_key', array('key' => $addkey, 'type' => $type));
            $editpath .="?args=" . urlencode(serialize($arguments));
            $editpath .="&default=" . $default;
            $outadd = '<a href="' . $editpath . '" class="simplecms-editlink simplecms-addlink" > </a> ADD '.$default.'';
            $outadd = "<div class=\"simplecms-edit simplecms-add $class\" id=\"simplcms-$addkey-$type\" >$outadd</div>";               
        }
    

        return "<div class=\"simplecms-edit-collection $class\" id=\"simplcms-collection-$key-$type\" >$out$outadd</div>"; 
    }
    
    private function wrapOutputForEdit($out,$key, $type, array $arguments = array(), $default=''){
        $class = '';
        if(isset($arguments['inline']) && $arguments['inline'] == true ){
            $class = 'inline';
        }
        
        $editpath = $this->env->getExtension('routing')->getPath('ibrows_simple_cms_content_edit_key', array('key' => $key, 'type' => $type));
        $editpath .="?args=" . urlencode(serialize($arguments));
        $editpath .="&default=" . $default;        
        $out = '<a href="' . $editpath . '" class="simplecms-editlink" ></a>' . $out;
        $out = '<a href="' . $this->env->getExtension('routing')->getPath('ibrows_simple_cms_content_delete_key', array('key' => $key, 'type' => $type)) . '" class="simplecms-deletelink" > </a>' . $out;
        $out = "<div class=\"simplecms-edit $class\" id=\"simplcms-$key-$type\" >$out</div>";
        
        return $out;
    }



    public function content($key, $type, array $arguments = array(), $default='') {

        $debugmessage = '';

        if ($this->env->isDebug()) {
            $debugmessage .= "<!--debug IbrowsSimpleCMS\n";
            $debugmessage .= "type=$type \n";
            $debugmessage .= "key=$key \n";
            $debugmessage .= "default=$default \n";
            $debugmessage .= "arguments=" . print_r($arguments, true) . " \n";
            $debugmessage .= '-->';

            if ($default == '') {
                $default = "$key-$type";
            }
        }

        $obj = $this->manager->find($type, $key);
        if ($obj) {
            $out = $debugmessage . $obj->toHTML($this, $arguments);
        } else {
            $out = $default;
        }


        
        $grant = $this->handler->isGranted('ibrows_simple_cms_content_edit_key', array('key'=> $key,'type'=>$type ));
        //$grant = $this->handler->isGranted('ibrows_simple_cms_content');
        if(!$grant){
          return $out;
        }
        

        return $this->wrapOutputForEdit($out, $key, $type, $arguments, $default);
    }

    public function filterHtml($string) {
        return twig_escape_filter($this->env, $string);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'simplecms';
    }

}