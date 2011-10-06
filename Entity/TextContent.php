<?php

namespace Ibrows\SimpleCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ibrows\SimpleCMSBundle\Entity\TextContent
 * 
 * @ORM\Table(name="scms_textcontent")
 * @ORM\Entity(repositoryClass="Ibrows\SimpleCMSBundle\Repository\TextContentRepository")
 */
 class TextContent extends Content
{
    
    /**
     * @var string $text
     *
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    //return html
    public function toHTML(\Ibrows\SimpleCMSBundle\Helper\HtmlFilter $filter,array $args){
        $return ='';
        $arr = parent::mergeUserArgs($args, array('attr'=>array('class'=>'simplecms-textcontent')));
        foreach($arr['attr'] as $key => $val){
            $return .= "$key=\"$val\"";
        } 
        $text = $this->getText();
        if(!isset ($args['html'])  ||  $args['html'] != true){
            $text = $filter->filterHtml($text);
            $text = nl2br($text); 
        }
            
        
        
        return '<span '.$return.'>'
                .$text.
            '</span>'
            ;
    }
}