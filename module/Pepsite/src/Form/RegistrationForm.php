<?php
namespace Pepsite\Form;

use Pepsite\Entity\User;
use Zend\Captcha\Figlet;
use Zend\Form\Form;
use Zend\Validator\{InArray, NotEmpty, StringLength, Regex, Db\NoRecordExists};
use Zend\Validator\File\{MimeType, Size};
use Zend\InputFilter\FileInput;

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
            ],
            'attributes' => [
                'required'  => true,
                'autofocus' => true,
            ]
        ]);

        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Пароль',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);

        $this->add([
            'type' => 'file',
            'name' => 'avatar',
            'options' => [
                'label' => 'Аватар',
            ],
            'attributes' => [
                'accept' => 'image/jpeg, image/jpg, image/gif, image/png'
            ]
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'gender',
            'options' => [
                'label' => 'Пол',
                'value_options' => [
                    User::GENDER_MALE  => 'Мужской',
                    User::GENDER_FEMALE => 'Женский'
                ],
                'empty_option' => 'Выберите пол...',
                'disable_inarray_validator' => true,
                'attributes' => [
                    'required' => true,
                ]
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
            'allow_empty' => false,
            'continue_if_empty' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Логин не может быть пустым'
                        ]
                    ]
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 15,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Длина логина должна быть не менее %min% символов',
                            StringLength::TOO_LONG  => 'Длина логина должна быть не более %max% символов'
                        ]
                    ],
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '~(?=.*[a-z,A-Z])^[a-z,A-Z,0-9]+$~',
                        'messages' => [
                            Regex::NOT_MATCH =>
                                'Логин должен состоять из букв латинского алфавита и, возможно, цифр.'
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
        ]);

        $inputFilter->add([
            'name'     => 'password',
            'required' => true,
            'allow_empty' => false,
            'continue_if_empty' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Пароль не может быть пустым'
                        ]
                    ]
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 25,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Длина пароля должна быть не менее %min% символов',
                            StringLength::TOO_LONG  => 'Длина пароля должна быть не более %max% символов'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '~(?=.*\d)[a-z,A-Z,\d]*~',
                        'messages' => [
                            Regex::NOT_MATCH =>
                                'Пароль должен состоять из букв латинского алфавита и содержать хотя бы одну цифру'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
            ]
        ]);

        $inputFilter->add([
            'type' => FileInput::class,
            'name'     => 'avatar',
            'required' => false,
            'validators' => [
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' => ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
                        'messages' => array_fill_keys(
                            [MimeType::FALSE_TYPE, MimeType::NOT_DETECTED, MimeType::NOT_READABLE],
                            'Аватар должен быть файлом расширения .jpeg, .jpg, .gif или .png'
                        )
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => '5MB',
                        'messages' => [
                            Size::TOO_BIG => 'Размер аватара не должен превышать %max%'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'gender',
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Пол не указан'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => InArray::class,
                    'options' => [
                        'haystack' => [
                            User::GENDER_MALE,
                            User::GENDER_FEMALE,
                        ],
                        'messages' => [
                            InArray::NOT_IN_ARRAY => 'Пол не существует'
                        ]
                    ],
                ],
            ]
        ]);

        $inputFilter->add([
            'name'     => 'captcha',
            'required' => true,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Пожалуйста, введите капчу'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ]
            ]
        ]);
    }
}
