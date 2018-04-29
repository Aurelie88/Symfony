<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Employee;
use AppBundle\ImageUpload;

class ImageUploadListener
{
    private $uploader;

    public function __construct(ImageUpload $uploader)
    {
        $this->uploader=$uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Employee){
            return;
        }

        $file = $entity->getImage();

        if(!$file instanceof UploadedFile)
        {
            return;
        }

        $fileName = $this->uploader->upload($file);
        $entity->setImage($fileName);
    }
}