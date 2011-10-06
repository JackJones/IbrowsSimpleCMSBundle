IbrowsSimpleCMSBundle - Simple CMS for anyone anywhere
========================================================

some blah !

Quick example
-------------

How to use simple CMS

1. Use filter 
``` twig
{{ 'mycustomtextidentifier'|scms('text') }}
```
2. OR
``` twig function
{{ scms('mycustomimageidentifier','image') }}
```

How to use the same for a flexible amount of items

1. Use filter
``` twig
{{ 'mycustomtextidentifier'|scms_collection('text') }}
```
2. OR
``` twig function
{{ scms_collection('mycustomimageidentifier','image') }}
```

Screenshots : ...



Advanced examples
-------------

Single image with `my` class and inline editorstyle (instead of block)

``` twig
{{ scms('mycustomidentifier','image',{'inline':true,'attr':{'class':'my'} }) }}
```

Flexible amount of wysiwyg text elements

``` twig
{{ scms_collection('mycustomidentifier','text',{'html':true}) }}
```

Screenshots : ...


Install & setup the bundle
--------------------------

1.  Fetch the source code

    Using dept File, add these lines:

    ``` bash 
    [IbrowsSimpleCMSBundle]
    git=https://github.com/ibrows/IbrowsSimpleCMSBundle
    target=/bundles/Ibrows/SimpleCMSBundle

    ```

    Using Git to control your project from project root directory:
    
    ``` bash 

    git submodule add https://github.com/ibrows/IbrowsSimpleCMSBundle vendor/bundles/Ibrows/SimpleCMSBundle

    ```
        
    By cloning repository:
    
    ``` bash 

    mkdir -p vendor/bundles/Ibrows/SimpleCMSBundle
    cd !$
    git clone https://github.com/ibrows/IbrowsSimpleCMSBundle

    ```

2.  Add the namespace to your autoloader

    ``` php

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Ibrows' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );
    ```

3.  Add the bundle to your `AppKernel` class

    ``` php

    // app/AppKernerl.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Ibrows\SimpleCMSBundle\IbrowsSimpleCMSBundle(),
            // ...
        );
        // ...
    }
    
    ```

4.  Add routing

    ``` yaml

    // app/config/routing.yml

    IbrowsSimpleCMSBundle:
        resource: "@IbrowsSimpleCMSBundle/Controller/"
        type:     annotation
        prefix:   /scms  

    ```

5.  Generate Schema

    ``` bash
    php app/console doctrine:schema:update  --force

    ```


Minimal configuration
---------------------

This bundle requires Nothing !


Additional configuration
------------------------

### Edit default config 
    // app/config/conf.yml

        ibrows_simple_cms:
          include_js_libs: true
          upload_dir: 'web/uploads/documents'
          role: ROLE_IS_AUTHENTICATED_ANONYMOUSLY



### Add security per type

    // app/config/conf.yml

        ibrows_simple_cms:
          types:
        //#defaults
            text: { class: Ibrows\SimpleCMSBundle\Entity\TextContent , type: Ibrows\SimpleCMSBundle\Form\TextContentType, security:{general:ROLE_ADMIN} }
            image: { class: Ibrows\SimpleCMSBundle\Entity\ImageContent, type: Ibrows\SimpleCMSBundle\Form\FileContentType, security:{general:ROLE_ADMIN, show: ROLE_SUPER_ADMIN, create: ROLE_SUPER_ADMIN , edit: ROLE_SUPER_ADMIN , delete: ROLE_SUPER_ADMIN  } }

### Edit TinyMCE Options


    // app/config/conf.yml

        ibrows_simple_cms:
          wysiwyg:
            theme: 'advanced'
            theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect'
        //#other configs...



### Add types  
Add / Edit types of Content:

    // app/config/conf.yml

        ibrows_simple_cms:
          types:
        //#defaults
            text: { class: Ibrows\SimpleCMSBundle\Entity\TextContent , type: Ibrows\SimpleCMSBundle\Form\TextContentType }
            image: { class: Ibrows\SimpleCMSBundle\Entity\ImageContent, type: Ibrows\SimpleCMSBundle\Form\FileContentType}
        //#custom
            mytext: { class: Ibrows\SimpleCMSBundle\Entity\TextContent , type: Ibrows\SimpleCMSBundle\Form\TextContentType , repository: Ibrows\SimpleCMSBundle\Repository\TextContent, label:first}
            mycustomentity: { class: Ibrows\XXXBundle\Entity\YYYContent , type: Ibrows\SimpleCMSBundle\Form\YYYContentType , repository: Ibrows\SimpleCMSBundle\Repository\Content, label:myone}


Your YYYContent Entity have to implement `Ibrows\SimpleCMSBundle\Entity\ContentInterface` or extend `Ibrows\SimpleCMSBundle\Entity\Content` or a Child of it.
It's also a good idea to extend `Ibrows\SimpleCMSBundle\ContentType` in your FormType.


TODO
----
 
  - create the ODM version


