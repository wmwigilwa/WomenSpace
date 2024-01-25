<?php
namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private FactoryInterface $factory;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * Add any other dependency you need...
     * @param FactoryInterface $factory
     * @param RequestStack $requestStack
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu(array $options): ItemInterface
    {

        $menu = $this->factory->createItem('root',
            [
                'childrenAttributes' => ['class' => 'space-y-1.5']
            ]
        );

        $menu->addChild('Home', ['route' => 'app_dashboard', 'extras'=>['icon'=>'house']]);

        $menu->addChild('Configuration', ['uri' => '#', 'extras'=>['icon'=>'gears']])
            ->setAttributes(['class'=>'hs-accordion', 'id'=>'configuration-accordion'])
            ->setChildrenAttribute('class', '')
            ->addChild('Manage Spaces', ['route' => 'app_configuration_space_index'])
                ->addChild('Add', ['route' => 'app_configuration_space_new'])->setDisplay(false)->getParent()
                ->addChild('Edit', ['route' => 'app_configuration_space_edit'])->setDisplay(false)->getParent()
                ->getParent()
            ->addChild('Manage Tags', ['route' => 'app_configuration_tag_index'])
            ->addChild('Add', ['route' => 'app_configuration_tag_new'])->setDisplay(false)->getParent()
            ->addChild('Edit', ['route' => 'app_configuration_tag_edit'])->setDisplay(false)->getParent()
            ->getParent();


        $menu->addChild('System users', ['uri' => '#', 'extras'=>['icon'=>'users']])
            ->setAttributes(['class'=>'hs-accordion', 'id'=>'system-users-accordion'])
            ->setChildrenAttribute('class', ' ')
                ->addChild('Manage Roles', ['route' => 'app_role_index'])
                ->addChild('Add', ['route' => 'app_role_new'])->setDisplay(false)->getParent()
                ->addChild('Edit', ['route' => 'app_role_edit'])->setDisplay(false)->getParent()
                ->addChild('Details', ['route' => 'app_user_accounts_user_show'])->setDisplay(false)->getParent()
            ->getParent()
                ->addChild('Manage Users', ['route' => 'app_user_accounts_user_index'])
                ->addChild('Add', ['route' => 'app_user_accounts_user_new'])->setDisplay(false)->getParent()
                ->addChild('Edit', ['route' => 'app_user_accounts_user_edit'])->setDisplay(false)->getParent()
            ->getParent();

        return $menu;
    }
    public function getParameter($name)
    {
        return $this->requestStack->getCurrentRequest()->get($name);
    }
}