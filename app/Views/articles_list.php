<?php

use App\Models\{Category};

use Tischmann\Atlantis\{Locale, Request, Template};

$reqest = Request::instance();

$category_id = mb_strtolower(strval($reqest->request('category_id') ?? 'all'));

$visible = strval($reqest->request('visible'));

$locale = strval($reqest->request('locale'));

$fixed = strval($reqest->request('fixed'));

$order = strval($reqest->request('order') ?? 'created_at');

$order_types = [
    'created_at',
    'title',
    'visible',
    'fixed'
];

$order = in_array($order, $order_types) ? $order : 'created_at';

$direction = strval($reqest->request('direction') ?? 'desc');

$direction = mb_strtolower($direction);

$direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

$category = null;

if ($category_id === 'all') {
    $category = new Category();
} else if ($category_id !== "") {
    $category = Category::find($category_id);
}


?>
<main class="md:container mx-4 md:mx-auto mb-4">
    <div class="mb-8 flex flex-col sm:flex-row gap-4">
        <?php

        $items = "";

        foreach ($order_types as $value) {
            $items .= Template::html(
                template: 'assets/option_field',
                args: [
                    'value' => $value,
                    'title' => get_str("article_order_{$value}"),
                    'class' => $order === $value
                        ? 'bg-sky-600 text-white'
                        : ''
                ]
            );
        }

        Template::echo(
            template: 'assets/select_field',
            args: [
                'label' => get_str("article_order"),
                'value' => $order,
                'name' => "order",
                'title' => get_str("article_order_{$order}"),
                'items' => $items
            ]
        );

        Template::echo(
            template: 'assets/select_field',
            args: [
                'label' => get_str("article_direction"),
                'value' => $direction,
                'name' => "direction",
                'title' => get_str("article_direction_{$direction}"),
                'items' =>  Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => 'asc',
                        'title' => get_str("article_direction_asc"),
                        'class' => $direction === 'asc'
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                ) .  Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => 'desc',
                        'title' => get_str("article_direction_desc"),
                        'class' => $direction === 'desc'
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                )
            ]
        );
        ?>
    </div>
    <div class="mb-8 flex flex-col sm:flex-row gap-4">
        <?php

        $items = Template::html(
            template: 'assets/option_field',
            args: [
                'value' => "",
                'title' => get_str('article_locale_all'),
                'class' => $locale === ""
                    ? 'bg-sky-600 text-white'
                    : ''
            ]
        );

        foreach (Locale::available() as $value) {
            $items .= Template::html(
                template: 'assets/option_field',
                args: [
                    'value' => $value,
                    'title' => get_str("article_locale_" . $value),
                    'class' => $locale === $value
                        ? 'bg-sky-600 text-white'
                        : ''
                ]
            );
        }

        Template::echo(
            template: 'assets/select_field',
            args: [
                'label' => get_str('article_locale'),
                'value' => $locale,
                'name' => "locale",
                'title' => get_str("article_locale_" . ($locale !== "" ? $locale : "all")),
                'items' => $items
            ]
        );

        $query = Category::query()
            ->where('parent_id', null)
            ->order('locale', 'ASC')
            ->order('title', 'ASC');

        $items = Template::html(
            template: 'assets/option_field',
            args: [
                'value' => 'all',
                'title' => get_str('article_category_all'),
                'class' => $category !== null && $category?->id === 0
                    ? 'bg-sky-600 text-white'
                    : ''
            ]
        ) . Template::html(
            template: 'assets/option_field',
            args: [
                'value' => '',
                'title' => '',
                'class' => $category === null
                    ? 'bg-sky-600 text-white'
                    : ''
            ]
        );

        foreach (Category::all($query) as $cat) {
            assert($cat instanceof Category);

            $items .= Template::html(
                template: 'assets/option_field',
                args: [
                    'value' => $cat->id,
                    'title' => $cat->title,
                    'class' => $cat->id === $category?->id
                        ? 'bg-sky-600 text-white'
                        : ''
                ]
            );

            $cat->children = $cat->fetchChildren();

            foreach ($cat->children as $child) {
                assert($child instanceof Category);

                $items .= Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => $child->id,
                        'title' => $child->title,
                        'class' => $child->id === $category?->id
                            ? 'bg-sky-600 text-white pl-8'
                            : 'pl-8'
                    ]
                );

                $child->children = $child->fetchChildren();

                foreach ($child->children as $grandchild) {
                    assert($grandchild instanceof Category);

                    $items .= Template::html(
                        template: 'assets/option_field',
                        args: [
                            'value' => $grandchild->id,
                            'title' => $grandchild->title,
                            'class' => $grandchild->id === $category?->id
                                ? 'bg-sky-600 text-white pl-12'
                                : 'pl-12'
                        ]
                    );
                }
            }
        }

        Template::echo(
            template: 'assets/select_field',
            args: [
                'label' => get_str('article_category'),
                'value' => $category?->id,
                'name' => "category_id",
                'title' => $category === null ? "" : ($category?->id ? $category?->title : get_str('article_category_all')),
                'items' => $items
            ]
        );

        Template::echo(
            template: 'assets/select_field',
            args: [
                'label' => get_str('article_visibility'),
                'value' => $visible,
                'name' => "visible",
                'title' => match ($visible) {
                    "0" => get_str('article_visible_invisible'),
                    "1" => get_str('article_visible_visible'),
                    default => get_str('article_visible_all')
                },
                'items' => Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => "",
                        'title' => get_str('article_visible_all'),
                        'class' => $visible === ""
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                ) . Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => "1",
                        'title' => get_str('article_visible_visible'),
                        'class' => $visible === "1"
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                ) . Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => "0",
                        'title' => get_str('article_visible_invisible'),
                        'class' => $visible === "0"
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                )
            ]
        );

        Template::echo(
            template: 'assets/select_field',
            args: [
                'label' => get_str('article_fixed'),
                'value' => $fixed,
                'name' => "fixed",
                'title' => match ($fixed) {
                    "1" => get_str('article_fixed_on'),
                    "0" => get_str('article_fixed_off'),
                    default => get_str('article_fixed_all')
                },
                'items' => Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => "",
                        'title' => get_str('article_fixed_all'),
                        'class' => $fixed === ""
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                ) . Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => "1",
                        'title' => get_str('article_fixed_on'),
                        'class' => $fixed === "1"
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                ) . Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => "0",
                        'title' => get_str('article_fixed_off'),
                        'class' => $fixed === "0"
                            ? 'bg-sky-600 text-white'
                            : ''
                    ]
                )
            ]
        );
        ?>
    </div>

    <?php

    use App\Models\Article;

    use Tischmann\Atlantis\Pagination;

    $query = Article::query()
        ->order($order, $direction);

    if ($category === null) {
        $query->where('category_id', null);
    } else if ($category?->id) {
        $query->where('category_id', $category?->id);
    }

    if ($visible !== "") {
        $query->where('visible', $visible);
    }

    if ($locale !== "") {
        $query->where('locale', $locale);
    }

    if ($fixed !== "") {
        $query->where('fixed', $fixed);
    }

    $pagination = new Pagination(query: $query, limit: 10);

    $articles = Article::all($query);

    if ($articles) {
        echo <<<HTML
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        HTML;

        foreach ($articles as $article) {
            assert($article instanceof Article);
            include  'article_main.php';
        }

        echo <<<HTML
        </div>
        HTML;
    } else {
        echo <<<HTML
        <div class="flex flex-col items-center justify-center m-8 h-[70vh]">
            <h2 class="m-8">{{lang=articles_not_found}}</h2>
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 500 500" class="block w-full h-full" xml:space="preserve">
            <g id="Floor">
                <ellipse id="Floor_7_" style="fill:#F5F5F5;" cx="253.742" cy="341.762" rx="235.295" ry="135.848"/>
            </g>
            <g id="Shadow">
                <ellipse id="Shadow_29_" style="fill:#E0E0E0;" cx="408.883" cy="369.843" rx="66.748" ry="38.537"/>
                <polygon id="Shadow_28_" style="fill:#E0E0E0;" points="239.95,390.631 164.652,347.144 250.72,297.454 326.029,340.933 	"/>
                <polygon id="Shadow_27_" style="fill:#E0E0E0;" points="34.91,377.176 132.029,433.248 213.492,386.232 116.373,330.16 	"/>
            </g>
            <g id="Shelf_1_">
                <g id="Shelf_2_">
                    <g id="Shelf">
                        <path style="fill:#F0F0F0;" d="M178.517,113.629v-5.515h-0.003c-0.017-0.524-0.385-1.057-1.104-1.47l-33.32-19.122
                            c-1.414-0.812-3.708-0.812-5.122,0l-95.786,54.97c-0.72,0.413-1.087,0.946-1.104,1.47h-0.005v5.515h0.005
                            c0.017,0.524,0.385,1.057,1.104,1.47l33.319,19.121c1.414,0.812,3.708,0.812,5.122,0l95.786-54.97
                            c0.72-0.413,1.087-0.946,1.104-1.47H178.517z"/>
                        <path style="fill:#FAFAFA;" d="M43.182,145.433l33.319,19.121c1.415,0.812,3.708,0.812,5.122,0l95.786-54.971
                            c1.415-0.812,1.415-2.128,0-2.94L144.09,87.523c-1.414-0.812-3.708-0.812-5.122,0l-95.786,54.97
                            C41.768,143.305,41.768,144.621,43.182,145.433z"/>
                        <path style="fill:#E0E0E0;" d="M79.058,165.168v5.515c-0.923,0-1.845-0.212-2.555-0.611l-33.324-19.12
                            c-0.722-0.411-1.088-0.952-1.1-1.47h-0.012v-5.515h0.012c0.008-0.21,0.09-0.42,0.21-0.625c-0.387,0.711-0.096,1.529,0.89,2.095
                            l33.324,19.119C77.201,164.968,78.136,165.168,79.058,165.168z"/>
                    </g>
                    <g id="Book_2_">
                        <g id="XMLID_866_">
                            <polygon id="XMLID_871_" style="fill:#E0E0E0;" points="96.959,118.53 125.189,134.624 171.825,107.86 143.598,91.764 				"/>
                            <path id="XMLID_870_" style="fill:#FFFFFF;" d="M125.511,133.4l-27.232-15.628c0.711-2.019,0.658-4.374-0.147-7.062
                                l27.058,15.462C125.189,126.173,125.706,127.878,125.511,133.4z"/>
                            <path id="XMLID_869_" style="fill:#EBEBEB;" d="M123.8,123.967c1.445,0.829,2.616,2.846,2.616,4.505v5.263
                                c0,1.659-1.171,2.331-2.616,1.502l-26.838-15.402l-0.003-1.304l26.479,15.1c0.867,0.498,1.569,0.094,1.568-0.901l-0.025-4.877
                                c-0.001-0.995-0.705-2.205-1.572-2.703l-26.448-15.107v-1.478L123.8,123.967z"/>
                            <path id="XMLID_868_" style="fill:#FAFAFA;" d="M96.962,108.568l46.637-26.764l26.835,15.4c1.444,0.829,2.616,2.847,2.616,4.505
                                l0.001,5.263c-0.001,0.826-0.291,1.409-0.767,1.682l-46.637,26.764c0.476-0.273,0.766-0.856,0.768-1.682l0.004-5.267
                                c0-1.658-1.172-3.676-2.616-4.505L96.962,108.568z"/>
                            <path id="XMLID_867_" style="fill:#F0F0F0;" d="M125.652,125.907c0.473,0.815,0.767,1.734,0.766,2.563l-0.004,5.267
                                c-0.002,0.826-0.291,1.408-0.768,1.682l46.637-26.764c0.476-0.273,0.766-0.856,0.767-1.682l-0.001-5.263
                                c0-0.829-0.293-1.748-0.767-2.563L125.652,125.907z"/>
                        </g>
                    </g>
                    <g id="Book_1_">
                        <polygon id="XMLID_2995_" style="fill:#EBEBEB;" points="68.257,78.477 67.993,82.041 99.51,98.947 100.221,96.872 			"/>
                        <polygon id="XMLID_2993_" style="fill:#FFFFFF;" points="99.802,97.698 69.272,80.075 63.963,83.14 95.093,101.352 			"/>
                        <path id="XMLID_2991_" style="fill:#E0E0E0;" d="M89.36,99.465l-3.018,35.378c0,0.723,0.505,1.599,1.129,1.961l17.711,10.25
                            c0.257,0.148,0.584,0.235,0.919,0.261l-11.007-44.811c-0.336-0.026-0.662-0.109-0.919-0.257l-1.276-0.741L89.36,99.465z"/>
                        <path id="XMLID_2990_" style="fill:#F0F0F0;" d="M96.439,102.247l3.156-2.704l2.32-1.987c0.237-0.378,0.513-0.66,0.365-0.969
                            l10.859,44.489l0.017,0.035c0.153,0.314,0.192,1.013-0.235,1.257l-5.475,4.687c-0.37,0.214-0.871,0.301-1.346,0.261
                            l-11.007-44.811C95.568,102.543,96.069,102.456,96.439,102.247z"/>
                        <path id="XMLID_2988_" style="fill:#FAFAFA;" d="M101.916,96.248c0.625,0.361,0.47,0.764,0,1.307l-5.477,4.69
                            c-0.625,0.361-1.639,0.361-2.264,0l-4.816-2.781c-0.625-0.361-0.736-0.882-0.247-1.164c0.489-0.282,1.392-0.218,2.017,0.143
                            l3.8,2.194c0.208,0.12,0.546,0.12,0.755,0l4.002-3.475c0.208-0.12,0.208-0.315,0-0.436L68.257,78.477l1.506-0.869L101.916,96.248
                            z"/>
                    </g>
                    <g id="Book">
                        <g id="XMLID_777_">
                            <polygon id="XMLID_856_" style="fill:#EBEBEB;" points="59.16,80.186 89.191,97.535 89.191,154.667 59.16,137.322 				"/>
                            <path id="XMLID_854_" style="fill:#FFFFFF;" d="M88.225,98.483L59.16,81.803c-1.497,1.731-3.717,2.939-6.646,3.633
                                l28.818,16.608C81.332,102.045,83.194,101.61,88.225,98.483z"/>
                            <path id="XMLID_852_" style="fill:#FAFAFA;" d="M78.539,101.946c1.542,0.885,4.043,0.885,5.586,0l4.894-2.809
                                c1.542-0.885,1.542-2.32,0-3.205L60.374,79.493l-1.214,0.693l28.173,16.271c0.925,0.531,0.925,1.392-0.001,1.922l-4.548,2.58
                                c-0.926,0.53-2.427,0.53-3.352-0.001L51.268,84.718l-1.374,0.788L78.539,101.946z"/>
                            <path id="XMLID_849_" style="fill:#EBEBEB;" d="M49.897,85.505v57.133l28.642,16.437c1.542,0.885,4.044,0.885,5.585,0
                                l4.895-2.808c0.767-0.442,1.154-1.019,1.154-1.603V97.532c0,0.583-0.387,1.16-1.154,1.603l-4.895,2.814
                                c-1.542,0.885-4.044,0.885-5.585,0L49.897,85.505z"/>
                            <path id="XMLID_801_" style="fill:#F0F0F0;" d="M81.332,102.612c1.011,0,2.022-0.221,2.793-0.663l4.895-2.814
                                c0.767-0.442,1.154-1.019,1.154-1.603v57.133c0,0.583-0.387,1.16-1.154,1.603l-4.895,2.808
                                c-0.771,0.442-1.782,0.663-2.793,0.663V102.612z"/>
                        </g>
                    </g>
                </g>
            </g>
            <g id="Plants_2_">
                <g id="Plants_5_">
                    <path style="fill:none;stroke:#27DEBF;stroke-width:0.8828;stroke-miterlimit:10;" d="M323.007,309.956
                        c-0.681-11.984-5.988-35.976,1.005-58.114c3.554-11.252,11.717-23.174,23.871-26.533c4.299-1.189,9.615-1.031,11.73,3.869
                        c0.355,0.823,0.554,1.711,0.618,2.605c0.264,3.723-1.428,7.017-3.311,10.059c-4.305,6.957-9.775,13.134-14.138,20.055
                        c-2.313,3.669-4.295,7.52-6.01,11.49"/>
                    <path style="fill:none;stroke:#27DEBF;stroke-width:0.8828;stroke-miterlimit:10;" d="M322.721,317.788
                        c0,1.437,0.379,3.238,0.061,4.61c-0.096,0.414-0.181,0.865-0.004,1.251c0.178,0.386,0.736,0.614,1.042,0.319
                        c0.221-0.213,0.453-1.757,0.595-2.15c0.303-0.836,0.762-1.648,1.155-2.445c0.796-1.617,1.645-3.208,2.543-4.77
                        c1.854-3.222,3.92-6.322,6.181-9.273c4.452-5.812,9.658-11.049,15.468-15.507c6.304-4.838,13.279-8.765,19.443-13.781
                        c1.816-1.478,3.626-3.146,4.406-5.354c1.164-3.298-0.381-7.15-3.122-9.323c-2.741-2.172-6.427-2.858-9.915-2.6
                        c-3.338,0.247-7.834,1.684-11.524,3.694c-7.52,4.096-12.687,10.441-16.82,17.627c-5.516,9.589-8.656,20.508-9.509,31.516
                        C322.721,313.664,322.721,315.726,322.721,317.788z"/>
                    
                        <path style="fill:none;stroke:#27DEBF;stroke-width:0.8828;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" d="
                        M365.648,264.752c-14.231,4.075-35.324,20.417-42.927,50.754"/>
                    
                        <path style="fill:none;stroke:#27DEBF;stroke-width:0.8828;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" d="
                        M351.014,231.792c-7.682,6.114-26.026,25.48-28.339,56.786"/>
                </g>
            </g>
            <g id="Bookshelf_1_">
                <g id="Bookshelf_2_">
                    <g>
                        <path style="fill:#263238;" d="M319.895,165.091l-55.603-32.056c-1.18-0.681-2.249-0.746-3.022-0.299
                            c-0.718,0.415-3.284,1.902-4.005,2.311c-0.781,0.443-1.264,1.404-1.264,2.774l0.001,152.165c0,2.726,1.914,6.04,4.274,7.403
                            l55.602,32.056c1.18,0.682,2.249,0.746,3.022,0.299c0.718-0.415,3.301-1.901,4.024-2.322c0.769-0.448,1.245-1.404,1.245-2.763
                            V172.494C324.169,169.768,322.256,166.454,319.895,165.091z"/>
                        <g>
                            <path style="fill:#263238;" d="M268.03,292.606l-4.009,2.315l0.001,12.655c0,0.681,0.479,1.51,1.069,1.851l1.872,1.081
                                c0.59,0.341,1.547,0.341,2.137,0l1.872-1.081c0.59-0.341,1.249-1.139,1.473-1.783l3.607-10.408L268.03,292.606z"/>
                            <path style="fill:#37474F;" d="M264.021,307.576c0,0.681,0.478,1.51,1.069,1.851l1.872,1.081
                                c0.59,0.341,1.249,0.095,1.472-0.549l3.607-10.409l-8.02-4.63L264.021,307.576z"/>
                        </g>
                        <g>
                            <path style="fill:#263238;" d="M308.125,315.704l-4.01,2.315l0.001,12.655c0,0.681,0.478,1.51,1.069,1.851l1.872,1.081
                                c0.59,0.341,1.547,0.341,2.137,0l1.872-1.081c0.59-0.341,1.249-1.139,1.472-1.783l3.607-10.409L308.125,315.704z"/>
                            <path style="fill:#37474F;" d="M304.116,330.675c0,0.681,0.478,1.51,1.068,1.851l1.872,1.081
                                c0.59,0.341,1.249,0.095,1.472-0.549l3.607-10.409l-8.02-4.63L304.116,330.675z"/>
                        </g>
                        <path style="fill:#37474F;" d="M260.561,135.426l55.603,32.056c2.361,1.363,4.274,4.677,4.274,7.403V327.05
                            c0,1.363-0.478,2.32-1.252,2.767c-0.773,0.447-1.842,0.382-3.022-0.299l-55.602-32.056c-2.361-1.363-4.274-4.677-4.274-7.403
                            l-0.001-152.165c0-1.37,0.484-2.331,1.264-2.774C258.323,134.681,259.387,134.749,260.561,135.426z"/>
                    </g>
                    <g id="Bottom_1_">
                        <polygon style="fill:#455A64;" points="179.821,343.479 239.963,378.202 324.161,329.59 320.152,327.275 260.01,292.552 
                            179.821,338.849 			"/>
                        <polygon style="fill:#263238;" points="239.963,378.202 239.963,373.572 324.161,324.96 324.161,329.59 			"/>
                    </g>
                    <g>
                        <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-miterlimit:10;" d="M240.036,371.257
                            c11.248-17.017,10.622-47.103,9.655-52.181c0,0,4.745,31.804,10.588,40.886"/>
                        <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-miterlimit:10;" d="M251.695,330.633
                            c1.931,9.272,23.87,15.817,23.87,15.817"/>
                        <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-miterlimit:10;" d="M249.533,339.855
                            c0.619-1.505,3.668-1.714,4.248,0.732c0,0,0.352-4.502,2.89-3.226"/>
                        <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-miterlimit:10;" d="M248.663,346.449
                            c1.129-2.42,5.29-3.748,7.118,1.988c0,0,0.276-10.266,7.066-7.18"/>
                        <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-miterlimit:10;" d="M246.73,355.538
                            c2.38-4.581,8.909-6.41,10.587-2.185c0.039-5.216,6.01-11.792,13.299-8.628"/>
                        <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-miterlimit:10;" d="M242.697,366.568
                            c2.795-5.077,9.646-14.239,15.622-10.547"/>
                    </g>
                    <g>
                        <polygon style="fill:#263238;" points="316.141,278.663 316.145,283.291 239.962,327.276 179.82,292.552 179.82,287.923 
                            256.002,243.941 			"/>
                        <polygon style="fill:#455A64;" points="316.141,278.663 239.962,322.645 179.82,287.923 256.002,243.941 			"/>
                    </g>
                    <g>
                        <polygon style="fill:#263238;" points="316.141,227.736 316.145,232.363 239.962,276.348 179.82,241.624 179.82,236.995 
                            256.002,193.013 			"/>
                        <polygon style="fill:#455A64;" points="316.141,227.736 239.962,271.717 179.82,236.995 256.002,193.013 			"/>
                    </g>
                    <g id="Side_1_">
                        <path style="fill:#263238;" d="M239.704,211.334l-55.603-32.056c-1.18-0.681-2.249-0.746-3.022-0.299
                            c-0.718,0.415-3.284,1.902-4.005,2.311c-0.781,0.443-1.264,1.404-1.264,2.774l0.001,152.165c0,2.726,1.914,6.04,4.274,7.403
                            l55.602,32.056c1.18,0.681,2.249,0.746,3.022,0.299c0.718-0.415,3.301-1.901,4.024-2.322c0.769-0.448,1.245-1.404,1.245-2.763
                            V218.737C243.979,216.011,242.065,212.697,239.704,211.334z"/>
                        <polygon style="opacity:0.2;" points="239.962,234.685 243.979,232.37 243.979,218.473 239.95,220.792 			"/>
                        <polygon style="opacity:0.2;" points="320.164,188.392 324.181,186.077 324.181,172.18 320.152,174.499 			"/>
                        <g>
                            <path style="fill:#263238;" d="M187.839,338.849l-4.009,2.315l0.001,12.655c0,0.681,0.478,1.51,1.068,1.851l1.872,1.081
                                c0.59,0.341,1.547,0.341,2.137,0l1.872-1.081c0.59-0.341,1.249-1.139,1.473-1.783l3.607-10.408L187.839,338.849z"/>
                            <path style="fill:#37474F;" d="M183.831,353.819c0,0.681,0.478,1.51,1.068,1.851l1.872,1.081
                                c0.59,0.341,1.249,0.095,1.472-0.549l3.607-10.408l-8.02-4.63L183.831,353.819z"/>
                        </g>
                        <g>
                            <path style="fill:#263238;" d="M227.934,361.948l-4.009,2.315l0.001,12.655c0,0.681,0.478,1.51,1.068,1.851l1.872,1.081
                                c0.59,0.341,1.547,0.341,2.137,0l1.872-1.081c0.59-0.341,1.249-1.139,1.473-1.783l3.607-10.409L227.934,361.948z"/>
                            <path style="fill:#37474F;" d="M223.926,376.918c0,0.682,0.478,1.51,1.068,1.851l1.872,1.081
                                c0.59,0.341,1.249,0.095,1.472-0.549l3.607-10.408l-8.02-4.63L223.926,376.918z"/>
                        </g>
                        <path style="fill:#37474F;" d="M180.085,181.596l55.603,32.056c2.361,1.363,4.274,4.677,4.274,7.403V373.22
                            c0,1.363,0.001,4.982,0.001,4.982s-3.095-1.833-4.275-2.514l-55.602-32.056c-2.36-1.363-4.274-4.677-4.274-7.403l-0.001-152.165
                            c0-1.37,0.483-2.331,1.264-2.774C177.847,180.851,178.911,180.918,180.085,181.596z"/>
                        <path style="opacity:0.2;" d="M235.688,213.652l-55.603-32.056c-1.174-0.678-2.237-0.745-3.01-0.306
                            c-0.781,0.443-1.264,1.404-1.264,2.774l0,13.584l64.151,37.038v-13.63C239.962,218.329,238.048,215.015,235.688,213.652z"/>
                    </g>
                    <g>
                        
                            <polyline style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" points="
                            268.028,206.903 312.132,181.439 312.133,227.736 			"/>
                        
                            <polyline style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" points="
                            304.113,216.162 312.132,181.439 296.094,209.217 			"/>
                        
                            <line style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="312.132" y1="181.439" x2="288.075" y2="204.587"/>
                        
                            <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" d="
                            M312.133,220.349c0.01-6.492-7.404-6.851-7.404-6.851c0.454-1.967-0.039-5.124-2.43-7.167c-2.391-2.043-4.007-0.921-4.489-0.085
                            c0,0,0.502-5.966-4.657-6.544c0,0,3.42-6.31,0-7.305"/>
                        
                            <path style="fill:none;stroke:#FAFAFA;stroke-width:0.7403;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" d="
                            M312.132,210.374c-0.262-3.321-2.802-7.545-5.41-5.508c0,0,0.689-7.247-4.48-6.297c0,0,2.423-6.722-2.657-5.057
                            c0,0,3.66-4.628,0.822-5.304"/>
                    </g>
                    <g id="Top_1_">
                        <path style="fill:#37474F;" d="M333.673,165.24c0.007-0.892-0.577-1.785-1.759-2.467l-59.613-34.418
                            c-2.36-1.363-6.188-1.363-8.548,0l-91.688,52.936c-1.182,0.682-1.766,1.575-1.759,2.468c0.007,0.828,0.007,3.802,0,4.63
                            c-0.007,0.892,0.577,1.785,1.759,2.467l59.613,34.417c2.36,1.363,6.188,1.363,8.548,0l91.688-52.936
                            c1.182-0.682,1.766-1.575,1.759-2.468C333.666,169.042,333.666,166.068,333.673,165.24z"/>
                        <path style="fill:#455A64;" d="M231.679,220.643l-59.613-34.417c-2.36-1.363-2.36-3.572,0-4.935l91.688-52.936
                            c2.361-1.363,6.188-1.363,8.548,0l59.613,34.418c2.36,1.363,2.36,3.573,0,4.935l-91.687,52.936
                            C237.867,222.006,234.04,222.006,231.679,220.643z"/>
                    </g>
                    <g id="Shadows_1_">
                        <polygon style="opacity:0.1;" points="320.151,179.124 292.087,213.847 243.979,241.629 243.971,223.106 			"/>
                        <polygon style="opacity:0.1;" points="316.145,232.363 292.087,264.774 243.979,292.556 243.978,274.03 			"/>
                        <polygon style="opacity:0.1;" points="316.145,283.293 292.087,315.703 243.979,343.485 243.979,324.956 			"/>
                    </g>
                </g>
            </g>
            <g id="Box_1_">
                <g id="Box_2_">
                    <g id="XMLID_820_">
                        <polygon id="XMLID_848_" style="fill:#27DEBF;" points="205.782,384.443 132.02,427.028 48.958,378.965 122.72,336.38 			"/>
                        <polygon id="XMLID_846_" style="opacity:0.5;" points="205.782,384.443 132.02,427.028 48.958,378.965 122.72,336.38 			"/>
                        <polygon id="XMLID_845_" style="fill:#27DEBF;" points="122.721,278.654 122.72,336.38 48.958,378.965 48.959,321.239 			"/>
                        <polygon id="XMLID_844_" style="opacity:0.23;" points="122.721,278.654 122.72,336.38 48.958,378.965 48.959,321.239 			"/>
                        <polygon id="XMLID_843_" style="fill:#27DEBF;" points="132.02,369.298 132.02,427.028 48.958,378.965 48.959,321.239 			"/>
                        <polygon id="XMLID_842_" style="opacity:0.38;" points="132.02,369.298 132.02,427.028 48.958,378.965 48.959,321.239 			"/>
                        <polygon id="XMLID_841_" style="fill:#27DEBF;" points="205.782,326.712 205.782,384.443 122.72,336.38 122.721,278.654 			"/>
                        <polygon id="XMLID_840_" style="opacity:0.4;" points="205.782,326.712 205.782,384.443 122.72,336.38 122.721,278.654 			"/>
                        <polygon id="XMLID_839_" style="fill:#27DEBF;" points="205.782,384.443 205.782,326.712 132.02,369.298 132.02,427.028 			"/>
                        <polygon id="XMLID_838_" style="opacity:0.23;" points="205.782,384.443 205.782,326.712 132.02,369.298 132.02,427.028 			"/>
                        <polygon id="XMLID_830_" style="fill:#27DEBF;" points="122.721,278.654 122.824,246.694 205.681,294.814 205.782,326.712 			"/>
                        <polygon id="XMLID_829_" style="opacity:0.2;" points="122.721,278.654 122.824,246.694 205.681,294.814 205.782,326.712 			"/>
                        <polygon id="XMLID_828_" style="fill:#27DEBF;" points="132.02,369.298 138.937,341.34 212.031,299.14 205.782,326.712 			"/>
                        <polygon id="XMLID_827_" style="opacity:0.3;" points="132.02,369.298 138.937,341.34 212.031,299.14 205.782,326.712 			"/>
                        <polygon id="XMLID_825_" style="fill:#27DEBF;" points="48.959,321.239 40.161,292.241 113.922,249.655 122.721,278.654 			"/>
                        <polygon id="XMLID_824_" style="opacity:0.1;" points="48.959,321.239 40.161,292.241 113.922,249.655 122.721,278.654 			"/>
                        <polygon id="XMLID_823_" style="fill:#27DEBF;" points="48.959,321.239 31.604,297.273 114.673,345.331 132.02,369.298 			"/>
                        <polygon id="XMLID_821_" style="opacity:0.45;" points="48.959,321.239 31.604,297.273 114.673,345.331 132.02,369.298 			"/>
                    </g>
                    <g id="XMLID_809_" style="opacity:0.7;">
                        <g id="XMLID_815_">
                            <g id="XMLID_818_">
                                <path id="XMLID_819_" style="fill:#FFFFFF;" d="M153.738,409.859c-0.251,0-0.453-0.204-0.453-0.453v-10.158
                                    c0-0.25,0.203-0.453,0.453-0.453c0.251,0,0.453,0.204,0.453,0.453v10.158C154.191,409.655,153.988,409.859,153.738,409.859z"/>
                            </g>
                            <g id="XMLID_816_">
                                <path id="XMLID_817_" style="fill:#FFFFFF;" d="M152.41,402.766c-0.06,0-0.121-0.012-0.18-0.037
                                    c-0.23-0.099-0.336-0.367-0.236-0.597l1.614-3.727l1.799,2.079c0.164,0.189,0.143,0.476-0.046,0.641
                                    c-0.189,0.159-0.476,0.143-0.639-0.046l-0.855-0.988l-1.04,2.403C152.752,402.663,152.586,402.766,152.41,402.766z"/>
                            </g>
                        </g>
                        <g id="XMLID_810_">
                            <g id="XMLID_813_">
                                <path id="XMLID_814_" style="fill:#FFFFFF;" d="M157.93,407.468c-0.251,0-0.453-0.204-0.453-0.453v-10.157
                                    c0-0.25,0.203-0.453,0.453-0.453s0.453,0.204,0.453,0.453v10.157C158.383,407.264,158.18,407.468,157.93,407.468z"/>
                            </g>
                            <g id="XMLID_811_">
                                <path id="XMLID_812_" style="fill:#FFFFFF;" d="M156.604,400.375c-0.06,0-0.121-0.012-0.18-0.037
                                    c-0.23-0.099-0.336-0.367-0.236-0.597l1.613-3.724l1.799,2.075c0.164,0.189,0.144,0.476-0.046,0.639
                                    c-0.19,0.165-0.474,0.144-0.639-0.044l-0.855-0.988l-1.039,2.403C156.945,400.272,156.779,400.375,156.604,400.375z"/>
                            </g>
                        </g>
                    </g>
                    <g id="XMLID_802_" style="opacity:0.7;">
                        <g id="XMLID_807_">
                            <path id="XMLID_808_" style="fill:#FFFFFF;" d="M141.651,416.753c-0.177,0-0.345-0.043-0.495-0.128
                                c-0.345-0.197-0.543-0.593-0.543-1.089v-1.243c0-0.25,0.203-0.453,0.453-0.453c0.251,0,0.453,0.204,0.453,0.453v1.243
                                c0,0.186,0.051,0.282,0.086,0.301c0.033,0.025,0.14,0.014,0.296-0.078c0.4-0.234,0.843-1.132,0.843-2.049v-5.2
                                c0-0.25,0.203-0.453,0.453-0.453c0.251,0,0.453,0.204,0.453,0.453v5.2c0,1.183-0.554,2.4-1.29,2.832
                                C142.124,416.682,141.879,416.753,141.651,416.753z"/>
                        </g>
                        <g id="XMLID_803_">
                            <path id="XMLID_804_" style="fill:#FFFFFF;" d="M136.877,412.564v-0.784c0-3.692,2.6-8.199,5.798-10.044
                                c1.635-0.942,3.197-1.082,4.393-0.389c1.199,0.692,1.859,2.113,1.859,4v0.262L136.877,412.564z M145.388,401.818
                                c-0.683,0-1.453,0.237-2.26,0.703c-2.717,1.567-4.966,5.22-5.301,8.449l10.188-5.883c-0.053-1.418-0.545-2.463-1.401-2.957
                                C146.253,401.923,145.84,401.818,145.388,401.818z"/>
                        </g>
                    </g>
                    <polygon style="opacity:0.7;fill:#FFFFFF;" points="31.604,297.273 114.673,345.331 132.02,369.298 113.75,346.177 		"/>
                    <polygon style="opacity:0.7;fill:#FFFFFF;" points="205.782,326.712 132.02,369.298 132.02,427.028 133.396,370.458 		"/>
                </g>
            </g>
            <g id="Moth_1_">
                <g id="Moth_2_">
                    <g>
                        <path style="fill:#E0E0E0;" d="M97.537,303.621c7.61-1.101,14.562-5.976,18.974-12.165c2.054-2.882,3.615-6.108,4.5-9.539
                            c0.812-3.148,1.267-6.683,0.464-9.881c-1.376-5.482-6.456-8.734-11.828-9.419c-2.708-0.346-5.512-0.095-8.095,0.798
                            c-2.753,0.951-5.001,2.682-6.697,5.046c-0.881,1.228-1.804,2.754-1.845,4.313c-0.036,1.357,0.935,2.327,2.238,2.549
                            c1.572,0.267,3.231-0.404,4.576-1.16c1.659-0.933,3.082-2.152,4.268-3.638c2.423-3.036,4.488-6.746,5.543-10.503
                            c0.911-3.246,0.715-6.791,0.02-10.057c-0.71-3.338-2.094-6.748-4.537-9.201c-2.556-2.566-6.21-3.835-9.733-4.296
                            c-3.575-0.467-7.334-0.145-10.746,1.035c-1.469,0.508-2.986,1.186-4.126,2.254c-0.858,0.804-1.839,2.127-1.488,3.421
                            c0.39,1.443,2.249,1.875,3.538,1.71c1.882-0.242,3.677-1.413,5.01-2.713c1.468-1.432,2.79-3.18,3.767-4.986
                            c0.72-1.332,1.162-2.813,1.131-4.337c-0.129-6.348-7.135-10.448-12.63-11.605c-1.491-0.314-2.995-0.412-4.512-0.298
                            c-0.509,0.038-0.199,0.838,0.214,0.807c2.885-0.217,5.855,0.65,8.415,1.927c2.483,1.239,4.846,3.072,6.211,5.533
                            c1.375,2.478,1.215,5.327-0.234,7.737c-0.992,1.65-2.194,3.325-3.622,4.623c-1.191,1.082-2.98,2.328-4.676,2.206
                            c-0.497-0.036-1.13-0.273-1.318-0.826c-0.223-0.655,0.274-1.416,0.596-1.816c0.81-1.006,2.049-1.613,3.228-2.136
                            c2.929-1.3,6.309-1.723,9.488-1.557c3.274,0.172,6.681,1.014,9.342,3.008c2.5,1.874,3.971,4.775,4.861,7.709
                            c0.919,3.032,1.305,6.35,0.876,9.502c-0.437,3.21-1.965,6.283-3.626,9.022c-1.851,3.053-4.174,5.965-7.654,7.2
                            c-0.86,0.305-2.66,0.758-3.004-0.497c-0.339-1.235,0.641-2.783,1.276-3.753c1.444-2.205,3.375-3.91,5.818-4.911
                            c2.29-0.938,4.829-1.289,7.293-1.103c4.709,0.356,9.492,2.816,11.132,7.495c0.961,2.741,0.765,5.787,0.253,8.601
                            c-0.567,3.111-1.725,6.107-3.31,8.839c-3.4,5.858-8.71,10.258-14.889,12.943c-1.481,0.643-3.018,1.179-4.551,1.685
                            C97.286,303.246,97.357,303.647,97.537,303.621L97.537,303.621z"/>
                    </g>
                    <g>
                        <path style="fill:#27DEBF;" d="M65.748,216.799c-4.053-0.285-9.51,2.86-7.551,5.162c1.96,2.302,7.165-1.422,7.165-1.422
                            s-0.491,4.652,1.212,5.177c1.702,0.525,3.955-3.905,2.297-6.741c0,0,2.556,1.134,4.972-0.647s-0.146-3.455-3.556-3.157
                            c0,0,2.44-7.975-2.489-6.468C65.363,209.448,64.488,212.342,65.748,216.799z"/>
                        <path style="opacity:0.65;fill:#FFFFFF;" d="M65.748,216.799c-4.053-0.285-9.51,2.86-7.551,5.162
                            c1.96,2.302,7.165-1.422,7.165-1.422s-0.491,4.652,1.212,5.177c1.702,0.525,3.955-3.905,2.297-6.741c0,0,2.556,1.134,4.972-0.647
                            s-0.146-3.455-3.556-3.157c0,0,2.44-7.975-2.489-6.468C65.363,209.448,64.488,212.342,65.748,216.799z"/>
                        <path style="fill:#455A64;" d="M69.469,218.499c-0.217-0.218-0.456-0.42-0.7-0.608c-0.351-0.271-0.716-0.526-1.073-0.788
                            c-0.341-0.25-0.7-0.469-1.071-0.671c-0.215-0.117-0.435-0.226-0.659-0.324c-0.346-0.151-0.645-0.207-1.025-0.159
                            c-0.353,0.045-0.666,0.446-0.402,0.781c0.315,0.4,0.701,0.477,1.05,0.657c0.183,0.094,0.363,0.194,0.54,0.298
                            c0.367,0.215,0.703,0.466,1.045,0.718c0.341,0.251,0.694,0.485,1.03,0.742c0.531,0.405,1.071,0.81,1.707,1.036
                            c0.057,0.02,0.131,0.01,0.175-0.033C70.508,219.743,69.763,218.794,69.469,218.499z"/>
                    </g>
                </g>
            </g>
            <g id="Character">
                <g id="Character_7_">
                    <path style="fill:#FFA8A7;" d="M388.285,106.793c-4.375,5.877-10.221,38.209-12.431,48.573
                        c-10.632-0.308-24.851-0.803-30.237-1.37c-5.965-0.628-18.807-1.855-25.093-1.032c-15.609,2.044-14.989,12.259-14.626,14.93
                        c0.416,3.069,2.605,4.463,10.958,2.557c1.871-0.427,3.803-0.636,5.721-0.675c0.817-0.016,2.521-0.405,3.16,0.286
                        c0.96,1.038-1.884,2.675-2.606,3.14c-1.112,0.718-2.27,1.627-2.505,2.929c-0.087,0.483,0.005,1.058,0.418,1.324
                        c0.365,0.235,0.842,0.15,1.264,0.05c3.829-0.905,7.537-2.316,10.999-4.186c1.343-0.726,2.662-1.526,4.115-1.995
                        c2.17-0.701,4.404-0.283,6.592-0.283c4.686,0,29.473,2.453,40.492,3.575c7.63,0.777,8.445-6.762,11.073-14.384
                        c3.269-9.48,10.921-59.253,10.921-59.253C398.785,99.878,393.483,99.812,388.285,106.793z"/>
                    <path style="fill:#37474F;" d="M406.618,100.219c-3.692-0.676-11.987-1.711-16.513,3.338c-4.526,5.049-5.592,10.357-7.584,17.995
                        c-1.991,7.638-5.487,21.361-5.487,21.361s5.826,8.365,24.079,5.995L406.618,100.219z"/>
                    <g id="XMLID_780_">
                        <polygon id="XMLID_800_" style="fill:#FFA8A7;" points="452.448,364.64 439.616,364.653 435.932,336.795 452.121,336.16 			"/>
                        <path id="XMLID_799_" style="fill:#FFA8A7;" d="M409.855,350.734c-2.189,3.781-9.193,3.474-15.038,2.135l-0.672-27.382
                            l17.16-0.673L409.855,350.734z"/>
                        <path id="XMLID_798_" style="fill:#27DEBF;" d="M451.576,188.662c0.464,11.846-1.102,81.919-1.102,81.919
                            c0.422,3.398,3.942,9.329,4.678,21.597c1.068,17.79-2.566,55.847-2.566,55.847s-7.348,3.841-15.265,0.901
                            c0,0-8.065-55.036-10.979-72.573c-2.543-15.305-5.807-56.343-5.807-56.343l-6.33,52.604c0,0,1.771,8.318,1.435,18.888
                            c-0.26,8.175-4.919,47.895-4.919,47.895s-7.821,2.93-16.417,0.396c0,0-3.826-61.642-4.263-69.116
                            c-0.498-8.521-0.157-83.727-0.157-83.727L451.576,188.662z"/>
                        <path id="XMLID_796_" style="opacity:0.2;" d="M420.535,220.01c0,0-11.208-3.176-18.846-9.999c0,0,3.578,6.11,13.718,11.53
                            l-0.581,45.911L420.535,220.01z"/>
                        <g>
                            <path id="XMLID_795_" style="fill:#263238;" d="M424.954,387.873c-0.415,0.421-0.176,3.185,0.328,3.912
                                c0.541,0.78,3.563,3.159,8.867,3.331c5.068,0.164,9.554-0.695,12.447-2.695c2.893-2,4.248-4.134,4.414-7.024
                                c0.166-2.89-0.263-5.654,1.023-7.924c1.187-2.096,3.116-4.481,3.512-5.607c0.862-2.451-0.196-5.865-0.196-5.865L424.954,387.873
                                z"/>
                            <path id="XMLID_794_" style="fill:#455A64;" d="M437.8,362.198c0.008,1.12-0.149,2.147-0.457,3.228
                                c-0.645,2.261-1.676,4.406-2.847,6.454c-0.603,1.054-1.243,2.092-1.991,3.056c-1.457,1.879-3.297,3.447-4.847,5.255
                                c-1.55,1.808-2.842,3.978-2.877,6.326c-0.06,3.972,4.295,5.15,7.63,5.61c3.561,0.491,7.329,0.154,10.742-0.962
                                c3.798-1.242,6.453-4.326,6.94-8.183c0.14-1.108,0.028-2.23,0.115-3.343c0.198-2.538,1.415-4.897,2.811-7.056
                                c0.81-1.252,1.695-2.478,2.217-3.867c0.938-2.494,0.001-5.222-0.756-7.64c-0.654-2.088-1.386-4.452-2.121-4.224
                                c-0.001,1.106,0.044,1.208-0.712,1.981c-0.956,0.977-0.944,2.39-1.961,3.26c0.441-1.305,0.451-1.852,0.626-2.848
                                c0.052-0.298-0.051-0.894-0.267-1.112c-0.291-0.293-0.772-0.464-1.243-0.603c-1.58-0.466-3.211-0.769-4.856-0.906
                                c-1.503-0.126-3.228-0.241-4.698,0.15c-0.452,0.12-0.887,0.355-1.154,0.729c-0.281,0.394-0.322,0.735-0.368,1.371
                                C437.679,359.51,437.793,361.144,437.8,362.198z"/>
                            <path id="XMLID_793_" style="fill:#F5F5F5;" d="M433.995,372.732c1.433-1.163,4.739-1.308,6.555-1.185
                                c2.274,0.154,4.139,0.876,5.21,1.602c0.475,0.322,1.118,0.238,1.509-0.179l0,0c0.47-0.501,0.394-1.293-0.178-1.68
                                c-1.135-0.769-3.177-1.833-5.868-1.917c-4.462-0.139-5.738,0.675-5.738,0.675S433.507,371.435,433.995,372.732z"/>
                            <path id="XMLID_789_" style="fill:#F5F5F5;" d="M430.76,376.926c1.813-1.17,5.457-1.154,7.273-1.031
                                c2.274,0.154,4.002,0.978,5.074,1.705c0.475,0.322,1.118,0.238,1.509-0.179l0,0c0.47-0.501,0.394-1.293-0.178-1.68
                                c-1.135-0.769-3.309-1.998-6-2.082c-4.462-0.139-5.586,0.815-5.586,0.815S431.003,375.275,430.76,376.926z"/>
                            <path id="XMLID_786_" style="fill:#F5F5F5;" d="M442.828,365.045c-3.087-0.197-4.378,0.198-5.576,0.689
                                c-0.767,0.47-1.235,1.729-0.718,2.023c0.969-0.589,2.743-0.887,4.932-0.875c1.469,0.008,3.339,0.529,4.864,1.133
                                c0.387,0.154,0.847,0.373,1.25,0.576c0.544,0.274,1.212,0.038,1.447-0.513l0,0c0.198-0.467,0.016-1.007-0.435-1.256
                                C447.491,366.214,445.323,365.204,442.828,365.045z"/>
                        </g>
                        <g>
                            <path id="XMLID_785_" style="fill:#263238;" d="M365.535,367.805c-0.461,0.856-0.417,2.504,0.03,3.569
                                c0.448,1.065,5.742,4.391,13.102,4.244c7.361-0.146,12.54-2.863,15.425-4.582c2.885-1.719,5.814-1.927,9.457-1.962
                                c3.643-0.036,8.5-1.62,9.378-3.282c0.878-1.661,0.1-4.632,0.1-4.632L365.535,367.805z"/>
                            <path id="XMLID_784_" style="fill:#455A64;" d="M396.232,346.312c-0.189-0.05-0.381-0.099-0.575-0.147
                                c-0.316-0.079-0.645-0.158-0.965-0.098c-0.364,0.068-0.671,0.308-0.935,0.568c-0.83,0.82-1.358,1.889-2.014,2.854
                                c-1.657,2.436-4.984,4.36-8.6,6.088c-2.458,1.175-4.854,2.062-7.382,3.058c-2.578,1.016-5.962,1.55-8,2.788
                                c-2.972,1.804-3.575,6.662-0.386,8.743c2.751,1.795,10.111,3.949,18.026,2.108c4.311-1.003,9.249-5.324,14.854-5.496
                                c3.564-0.109,10.326-0.788,12.74-3.375c0.902-1.156-0.046-5.001-0.817-8.317c-0.841-3.62-0.458-9.039-2.072-8.976l-0.101,1.809
                                c-0.15,0.543-1.426,1.072-1.82,1.488c-0.481,0.508-0.962,1.015-1.443,1.523c-0.63,0.664-1.282,1.345-2.117,1.719
                                c-1.438,0.643-3.103,0.26-4.628-0.134c-0.07-0.018,0.77-2.783,0.854-3.051c0.069-0.219,0.144-0.452,0.085-0.674
                                c-0.061-0.224-0.247-0.39-0.431-0.533C399.18,347.231,397.786,346.725,396.232,346.312z"/>
                            <path id="XMLID_783_" style="fill:#F5F5F5;" d="M386.551,353.803c0,0,0.575-1.338,2.706-1.631
                                c1.63-0.224,4.427,1.102,6.132,2.648c0.698,0.633,0.549,1.764-0.296,2.18l0,0c-0.503,0.247-1.1,0.148-1.505-0.24
                                C392.473,355.693,389.952,353.73,386.551,353.803z"/>
                            <path id="XMLID_782_" style="fill:#F5F5F5;" d="M381.41,356.369c0,0,1.121-1.582,3.184-1.453c0,0,4.213,0.322,6.709,2.88
                                c0.637,0.653,0.54,1.707-0.279,2.108v0c-0.487,0.238-1.062,0.141-1.449-0.238C388.448,358.559,386.194,356.239,381.41,356.369z"
                                />
                            <path id="XMLID_781_" style="fill:#F5F5F5;" d="M375.761,358.634c0,0,1.233-1.422,3.296-1.293c0,0,3.924,0.334,6.42,2.892
                                c0.637,0.653,0.54,1.707-0.279,2.108l0,0c-0.487,0.238-1.063,0.142-1.449-0.238
                                C382.622,360.997,380.545,358.504,375.761,358.634z"/>
                        </g>
                    </g>
                    <g id="XMLID_778_">
                        <path id="XMLID_779_" style="fill:#455A64;" d="M428.005,100.441c4.723,0.536,10.306,0.828,14.813,1.42
                            c5.81,0.762,10.344,6.773,11.759,16.067c1.559,10.245-4.965,39.604-4.965,39.604l2.09,34.616
                            c-8.516,6.274-44.751,9.565-62.816-3.478c0,0,0.421-58.922,1.834-67.965c1.943-12.442,4.976-20.175,19.013-21.067
                            L428.005,100.441z"/>
                    </g>
                    <g id="XMLID_771_">
                        <path id="XMLID_770_" style="fill:#FFA8A7;" d="M460.272,175.444c-1.531,2.56-3.069,5.116-4.649,7.646
                            c-0.585,0.937-1.173,1.872-1.786,2.79c-0.219,0.329-0.839,0.99-1.151,1.206c-0.758,0.526-2.337,0.194-3.255,0.224
                            c-4.517,0.149-9.029,0.477-13.52,0.985c-0.745,0.084-1.588,0.232-1.99,0.864c-0.46,0.721-0.058,1.738,0.637,2.238
                            s1.584,0.617,2.434,0.719c1.301,0.156,2.601,0.312,3.902,0.467c0.549,0.066,1.13,0.145,1.559,0.494
                            c1.417,1.153-1.289,2.534-2.103,3.094c-1.627,1.12-3.367,2.106-5.188,2.874c-8.127,3.427-9.119,5.908-7.623,8.723
                            c1.303,2.451,6.922,11.362,21.198,3.69c5.749-3.09,14.506-12.851,18.489-17.57c5.748-6.81,15.454-22.015,15.454-22.015
                            c5.519-7.581,6.235-10.691,6.357-13.849c0.122-3.158-17.008-27.378-22.815-35.882s-8.59-18.863-23.403-20.283
                            c0,0-5.634,17.883,3.699,29.453l21.571,30.085C468.088,161.4,462.374,171.93,460.272,175.444z"/>
                        <path id="XMLID_767_" style="fill:#37474F;" d="M475.916,133.706c-2.765-4.001-5.655-8.119-8.449-12.198
                            c-11.743-17.146-10.761-20.286-27.698-20.528c-1.499,2.305-2.393,12.197-0.133,22.139c1.899,8.354,8.971,14.24,8.971,14.24
                            l8.485,11.492c0,0,5.189-0.159,11.412-4.722S475.916,133.706,475.916,133.706z"/>
                    </g>
                    <g id="XMLID_605_">
                        <g id="XMLID_756_">
                            <path style="fill:#263238;" d="M439.167,55.375c-3.197-4.471-7.849-3.701-7.849-3.701l-0.874,21.47l-1.417,7.931l0.25,8.823
                                c0,0,2.391-0.119,3.835-2.914c1.312-2.54,4.962-14.825,4.962-14.825l-0.016,0.016C441.413,62.094,440.706,57.527,439.167,55.375
                                z"/>
                            <path id="XMLID_765_" style="fill:#FFA8A7;" d="M429.436,73.24c0.419-1.345,0.832-2.103,2.071-3.423
                                c1.232-1.313,6.132-2.75,8.366,1.412c2.276,4.242-1.53,9.652-4.429,11.333c-3.717,2.155-5.895-0.781-5.895-0.781l0.086,19.766
                                l0,0c-1.728,3.976-9.54,7.564-14.181,6.974c-3.704-0.471-7.117-3.386-3.808-7.626l-0.265-5.632c0,0-4.948,1.004-7.398,0.749
                                c-4.077-0.425-6.781-3.491-8.226-7.81c-2.32-6.937-3.35-12.58-2.364-26.498c1.081-15.255,18.711-16.288,28.533-10.765
                                C431.748,56.46,429.436,73.24,429.436,73.24z"/>
                            <path id="XMLID_764_" style="fill:#263238;" d="M387.777,45.889c-0.447-3.33,0.706-6.901,4.19-8.08
                                c2.628-0.893,5.325-0.841,7.744,1.288c0.864-2.347,3.132-4.113,5.619-4.374c2.487-0.261,5.073,0.994,6.406,3.11
                                c1.053-1.482,2.295-2.375,4.02-2.795c2.016-0.49,4.51,0.023,5.939,1.078c1.429,1.055,3.094,3.047,2.672,5.531
                                c2.813-1.125,5.741-0.192,7.828,2.296c1.251,1.492,1.935,3.423,1.992,5.362c0.028,0.944-0.031,2.096-0.423,2.973
                                c-0.407,0.914-1.228,1.511-1.462,2.542c-0.22,0.968-0.668,1.864-0.535,2.839c0.229,1.682,0.6,3.354,0.821,5.043
                                c0.213,1.629,0.403,3.276,0.412,4.921c0.004,0.787-0.558,1.344-1.214,1.925c-1.068,0.947-1.308,1.441-2.102,3.051
                                c-0.354,0.718-0.691,2.495-1.387,2.982c-2.129,1.487-2.365-3.338-2.467-4.262c-0.103-0.924-0.131-3.342,0.045-6.409
                                c-0.683-0.044-3.495-0.089-5.671-2.608c-0.71-0.823-1.263-2.075-1.408-3.135c-0.093-0.682-0.196-1.277-0.166-1.961
                                c-1.385,2.298-4.622,3.239-6.917,3.032c-3.865-0.348-5.188-1.997-5.811-3.214c-0.284,1.079-0.752,2.459-1.535,3.26
                                c-0.987,1.01-2.303,1.621-3.461,2.016c-2.155,0.735-4.727-0.19-6.316-1.82c-1.568,2.252-5.547,1.248-6.429,0.614
                                c-2.021-1.451-2.647-3.173-2.867-5.651c-0.219-2.478,0.778-5.283,3.514-6.256C388.288,48.195,387.934,47.057,387.777,45.889z"/>
                            <path id="XMLID_759_" style="fill:#F28F8F;" d="M411.381,95.263c0,0,8.942-2.268,12.017-4.103
                                c2.058-1.228,3.702-3.262,4.151-4.535c0,0-0.459,2.709-2.227,5.217c-2.163,3.067-13.821,5.824-13.821,5.824L411.381,95.263z"/>
                        </g>
                        <g id="XMLID_691_">
                            <path id="XMLID_753_" style="fill:#263238;" d="M411.879,72.582c0.055,1.112,0.967,1.968,2.037,1.911
                                c1.07-0.056,1.892-1.002,1.837-2.114c-0.055-1.111-0.967-1.967-2.037-1.911C412.647,70.524,411.824,71.471,411.879,72.582z"/>
                            <path id="XMLID_749_" style="fill:#F28F8F;" d="M408.887,87.047c0.071,1.431,1.245,2.534,2.623,2.461
                                c1.378-0.072,2.436-1.291,2.365-2.722c-0.071-1.431-1.245-2.533-2.622-2.461C409.875,84.397,408.816,85.616,408.887,87.047z"/>
                            <path id="XMLID_750_" style="fill:#263238;" d="M413.491,65.923l4.119,2.014c0.534-1.181,0.045-2.59-1.093-3.146
                                C415.38,64.234,414.025,64.741,413.491,65.923z"/>
                            <path id="XMLID_748_" style="fill:#263238;" d="M395.568,67.647l3.516-2.997c-0.798-1.01-2.231-1.157-3.202-0.33
                                C394.911,65.148,394.77,66.637,395.568,67.647z"/>
                            <path id="XMLID_745_" style="fill:#263238;" d="M396.621,72.192c0.053,1.075,0.935,1.903,1.971,1.849
                                c1.035-0.054,1.83-0.97,1.777-2.045c-0.053-1.075-0.936-1.903-1.97-1.849C397.363,70.2,396.567,71.116,396.621,72.192z"/>
                            <polygon id="XMLID_724_" style="fill:#F28F8F;" points="405.901,69.966 405.842,81.8 399.576,80.005 				"/>
                        </g>
                    </g>
                    <g>
                        <g>
                            <path style="fill:#E0E0E0;" d="M390.189,86.519c0.113-0.025,0.216-0.092,0.285-0.196c0.142-0.213,0.085-0.502-0.128-0.644
                                l-5.435-3.621c-0.214-0.142-0.502-0.084-0.644,0.129c-0.142,0.213-0.085,0.502,0.128,0.644l5.435,3.621
                                C389.94,86.525,390.069,86.546,390.189,86.519z"/>
                        </g>
                        <g>
                            <path style="fill:#E0E0E0;" d="M389.606,88.857c0.169-0.038,0.31-0.168,0.352-0.349c0.058-0.25-0.098-0.499-0.348-0.558
                                l-6.361-1.475c-0.248-0.059-0.5,0.097-0.558,0.348c-0.058,0.25,0.098,0.499,0.348,0.557l6.362,1.475
                                C389.47,88.872,389.54,88.871,389.606,88.857z"/>
                        </g>
                        <g>
                            <path style="fill:#E0E0E0;" d="M383.702,93.089c0.02-0.005,0.04-0.01,0.059-0.017l6.132-2.244
                                c0.241-0.088,0.365-0.355,0.277-0.596c-0.089-0.241-0.374-0.361-0.596-0.277l-6.132,2.243c-0.241,0.088-0.365,0.355-0.277,0.596
                                C383.247,93.017,383.479,93.139,383.702,93.089z"/>
                        </g>
                    </g>
                    <g>
                        <g>
                            <path style="fill:#E0E0E0;" d="M331.837,141.057c0.161,0.324,0.441,0.586,0.806,0.717c0.751,0.271,1.582-0.119,1.853-0.869
                                l4.568-11.25c0.27-0.753-0.12-1.581-0.872-1.852c-0.751-0.271-1.582,0.119-1.853,0.869l-4.568,11.25
                                C331.634,140.31,331.668,140.716,331.837,141.057z"/>
                        </g>
                        <g>
                            <path style="fill:#E0E0E0;" d="M324.332,141.04c0.24,0.484,0.741,0.812,1.317,0.804c0.799-0.012,1.437-0.669,1.426-1.468
                                l-0.145-14.034c-0.008-0.794-0.668-1.439-1.468-1.426c-0.799,0.011-1.437,0.669-1.426,1.468l0.145,14.034
                                C324.184,140.64,324.239,140.853,324.332,141.04z"/>
                        </g>
                        <g>
                            <path style="fill:#E0E0E0;" d="M311.596,133.213c0.029,0.058,0.062,0.115,0.097,0.167l6.889,10.003
                                c0.447,0.665,1.346,0.84,2.01,0.394c0.664-0.449,0.813-1.401,0.392-2.009l-6.889-10.003c-0.447-0.665-1.346-0.84-2.01-0.394
                                C311.477,131.783,311.28,132.575,311.596,133.213z"/>
                        </g>
                    </g>
                </g>
            </g>
            </svg>
        </div>
        
        HTML;
    }

    ?>
    <div class="my-4"><?php include 'pagination.php'; ?></div>
</main>
<script nonce="{{nonce}}">
    const fields = [
        'category_id',
        'visible',
        'locale',
        'fixed',
        'order',
        'direction'
    ]

    fields.forEach((field) => {
        document.select(
            document.getElementById(`select_field_${field}`),
            document.getElementById(`options_list_${field}`),
            (value) => {
                const url = new URL(window.location.href);
                url.searchParams.set(field, value);
                window.location.href = url.toString();
            })
    })
</script>