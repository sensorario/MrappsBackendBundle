<?php

namespace Mrapps\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mrapps\BackendBundle\Classes\Utils;
use Mrapps\BackendBundle\Entity\Immagine;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/panel")
 */
class DefaultController extends Controller
{

    public function __navigationAction()
    {

        return $this->render('MrappsBackendBundle:Default:navigation.html.twig', array());
    }

    public function __footerAction()
    {

        return $this->render('MrappsBackendBundle:Default:footer.html.twig', array());
    }

    public function getLocalUploadDir($dir = 'mrapps_backend_files')
    {
        return $this->container->getParameter('kernel.root_dir') . '/../web/uploads/' . $dir;
    }

    public function getLocalUrlDir($dir = 'mrapps_backend_files')
    {
        return $this->getRequest()->getSchemeAndHttpHost() . '/uploads/' . $dir;
    }

    public function __topNavBarAction()
    {
        $defaultRouteName = $this->container->getParameter('mrapps_backend.default_route_name');

        return $this->render('MrappsBackendBundle:Default:top-navbar.html.twig',
            array("logo_path" => $this->container->hasParameter('mrapps_backend.logo_path') ? $this->container->getParameter('mrapps_backend.logo_path') : null,
                "default_route_name" => $defaultRouteName,));
    }


    public function __sideBarAction()
    {
        $menu = array();

        $sidebar = ($this->container->hasParameter('mrapps_backend.sidebar_menu')) ? $this->container->getParameter('mrapps_backend.sidebar_menu') : null;
        if (!is_array($sidebar)) $sidebar = array();

        foreach ($sidebar as $firstLevel) {

            $hasSubmenu = (isset($firstLevel['has_submenu'])) ? (bool)$firstLevel['has_submenu'] : false;
            $minRole = (isset($firstLevel['min_role'])) ? trim($firstLevel['min_role']) : '';
            $icon = (isset($firstLevel['icon'])) ? trim($firstLevel['icon']) : '';
            $routeName = (isset($firstLevel['route_name'])) ? trim($firstLevel['route_name']) : '';
            $title = (isset($firstLevel['title'])) ? trim($firstLevel['title']) : '';

            if ($hasSubmenu) {

                $submenu = array();
                $sub = (isset($firstLevel['submenu']) && is_array($firstLevel['submenu'])) ? $firstLevel['submenu'] : array();
                foreach ($sub as $secondLevel) {

                    $subTitle = (isset($secondLevel['title'])) ? trim($secondLevel['title']) : '';
                    $subRouteName = (isset($secondLevel['route_name'])) ? trim($secondLevel['route_name']) : '';
                    $subUrl = (strlen($subRouteName) > 0) ? $this->generateUrl($subRouteName) : '';

                    $submenu[] = array('title' => $subTitle, 'url' => $subUrl, 'route_name' => $subRouteName);
                }

                $url = $submenu;

            } else {
                $url = (strlen($routeName) > 0) ? $this->generateUrl($routeName) : '';
            }

            $menu[] = array(
                'has_submenu' => $hasSubmenu,
                'title' => $title,
                'icon' => $icon,
                'url' => $url,
                'route_name' => $routeName,
                'min_role' => $minRole,
            );
        }

        $actual_link = preg_replace('/\/app_dev.php/', '', $_SERVER['REQUEST_URI']);
        $route = $this->get('router')->match($actual_link)['_route'];

        return $this->render('MrappsBackendBundle:Default:sidebar.html.twig', array(
            'menu' => $menu,
            'active_page' => $route,
        ));
    }

    public function __offSideBarAction()
    {
        return $this->render('MrappsBackendBundle:Default:offsidebar.html.twig', array());
    }

