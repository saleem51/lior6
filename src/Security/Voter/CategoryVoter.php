<?php

namespace App\Security\Voter;

use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{

    public function __construct(protected CategoryRepository $categoryRepository){}

    public const EDIT = 'CAN_EDIT';
    //public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, ])//self::VIEW
            && //is_numeric($subject);
            $subject instanceof \App\Entity\Category;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /*$category = $this->categoryRepository->find($subject);

        if(!$category){
            return false;
        }*/

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $subject->getOwner() === $user;
        }
        return false;
    }
}
