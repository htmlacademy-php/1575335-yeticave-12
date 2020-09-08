<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}
/**
* Возвращает отформатированную цену  товара (пробелы между тысячными разрядами) с добавлением знака Рубля (₽).
* @param float $price Цена товара
* @return string Отформатированная цена товара
*/
function format_price(float $price) : string {
	
    $rounded = ceil($price);
	
    return number_format($rounded,0,'.',' ') . ' ₽';
		
}

/**
* Возвращает оставшееся до переданной в функцию даты время в виде массива [ЧЧ, ММ]
* @param string $future_date Конечная дата в формате 'ГГГГ-ММ-ДД'
* @return array|null Возвращает массив строк в формате [ЧЧ, ММ] или ничего не возращает если входной параметр имеет неверный формат.
*/

function get_time_remaining(string $future_date) : ?array {
	
	if(is_date_valid($future_date))
    {
        $current_date = strtotime('now');
        $future_date = strtotime($future_date);
        $diff = $future_date - $current_date;
        $hours = floor($diff/3600);
        $minutes = ceil(($diff%3600)/60);
        
        if($minutes == 60)
        {
            $hours += 1;
            $minutes = 0;
        }
        
        $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
       
        
        return [ $hours, $minutes ];
    }
	
    return null;
}
/**
* Выставляет http заголовок 404, показывает страницу, адрес которой передан в функцию и останавливает дальнейшее выполнение сценария.
* @param string $path относительный путь до страницы 404
**/
function send_status_404_page(string $path) : void {
    
    http_response_code(404); 
    readfile($path);
    die();
     
}
/**
* Функция проверки полей формы на пустоту, ничего не возвращает если поле не пустое.
* @param string $field_name Имя поля
* @return string|null Ошибка валидации или null если ошибок нет
**/
function validate_filled(string $field_name) : ?string{
    if (empty($_POST[$field_name])) {
        return "Это поле должно быть заполнено";
    }
    return null;
}
/**
* Функция валидации начальной цены лота, в случае успешной валидации ничего не возвращает.
* @param string $field_name Имя поля
* @return string|null Причина ошибки валидации или null если ошибок нет
**/
function validate_starting_price(string $field_name) : ?string {
    if($empty = validate_filled($field_name)){
        return $empty;
    } else if (!is_numeric($_POST[$field_name])){
        return 'Начальная цена должна быть числом';
    } elseif ($_POST[$field_name] <=0) {
        return 'Начальная цена должна быть больше нуля';
    }
    return null;
}

/**
* Функция валидации даты окончания лота, в случае успешной валидации ничего не возвращает.
* @param string $field_name Имя поля
* @return string|null Причина ошибки валидации или null если ошибок нет
**/
function validate_date_end(string $field_name) : ?string {
    $tomorrow_date = date_create('tomorrow');
    
    if($empty = validate_filled($field_name)){
        return $empty;
    } elseif(!is_date_valid($_POST[$field_name])) {
        return 'Неккоректный формат даты';
    } elseif (date_create($_POST[$field_name]) < $tomorrow_date) {
        return 'Некорректная дата завершения лота';
    }
    return null;
}

