<?php
/**
 * This file is part of the Alom project.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Alom\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Alom\WebsiteBundle\Entity\PostComment;

class PostCommentController extends Controller
{
    public function listAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $comments = $this->getDoctrine()
            ->getRepository('AlomWebsiteBundle:PostComment')
            ->fetchAllOrderedByDate()
        ;

        return $this->render('AlomWebsiteBundle:PostComment:list.html.twig', array(
            'comments' => $comments
        ));
    }

    public function deleteAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $comment = $em->getRepository('AlomWebsiteBundle:PostComment')->find($id);

        if (!$comment) {
            throw new NotFoundHttpException();
        }
        $comment->activate();
        $em->remove($comment);
        $em->flush();

        $fallbackUrl = $this->generateUrl('alom_website_post_view', array('slug' => $comment->getPost()->getSlug()));

        return $this->redirect($request->headers->get('Referer', fallbackUrl));
    }

    public function activateAction($id)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $comment = $em->getRepository('AlomWebsiteBundle:PostComment')->find($id);

        if (!$comment) {
            throw new NotFoundHttpException();
        }
        $comment->activate();
        $em->persist($comment);
        $em->flush();

        $fallbackUrl = $this->generateUrl('alom_website_post_view', array('slug' => $comment->getPost()->getSlug()));

        return $this->redirect($this->getRequest()->headers->get('Referer', $fallbackUrl));
    }

    public function inactivateAction($id)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $comment = $em->getRepository('AlomWebsiteBundle:PostComment')->find($id);

        if (!$comment) {
            throw new NotFoundHttpException();
        }
        $comment->inactivate();
        $em->persist($comment);
        $em->flush();

        $fallbackUrl = $this->generateUrl('alom_website_post_view', array('slug' => $comment->getPost()->getSlug()));

        return $this->redirect($this->getRequest()->headers->get('Referer', $fallbackUrl));
    }
}
