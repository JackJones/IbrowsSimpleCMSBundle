<?php

namespace Ibrows\SimpleCMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ibrows\SimpleCMSBundle\Entity\TextContent;
use Ibrows\SimpleCMSBundle\Form\TextContentType;

/**
 * Content controller.
 *
 */
class ContentController extends Controller
{
    
    /**
     *
     * @return \Ibrows\SimpleCMSBundle\Model\ContentManager 
     */
    public function getManager(){
        $manager = $this->container->get('ibrows_simple_cms.content_manager');
        return $manager;       
    }
    
    
    
    /**
     * Lists all Content entities.
     *
     * @Route("/{type}" ,defaults={"type" = null}, name="ibrows_simple_cms_content")
     * @Template()
     */
    public function indexAction($type = null)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content not allowed');
        
        $entities = null;
        if($type !=null){
            $entities = $this->getManager()->getRepository($type)->findAll();
        }
        return array('entities' => $entities,'type'=>$type,'types'=>$this->getManager()->getContentModelItems());
    }

    /**
     * Finds and displays a Content entity.
     *
     * @Route("/{type}/{id}/show", name="ibrows_simple_cms_content_show")
     * @Template()
     */
    public function showAction($id, $type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_show',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_show not allowed');
        
        $entity = $this->getManager()->getRepository($type)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        $deleteForm = $this->createDeleteForm($id,$type);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),'type'=>$type        );
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/{type}/new", name="ibrows_simple_cms_content_new")
     * @Template()
     */
    public function newAction($type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_new',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_new not allowed');        
        $entity = $this->getManager()->create($type);
        $form   = $this->createForm($this->getManager()->getFormType($type), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),'type'=>$type
        );
    }

    /**
     * Creates a new  entity.
     *
     * @Route("/{type}/create", name="ibrows_simple_cms_content_create")
     * @Method("post")
     * @Template("IbrowsSimpleCMSBundle:Content:new.html.twig")
     */
    public function createAction($type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_create',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_create not allowed');           
        $entity  = $this->getManager()->getEntity($type);
        $request = $this->getRequest();
        $form    = $this->createForm($this->getManager()->getFormType($type), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity->setParameters($this->container);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ibrows_simple_cms_content_show', array('id' => $entity->getId(),'type'=>$type)));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),'type'=>$type
        );
    }

    /**
     * Displays a form to edit an existing Content entity.
     *
     * @Route("/{type}/{id}/edit", name="ibrows_simple_cms_content_edit")
     * @Template()
     */
    public function editAction($id,$type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_edit',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_edit not allowed');           
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $this->getManager()->getRepository($type)->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find  entity.');
        }

        $editForm = $this->createForm($this->getManager()->getFormType($type), $entity);
        $deleteForm = $this->createDeleteForm($id,$type);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),'type'=>$type
        );
    }

    /**
     * Edits an existing Content entity.
     *
     * @Route("/{type}/update/{id}",requirements={"id" = "\d+"},defaults={"id" = 0}, name="ibrows_simple_cms_content_update")
     * @Method("post")
     * @Template("IbrowsSimpleCMSBundle:Content:edit.html.twig")
     */
    public function updateAction($id,$type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_update',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_update not allowed');         
        $em = $this->getDoctrine()->getEntityManager();
        $additional = $this->getRequest()->getQueryString();
        $groupkey = false;
        if($id!=0){
            $entity = $this->getManager()->getRepository($type)->find($id);  
        }else{
            $entity = $this->getManager()->create($type);               
        }
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $entity->setParameters($this->container);
        $editForm   = $this->createForm($this->getManager()->getFormType($type), $entity);
        

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            if($id==0){
                $groupkey = $this->getManager()->splitGroupKey($entity->getKeyword());  
            }
            if ($this->get('request')->isXmlHttpRequest() || $this->get('request')->get('_xml_http_request')) {
                return $this->render('IbrowsSimpleCMSBundle:Content:editedByKey.html.twig', array('groupkey'=>$groupkey,'key' => $entity->getKeyWord(),'type'=>$type, 'args' => unserialize($this->getRequest()->get('args')),'default' => $this->getRequest()->get('default')));
            }
            return $this->redirect($this->generateUrl('ibrows_simple_cms_content_edit', array('id' => $id,'type'=>$type)));
        }else if ($this->get('request')->isXmlHttpRequest() || $this->get('request')->get('_xml_http_request')) {            
                throw new \Symfony\Component\Form\Exception\NotValidException (print_r($editForm->getErrors(),true));
                
        }
        $deleteForm = $this->createDeleteForm($id,$type);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),'type'=>$type
        );
    }

    
    
    
    /**
     * Displays a form to edit an existing Content entity.
     *
     * @Route("/{type}/{key}/delete/key", name="ibrows_simple_cms_content_delete_key")
     * @Template()
     */
    public function deleteByKeyAction($key,$type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_delete_key',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_delete_key not allowed');           
        $entity = $this->getManager()->find($type,$key);        
        if (!$entity) {
                throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $em = $this->getDoctrine()->getEntityManager();
        $entity->setParameters($this->container);
        $em->remove($entity);
        $em->flush();

        return $this->render('IbrowsSimpleCMSBundle:Content:editedByKey.html.twig', array('key' => $key,'type'=>$type,'args'=>array(),'default'=>''));
       
    }
    
    /**
     * Displays a form to edit an existing Content entity.
     *
     * @Route("/{type}/{key}/editkey", name="ibrows_simple_cms_content_edit_key")
     * @Template()
     */
    public function editByKeyAction($key,$type)
    {
       
        $entity = $this->getManager()->find($type,$key);
        
        $additional = $this->getRequest()->getQueryString();
       
        if (!$entity) {
            if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_create',array('type'=>$type)) ){
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_create not allowed');     
            }
            $entity = $this->getManager()->create($type,$key);
            $em = $this->getDoctrine()->getEntityManager();            
          //  $em->persist($entity);
          //  $em->flush($entity);
        }else if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_edit_key',array('type'=>$type)) ){
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_edit_key not allowed');  
        }
        $formtype = $this->getManager()->getFormType($type);
        /* @var $formtype \Ibrows\SimpleCMSBundle\Form\ContentType */
        $form = $this->createFormBuilder(array('_xml_http_request'=>true,'content'=>$entity))->add('_xml_http_request','hidden');
        
        $editForm = $this->createForm($formtype, $entity,array('attr'=>array()));       
        /* @var $editForm Symfony\Component\Form\Form */
        $form->add('content', $formtype);
     //   $editForm = $form->getForm();
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'type'=>$type,
            'additional'=>$additional,
            'args'=> unserialize($this->getRequest()->get('args')),
        );
    }    
    
    /**
     * Deletes a Content entity.
     *
     * @Route("/{type}/{id}/delete", name="ibrows_simple_cms_content_delete")
     * @Method("post")
     */
    public function deleteAction($id,$type)
    {
        if (!$this->container->get('ibrows_simple_cms.securityhandler')->isGranted('ibrows_simple_cms_content_delete',array('type'=>$type)) )
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ('ibrows_simple_cms_content_delete not allowed');         
        $form = $this->createDeleteForm($id,$type);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $this->getManager()->getRepository($type)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Content entity.');
            }
            $entity->setParameters($this->container);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ibrows_simple_cms_content',array('type'=>$type)));
    }

    private function createDeleteForm($id,$type)
    {
        return $this->createFormBuilder(array('id' => $id,'type'=>$type))
            ->add('id', 'hidden')
            ->add('type', 'hidden')
            ->getForm()
        ;
    }
}
