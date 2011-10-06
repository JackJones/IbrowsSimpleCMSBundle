<?php

namespace Ibrows\SimpleCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TextContentType extends ContentType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder                        
            ->add('text','textarea',array('label'=>'none'))
        ;
    }

    public function getName()
    {
        return 'ibrows_simplecmsbundle_textcontenttype';
    }
    

}
