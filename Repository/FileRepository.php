<?php

namespace Mrapps\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Mrapps\BackendBundle\Entity\File;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileRepository extends EntityRepository
{
    public function getMimeType($filePath = '') {

        $mime = '';

        if(file_exists($filePath)) {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filePath);
            finfo_close($finfo);
        }

        return $mime;
    }

    public function getNormalizedType($container = null, $mime = '') {

        $normalizedType = 'file';
        $mime = strtolower(trim($mime));

        $types = array('image', 'video', 'pdf');
        $canProceed = true;
        foreach($types as $t) {

            if($canProceed) {
                $parameterName = 'mrapps_backend.file_accepted_types.'.$t;
                $exploded = explode(',', $container->hasParameter($parameterName) ? $container->getParameter($parameterName) : '');

                foreach ($exploded as $t) {
                    $t = strtolower(trim($t));
                    if(strlen($t) > 0 && $t == $mime) {
                        $normalizedType = $t;
                        $canProceed = false;
                        break;
                    }
                }
            }
        }

        return $normalizedType;
    }


    public function createFile($key = '', $bucket = null, $originalName = null, $mimeType = null) {

        $key = trim($key);
        if(strlen($key) > 0) {

            $file = $this->findOneBy(array('s3Key' => $key));
            if($file == null) {
                $file = new File();
                $file->setS3Key($key);
            }

            if($bucket !== null) $file->setBucket(trim($bucket));
            if($originalName !== null) $file->setOriginalName(trim($originalName));
            if($mimeType !== null) $file->setMimeType($mimeType);

            $em = $this->getEntityManager();
            $em->persist($file);
            $em->flush($file);

            return $file;
        }

        return null;
    }
}