    /**
     * @Route("/", name="mrapps_backend_index")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $defaultRouteName = $this->container->getParameter('mrapps_backend.default_route_name');
        return new RedirectResponse($this->generateUrl($defaultRouteName));
    }

    public function __listAction($title, $tableColumns, $defaultSorting, $defaultFilter, $linkData, $linkNew = null, $linkEdit = null, $linkDelete = null, $linkOrder = null, $linkBreadcrumb = null, $linkCustom = null, $linkAction = null, $deleteMessages = array())
    {
        if (!is_array($deleteMessages)) $deleteMessages = array();
        if (!isset($deleteMessages['question'])) $deleteMessages['question'] = "Procedere con l'eliminazione?";
        if (!isset($deleteMessages['success'])) $deleteMessages['success'] = 'Procedura completata con successo.';
        if (!isset($deleteMessages['error'])) $deleteMessages['error'] = 'Si è verificato un problema durante la procedura di eliminazione; si prega di riprovare più tardi.';
        if (!isset($deleteMessages['cancel'])) $deleteMessages['cancel'] = 'Operazione annullata.';

        return $this->render('MrappsBackendBundle:Default:table.html.twig', array(
            'title' => $title,
            'tableColumns' => $tableColumns,
            'defaultSorting' => json_encode($defaultSorting),
            'defaultFilter' => json_encode($defaultFilter),
            'linkData' => $linkData,
            'linkNew' => $linkNew,
            'linkEdit' => $linkEdit,
            'linkDelete' => $linkDelete,
            'linkOrder' => $linkOrder,
            'linkBreadcrumb' => $linkBreadcrumb,
            'linkCustom' => $linkCustom,
            'linkAction' => $linkAction,
            'angular' => '"ngTable","ngResource","ui.sortable"',
            'deleteMessages' => $deleteMessages,
        ));
    }

    public function __newAction($title, $fields, $linkSave = null, $linkEdit = null, $linkBreadcrumb = null, $create, $edit, $confirmSave = false)
    {
        if ($confirmSave == null) $confirmSave = false;

        $imagesUrl = '';
        if (Utils::bundleMrappsAmazonExists($this->container)) {
            $imagesUrl = ($this->container->hasParameter('mrapps_backend.images_url')) ? $this->container->getParameter('mrapps_backend.images_url') : '';
        } else {
            $imagesUrl = $this->getLocalUrlDir('');
        }


        return $this->render('MrappsBackendBundle:Default:new.html.twig', array(
            'title' => $title,
            'fields' => $fields,
            'linkSave' => $linkSave,
            'linkEdit' => $linkEdit,
            'create' => $create,
            'edit' => $edit,
            'linkBreadcrumb' => $linkBreadcrumb,
            'confirmSave' => $confirmSave,
            'images_url' => $imagesUrl,
            'angular' => '"localytics.directives","angularFileUpload","ui.tinymce","ui.sortable","ui.bootstrap","ngJsTree","ui.validate"',
        ));
    }

    private function getThumbnailUrl($imageUrl = null)
    {
        if (Utils::bundleLiipExists($this->container)) {

            return $this->get('liip_imagine.cache.manager')->getBrowserPath($imageUrl, 'backend_thumbnail');

        } else {
            return $imageUrl;
        }
    }

    /**
     * @Route("/upload/immagine", name="mrapps_backend_uploadimmagine")
     * @Method({"POST"})
     */
    public function uploadImmagineAction(Request $request)
    {
        $responseLocation = '';
        $responseId = 0;
        $responseUrl = '';
        $responseError = '';

        $tmpImg = $request->files->all();

        if (isset($tmpImg['file']) && !$tmpImg['file']->getError()) {

            $file = $tmpImg['file'];

            if (!Utils::isValidFile($this->container, 'image', $file)) {
                return new JsonResponse(array('location' => '', 'id' => 0, 'error' => 'Immagine non valida.'));
            }

            $em = $this->getDoctrine()->getManager();

            $filePath = $file->getPathname();
            $sha1 = sha1(file_get_contents($filePath));

            $localDir = $this->getLocalUploadDir('mrapps_backend_images');

            // $this->container->get('liip_imagine.controller')->filterAction($request, $notizia->getFoto()->getUrl(), 'backend_thumbnail')

            if (Utils::bundleMrappsAmazonExists($this->container)) {

                /* @var $s3 \Mrapps\AmazonBundle\Handler\S3Handler */
                $s3 = $this->container->get('mrapps.amazon.s3');

                $s3Key = 'mrapps_backend_images/' . $sha1;

                $position = strrpos($file->getClientOriginalName(), ".");
                $s3Path = $s3Key . substr($file->getClientOriginalName(), $position);

                //Upload immagine su s3
                if (!$s3->objectExists($s3Path)) $s3->uploadObject($s3Path, $filePath);

                //Entity
                $immagine = $em->getRepository('MrappsBackendBundle:Immagine')->findOneBy(array('url' => $s3Path));
                if ($immagine == null) {
                    $immagine = new Immagine();
                }
                $immagine->setUrl($s3Path);
                $em->persist($immagine);
                $em->flush();

                $thumbnailUrl = $this->getThumbnailUrl($immagine->getUrl());

                $responseLocation = $thumbnailUrl;
                $responseId = $immagine->getId();
                $responseUrl = $thumbnailUrl;
                $responseError = '';

            } else {

                $dirAvailable = false;


                if (!is_dir($localDir)) {

                    if (false === mkdir($localDir, 0755, true)) {
                        $dirAvailable = false;
                    } else {
                        $dirAvailable = true;
                    }

                } else {
                    $dirAvailable = true;
                }

                if ($dirAvailable) {

                    $s3Key = $sha1;
                    $position = strrpos($file->getClientOriginalName(), ".");
                    $fileName = $s3Key . substr($file->getClientOriginalName(), $position);

                    $file->move(
                        $localDir,
                        $fileName
                    );

                    $url = 'uploads/mrapps_backend_images/' . $fileName;

                    //Entity
                    $immagine = $em->getRepository('MrappsBackendBundle:Immagine')->findOneBy(array('url' => $url));
                    if ($immagine == null) {
                        $immagine = new Immagine();
                    }
                    $immagine->setUrl($url);
                    $em->persist($immagine);
                    $em->flush();

                    $thumbnailUrl = $this->getThumbnailUrl($url);

                    $responseLocation = $thumbnailUrl;
                    $responseId = $immagine->getId();
                    $responseUrl = $thumbnailUrl;
                    $responseError = '';

                    $success = true;
                } else {
                    $success = false;
                    $responseError = "Non è stato possibile salvare l'immagine";
                }

            }

        } else {
            $responseError = 'Immagine non trovata.';
        }

        $data = array(
            'location' => $responseLocation,       //location viene usato da tinymce
            'id' => $responseId,
            'url' => $responseUrl,
            'error' => $responseError,
        );

        return new JsonResponse($data);
    }

