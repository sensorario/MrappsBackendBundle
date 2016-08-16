<?php

namespace Mrapps\BackendBundle\Repository;

use Imagine\Imagick\Imagine;
use Doctrine\ORM\EntityRepository;
use Mrapps\BackendBundle\Classes\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mrapps\BackendBundle\Entity\Immagine;

/**
 * ImmagineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ImmagineRepository extends EntityRepository
{
    public function generateImageFromS3(ContainerInterface $container, $bucket, $key) {

        $em = $this->getEntityManager();

        if (Utils::bundleMrappsAmazonExists($container)) {

            /* @var $s3 \Mrapps\AmazonBundle\Handler\S3Handler */
            $s3 = $container->get('mrapps.amazon.s3');

            $head = $s3->headObject($key, $bucket);
            if($head !== null) {

                $etag = str_replace('"', '', $head['ETag']);

                $webFolder = realpath($container->get('kernel')->getRootDir() . '/../web') . '/';
                $tempRelativeFolder = $container->getParameter('mrapps_backend.temp_folder') . '/';
                $tempFolder = $webFolder . $tempRelativeFolder;

                $savePath = $tempFolder.$etag;

                $s3->downloadObject($key, $savePath, false, $bucket);
                if(file_exists($savePath)) {
			
		 try {
                        $imagine = new Imagine();
                        $imagine->load($savePath);
                    } catch (Exception $e) {
                        $logger = $container->get("logger");
                        $logger->error("Image " . $key . " - " . $e->getMessage());
                        return null;
                    }

                    $sha1 = sha1_file($savePath);

                    $s3Key = 'mrapps_backend_images/' . $sha1;
                    $position = strrpos($key, ".");
                    $s3Path = $s3Key . substr($key, $position);

                    if(!$s3->objectExists($s3Path)) $s3->uploadObject($s3Path, $savePath);

                    //Entity
                    $immagine = $this->findOneBy(array('url' => $s3Path));
                    if ($immagine == null) {
                        $immagine = new Immagine();
                    }
                    $immagine->setUrl($s3Path);
                    $em->persist($immagine);
                    $em->flush($immagine);

                    unlink($savePath);

                    return $immagine;
                }
            }
        }

        return null;
    }
}
