<?php
namespace Pepsite\Controller;

use Pepsite\Entity\Comment;
use Pepsite\Form\{CommentForm, ProfileEditForm};
use Pepsite\Model\CommentsTable;
use Zend\Mvc\Controller\AbstractActionController;
use Pepsite\Service\{ImageManager, UserManager, IdentityManager};
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    private $userManager;
    private $identityManager;
    private $imageManager;
    private $commentsTable;

    public function __construct(
        UserManager $userManager,
        IdentityManager $identityManager,
        ImageManager $imageManager,
        CommentsTable $commentsTable
    ) {
        $this->userManager = $userManager;
        $this->identityManager = $identityManager;
        $this->commentsTable = $commentsTable;
        $this->imageManager = $imageManager;
    }

    public function profileAction()
    {
        $userLogin = $this->params()->fromRoute('login');
        $user = $this->userManager->getUser($userLogin);
        if (is_null($user)) {
            return $this->notFoundAction();
        }
        $votesResSet = $this->userManager->getVotesFor($userLogin, 3);
        $genders = [];
        $votes = [];
        foreach ($votesResSet as $vote) {
            $votes[] = $vote;
            $genders[] = $vote->getVoter();
        }
        $commentsResSet = $this->commentsTable->getCommentsOnPage($userLogin);
        $comments = [];
        foreach ($commentsResSet as $comment) {
            $comments[] = $comment;
            $genders[] = $comment->getAuthor();
        }

        $users = $this->userManager->getUsers(array_unique($genders));
        if (!is_null($users)) {
            $genders = [];
            foreach ($users as $u) {
                $genders[$u->getLogin()] = $u->getGender();
            }
        }

        $form = new CommentForm();
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $data['author'] = $this->identityManager->getIdentity();
                $data['target'] = $userLogin;
                $comment = new Comment();
                $comment->exchangeArray($data);
                $this->commentsTable->createComment($comment);
                return $this->getResponse();
            }
        }
            return new ViewModel([
                'form'   => $form,
                'user'   => $user,
                'votes'  => $votes,
                'genders'  => empty($genders) ? null : $genders,
                'comments' => empty($comments) ? null : $comments,
            ]);
    }

    public function editAction()
    {
        $userLogin = $this->params()->fromRoute('login');
        if ($this->identityManager->hasIdentity()) {
            $identity = $this->identityManager->getIdentity();
            if ($identity === $userLogin) {
                $user = $this->userManager->getUser($userLogin);
                if (is_null($user)) {
                    return $this->notFoundAction();
                }
                $form = new ProfileEditForm();
                $form->get('login')->setValue($userLogin);
                $form->get('gender')->setValue($user->getGender());
                if ($this->getRequest()->isPost()) {
                    $data = $this->params()->fromPost() + $this->params()->fromFiles();
                    $form->setData($data);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $user->setGender($data['gender']);
                        $user->setAvatar(
                            $this->imageManager->saveImage($data['avatar'])
                        );
                        $this->userManager->updateUser($user);
                    }
                }
                return new ViewModel([
                    'form' => $form
                ]);
            }
        }
        return $this->redirect()->toRoute('home');
    }

    public function voteAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->notFoundAction();
        }
        $responce = $this->getResponse();
        $data = $this->params()->fromPost();
        if (!isset($data['voter'], $data['target'], $data['effect'], $data['token'])) {
            $responce->setStatusCode(400);
            return $responce;
        }
        if (!$this->identityManager->validateToken($data['token']) or $data['voter'] === $data['target']) {
            $responce->setStatusCode(403);
            return $responce;
        }
        if ($data['effect'] === 'UP') {
            $this->userManager->upvote($data['voter'], $data['target']);
        } elseif ($data['effect'] === 'DOWN') {
            $this->userManager->downvote($data['voter'], $data['target']);
        }
        $responce->setStatusCode(200);
        return $responce;
    }
}
