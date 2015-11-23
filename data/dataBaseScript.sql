CREATE DATABASE Disenazo;
USE Disenazo;

CREATE TABLE User (
	fName VARCHAR(50) NOT NULL,
    lName VARCHAR(50) NOT NULL,
    userName VARCHAR(50) NOT NULL PRIMARY KEY,
    email VARCHAR(50) NOT NULL,
    passwrd VARCHAR(250) NOT NULL,
    avatar VARCHAR(50),
    city VARCHAR(50),
    address VARCHAR(200),
    aboutMe VARCHAR(400)
);

CREATE TABLE Design(
	designId VARCHAR(50) NOT NULL PRIMARY KEY,
    userName VARCHAR(50) NOT NULL,
	designName VARCHAR(50) NOT NULL,
	uploadDate DATETIME NOT NULL DEFAULT NOW(),
	description VARCHAR(250),
    pricePercent INT NOT NULL,
    views INT DEFAULT 0,
    rate TINYINT DEFAULT 0,
    FOREIGN KEY (userName)
		REFERENCES User (userName)
		ON DELETE CASCADE
);

CREATE TABLE Product(
	productName VARCHAR(30) NOT NULL PRIMARY KEY,
	type VARCHAR(30),
    price DECIMAL NOT NULL
	smallImage VARCHAR(30),
	mediumImage VARCHAR(30),
	largeImage VARCHAR(30),
);

CREATE TABLE Comment(
	commentId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	comment VARCHAR(500) NOT NULL,
	userName VARCHAR(50) NOT NULL,
    design VARCHAR(50) NOT NULL,
	commentDate DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (design)
		REFERENCES Design (designId)
		ON DELETE CASCADE,
    FOREIGN KEY (userName)
    	REFERENCES User (userName)
    	ON DELETE CASCADE
);

CREATE TABLE Rate(
	rateId VARCHAR(30) NOT NULL PRIMARY KEY,
	rate TINYINT,
    userName VARCHAR(50),
    design VARCHAR(50) NOT NULL,
    FOREIGN KEY (design)
		REFERENCES Design (designId)
		ON DELETE CASCADE,
    FOREIGN KEY (userName)
    	REFERENCES User (userName)
    	ON DELETE CASCADE
);

CREATE TABLE Cart(
	orderId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userName VARCHAR(50) NOT NULL,
	design VARCHAR(50) NOT NULL,
    product VARCHAR(30) NOT NULL,
    color VARCHAR(20) NOT NULL,
    size VARCHAR(20) NOT NULL,
    unitPrice INT NOT NULL,
	quantity INT NOT NULL,
    totalPrice DECIMAL NOT NULL,
	status CHAR(1) NOT NULL,
	FOREIGN KEY (userName)
		REFERENCES User (userName)
		ON DELETE CASCADE,
	FOREIGN KEY (design)
		REFERENCES Design (designId)
		ON DELETE CASCADE,
    FOREIGN KEY (product)
    	REFERENCES Product (productName)
    	ON DELETE CASCADE
);

--ALTER TABLE Design add uploadDate DATETIME DEFAULT NOW() AFTER designName;

--UPDATE Design SET views = 32 WHERE designId = alien_j3s94hjdolp8x;

--Static products
--Divider is to balance the price of design per product
INSERT INTO Product (productName, type, price, smallImage)
VALUES 	('t-shirt', 'clothing', 200, 'tshirt.png'),
		('v-neck', 'clothing', 200, 'vneck.png'),
		('long sleeve', 'clothing', 230, 'logsleeve.png'),
		('hoodie', 'clothing', 300, 'hoodie.png'),
		('poster', 'wall-art', 100, 'poster.jpg'),
		('canvas print', 'wall-art', 500, 'canvas.jpg'),
		('art print', 'wall-art', 120, 'artprint.jpg'),
		('framed print', 'wall-art', 1000, 'framedart.png'),
		('metal print', 'wall-art', 450, 'metal.jpg'),
		('iphone case', 'cases', 200, 'iphonecase.jpg'),
		('samsung case', 'cases', 200, 'samsungcase.jpg'),
		('ipad case', 'cases', 400, 'ipadcase.jpg'),
		('laptop skin', 'cases', 200, 'laptopskin.jpg'),
		('sticker', 'stationery', 15, 'sticker.png'),
		('notebook', 'stationery', 70, 'notebook.jpg'),
		('hardcover journal', 'stationery', 150, 'harcoverjournal.png'),
		('cup', 'home', 100, 'cup.png'),
		('thermo', 'home', 200, 'thermo.png'),
		('popcorn bowl', 'home', 150, 'popcornbowl.png');

--Estos son solo para probar la base de datos
INSERT INTO User (fName, lName, userName, email, passwrd)
VALUES 	('Matt', 'Murdock', 'Daredevil', 'dare@devil', 'daredevil');

INSERT INTO Design (designId, userName, designName, description, pricePercent)
VALUES 	('alien_j3s94hjdolp8x.jpg', 'Daredevil', 'alienigena', 'Un diseño espacial de un alien', 20),
		('coffee_42jh4ilos39uc.png', 'Daredevil', 'coffee', 'Diseño de una taza bien rara', 15),
		('owl_yinyang_jkj48sidl98j3.jpg', 'Daredevil', 'owl_yinyang', 'El yinyang representado con unas lechuzas', 25),
		('564935619e489_superheroes.jpg', 'Daredevil', 'superheroes', 'Un chido diseno de grandes superheroes de DC', 25);
