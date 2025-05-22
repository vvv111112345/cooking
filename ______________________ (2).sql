-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 22 2025 г., 19:54
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
-- База данных: `Поликлиника`
--

-- --------------------------------------------------------

--
-- Структура таблицы `additional_codes`
--

CREATE TABLE `additional_codes` (
  `id` int NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `additional_codes`
--

INSERT INTO `additional_codes` (`id`, `code`, `description`) VALUES
(1, '017', 'Санаторно-курортное лечение туберкулёза'),
(2, '018', 'Медицинская реабилитация после производственной травмы'),
(3, '019', 'Лечение пациентов с туберкулёзом в санатории'),
(4, '020', 'Дополнительный отпуск по беременности и родам'),
(5, '021', 'Заболевание/травма вследствие опьянения');

-- --------------------------------------------------------

--
-- Структура таблицы `main_codes`
--

CREATE TABLE `main_codes` (
  `id` int NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `main_codes`
--

INSERT INTO `main_codes` (`id`, `code`, `description`) VALUES
(1, '01', 'Заболевание, любая болезнь в хронической или острой форме'),
(2, '02', 'Травма, бытовые повреждения'),
(3, '03', 'Карантин, контакт с носителем инфекции'),
(4, '05', 'Отпуск по беременности и родам'),
(5, '06', 'Протезирование в условиях стационара'),
(6, '08', 'Лечение в санаторно-курортном учреждении'),
(7, '10', 'Иное состояние, патологические состояния'),
(8, '11', 'Патология из Перечня социально значимых заболеваний');

-- --------------------------------------------------------

--
-- Структура таблицы `mkb_codes`
--

CREATE TABLE `mkb_codes` (
  `id` int NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `mkb_codes`
--

INSERT INTO `mkb_codes` (`id`, `code`, `description`) VALUES
(1, 'A00', 'Холера'),
(2, 'A00.0', 'Холера, вызванная холерным вибрионом 01, биовар cholerae'),
(3, 'A00.1', 'Холера, вызванная холерным вибрионом 01, биовар eltor'),
(4, 'A00.9', 'Холера неуточнённая'),
(5, 'A01', 'Тиф и паратиф'),
(6, 'A01.0', 'Брюшной тиф'),
(7, 'A01.1', 'Паратиф A'),
(8, 'A01.2', 'Паратиф B'),
(9, 'A01.3', 'Паратиф C'),
(10, 'A01.4', 'Паратиф неуточнённый');

-- --------------------------------------------------------

--
-- Структура таблицы `Коды_больничных`
--

CREATE TABLE `Коды_больничных` (
  `id` int NOT NULL,
  `код` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `описание` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `МКБ10`
--

CREATE TABLE `МКБ10` (
  `id` int NOT NULL,
  `код` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `описание` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `полное_описание` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Операция`
--

CREATE TABLE `Операция` (
  `id` int NOT NULL,
  `opicanie_opera` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `data_opera` date NOT NULL,
  `pezyltat_oper` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_patient` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Операция`
--

INSERT INTO `Операция` (`id`, `opicanie_opera`, `data_opera`, `pezyltat_oper`, `id_patient`) VALUES
(1, 'паллиативная операция', '2025-04-09', 'Ближайшие результаты', 4),
(2, ' симптоматическая операция', '2025-04-13', ' Средний период ', 5),
(3, 'Дренирующая холецистотомия ', '2025-06-09', 'отдалённый', 2),
(4, 'Санитарная мастэктомия', '2025-03-17', 'Ближайшие результаты', 1),
(5, 'Трансплантация ', '2024-11-11', 'Средний период', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `Пациенты`
--

CREATE TABLE `Пациенты` (
  `id` int NOT NULL,
  `FIO` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `gorod` varchar(60) NOT NULL,
  `vozract` int NOT NULL,
  `pol` varchar(10) NOT NULL,
  `mesto_rad` varchar(60) NOT NULL,
  `prinad` varchar(60) NOT NULL,
  `data_rozdeniaya` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Пациенты`
--

INSERT INTO `Пациенты` (`id`, `FIO`, `address`, `gorod`, `vozract`, `pol`, `mesto_rad`, `prinad`, `data_rozdeniaya`) VALUES
(1, 'Нургалиева Самира Р.', 'Дагестанская 135', 'Махачкала', 20, 'ж', 'студент', 'основное', '24.12.2005'),
(2, 'Альбеков Аскар А.', 'Проспект Октября 45', 'Уфа', 18, 'М', 'студент', 'основное', '12.05.2007'),
(3, 'Сирбаева Айсылу А.', 'Дагестанская 137', 'Махачкала', 20, 'ж', 'студент', 'основное', '30.10.2005'),
(4, 'Ахунов Айназ И.', 'Проспект Октября 90', 'Уфа', 18, 'М', 'студент', 'основное', '05.11.2007'),
(5, 'Бикбулатов Артур А.', 'Буржуа 13', 'Уфа', 21, 'м', 'студент', 'основное', '21.12.2004');

-- --------------------------------------------------------

--
-- Структура таблицы `Врачи`
--

CREATE TABLE `Врачи` (
  `id` int NOT NULL,
  `FIO` varchar(60) NOT NULL,
  `Doljinost` varchar(60) NOT NULL,
  `ctaj` int NOT NULL,
  `zvanie` varchar(30) NOT NULL,
  `address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Врачи`
--

INSERT INTO `Врачи` (`id`, `FIO`, `Doljinost`, `ctaj`, `zvanie`, `address`) VALUES
(1, 'Дайбова Кристина Андреевна', 'врач-онколог', 10, 'канд.мед.наук', 'Российская дом 100/3'),
(2, 'Галимова Дильбар Айдаровна', 'гастроэнтеролог', 6, 'докт.мед.наук', 'Российская дом 100/3'),
(3, 'Дымченко Александра Владимировна', 'терапевт', 3, 'канд.мед.наук', 'Российская дом 100/3.'),
(4, 'Нозирова Сарвиноз С.', 'врач-кардиолог', 5, 'докт.мед.наук', 'Российская дом 100/3'),
(5, 'Кузина Светлана А.', 'хирург', 7, 'канд.мед.наук', 'Российская дом 100/3.');

-- --------------------------------------------------------

--
-- Структура таблицы `История болезни`
--

CREATE TABLE `История болезни` (
  `id` int NOT NULL,
  `diagnoz` varchar(60) NOT NULL,
  `data_zabolevania` date NOT NULL,
  `data_thecure` date NOT NULL,
  `vid_lechenia` varchar(60) NOT NULL,
  `id_patient` int NOT NULL,
  `id_vrach` int NOT NULL,
  `id_operaz` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `История болезни`
--

INSERT INTO `История болезни` (`id`, `diagnoz`, `data_zabolevania`, `data_thecure`, `vid_lechenia`, `id_patient`, `id_vrach`, `id_operaz`) VALUES
(1, 'дисфункция трансплантата сердца', '2025-04-01', '2025-06-01', 'Медикаментозная терапия', 1, 1, 5),
(2, 'рак молочных желез', '2025-02-03', '2025-11-15', 'Химиотерапия', 2, 2, 4),
(3, 'острый деструктивный холецистит ', '2024-11-18', '2025-05-22', 'Медикаментозная терапия', 3, 3, 3),
(4, 'Нарушения глотательной функции', '2024-12-15', '2025-06-13', 'Физическая терапия', 4, 4, 2),
(5, 'кишечная непроходимость', '2025-01-13', '2025-06-10', 'Хирургическое вмешательство', 5, 5, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `additional_codes`
--
ALTER TABLE `additional_codes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `main_codes`
--
ALTER TABLE `main_codes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `mkb_codes`
--
ALTER TABLE `mkb_codes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Коды_больничных`
--
ALTER TABLE `Коды_больничных`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `МКБ10`
--
ALTER TABLE `МКБ10`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Операция`
--
ALTER TABLE `Операция`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_patient` (`id_patient`);

--
-- Индексы таблицы `Пациенты`
--
ALTER TABLE `Пациенты`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Врачи`
--
ALTER TABLE `Врачи`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `История болезни`
--
ALTER TABLE `История болезни`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kodpation` (`id_patient`),
  ADD KEY `id_vrach` (`id_vrach`),
  ADD KEY `id_operaz` (`id_operaz`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `additional_codes`
--
ALTER TABLE `additional_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `main_codes`
--
ALTER TABLE `main_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `mkb_codes`
--
ALTER TABLE `mkb_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `Коды_больничных`
--
ALTER TABLE `Коды_больничных`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `МКБ10`
--
ALTER TABLE `МКБ10`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `Операция`
--
ALTER TABLE `Операция`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `Пациенты`
--
ALTER TABLE `Пациенты`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `Врачи`
--
ALTER TABLE `Врачи`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `История болезни`
--
ALTER TABLE `История болезни`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Операция`
--
ALTER TABLE `Операция`
  ADD CONSTRAINT `операция_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `Пациенты` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `История болезни`
--
ALTER TABLE `История болезни`
  ADD CONSTRAINT `история болезни_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `Пациенты` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `история болезни_ibfk_2` FOREIGN KEY (`id_vrach`) REFERENCES `Врачи` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `история болезни_ibfk_3` FOREIGN KEY (`id_operaz`) REFERENCES `Операция` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
