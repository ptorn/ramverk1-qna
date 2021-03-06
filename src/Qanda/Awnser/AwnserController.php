<?php
namespace Peto16\Qanda\Awnser;

use \Peto16\Qanda\Common\CommonController;
use \Peto16\Qanda\Awnser\HTMLForm\CreateAwnserForm;
use \Peto16\Qanda\Awnser\HTMLForm\UpdateAwnserForm;
use \Peto16\Qanda\Comment\HTMLForm\CreateCommentForm;

/**
 * Controller for Awnser
 */
class AwnserController extends CommonController
{

    /**
     * Delete a awnser
     *
     * @param int       $awnserId
     * @return void
     */
    public function delAwnser($questionId, $awnserId)
    {
        $this->awnserService->delAwnser($awnserId);
        $this->utils->redirect("question/" . $questionId);
    }



    /**
     * Edit awnser page.
     *
     * @throws Exception
     *
     * @return void
     */
    public function getPostEditAwnser($questionId, $awnserId)
    {
        $title  = "Redigera svar";
        $form   = new UpdateAwnserForm($this->di, $questionId, $awnserId);

        $form->check();

        $data = [
            "content" => $form->getHTML(),
        ];

        $this->view->add("default2/article", $data);
        $this->pageRender->renderPage(["title" => $title]);
    }



    /**
     * Get and Post with awnsers and awnserform
     * @param  int      $questionId question id.
     * @param  int      $awnserId   [description]
     * @return void             [description]
     */
    public function getPostAwnserPage($questionId, $awnserId)
    {
        $title      = "Svar";
        $awnser     = $this->awnserService->getAwnser($awnserId);
        $question   = $this->questionService->getQuestion($questionId);
        $loggedInUser   = $this->di->get("userService")->getCurrentLoggedInUser();

        // Awnser escape and parse markdown
        $awnser->content = $this->utils->escapeParseMarkdown($awnser->content);

        $awnser->title      = htmlspecialchars($awnser->title);
        $awnser->firstname  = htmlspecialchars($awnser->firstname);
        $awnser->lastname   = htmlspecialchars($awnser->lastname);

        $this->view->add("qanda/awnser/awnser", [
            "awnser"        => $awnser,
            "question"      => $question,
            "questionIdUrl" => htmlspecialchars($questionId),
            "loggedInUser"  => $loggedInUser,
            "type"          => "awnserId",
            "id"            => $awnser->id,
            "urlReturn"     => $this->di->get("url")->create("question/" . $questionId . "/awnser/" . $awnser->id),
            "nrVotesUp"     => sizeof($this->voteService->getAllVotesUp("awnserId", $awnser->id)),
            "nrVotesDown"   => sizeof($this->voteService->getAllVotesDown("awnserId", $awnser->id))
        ], "main");

        // Comments
        $comments = $this->commentService->getComByAwnserId($awnserId);
        foreach ($comments as $comment) {
            if ($comment->deleted !== null) {
                continue;
            }

            // Parse markdown
            $comment->content = $this->utils->escapeParseMarkdown($comment->content);

            $comment->title     = htmlspecialchars($comment->title);
            $comment->firstname = htmlspecialchars($comment->firstname);
            $comment->lastname  = htmlspecialchars($comment->lastname);

            $this->di->get("view")->add("qanda/comment/comment", [
                "comment" => $comment,
                "questionIdUrl" => htmlspecialchars($questionId),
                "awnserIdUrl" => htmlspecialchars($awnserId),
                "loggedInUser"  => $loggedInUser,
                "type"          => "commentId",
                "id"            => $comment->id,
                "urlReturn"     => $this->di->get("url")->create("question/" . $questionId . "/awnser/" . $awnser->id),
                "nrVotesUp"     => sizeof($this->voteService->getAllVotesUp("commentId", $comment->id)),
                "nrVotesDown"   => sizeof($this->voteService->getAllVotesDown("commentId", $comment->id))
            ], "comment");
        }

        $form = new CreateCommentForm($this->di, $questionId, $awnserId);

        $form->check();

        $data = [
            "form" => $form->getHTML(),
        ];

        if ($this->di->get("userService")->getCurrentLoggedInUser()) {
            $this->view->add("qanda/crud/create", $data, "main");
        }
        $this->pageRender->renderPage(["title" => $title]);
    }



    /**
     * Get call to accept awnser
     * @param  int $questionId question id
     * @param  int $awnserId   awnser id
     * @return void
     */
    public function getAcceptAwnser($questionId, $awnserId)
    {
        $this->awnserService->setAcceptedAwnserToQuestion($questionId, $awnserId);
        $this->utils->redirect("question/" . $questionId);

    }
}
