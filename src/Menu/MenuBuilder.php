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
            ->addChild('Manage Content Types', ['route' => 'app_configuration_content_type_index'])
                ->addChild('Add', ['route' => 'app_configuration_content_type_new'])->setDisplay(false)->getParent()
                ->addChild('Edit', ['route' => 'app_configuration_content_type_edit'])->setDisplay(false)->getParent()
                ->getParent()
            ->addChild('Manage Fact Types', ['route' => 'app_configuration_fact_type_index'])
                ->addChild('Add', ['route' => 'app_configuration_fact_type_new'])->setDisplay(false)->getParent()
                ->addChild('Edit', ['route' => 'app_configuration_fact_type_edit'])->setDisplay(false)->getParent()
                ->getParent();

        $menu->addChild('Location', ['uri' => '#', 'extras'=>['icon'=>'location-dot']])
                ->setAttributes(['class'=>'hs-accordion', 'id'=>'location-accordion'])
                    ->addChild('Manage Regions', ['route' => 'app_location_region_index'])
                    ->addChild('Add', ['route' => 'app_location_region_new'])->setDisplay(false)->getParent()
                    ->addChild('Edit', ['route' => 'app_location_region_edit'])->setDisplay(false)->getParent()
                ->getParent()
                    ->addChild('Manage Sub-Regions', ['route' => 'app_location_sub_region_index'])
                    ->addChild('Add', ['route' => 'app_location_sub_region_new'])->setDisplay(false)->getParent()
                    ->addChild('Edit', ['route' => 'app_location_sub_region_edit'])->setDisplay(false)->getParent()
                ->getParent()
                    ->addChild('Manage Countries', ['route' => 'app_location_country_index'])
                    ->addChild('Add', ['route' => 'app_location_country_new'])->setDisplay(false)->getParent()
                    ->addChild('Edit', ['route' => 'app_location_country_edit'])->setDisplay(false)->getParent()
                    ->addChild('Show', ['route' => 'app_location_country_show'])->setDisplay(false)->getParent()
                    ->addChild('View Facts', ['route' => 'app_data_country_fact_index'])->setDisplay(false)->getParent()
                    ->addChild('Add Facts', ['route' => 'app_data_country_fact_new'])->setDisplay(false)->getParent()->getParent()
                    ->addChild('Edit Facts', ['route' => 'app_data_country_fact_edit'])->setDisplay(false)->getParent()->getParent()
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