<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Base controller extended by all controllers.
 * It contains some generic methods.
 *
 */
class ControllerBase extends Controller {

    protected function trans($msg, $params = null) {
        $trans = $this->get('translator')->trans($msg);

        if($params){
            foreach($params as $k => $value){
                $trans = str_replace($k, $value, $trans);
            }
        }

        return $trans;
    }
}
