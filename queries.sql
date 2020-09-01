USE yeti_cave_db;

/*Добавление категорий*/
INSERT INTO categories (name, symbol_code)
VALUES 
		('Доски и лыжи', 'boards'), 
		('Крепления', 'attachment'), 
		('Ботинки', 'boots'), 
		('Одежда', 'clothing'), 
		('Инструменты', 'tools'), 
		('Разное', 'other');

/*#Создание пользователей*/
INSERT INTO users (email, user_name, password, contacts)
VALUES 
		('asd@gmail.com', 'asd', '682c96bd5c1e664184134509e6784e3353b06716b03d48244804552ac876bc00', '+7456323526'),
		('gman@gmail.com', 'G-man', '67e828614a0555c66632fabd139ca253e97feb8c9ea95fe667f8d24ec6657ff4', 'no contacts'),
		('harambe@gmail.com', 'H@r@mb3', '6876db903aa3df51b6f357c188788fdfc270468eee79e9fcdaa82c7d09d85ca7', '6 feet under right now'),
		('me@gmail.com', 'iam', '67e828614a0555c66632fabd139ca253e97feb8c9ea95fe667f8d24ec6657f24', 'pls dont contact me');
		
/*Создание лотов. Все цены указаны в копейках*/
INSERT INTO lots (lot_name, img_url, starting_price, date_end, rate, author_id, winner, category_id)
VALUES 
		('2014 Rossignol District Snowboard', 'img/lot-1.jpg', 1099900, '2020-09-15', 5000, 2, NULL, 1),
		('DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 15999900, '2020-09-10', 20000, 2, NULL, 1),
		('Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 800000, '2020-09-11', 4000, 1, NULL, 2),
		('Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 1099900, '2020-08-28', 20000, 3, 4, 3),
		('Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 750000, '2020-09-01', 5000, 2, NULL, 4),
		('Маска Oakley Canopy', 'img/lot-6.jpg', 540000, '2020-08-27', 4000, 4, NULL, 6);

/*Создание ставки*/
INSERT INTO bids (bid_price, user_id, lot_id)
VALUES 
		(755000, 3, 5),
		(760000, 4, 5),
		(765000, 3, 5),
		(16019900, 1, 2),
		(804000, 2, 3);

/*Запрос на получениех всех категорий*/
SELECT name, symbol_code  FROM categories;

/*Запрос на получение самых новых, открытых лотов*/
SELECT lot_name AS 'name', cat.name AS 'category', img_url, starting_price AS 'price',  MAX(bid_price) as 'bid_price' , date_end AS 'expiration_date' 
FROM lots 
INNER JOIN categories as cat ON lots.category_id = cat.category_id
LEFT JOIN bids ON lots.lot_id = bids.lot_id
WHERE date_end > CURDATE()
GROUP BY lots.lot_id
ORDER BY date_created DESC;

/*Запрос на получение лота по его id*/
SELECT lots.*, cat.name FROM lots
INNER JOIN categories as cat
ON lots.category_id = cat.category_id
WHERE lot_id = ?;

/*Запрос на изменение лота по его идентификатору*/
UPDATE lots SET lot_name = ?
WHERE lot_id = ?;

/*Запрос на получение списка ставок для лота по его идентификатору*/
SELECT users.user_name, bids.bid_price, bids.date_time FROM bids
INNER JOIN users ON bids.user_id = users.user_id
WHERE lot_id = ?
ORDER BY date_time DESC;




