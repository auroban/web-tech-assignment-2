DROP DATABASE IF EXISTS assignment2;
CREATE DATABASE assignment2;

USE assignment2;

CREATE TABLE IF NOT EXISTS product(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) UNIQUE NOT NULL,
	description TEXT DEFAULT NULL,
	price FLOAT NOT NULL,
	shipping_cost FLOAT NOT NULL,
	image TEXT DEFAULT NULL,
	currency ENUM('USD', 'CAD') NOT NULL,
	created_on DATETIME NOT NULL DEFAULT NOW(),
	updated_on DATETIME NOT NULL DEFAULT NOW()
);


CREATE TABLE IF NOT EXISTS user(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) UNIQUE NOT NULL,
	email VARCHAR(255) UNIQUE NOT NULL,
	passwd VARCHAR(255) NOT NULL,
	purchase_history TEXT DEFAULT NULL,
	shipping_address VARCHAR(255) NOT NULL
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

CREATE TABLE IF NOT EXISTS comment_resource(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	comment_id BIGINT NOT NULL,
	type ENUM('image', 'video', 'audio') NOT NULL,
	url TEXT NOT NULL,
	created_on DATETIME NOT NULL DEFAULT NOW(),
	updated_on DATETIME NOT NULL DEFAULT NOW(),
	CONSTRAINT fk_cr_comment_id FOREIGN KEY(comment_id) REFERENCES comment(id)
);

CREATE TABLE IF NOT EXISTS cart(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT NOT NULL,
	created_on DATETIME NOT NULL DEFAULT NOW(),
	updated_on DATETIME NOT NULL DEFAULT NOW(),
	CONSTRAINT fk_c_user_id FOREIGN KEY(user_id) REFERENCES user(id)
);

CREATE TABLE IF NOT EXISTS cart_item(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	cart_id BIGINT NOT NULL,
	product_id BIGINT NOT NULL,
	quantity INT NOT NULL DEFAULT 0,
	created_on DATETIME NOT NULL DEFAULT NOW(),
	updated_on DATETIME NOT NULL DEFAULT NOW(),
	CONSTRAINT fk_ci_cart_id FOREIGN KEY(cart_id) REFERENCES cart(id),
	CONSTRAINT fk_ci_product_id FOREIGN KEY(product_id) REFERENCES product(id),
	CONSTRAINT uk_ci_cid_pid UNIQUE(cart_id, product_id)
);

CREATE TABLE IF NOT EXISTS purchase_order(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT NOT NULL,
	status ENUM('CREATED','PROCESSING','COMPLETED') NOT NULL,
	created_on DATETIME NOT NULL DEFAULT NOW(),
	updated_on DATETIME NOT NULL DEFAULT NOW(),
	CONSTRAINT fk_po_user_id FOREIGN KEY(user_id) REFERENCES user(id)
);

CREATE TABLE IF NOT EXISTS order_item(
	id BIGINT AUTO_INCREMENT PRIMARY KEY,
	order_id BIGINT NOT NULL,
	product_id BIGINT NOT NULL,
	totalCost FLOAT NOT NULL,
	created_on DATETIME NOT NULL DEFAULT NOW(),
	updated_on DATETIME NOT NULL DEFAULT NOW(),
	CONSTRAINT fk_oi_order_id FOREIGN KEY(order_id) REFERENCES purchase_order(id),
	CONSTRAINT fk_oi_product_id FOREIGN KEY(product_id) REFERENCES product(id)
);