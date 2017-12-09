<?php

namespace Api\Controller;

use Api\Controller\AbstractController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Application\Form\InputFilter;

class IndexController extends AbstractController
{
    public function feedbackAction()
    {
        $filter = new InputFilter\System\Feedback($this->getServiceLocator());
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk();
        }
        return $this->outError($filter->getMessages());
    }
    
    public function templatesAction()
    {
        $renderer = new PhpRenderer();
        $renderer->setHelperPluginManager($this->getServiceLocator()->get('ViewHelperManager'));
        $resolver = new Resolver\AggregateResolver();
        $renderer->setResolver($resolver);

        $folder = realpath(dirname(INDEX_PATH).'/templates/');
        if(!is_dir($folder)){
            echo 'folder for templates not found';exit;
        }
        $stack = new Resolver\TemplatePathStack(array(
            'script_paths' => array(
                $folder
            )
        ));
        $resolver->attach($stack);
        
        $f = function($folder, &$list = array()) use(&$f) {
            $files = scandir($folder);
            foreach($files AS $file){
                if($file!='.' and $file!='..'){
                    $filename = $folder.'/'.$file;
                    if(is_dir($filename)){
                        $f($filename, $list);
                    }else{
                        if(preg_match('/^(.*)\.phtml$/', $filename))
                            $list[] = $filename;
                    }
                }
            }
            return $list;
        };
        $list = array();
        $f($folder, $list);
        $data = array();
        foreach($list AS $file){
            $name = str_replace(array($folder.'/', '/', '.phtml'), array('','-', ''), $file);
            $model = new ViewModel();
            $model->setTemplate(str_replace(array($folder.'/', '.phtml'), array('', ''), $file));
            $data[$name] = $renderer->render($model);
        }
        return $this->outOk(array('list'=>$data));
    }
}