/**
* Функция валидации шага ставки, в случае успешной валидации (шаг ставки целое чило>0) ничего не возвращает.
* @param string $field_name Имя поля
* @return string|null Причина ошибки валидации или null если ошибок нет
**/
function validate_step(string $field_name) : ?string {
    if($empty = validate_filled($field_name)){
        return $empty;
    } elseif (!is_numeric($_POST[$field_name]) || !ctype_digit($_POST[$field_name])) {
        return 'Шаг ставки должен быть целым числом';
    } elseif ($_POST[$field_name] <= 0) {
        return 'Шаг ставки должен быть больше ноля';
    }
    return null;
}
/* Функция валидации категории, если категория равна "Выберите категорию" валидация не пройдена.
*  @param string $field_name Имя поля
*  @return string|null Причина ошибки валидации или null если ошибок нет
**/
function validate_category(string $field_name) {
    if($empty = validate_filled($field_name)){
        return $empty;
    } elseif ($_POST[$field_name]=='Выберите категорию'){
        return "Выберите категорию";
    }
    return null;
}
/**
* Функция валидации изображения, в случае успешной валидации ничего не возвращает.
* @param string $field_name Имя поля изображения
* @param array $allowed_mime_type Массив строк с разрешенным MIME типами изображений
* @return string|null Причина ошибки валидации
**/
function validate_image(string $field_name, array $allowed_mime_types) : ?string {
  
    if (isset($_FILES[$field_name])) {
        if ($_FILES[$field_name]['error'] == 4)
        {
            return 'Добавьте изображение лота';
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $path = $_FILES[$field_name]['tmp_name'];
        $mime_type = mime_content_type($path);
    
        if (!in_array($mime_type, $allowed_mime_types)){
            return 'Изображение должно быть в одном из следующих форматов: ' . implode(", ", $allowed_mime_types);
        }
    }
    return null;
}
/**
* Сохраняет изображение в папку /uploads/ не изменяя имени файла.
* @param field_name string Имя поля изображения
* @return string|null Возвращает url сохраненного изображения или не возвращает ничего в случае ошибки
**/
function save_image(string $field_name) : ?string {
    if (isset($_FILES[$field_name])) {
        $file_name = $_FILES[$field_name]['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
    
        if(move_uploaded_file($_FILES[$field_name]['tmp_name'], $file_path . $file_name))
        {
            return $file_url;
        }
    }
    return null;
}

/**
* Функция для сохранения введенных в форме значений
* @param string $field_name Имя поля формы
* @return mixed Значение _POST если такой ключ есть 
**/
function get_post_val(string $field_name) {
    return htmlspecialchars($_POST[$field_name] ?? "") ;
}

/**
* Функция валидации имени пользователя
* @param $field_name имя поля формы
* @return string|null Ошибка валидации/ошибка подключения или не возвращает ничего
**/
function validate_username(string $field_name) : ?string {
    if($empty = validate_filled($field_name)){
        return $empty;
    } 
    if (!$connection = mysqli_connect('localhost','root','root','yeti_cave_db')) {
        
        return 'Ошибка подключения к БД ' . mysqli_connect_error();
        
    } 
    mysqli_set_charset($connection, "utf8");
    $sql = "SELECT user_id, user_name FROM users WHERE user_name = ? LIMIT 1";
    $prepared_sql = db_get_prepare_stmt($connection, $sql, [$_POST[$field_name]]);
    if (!mysqli_stmt_execute($prepared_sql)){
        
       return 'Ошибка запроса к БД ' . mysqli_error($connection);
    
    } 
    $result = mysqli_fetch_all(mysqli_stmt_get_result($prepared_sql), MYSQLI_ASSOC);
    if (!empty($result)){
        return 'Имя пользователя занято';
        }
        
    return null;
    
}

/**
* Функция валидации пароля
* @param string $field_name Имя поля формы
* @return string|null Возвращает ошибку валидации или не возращает ничего в случае отсутствия ошибок
**/
function validate_password(string $field_name) : ?string {
    if($empty = validate_filled($field_name)){
        return $empty;
    } elseif (strlen($_POST[$field_name]) < 8) {
        return 'Минимальная длина пароля 8 символов';
    }
    return null;
}
/**
* Функция валидации адреса электронной почты
* @param string $field_name Имя поля формы, в котором находится строка адреса
* @return string|null Возвращает ошибку валидации или не возращает ничего в случае отсутствия ошибок
*
**/
function validate_email(string $field_name) : ?string {
    if($empty = validate_filled($field_name)){
        return $empty;
    }
    
    if (!filter_var($_POST[$field_name],FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный адрес электронной почты';      
    }
    
    if (!$connection = mysqli_connect('localhost','root','root','yeti_cave_db')) {
        return 'Ошибка подключения к БД ' . mysqli_connect_error();
    } 
    
    mysqli_set_charset($connection, "utf8");
    $sql = "SELECT user_id, email FROM users WHERE LOWER(email) = ? LIMIT 1";
    $prepared_sql = db_get_prepare_stmt($connection, $sql, [$_POST[$field_name]]);
    
    if (!mysqli_stmt_execute($prepared_sql)){
        return 'Ошибка запроса к БД' . mysqli_error($connection);
    } 
    
    $result = mysqli_fetch_all(mysqli_stmt_get_result($prepared_sql), MYSQLI_ASSOC);
    if (!empty($result)){
        return 'Пользователь с таким email уже существует';
    }
    return null;
}

