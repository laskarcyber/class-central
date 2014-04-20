<?php
/**
 * Created by PhpStorm.
 * User: dhawal
 * Date: 3/30/14
 * Time: 7:33 PM
 */

namespace ClassCentral\ElasticSearchBundle;


use ClassCentral\ElasticSearchBundle\DocumentType\CourseDocumentType;
use ClassCentral\ElasticSearchBundle\DocumentType\SubjectDocumentType;
use ClassCentral\ElasticSearchBundle\Scheduler\ESJob;
use ClassCentral\SiteBundle\Entity\Course;
use ClassCentral\SiteBundle\Entity\Institution;
use ClassCentral\SiteBundle\Entity\Stream;
use ClassCentral\SiteBundle\Swiftype\DocumentType\InstitutionDocumentType;
use Elasticsearch\Client;

class Indexer {

    private $esClient;
    private $container;


    public function setContainer($container)
    {
        $this->container = $container;
    }


    /**
     * Index the particular entity
     * @param $entity
     */
    public function index($entity, $indexName = null)
    {
        $this->esClient = $this->container->get('es_client');


        // Index the course
        if($entity instanceof Course)
        {
            $cDoc = new CourseDocumentType($entity,$this->container);
            $doc = $cDoc->getDocument( $this->getIndexName('es_index_name') );
            $this->esClient->index($doc);
        }

        // Index the institution
        if($entity instanceof Institution)
        {
            $iDoc = new InstitutionDocumentType($entity, $this->container);
            $doc = $iDoc->getDocument( $this->getIndexName('es_index_name') );

            $this->esClient->index($doc);
        }

        if($entity instanceof Stream)
        {
            $sDoc = new SubjectDocumentType($entity, $this->container);
            $doc = $sDoc->getDocument( $this->getIndexName('es_index_name') );

            $this->esClient->index($doc);
        }

        if($entity instanceof ESJob)
        {
            $jobDoc = new ESJobDocumentType($entity, $this->container);
            $doc = $jobDoc->getDocument( $this->getIndexName('es_scheduler_index_name') );
            $this->esClient->index( $doc );
        }
    }

    private function getIndexName( $configValue )
    {
        return $this->container->getParameter( $configValue );
    }

} 