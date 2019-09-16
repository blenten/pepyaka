<?php
namespace Pepsite\Form;

use Zend\Captcha\Dumb;
use Zend\Form\Form;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('login-form');

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
            ],
            'attributes' => [
                'required'    => true,
                'autofocus'   => true,
                'placeholder' => 'Логин'
            ]
        ]);

        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Пароль',
            ],
            'attributes' => [
                'required'    => true,
                'placeholder' => 'Пароль'
            ]
        ]);

        $this->add([
            'type'  => 'captcha',
            'name'  => 'captcha',
            'options' => [
                'label'   => 'Human check',
                'captcha' => [
                    'class' => Dumb::class,
                    'wordLen'    => 6,
                    'messages' => [
                        Dumb::BAD_CAPTCHA => 'Капча не валидна'
                    ]
                ],
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Войти'
            ]
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
            'validators' => [],
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
