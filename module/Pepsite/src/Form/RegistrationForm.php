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
            'type' => 'checkbox',
            'name' => 'showPassword',
            'options' => [
                'label' => 'Показать пароль',
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
//        TODO: add captcha
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'options' => [
                'value' => 'Зарегистрироваться',
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
                'validators' => [
                    [
                        [
                            'name' => StringLength::class,
                            'options' => [
                                'min' => 4,
                                'max' => 15,
                                'messages' => [
                                    StringLength::INVALID => 'Логин должен быть от 4 до 15 символов'
                                ]
                            ],
                        ],
                        [
                            'name' => Regex::class,
                            'options' => [
                                'pattern' => '[a-z,A-Z,0-9]+',
                                'messages' => [
                                    Regex::NOT_MATCH => 'Логин должен состоять из букв латинского алфавитаи и цифр'
                                ]
                            ],
                            'break_chain_on_failure' => true
                        ],
                        [
                            'name' => NoRecordExists::class,
                            'options' => [
                                'table'   => 'users',
                                'field'   => 'login',
                                'adapter' => $this->dbAdapter,
                                'messages' => [
                                    NoRecordExists::ERROR_RECORD_FOUND => 'Логин занят'
                                ]
                            ],
                        ]
                    ],
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
                        'min' => 4,
                        'max' => 15,
                        'messages' => [
                            StringLength::INVALID => 'Пароль должен быть от 5 до 25 символов'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
//                    TODO: add pass characters validation
                ]
            ]
        ]);

        $inputFilter->add([
            'name'     => 'avatar',
            'required' => false,
            'filters'  => [
//                TODO: possible rename here
            ],
            'validators' => [
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' => ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
                        'messages' => [
                            MimeType::FALSE_TYPE => 'Аватар должен быть файлом расширения .jpeg, .jpg, .gif или .png'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => '5MB',
                        'messages' => [
                            Size::TOO_BIG => 'Аватар не долженпревышать по размеру 5 МБ'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
//                    TODO: image aspect ration + min_width=50px validator
                ],
            ]
        ]);
    }
}
