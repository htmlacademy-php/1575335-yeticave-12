CREATE DATABASE yeti_cave_db
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;
	
USE yeti_cave_db;
	
CREATE TABLE users (
	user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	email VARCHAR(255) NOT NULL UNIQUE,
	user_name VARCHAR(50) NOT NULL UNIQUE,
	password VARCHAR(64) NOT NULL,
	contacts VARCHAR(255)
	);

CREATE TABLE categories (
	category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50),
	symbol_code VARCHAR(50),
	INDEX (name)
	);
	
	
CREATE TABLE lots (
	lot_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	lot_name VARCHAR(120),
	lot_description VARCHAR(255),
	img_url VARCHAR(255),
	starting_price INT UNSIGNED,
	date_end DATE,
	rate INT UNSIGNED,
	author_id	INT UNSIGNED,
	winner	INT UNSIGNED,
	category_id	INT UNSIGNED,
	FOREIGN KEY (author_id)
		REFERENCES users(user_id),
	FOREIGN KEY (winner)
		REFERENCES users(user_id),
	FOREIGN KEY (category_id)
		REFERENCES categories(category_id),
	INDEX (lot_name)
	);
	
	
	

CREATE TABLE bids (
	bid_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	bid_price INT UNSIGNED,
	user_id INT UNSIGNED,
	lot_id INT UNSIGNED,
	FOREIGN KEY (user_id)
		REFERENCES users(user_id),
	FOREIGN KEY (lot_id) 
		REFERENCES lots(lot_id)
	);

CREATE FULLTEXT 
INDEX lot_ft_search
ON lots(lot_name, lot_description); 
