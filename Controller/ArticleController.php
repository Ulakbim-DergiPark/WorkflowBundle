<?php

namespace Ojs\WorkflowBundle\Controller;

use Ojs\WorkflowBundle\Form\Type\ArticleMetadataEditType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * Edit article metadata action
     * works on timeline template
     *
     * @param Request $request
     * @param $workflowId
     *
     * @return Response
     */
    public function editMetadataAction(Request $request, $workflowId)
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $this->throw404IfNotFound($journal);
        $workflowService = $this->get('dp.workflow_service');
        $workflow = $workflowService->getArticleWorkflow($workflowId);
        $em = $this->getDoctrine()->getManager();
        $article = $workflow->getArticle();

        $form = $this->createForm(new ArticleMetadataEditType(), $article, [
            'action' => $this->generateUrl('ojs_workflow_edit_article_metadata', [
                'journalId' => $journal->getId(),
                'workflowId' => $workflow->getId(),
            ])
        ]);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){
            $em->persist($article);
            $em->flush();
            $this->successFlashBag('successful.update');
        }

        return $this->render('OjsWorkflowBundle:Article:_edit_metadata.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
