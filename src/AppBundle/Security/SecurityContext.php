<?php
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Entity\User;


class SecurityContext {

    protected $securityTokenStorage;
    protected $translator;

    public function __construct($securityTokenStorage,$translator) {
        $this->securityTokenStorage = $securityTokenStorage;
        $this->translator = $translator;
    }

    public function makeSecurityContext(\AppBundle\Entity\User $user) {

        $roles = $user->getUserRoles();

        $securityContext = array();

        foreach($roles as $role) {
            foreach($role->getPermissions() as $permission) {
                // Test if the key of with the resource exist
                if(!array_key_exists($permission->getResource(), $securityContext)) {
                    $securityContext[$permission->getResource()] = intval($permission->getAccesslevel());
                }
                // The key exist, we keep the higher permission
                else {
                    $securityContext[$permission->getResource()] |= intval($permission->getAccesslevel());
                }
            }
        }

        return $securityContext;
    }
    
    public function isGranted($resource, $accesslevel) {
        $securityContext = $this->securityTokenStorage->getToken()->getAttribute('security-context');
        return array_key_exists($resource, $securityContext) && ($securityContext[$resource] & $accesslevel);
    }

    public function checkRight($resource,$accesslevel) {
        if(!$this->isGranted($resource, $accesslevel)) {
            throw new AccessDeniedHttpException($this->translator->trans('error.accessdenied'));
        }
    }

}
