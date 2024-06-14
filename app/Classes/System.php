<?php
namespace App\Classes;

class System{
    public function config(){
        $data['homepage'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin chung của website.Tên thương hiệu website, Logo, Favicon, vv.....',
            'value' => [
                'company' => ['type' => 'text', 'label'=> 'Tên công ty'],
                'brand' => ['type' => 'text', 'label'=> 'Tên thương hiệu'],
                'slogan' => ['type' => 'text', 'label'=> 'Slogan'],
                'logo' => ['type' => 'images', 'label'=> 'Logo Website','title' => 'Click vào ô phía dưới để tải logo' ],
                'favicon' => ['type' => 'images', 'label'=> 'Favicon','title' => 'Click vào ô phía dưới để tải logo' ],
                'copyright' => ['type' => 'text', 'label'=> 'Copyright'],
                'website' => [
                    'type' => 'select', 
                    'label'=> 'Tình trạng website',
                    'option' => [
                        'open' => 'Mở cửa website',
                        'close' => 'Website đang bảo trì',
                    ]
                ],
                'short_intro' => ['type' => 'editor', 'label' => 'Giới thiệu ngắn'],
            ]
        ];

        $data['contact'] = [
            'label' => 'Thông tin liên hệ',
            'description' => 'Cài đặt đầy đủ Thông tin liên hệ của website ví dụ: Địa chỉ công ty, Văn phòng giao dịch, Hotline, Bản đồ,vv.....',
            'value' => [
                'office' => ['type' => 'text', 'label'=> 'Địa chỉ công ty'],
                'address' => ['type' => 'text', 'label'=> 'Văn phòng giao dịch '],
                'hotline' => ['type' => 'text', 'label'=> 'Hotline'],
                'technical_phone' => ['type' => 'text', 'label'=>'Hotline kỹ thuật' ],
                'sell_phone' => ['type' => 'text', 'label'=> 'Hotline doanh nghiệp'],
                'phone' => ['type' => 'text', 'label'=> 'Số cố định'],
                'fax' => ['type' => 'text', 'label'=> 'Fax'],
                'emai' => ['type' => 'text', 'label'=> 'Email'],
                'tax' => ['type' => 'text', 'label'=> 'Mã số thuế'],
                'website' => ['type' => 'text', 'label'=> 'Website'],
                'map' => [
                    'type' => 'textarea', 
                    'label'=> 'Bản đồ',
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => '#' ,
                        'href' => 'https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/',
                        'target' => '_blank',
                        ]
                    ],
            ]
        ];

        $data['seo'] = [
            'label' => 'Cấu hình SEO dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm tiêu đề SEO, Từ khóa SEO, Mô tả SEO, Meta images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label'=> 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label'=> 'Từ khóa SEO'],
                'meta_description' => ['type' => 'text', 'label'=> 'Mô tả SEO'],
                'meta_images' => ['type' => 'images', 'label'=> 'Ảnh SEO'],
            ]
        ];

        $data['social'] = [
            'label' => 'Cấu hình mạng xã hội dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về mạng xã hội của trang chủ website. Bao gồm tiêu đề mạng xã hội, Từ khóa SEO, Mô tả SEO, Meta images',
            'value' => [
                'facebook' => ['type' => 'text', 'label'=> 'Fcebook'],
                'youtube' => ['type' => 'text', 'label'=> 'Youtube'],
                'twitter' => ['type' => 'text', 'label'=> 'Twitter'],
                'tiktok' => ['type' => 'text', 'label'=> 'Tiktok'],
                'instagram' => ['type' => 'text', 'label'=> 'Instagram'],


            ]
        ];

        return $data;
    }
}
