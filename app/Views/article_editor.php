<?php

use App\Models\{Article, Category};

assert($article instanceof Article);

$category = $article->getCategory();

?>
<main class="md:container mx-8 md:mx-auto">
    <form>
        {{csrf}}
        <div class="mb-8 relative">
            <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_title}}</label>
            <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="title" id="title" name="title" value="<?= $article->title ?>" required>
        </div>
        <div class="mb-8 relative">
            <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_category}}</label>
            <div class="px-3 py-2 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" data-select><?= $category->title ?></div>
            <input value="<?= $category->id ?>" name="category" class="hidden" required />
            <div class="absolute select-none mt-1 hidden bg-white border-2 border-gray-200 rounded-lg shadow-lg max-h-[50vh] overflow-y-auto z-20" data-options>
                <?php

                $query = Category::query()
                    ->where('parent_id', null)
                    ->order('locale', 'ASC')
                    ->order('title', 'ASC');

                foreach (Category::all($query) as $cat) {
                    assert($cat instanceof Category);

                    $class = $cat->id === $category->id ? 'bg-sky-600 text-white' : '';

                    echo <<<HTML
                        <div data-value="{$cat->id}" class="px-4 py-3 cursor-pointer hover:bg-sky-600 hover:text-white {$class}">{$cat->title}</div>
                    HTML;

                    $cat->children = $cat->fetchChildren();

                    foreach ($cat->children as $child) {
                        assert($child instanceof Category);

                        $class = $child->id === $category->id ? 'bg-sky-600 text-white' : '';

                        echo <<<HTML
                            <div data-value="{$child->id}" class="px-4 py-3 pl-8 cursor-pointer bg-gray-100 hover:bg-sky-600 hover:text-white {$class}">{$child->title}</div>
                        HTML;

                        $child->children = $child->fetchChildren();

                        foreach ($child->children as $grandchild) {
                            assert($grandchild instanceof Category);

                            $class = $grandchild->id === $category->id ? 'bg-sky-600 text-white' : '';

                            echo <<<HTML
                                <div data-value="{$grandchild->id}" class="px-4 py-3 pl-12 cursor-pointer bg-gray-200 hover:bg-sky-600 hover:text-white {$class}">{$grandchild->title}</div>
                            HTML;
                        }
                    }
                }

                ?>
            </div>
        </div>
        <div class="mb-8 grid grid-cols-1 xl:grid-cols-4 gap-8">
            <div class="relative order-2 xl:order-1">
                <div class="mb-8 relative">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <img src=" <?= $article->getImage() ?>" alt="<?= $article->title ?>" width="400" height="300" class="bg-gray-200 rounded-t-md w-full" decoding="async" loading="lazy">
                            <div class="flex items-center justify-center flex-col select-none">
                                <div class="flex items-center justify-center px-3 py-2 w-full bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg" title="{{lang=delete}}">{{lang=delete}}</div>
                                <div class="flex items-center justify-center px-3 py-2 w-full rounded-b-md bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg" title="{{lang=upload}}">{{lang=upload}}</div>
                            </div>
                        </div>
                    </div>
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_image}}</label>
                </div>
                <div class="mb-8 relative">
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_gallery}}</label>
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-2 gap-4">
                                <div class="w-full rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <?php
                                foreach ($article->getGalleryImages() as $image) {
                                    echo <<<HTML
                                    <div class="text-sm select-none relative">
                                        <img src="{$image['thumb']}" width="400" height="300" alt="{$article->title}" decoding="async" loading="lazy" class="block w-full rounded-md">
                                        <div class="absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    </div>
                                    HTML;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_video}}</label>
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <?php
                                foreach ($article->getVideos() as $video) {
                                    echo <<<HTML
                                    <div class="text-sm select-none relative">
                                        <video src="{$video['url']}" class="block w-full rounded-md" controls></video>
                                        <div class="absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    </div>
                                    HTML;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative order-3">
                <div class="mb-8 relative">
                    <label for="attachement" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_attachement}}</label>
                    <div class="border-2 border-gray-200 rounded-lg p-4 transition flex flex-col gap-4" aria-label="attachement" name="attachement">
                        <?php

                        foreach ($article->getAttachements() as $attachement) {
                            echo <<<HTML
                            <div class="flex flex-nowrap gap-2 items-center justify-between text-gray-800">
                                <a href="{$attachement['url']}" class="hover:underline line-clamp-1" target="_blank" title="{{lang=delete}}">
                                    {$attachement['name']}
                                </a>
                                <div class="text-gray-500 cursor-pointer hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            HTML;
                        }
                        ?>
                        <div class="w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="tags" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_tags}}</label>
                    <textarea class="block flex-grow w-full min-h-48 outline-none border-2 border-gray-200 rounded-t-lg p-4 focus:border-sky-600 transition" aria-label="tags" name="tags"><?= implode(", ", $article->tags) ?></textarea>
                    <div class="flex items-center justify-center px-3 py-2 w-full bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-b-lg" title="{{lang=article_generate_tags}}">{{lang=article_generate_tags}}</div>
                </div>
                <div class="mb-8 relative">
                    <label for="views" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_views}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="views" id="views" name="views" type="number" min="0" step="1" value="<?= $article->views ?>">
                </div>
                <div class="mb-8 relative">
                    <label for="rating" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_rating}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="rating" id="rating" name="rating" type="number" min="0" max="5" step="0.1" value="<?= $article->rating ?>">
                </div>
                <div class="mb-8 relative">
                    <label for="created_at" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_created_at}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="created_at" id="created_at" name="created_at" type="datetime-local" value="<?= $article->created_at->format("Y-m-d H:i") ?>">
                </div>
                <div class="flex flex-col gap-4">
                    <button class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=delete}}">{{lang=delete}}</button>
                    <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=save}}">{{lang=save}}</button>
                </div>
            </div>
            <div class="relative order-1 xl:order-2 xl:col-span-2 flex flex-col">
                <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_text}}</label>
                <textarea class="flex-grow w-full min-h-96 outline-none border-2 border-gray-200 rounded-lg p-4 focus:border-sky-600 transition" aria-label="text" name="text"><?= $article->text ?></textarea>
            </div>
        </div>
    </form>
    <script nonce="{{nonce}}">
        document.querySelectorAll('[data-select]').forEach(el => {
            el.addEventListener('click', function(event) {
                this.classList.toggle('border-sky-600')

                const optionsElement = this.parentElement.querySelector('[data-options]')

                optionsElement.classList.toggle('hidden')

                event.stopPropagation()

                document.addEventListener('click', () => {
                    this.classList.remove('border-sky-600')
                    optionsElement.classList.add('hidden')
                }, {
                    once: true
                })
            })
        })

        document.querySelectorAll('[data-options] > div').forEach(el => {
            el.addEventListener('click', function(event) {
                const optionsElement = this.parentElement

                const parent = optionsElement.parentElement

                parent.querySelector('input').setAttribute('value', this.dataset.value)

                parent.querySelector('[data-select]').textContent = this.textContent

                optionsElement.querySelectorAll('div').forEach(el => {
                    el.classList.remove('bg-sky-600', 'text-white')

                    if (el.dataset.value === this.dataset.value) {
                        el.classList.add('bg-sky-600', 'text-white')
                    }
                })
            })
        })
    </script>
</main>