    /**
     * @Route("/upload/file", name="mrapps_backend_uploadfile")
     * @Method({"POST"})
     */
    public function uploadFileAction(Request $request)
    {
        $success = false;
        $message = '';
        $data = array(
            'id' => null,
            'mime' => null,
            'file_name' => null,
            'normalized_type' => null,
        );

        $tmpFile = $request->files->all();
        if (isset($tmpFile['file']) && !$tmpFile['file']->getError()) {

            $file = $tmpFile['file'];

            $em = $this->getDoctrine()->getManager();

            $originalName = $file->getClientOriginalName();

            $filePath = $file->getPathname();
            $sha1 = sha1(file_get_contents($filePath));

            $localDir = $this->getLocalUploadDir('mrapps_backend_files');

            $mimeType = $em->getRepository('MrappsBackendBundle:File')->getMimeType($filePath);

            $url = null;

            if (Utils::bundleMrappsAmazonExists($this->container)) {

                $s3Key = 'mrapps_backend_files/' . $sha1;
                $position = strrpos($file->getClientOriginalName(), ".");
                $url = $s3Key . substr($file->getClientOriginalName(), $position);

                /* @var $s3 \Mrapps\AmazonBundle\Handler\S3Handler */
                $s3 = $this->container->get('mrapps.amazon.s3');

                $defaultBucket = $this->container->getParameter('mrapps_amazon.parameters.default_bucket');

                //Upload file su s3
                if (!$s3->objectExists($url)) $s3->uploadObject($url, $filePath);

                $success = true;

            } else {
                $localDir = $this->getLocalUploadDir();
                $s3Key = $sha1;

                $dirAvailable = false;


                if (!is_dir($localDir)) {

                    if (false === mkdir($localDir, 0755, true)) {
                        $dirAvailable = false;
                    } else {
                        $dirAvailable = true;
                    }

                } else {
                    $dirAvailable = true;
                }

                if ($dirAvailable) {
                    $s3Key = $sha1;
                    $position = strrpos($file->getClientOriginalName(), ".");
                    $fileName = $s3Key . substr($file->getClientOriginalName(), $position);

                    $url = 'uploads/mrapps_backend_files/' . $fileName;

                    $file->move(
                        $localDir,
                        $fileName
                    );

                    $success = true;
                } else {
                    $success = false;
                    $message = "Non è stato possibile salvare il file";
                }
            }

            //Entity
            $fileEntity = null;

            if ($success) {
                $fileEntity = $em->getRepository('MrappsBackendBundle:File')->createFile($url, $defaultBucket, $originalName, $mimeType);
            }

            if ($fileEntity !== null) {

                $data['id'] = $fileEntity->getId();
                $data['mime'] = $mimeType;
                $data['file_name'] = $originalName;
                $data['normalized_type'] = $em->getRepository('MrappsBackendBundle:File')->getNormalizedType($this->container, $mimeType);

                $success = true;
                $message = '';

            } else {
                $success = false;
                $message = "Si è verificato un problema imprevisto durante l'elaborazione del file. Riprovare tra qualche minuto.";
            }


        } else {
            $success = false;
            $message = 'File non trovato.';
        }

        return Utils::generateResponse($success, $message, $data);
    }

