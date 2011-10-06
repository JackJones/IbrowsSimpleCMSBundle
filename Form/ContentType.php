<?php

namespace Ibrows\SimpleCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $data = $options['data'];
        if($data && $data->getKeyword()){
            $builder->add('keyword','hidden');
        }
        else{
            $builder->add('keyword');
        }        
        ;
    }

    public function getName()
    {
        return 'ibrows_simplecmsbundle_contenttype';
    }
}
