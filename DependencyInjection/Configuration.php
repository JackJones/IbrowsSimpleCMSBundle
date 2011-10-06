<?php

namespace Ibrows\SimpleCMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        
        $node = $treeBuilder->root('ibrows_simple_cms');
        
                
        $this->addTemplateSection($node);
        $nodew = $node->children()->arrayNode('Wysiwyg');
        $this->addTemplateSectionWysiwyg($node);


        return $treeBuilder;
    }
    
    private function addTemplateSection(\Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node)
    {

        
        $node
            ->children()
               
                ->booleanNode('include_js_libs')->defaultTrue()->end()
                ->scalarNode('upload_dir')->defaultValue('uploads/documents')->end()            
                ->scalarNode('role')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()            
                ->arrayNode('types')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('repository')->defaultValue(null)->end()
                            ->scalarNode('label')->defaultValue(null)->end()
                            ->scalarNode('type')->defaultValue(null)->end()
                            ->arrayNode('security')->addDefaultsIfNotSet()
                                ->children() 
                                    ->scalarNode('general')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                    ->scalarNode('show')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                    ->scalarNode('create')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                    ->scalarNode('edit')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                    ->scalarNode('delete')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->end()    
                            ->end()
                        ->end()
                    ->end()
                    
                    ->defaultValue(array(
                        'text'=> array('class'=> 'Ibrows\SimpleCMSBundle\Entity\TextContent','type'=>'Ibrows\SimpleCMSBundle\Form\TextContentType','repository'=>null,'label'=>null,'security'=>array(
                            'general' => 'IS_AUTHENTICATED_ANONYMOUSLY','show' => 'IS_AUTHENTICATED_ANONYMOUSLY','create' => 'IS_AUTHENTICATED_ANONYMOUSLY','edit' => 'IS_AUTHENTICATED_ANONYMOUSLY','delete'=> 'IS_AUTHENTICATED_ANONYMOUSLY'
                            )),
                        'image'=> array('class'=> 'Ibrows\SimpleCMSBundle\Entity\ImageContent','type'=>'Ibrows\SimpleCMSBundle\Form\FileContentType','repository'=>null,'label'=>null,'security'=>array(
                            'general' => 'IS_AUTHENTICATED_ANONYMOUSLY','show' => 'IS_AUTHENTICATED_ANONYMOUSLY','create' => 'IS_AUTHENTICATED_ANONYMOUSLY','edit' => 'IS_AUTHENTICATED_ANONYMOUSLY','delete'=> 'IS_AUTHENTICATED_ANONYMOUSLY'
                            )),
                        ))
                ->end()
                        
            ->end()
        ->end();

    }
    
    
    public function addTemplateSectionWysiwyg(\Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node)
    {
        $node
        ->children()->arrayNode('wysiwyg')->addDefaultsIfNotSet()
            
            ->children() 
                ->scalarNode('theme')->defaultValue('advanced')->end()
                ->scalarNode('theme_advanced_buttons1')->defaultValue("save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect")->end()
                ->scalarNode('theme_advanced_buttons2')->defaultValue("cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor")->end()
                ->scalarNode('theme_advanced_buttons3')->defaultValue("tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen")->end()
                ->scalarNode('theme_advanced_buttons4')->defaultValue("insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak")->end()
                ->scalarNode('theme_advanced_toolbar_location')->defaultValue("top")->end()
                ->scalarNode('theme_advanced_toolbar_align')->defaultValue("left")->end()
                ->scalarNode('theme_advanced_statusbar_location')->defaultValue("bottom")->end()
                ->booleanNode ('theme_advanced_resizing')->defaultValue(true)->end()
                
                ->scalarNode('plugins')->defaultValue("autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist")->end()
            ->end()
        ->end()->end();

    }    
/*sample
 * 			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
 */    
}
