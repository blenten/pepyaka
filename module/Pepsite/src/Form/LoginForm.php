<?php
namespace Pepsite\Form;

use Zend\Captcha\Figlet;
use Zend\Form\Form;

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
            'name' => 'captcha',
            'attributes' => [
            ],
            'options' => [
                'captcha' => [
                    'class' => Figlet::class,
                    'wordLen' => 6,
                    'expiration' => 600,
                    'messages' => [
                        Figlet::BAD_CAPTCHA => 'Неверно введены цифры с картинки'
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
