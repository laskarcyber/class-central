<?php

namespace ClassCentral\SiteBundle\Controller;

use ClassCentral\SiteBundle\Entity\Offering;
use ClassCentral\SiteBundle\Entity\UserCourse;
use ClassCentral\SiteBundle\Utility\Breadcrumb;
use ClassCentral\SiteBundle\Utility\PageHeader\PageHeaderFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ClassCentral\SiteBundle\Entity\Language;
use ClassCentral\SiteBundle\Form\LanguageType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Language controller.
 *
 */
class LanguageController extends Controller
{
    /**
     * Lists all Language entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ClassCentralSiteBundle:Language')->findAll();

        return $this->render('ClassCentralSiteBundle:Language:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Language entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClassCentralSiteBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ClassCentralSiteBundle:Language:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Language entity.
     *
     */
    public function newAction()
    {
        $entity = new Language();
        $form   = $this->createForm(new LanguageType(), $entity);

        return $this->render('ClassCentralSiteBundle:Language:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Language entity.
     *
     */
    public function createAction()
    {
        $entity  = new Language();
        $request = $this->getRequest();
        $form    = $this->createForm(new LanguageType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('language_show', array('id' => $entity->getId())));
            
        }

        return $this->render('ClassCentralSiteBundle:Language:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Language entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClassCentralSiteBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $editForm = $this->createForm(new LanguageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ClassCentralSiteBundle:Language:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Language entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClassCentralSiteBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $editForm   = $this->createForm(new LanguageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('language_edit', array('id' => $id)));
        }

        return $this->render('ClassCentralSiteBundle:Language:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Language entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ClassCentralSiteBundle:Language')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Language entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('language'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Displays courses by languages
     * @param Request $request
     * @param $slug
     */
    public function viewAction(Request $request, $slug)
    {
        $cache = $this->get('cache');

        $data = $cache->get(
            'language_' . $slug,
            function($slug, $container) {
                $esCourses = $this->get('es_courses');
                $filter =$this->get('filter');
                $em = $container->get('doctrine')->getManager();

                $language = $em->getRepository('ClassCentralSiteBundle:Language')->findOneBySlug($slug);
                if(!$language) {
                    // TODO: render an error page
                    return array();
                }

                $pageInfo =  PageHeaderFactory::get($language);
                $pageInfo->setPageUrl(
                    $container->getParameter('baseurl'). $container->get('router')->generate('lang', array('slug' => $slug))
                );

                $response = $esCourses->findByLanguage($slug);
                $allSubjects = $filter->getCourseSubjects($response['subjectIds']);
                $allLanguages = $filter->getCourseLanguages($response['languageIds']);
                $allSessions  = $filter->getCourseSessions( $response['sessions'] );

                $breadcrumbs = array(
                    Breadcrumb::getBreadCrumb('Languages',$container->get('router')->generate('languages')),
                    Breadcrumb::getBreadCrumb($language->getName(), $container->get('router')->generate('lang',array('slug' => $language->getSlug())))
                );


                return array(
                    'response' => $response,
                    'language' => $language,
                    'pageInfo' => $pageInfo,
                    'allSubjects' => $allSubjects,
                    'allLanguages' => $allLanguages,
                    'breadcrumbs' => $breadcrumbs,
                    'allSessions'  => $allSessions
                );
            },
            array($slug, $this->container)
        );

        if( empty($data) )
        {
            // Show an error message
            return;
        }



        return $this->render('ClassCentralSiteBundle:Language:view.html.twig',
            array(
                'language' => $data['language'],
                'page'=>'language',
                'offeringTypes'=> Offering::$types,
                'slug' => $slug,
                'results' => $data['response']['results'],
                'listTypes' => UserCourse::$lists,
                'allSubjects' => $data['allSubjects'],
                'allLanguages' => $data['allLanguages'],
                'allSessions' => $data['allSessions'],
                'pageInfo' => $data['pageInfo'],
                'breadcrumbs' => $data['breadcrumbs']
            ));
    }


    public function getOfferingsByLanguage(Language $language)
    {
        $courses = $language->getCourses();
        $courseIds = array();
        foreach($courses as $course)
        {
            $courseIds[] = $course->getId();
        }

        return $this->getDoctrine()->getRepository('ClassCentralSiteBundle:Offering')->findAllByCourseIds($courseIds);
    }

    /**
     * Shows a page which lists all languages
     * @param Request $request
     */
    public function languagesAction(Request $request)
    {
        $cache = $this->get('cache');
        $languages = $cache->get('language_list_count', array($this, 'getLanguagesList'),array($this->container));
        $breadcrumbs = array(
            Breadcrumb::getBreadCrumb('Languages',$this->generateUrl('languages'))
        );
        return $this->render('ClassCentralSiteBundle:Language:languages.html.twig',array(
                'page' => 'languages',
                'languages' => $languages,
                'breadcrumbs' => $breadcrumbs
            ));
    }

    public function getLanguagesList($container)
    {
        $em = $container->get('doctrine')->getManager();
        $esCourses = $container->get('es_courses');

        $count = $esCourses->getCounts();
        $languagesCount = $count['languages'];

        $allLanguages = $em->getRepository('ClassCentralSiteBundle:Language')->findAll();
        $languages = array();
        foreach($allLanguages as $language)
        {
            if(!isset($languagesCount[$language->getId()]))
            {
                continue; // no count exists. Do not show the language
            }

            $count = $languagesCount[$language->getId()];
            $language->setCourseCount($count);
            $languages[$language->getId()] = $language;

            $em->detach($language);
        }

        return $languages;
    }


}
