<?php
namespace Pepsite\Form;

use Zend\Form\Form;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class RegistrationForm extends Form
{
    private $dbAdapter;

    public function __construct($dbAdapter)
    {
        parent::__construct('registration-form');

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
            'type' => 'file',
            'name' => 'avatar',
            'options' => [
                'label' => 'Аватар',
            ]
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'sex',
            'options' => [
                'label' => 'Пол',
                'value_options' => [
                    'M' => 'Мужской',
                    'F' => 'Женский'
                ],
                'empty_option' => false
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
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 15,
                    ],
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '~^[a-z,A-Z,0-9]+$~',
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => NoRecordExists::class,
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
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 15,
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '~(?=.*\d)[a-z,A-Z,\d]*~',
                    ],
                    'break_chain_on_failure' => true
                ],
            ]
        ]);

        $inputFilter->add([
            'name'     => 'avatar',
            'required' => false,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' => ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => '5MB',
                    ],
                    'break_chain_on_failure' => true
                ],
//                [
//                    TODO: image aspect ration + min_width=50px validator
//                ],
            ]
        ]);
    }
}
