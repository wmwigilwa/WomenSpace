<?php

namespace App\Security;

use App\Entity\UserAccounts\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ACLVoter extends Voter
{

    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const INFO = 'info';
    const BLOCK = 'block';
    const UNBLOCK = 'unblock';
    const RESET = 'reset';
    const BULK_UPLOAD = 'bulk-upload';
    const UPLOAD = 'upload';
    const APPROVE = 'approve';
    const DECLINE = 'decline';
    const SOFT_RESET ='soft-reset';
    const HARD_RESET ='hard-reset';


    /**
     * @var ACLSecurityProvider
     */
    private ACLSecurityProvider $acl;


    /**
     * ACLVoter constructor.
     * @param ACLSecurityProvider $acl
     */
    public function __construct(ACLSecurityProvider $acl)
    {
        $this->acl = $acl;
    }


    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports(string $attribute, mixed $subject) :bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW,
                self::EDIT,
                self::DELETE,
                self::INFO,
                self::BLOCK,
                self::UNBLOCK,
                self::RESET,
                self::ADD,
                self::APPROVE,
                self::DECLINE,
                self::BULK_UPLOAD,
                self::UPLOAD,
                self::HARD_RESET,
                self::SOFT_RESET]
        )) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token) :bool
    {
        $user = $token->getUser();


        if (!$user instanceof User || $user->getAccountStatus()!='A') {
            // the user must be logged in with active account; if not, deny access
            return false;
        }

        $permissions = $this->acl->getCurrentACLs($subject);

        if(is_array($permissions)) {

            if (in_array($attribute, $permissions)) {
                return true;
            }
        }

        return false;
    }


}