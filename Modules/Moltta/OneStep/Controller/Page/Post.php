<?php

namespace Moltta\OneStep\Controller\Page;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Request\DataPersistorInterface;
use Zend\Log\Filter\Timestamp;

class Post extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';

    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        array $data = []

        )
    {
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();

        parent::__construct($context);

    }

    public function execute()
    {
        $post = $this->getRequest()->getPost();
        try
        {
            // Define variables
            $vars = array(
            'name'  => $post['name'],
            'email'  => $post['email'],
            'country' => $post['country'],
            'company' => $post['company'],
            'address1' => $post['address1'],
            'address2' => $post['address2'],
            'city' => $post['city'],
            'state' => $post['state'],
            'zipcode' => $post['zipcode'],
            'comment' => $post['comment'],
            'copy' => $post['copy']
            );

            // Send Mail with or without copy
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

            $sender = [
                'name' => 'Sales Representative', /* Sender name, optional */
                'email' => 'example@.gmail.com', /* Admin email, optional */
            ];

            $sentToEmail = $this->_scopeConfig ->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $sentToName = $this->_scopeConfig ->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $transport = $this->_transportBuilder
            ->setTemplateIdentifier('customemail_email_template')
            ->setTemplateOptions(
                [
                    'area' =>  \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
                )
                ->setTemplateVars($vars)
                ->setFrom($sender)
                ->addTo($sentToEmail,$sentToName)
                //->addTo($anemail,$aname)
                ->getTransport();

                if($post['copy'] == 'send') {
                  $anemail = $post['email'];
                  $aname = $post['name'];
                  $transport = $this->_transportBuilder
                  ->setTemplateIdentifier('customemail_email_template')
                  ->setTemplateOptions(
                      [
                          'area' =>  \Magento\Framework\App\Area::AREA_FRONTEND,
                          'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                      ]
                      )
                      ->setTemplateVars($vars)
                      ->setFrom($sender)
                      ->addTo($sentToEmail,$sentToName)
                      ->addTo($anemail,$aname)
                      ->getTransport();
                }

                $transport->sendMessage();

                $this->_inlineTranslation->resume();
                $this->messageManager->addSuccess('Email sent successfully');
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cartObject = $objectManager->create('Magento\Checkout\Model\Cart')->truncate();
                $cartObject->saveQuote();

                $this->_redirect('onestep/page/view');

        }

        catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
            $this->_logLoggerInterface->debug($e->getMessage());
            exit;
        }
    }
}
