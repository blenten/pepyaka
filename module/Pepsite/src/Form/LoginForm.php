<?php
namespace Pepsite\Form;

use Zend\Form\Form;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class LoginForm extends Form
{
    private $dbAdapter;

    public function __construct($dbAdapter)
    {
        parent::__construct('login-form');

        $this->dbAdapter = $dbAdapter;
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addFilters();
    }

    private function addElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'login',
            'options' => [
                'label' => 'Логин',
            ]
        ]);

        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Пароль',
            ]
        ]);

        $this->add([
            'type'  => 'captcha',
            'name' => 'captcha',
            'options' => [
                'label' => 'Human check',
                'captcha' => [
                    'class' => 'Dumb',
                    'wordLen' => 6,
                    'expiration' => 600,
                ],
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
        ]);
    }

    private function addFilters()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'     => 'login',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => RecordExists::class,
                    'options' => [
                        'table'   => 'users',
                        'field'   => 'login',
                        'adapter' => $this->dbAdapter,
                    ],
                ]
            ],
        ]);

        $inputFilter->add([
            'name'     => 'password',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => []
        ]);
    }
}
