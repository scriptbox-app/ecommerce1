<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Widget\Models\Widget as WidgetModel;
use Botble\Theme\Facades\Theme;

class WidgetSeeder extends BaseSeeder
{
    public function run(): void
    {
        WidgetModel::query()->truncate();

        $data = [
            'en_US' => [
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 1,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Useful Links',
                        'menu_id' => 'useful-links',
                    ],
                ],
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 2,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Categories',
                        'menu_id' => 'categories',
                    ],
                ],
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 3,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'My Account',
                        'menu_id' => 'my-account',
                    ],
                ],
            ],
            'vi' => [
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 1,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Liên kết hữu ích',
                        'menu_id' => 'lien-ket-huu-ich',
                    ],
                ],
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 2,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Danh mục',
                        'menu_id' => 'danh-muc',
                    ],
                ],
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 3,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Tài khoản',
                        'menu_id' => 'tai-khoan',
                    ],
                ],
            ],
        ];

        $theme = Theme::getThemeName();

        foreach ($data as $locale => $widgets) {
            foreach ($widgets as $item) {
                $item['theme'] = $locale == 'en_US' ? $theme : ($theme . '-' . $locale);
                WidgetModel::query()->create($item);
            }
        }
    }
}
