<?php

declare(strict_types=1);

use App\Controllers\{
    ArticlesController,
    CategoriesController,
    IndexController,
    UsersController
};

use Tischmann\Atlantis\{
    App,
    Router,
    Route
};

$user = App::getUser();

if ($user->isAdmin() || $user->canModerate() || $user->canAuthor()) {
    Router::add(new Route(
        controller: new IndexController(),
        path: 'dashboard',
        action: 'showDashboard',
        method: 'GET',
        title: get_str('dashboard')
    ));
}

if ($user->isAdmin()) {
    Router::add(new Route(
        controller: new UsersController(),
        path: 'users',
        action: 'showAllUsers',
        method: 'GET',
        title: get_str('users_list')
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user',
        action: 'getUser',
        args: ['id' => 0],
        method: 'GET',
        title: get_str('user_new')
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user',
        action: 'updateUser',
        method: 'POST',
        args: ['id' => 0]
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'getUser',
        method: 'GET',
        title: get_str('user_update')
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'updateUser',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'deleteUser',
        method: 'DELETE'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/articles',
        action: 'showAllArticles',
        method: 'GET',
        title: get_str('articles_list')
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/article/{id}',
        action: 'getArticleEditor',
        method: 'GET',
        title: get_str('article_edit')
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'new/article',
        action: 'getArticleEditor',
        method: 'GET',
        args: ['id' => 0],
        title: get_str('article_new')
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article',
        action: 'updateArticle',
        method: 'POST',
        args: ['id' => 0]
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/{id}',
        action: 'updateArticle',
        method: 'PUT',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/{id}',
        action: 'deleteArticle',
        method: 'DELETE'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/image',
        action: 'uploadImage',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/gallery',
        action: 'uploadGalleryImage',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/images',
        action: 'uploadImages',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/videos',
        action: 'uploadVideos',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/attachements',
        action: 'uploadAttachements',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/image/{id}',
        action: 'uploadImage',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/temp/image',
        action: 'deleteTempImage',
        method: 'DELETE',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'locale/categories/{locale}',
        action: 'fetchCategories',
        method: 'GET',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'edit/categories',
        action: 'showAllCategories',
        method: 'GET',
        title: get_str('categories_list')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'sort/categories',
        action: 'sortCategories',
        method: 'PUT',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'edit/category/{id}',
        action: 'getCategoryEditor',
        method: 'GET',
        title: get_str('category_edit')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'new/category',
        action: 'getCategoryEditor',
        method: 'GET',
        args: ['id' => 0],
        title: get_str('category_new')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category',
        action: 'insertCategory',
        method: 'POST',
        args: ['id' => 0]
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/{id}',
        action: 'updateCategory',
        method: 'PUT',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/{id}',
        action: 'deleteCategory',
        method: 'DELETE'
    ));
}
