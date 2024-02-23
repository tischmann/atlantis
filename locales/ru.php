<?php

return [
    // Общие
    'access_denied' => 'Доступ запрещен',
    'back' => 'Назад',
    'add' => 'Добавить',
    'actions' => 'Действия',
    'edit' => 'Изменить',
    'delete' => 'Удалить',
    'upload' => 'Загрузить',
    'close' => 'Закрыть',
    'save' => 'Сохранить',
    'error' => 'Ошибка',
    'json_error' => 'Ошибка JSON',
    'not_found' => 'Запрашиваемый ресурс не найден',
    'route_not_found' => 'Маршрут не найден',
    'template_not_found' => 'Шаблон не найден',
    'variable_required' => 'Переменная обязательна',
    'invalid_type' => 'Неверный тип',
    'confirm_delete' => 'Вы уверены, что хотите удалить?',
    'error_code' => 'Код ошибки',
    'response_code' => 'Код ответа',
    'csrf_failed' => 'CSRF токен не прошел проверку',
    'bad_request' => 'Неверный запрос',
    // Ошибки Json Web Token
    'jwt_missing_public_key' => 'Отсутствует публичный ключ',
    'jwt_wrong_segment_amount' => 'Неверное количество сегментов',
    'jwt_bad_header' => 'Неверный заголовок',
    'jwt_bad_payload' => 'Неверное тело',
    'jwt_algorithm_not_supported' => 'Алгоритм не поддерживается',
    'jwt_algorithm_not_allowed' => 'Алгоритм не разрешен',
    'jwt_key_id_invalid' => 'Неверный идентификатор ключа',
    'jwt_key_id_missing' => 'Отсутствует идентификатор ключа',
    'jwt_token_expired' => 'Истёк срок действия токена',
    'jwt_token_not_yet_valid' => 'Токен еще не действителен',
    'jwt_ssl_unable_to_sign' => 'Не удалось подписать токен',
    'jwt_null_result' => 'Пустой результат',
    // Авторизация
    'signin_login' => 'Логин',
    'signin_password' => 'Пароль',
    'signin_submit' => 'Войти',
    'signout_submit' => 'Выйти',
    // Пользователи
    'user_not_found' => 'Пользователь не найден',
    'user_save_error' => 'Ошибка сохранения пользователя',
    'user_delete_error' => 'Ошибка удаления пользователя',
    'user_last_admin' => 'Последний администратор не может быть удален',
    'user_login_format' => 'Логин должен содержать не менее 3 символов и состоять из латинских букв и цифр а также символов _ и -',
    'user_login_exists' => 'Пользователь с таким логином уже существует',
    'user_name_format' => 'Имя должно содержать не менее 3 символов',
    'user_password_complexity' => 'Пароль должен содержать не менее 8 символов, включая цифры, строчные и прописные буквы',
    'user_passwords_not_match' => 'Пароли не совпадают',
    'user_new' => 'Новый пользователь',
    'user_update' => 'Изменение пользователя',
    'user_list' => 'Список пользователей',
    'user_name' => 'Имя',
    'user_login' => 'Логин',
    'user_password' => 'Пароль',
    'user_password_repeat' => 'Повторите пароль',
    'user_password_mismatch' => 'Пароли не совпадают',
    'user_status' => 'Статус',
    'user_status_active' => 'Активен',
    'user_status_inactive' => 'Неактивен',
    'user_role' => 'Роль',
    'user_role_guest' => 'Гость',
    'user_role_user' => 'Пользователь',
    'user_role_admin' => 'Администратор',
    'user_remarks' => 'Примечание',
    // Даты
    'year_ago' => 'год',
    'years_ago' => 'лет',
    'years_ago_2_4' => 'года',
    'month_ago' => 'месяц',
    'months_ago' => 'месяцев',
    'months_ago_2_4' => 'месяца',
    'day_ago' => 'день',
    'days_ago' => 'дней',
    'days_ago_2_4' => 'дня',
    'hour_ago' => 'час',
    'hours_ago' => 'часов',
    'hours_ago_2_4' => 'часа',
    'minute_ago' => 'минута',
    'minutes_ago' => 'минут',
    'minutes_ago_2_4' => 'минуты',
    'second_ago' => 'секунда',
    'seconds_ago' => 'секунд',
    'seconds_ago_2_4' => 'секунды',
    // Статьи
    'article_edit' => 'Редактирование статьи',
    'article_new' => 'Новая статья',
    'article_list' => 'Список статей',
    'article_title' => 'Заголовок',
    'article_text' => 'Текст',
    'article_author' => 'Автор',
    'article_category' => 'Категория',
    'article_image' => 'Изображение',
    'article_gallery' => 'Галерея',
    'article_locale' => 'Локаль',
    'article_tags' => 'Теги',
    'article_views' => 'Просмотры',
    'article_rating' => 'Рейтинг',
    'article_created_at' => 'Создано',
    'article_updated_at' => 'Изменено',
    'article_not_found' => 'Статья не найдена',
    'article_generate_tags' => 'Сгенерировать теги',
    'article_attachement' => 'Вложения',
    'article_created_at' => 'Дата и время создания',
    'article_video' => 'Видео',
];
