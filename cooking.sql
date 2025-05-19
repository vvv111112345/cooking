-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 19 2025 г., 20:03
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cooking`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Закуски', 'Легкие блюда перед основным приемом пищи.'),
(2, 'Основные блюда', 'Блюда, являющиеся центром трапезы.'),
(3, 'Десерты', 'Сладкие блюда, подаваемые в конце еды.'),
(4, 'Напитки', 'Различные виды напитков.');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `recipe_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`comment_id`, `recipe_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 2, 1, 'комментарий\r\n', '2025-05-14 09:59:18'),
(3, 5, 1, 'оаоаооаао пацантре', '2025-05-16 05:45:13');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int NOT NULL,
  `user_id` int NOT NULL,
  `recipe_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `recipe_id`, `created_at`) VALUES
(1, 5, 6, '2025-05-18 20:09:47');

-- --------------------------------------------------------

--
-- Структура таблицы `followers`
--

CREATE TABLE `followers` (
  `follower_id` int NOT NULL,
  `following_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `description` text,
  `unit` varchar(50) DEFAULT NULL,
  `calories` decimal(10,2) DEFAULT NULL COMMENT 'Ккал на 100г',
  `protein` decimal(10,2) DEFAULT NULL COMMENT 'Белки (г)',
  `fat` decimal(10,2) DEFAULT NULL COMMENT 'Жиры (г)',
  `carbs` decimal(10,2) DEFAULT NULL COMMENT 'Углеводы (г)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `ingredient_name`, `description`, `unit`, `calories`, `protein`, `fat`, `carbs`) VALUES
(1, 'Мука', 'Пшеничная мука высшего сорта.', 'грамм', '364.00', '10.30', '1.00', '76.10'),
(2, 'Яйца', 'Куриные яйца.', 'шт', '155.00', '13.00', '11.00', '1.10'),
(3, 'Сахар', 'Белый кристаллический сахар.', 'грамм', NULL, NULL, NULL, NULL),
(4, 'Молоко', 'Коровье молоко.', 'мл', NULL, NULL, NULL, NULL),
(5, 'Соль', 'Поваренная соль.', 'грамм', NULL, NULL, NULL, NULL),
(6, 'Перец черный молотый', 'Молотый черный перец.', 'грамм', NULL, NULL, NULL, NULL),
(7, 'да', NULL, 'мл', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int NOT NULL,
  `user_id` int NOT NULL,
  `recipe_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int NOT NULL,
  `category_id` int NOT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `description` text,
  `instructions` text NOT NULL,
  `prep_time` int DEFAULT NULL,
  `cook_time` int DEFAULT NULL,
  `servings` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `category_id`, `recipe_name`, `description`, `instructions`, `prep_time`, `cook_time`, `servings`, `user_id`, `image_url`, `created_at`) VALUES
(1, 3, 'Шоколадный торт', 'Классический шоколадный торт.', '1. Смешайте сухие ингредиенты...\r\n2. Добавьте жидкие ингредиенты...\r\n3. Выпекайте в духовке...', 20, 40, 8, 1, '/uploads/recipe_6826d4b924850.jpg', '2025-05-13 10:06:23'),
(2, 2, 'Жареная курица', 'Хрустящая жареная курица.', '1. Замаринуйте курицу...\r\n2. Обваляйте в панировке...\r\n3. Обжарьте до золотистой корочки...', 15, 25, 4, 1, '/uploads/recipe_6826d5a7448b8.jpg', '2025-05-13 10:06:23'),
(5, 3, 'Пирожное картошка', 'Пирожное «Картошка» — десерт, для приготовления которого используют измельчённые бисквиты, печенье или сухари. Чаще всего форма изделия — клубневидная, но может быть и другой. Пирожное обваливают в какао-порошке, чтобы скрыть разнородный состав.', 'Для бисквита: мука пшеничная — 300 г, яйца — 6 шт., сахар-песок — 150 г, сахар ванильный — 10 г, соль — на кончике ножа.  \r\ngastronom.ru\r\nДля крема: масло сливочное — 200 г, сгущённое молоко с сахаром — 240 г, ром — 60 мл.  \r\ngastronom.ru\r\nДля декора: какао-порошок — 10 г, сахарная пудра — 5 г.', 30, 60, 12, 1, 'uploads/recipes/recipe_6826ced840794.jpg', '2025-05-16 05:36:24'),
(6, 3, 'Пирожное картошка', 'да', 'да', 20, 30, 4, 5, 'uploads/recipes/recipe_682a3b63b1009.jpg', '2025-05-18 19:56:19');

-- --------------------------------------------------------

--
-- Структура таблицы `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `recipe_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`, `quantity`) VALUES
(1, 1, '300.00'),
(1, 2, '4.00'),
(1, 3, '200.00'),
(1, 4, '200.00'),
(2, 2, '2.00'),
(2, 5, '5.00'),
(2, 6, '2.00'),
(6, 7, '1.00');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `avatar` varchar(255) DEFAULT 'images/default-avatar.jpg',
  `bio` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `username`, `name`, `password`, `email`, `created_at`, `avatar`, `bio`) VALUES
(1, 'vvv', 'ввв', '$2y$10$mJ5xytyJ3TwvudwJGtW77eRX9Lo41tszVaj3VTlV/Og21lcsZDFye', 'vvvv@12345', '2025-05-13 10:37:27', 'uploads/avatars/avatar_1_1747588627.jpg', NULL),
(2, 'ллл', NULL, '$2y$10$bluJLKVE8tlntpEL1j9mjudSlPutb5fLNM8VLt0OzXOGopZsbRJIy', 'lll@2', '2025-05-14 10:01:26', 'images/default-avatar.jpg', NULL),
(3, 'aaa', NULL, '$2y$10$fUzlnE46O8I8w5KaE888bejD.Oxk5Os7ozuV.W5YSBHIM5lDnh0T2', 'vladislavydin8@gmail.com', '2025-05-15 17:39:42', 'images/default-avatar.jpg', NULL),
(4, '', 'hhh', '$2y$10$wOIDBbCRLuOdq/UxXjvgAeBI40.Txsh2CVDxV9hJfxSaOCvv6aTBy', NULL, '2025-05-18 19:24:48', 'images/default-avatar.jpg', NULL),
(5, 'kkk', '', '$2y$10$eMIpvyK9SvLE1pT2BaX.AeVsY2TAAJ19NRCc5D.txSI4PmpMM26pK', 'k@5', '2025-05-18 19:47:03', 'uploads/avatars/avatar_5_1747598213.jpg', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Индексы таблицы `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Индексы таблицы `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`);

--
-- Индексы таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Индексы таблицы `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_recipes_user` (`user_id`);

--
-- Индексы таблицы `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`recipe_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`);

--
-- Ограничения внешнего ключа таблицы `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`);

--
-- Ограничения внешнего ключа таблицы `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Ограничения внешнего ключа таблицы `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`),
  ADD CONSTRAINT `recipe_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
