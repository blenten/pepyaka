<?php

namespace Pepsite\ViewHelper;

use Pepsite\Service\ImageManager;
use Pepsite\Service\UserManager;
use Zend\View\Helper\AbstractHelper;

class Avatar extends AbstractHelper
{
    private $userManager;
    private $imageManager;

    public function __construct(UserManager $userManager, ImageManager $imageManager)
    {
        $this->userManager = $userManager;
        $this->imageManager = $imageManager;
    }

    public function __invoke($avatar)
    {
        $name = $avatar ?? 'default.jpg';
        $imageName = $this->imageManager::AVATAR_DIR . $name;
        $imageData = $this->imageManager->getBase64Data($imageName);
        return 'data:image/jpeg;base64,' . $imageData;
    }
}
