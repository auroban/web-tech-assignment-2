DROP DATABASE IF EXISTS assignment2;
CREATE DATABASE IF NOT EXISTS assignment2;

USE assignment2;

CREATE TABLE IF NOT EXISTS product(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) UNIQUE NOT NULL,
	description TEXT DEFAULT NULL,
	price FLOAT NOT NULL,
	shipping_cost FLOAT NOT NULL,
	currency ENUM('USD', 'CAD') NOT NULL
);

CREATE TABLE IF NOT EXISTS user(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) UNIQUE NOT NULL,
	email VARCHAR(255) UNIQUE NOT NULL,
	passwd VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS product_resource(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	product_id BIGINT NOT NULL,
	type ENUM('IMAGE', 'VIDEO', 'AUDIO') NOT NULL,
	uri TEXT NOT NULL,
	CONSTRAINT fk_pr_product_id FOREIGN KEY(product_id) REFERENCES product(id)
);

CREATE TABLE IF NOT EXISTS purchase_order(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	userId BIGINT NOT NULL,
	status ENUM('CREATED','PROCESSING','COMPLETED') NOT NULL,
	createdOn DATETIME DEFAULT NOW(),
	CONSTRAINT fk_po_user_id FOREIGN KEY(userId) REFERENCES user(id)
);

CREATE TABLE IF NOT EXISTS order_item(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	orderId BIGINT NOT NULL,
	productId BIGINT NOT NULL,
	quantity INT NOT NULL,
	totalCost FLOAT NOT NULL,
	CONSTRAINT fk_oi_order_id FOREIGN KEY(orderId) REFERENCES purchase_order(id),
	CONSTRAINT fk_oi_product_id FOREIGN KEY(productId) REFERENCES product(id),
	CONSTRAINT uk_oi_order_id_product_id UNIQUE(orderId, productId)
);



CREATE TABLE IF NOT EXISTS shipping_address(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	userId BIGINT UNIQUE NOT NULL,
	unitNo VARCHAR(255) DEFAULT NULL,
	street TEXT NOT NULL,
	city VARCHAR(255) NOT NULL,
	province VARCHAR(255) NOT NULL,
	country VARCHAR(255) NOT NULL,
	zipCode VARCHAR(20) NOT NULL,
	CONSTRAINT fk_sa_user_id FOREIGN KEY(userId) REFERENCES user(id)
);

CREATE TABLE IF NOT EXISTS comment(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT NOT NULL,
	product_id BIGINT NOT NULL,
	rating INT NOT NULL,
	images TEXT DEFAULT NULL,
	review TEXT DEFAULT NULL, 
	CONSTRAINT fk_comment_user_id FOREIGN KEY(user_id) REFERENCES user(id),
	CONSTRAINT fk_comment_product_id FOREIGN KEY(product_id) REFERENCES product(id),
	CONSTRAINT uk_comment_uid_pid UNIQUE(user_id, product_id)
);

CREATE TABLE IF NOT EXISTS cart(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT UNIQUE NOT NULL,
	CONSTRAINT fk_c_user_id FOREIGN KEY(user_id) REFERENCES user(id)
);

CREATE TABLE IF NOT EXISTS cart_item(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	cart_id BIGINT NOT NULL,
	product_id BIGINT NOT NULL,
	quantity INT NOT NULL DEFAULT 0,
	CONSTRAINT fk_ci_cart_id FOREIGN KEY(cart_id) REFERENCES cart(id),
	CONSTRAINT fk_ci_product_id FOREIGN KEY(product_id) REFERENCES product(id),
	CONSTRAINT uk_ci_cid_pid UNIQUE(cart_id, product_id)
);