    /**
     * @Route("/upload/pdf", name="mrapps_backend_uploadpdf")
     * @Method({"POST"})
     */
    public function uploadPdfAction(Request $request)
    {
        if (Utils::bundleMrappsAmazonExists($this->container)) {

            /* @var $s3 \Mrapps\AmazonBundle\Handler\S3Handler */
            $s3 = $this->container->get('mrapps.amazon.s3');

            $pdf = $request->files->all();
            $array = [];

            $webFolder = realpath($this->container->get('kernel')->getRootDir() . '/../web') . '/';
            $tempRelativeFolder = $this->container->get('mrapps_backend.temp_folder') . '/';
            $tempFolder = $webFolder . $tempRelativeFolder;

            $success = false;
            $message = '';

            if (isset($pdf['file']) && !$pdf['file']->getError()) {

                $file = $pdf['file'];
                if (Utils::isValidFile($this->container, 'pdf', $file)) {

                    $em = $this->getDoctrine()->getManager();
                    $filePath = $file->getPathname();

                    $images = new \Imagick();
                    $images->setResolution(300, 300);
                    $images->readImage($filePath);

                    foreach ($images as $image) {

                        //$relativePath parte da "web" (dentro il progetto)
                        //$absolutePath parte da /
                        $tempName = sprintf("pdf_page_%s.jpg", Utils::getDateStringForUniqueFiles());
                        $relativePath = $tempRelativeFolder . $tempName;
                        $absolutePath = $tempFolder . $tempName;

                        $image->writeImage($relativePath);
                        $s3Key = 'mrapps_backend_images/' . sha1(file_get_contents($absolutePath));

                        //Entity
                        $immagine = $em->getRepository('MrappsBackendBundle:Immagine')->findOneBy(array('url' => $s3Key));
                        if ($immagine == null) {
                            $immagine = new Immagine();
                        }

                        $immagine->setUrl($s3Key);
                        $em->persist($immagine);
                        $em->flush($immagine);

                        //Caricamento file su s3
                        if (!$s3->objectExists($s3Key)) {
                            $s3->uploadObject($s3Key, $absolutePath);
                        }

                        $array[] = $immagine->getId();
                    }

                    $success = true;
                    $message = 'Pdf Caricato.';

                } else {
                    $success = false;
                    $message = 'Pdf non valido.';
                }

            } else {
                $success = false;
                $message = 'Si è verificato un problema durante il caricamento del pdf; Riprovare più tardi.';
            }

        } else {
            $success = false;
            $message = 'Bundle MrappsAmazonBundle non installato.';
        }

        return Utils::generateResponse($success, $message, $array);
    }

}
