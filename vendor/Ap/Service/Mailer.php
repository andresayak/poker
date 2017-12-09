<?php

namespace Ap\Service;

use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
use Zend\View\Resolver;
use Zend\Mail\Message;
use Zend\Mail;
use Zend\Mail\Transport\SmtpOptions;

class Mailer 
{
    protected $_transport, $_renderer;
    protected $_options = array(
        'status'        =>  false,
        'site_email'    =>  null,
        'script_paths'  =>  array()
    );
    
    public function __construct($options)
    {
        $this->_options = array_merge($this->_options, $options);
        return $this;
    }
    
    public function getOption($var)
    {
        if($this->_options[$var] === null){
            throw new \Exception('Option not set ['.$var.']');
        }
        return $this->_options[$var];
    }
    
    public function setOption($var, $value)
    {
        $this->_options[$var] = $value;
        return $this;
    }
    
    public function setScriptPaths($paths)
    {
        foreach($paths AS $path){
            if(!is_dir($path)){
                throw new \Exception('Path invalid ['.$path.']');
            }
        }
        $this->setOption('script_paths', $paths);
        return $this;
    }
    public function getTransport()
    {
        if($this->_transport === null){
            $this->_transport = new Mail\Transport\Smtp(new SmtpOptions($this->_options['smtp']));
        }
        return $this->_transport;
    }
    
    public function getRenderer()
    {
        if($this->_renderer === null){
            $this->_renderer = new PhpRenderer();
            $resolver = new Resolver\AggregateResolver();
            $this->_renderer->setResolver($resolver);
            $stack = new Resolver\TemplatePathStack(array(
                'script_paths' => $this->_options['script_paths']));
            $resolver->attach($stack);
        }
        return $this->_renderer;
    }
    
    public function setRenderer(PhpRenderer $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }
    
    function send($viewModel, Message $message) 
    {
        if($viewModel instanceof ViewModel){
            $html = $this->getRenderer()->render($viewModel);
            $bodyMessage = new \Zend\Mime\Part($html);
            $bodyMessage->type = 'text/html';
            $bodyPart = new \Zend\Mime\Message();
            $bodyPart->setParts(array($bodyMessage));
        }else{
            $bodyPart = $viewModel;
        }
        $siteEmail = $this->getOption('site_email');
        $siteName = $this->getOption('site_name');
        $message->setSender($siteEmail, $siteName)
                ->addFrom($siteEmail, $siteName);
        $message->setBody($bodyPart);
        $message->setEncoding('UTF-8');

        $this->getTransport()->send($message);
    }
}